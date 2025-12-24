<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\CheatingLog;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ExamController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Get exams assigned to the student
        $exams = $student->assignedExams()
            ->whereIn('status', ['published', 'finished'])
            ->with(['subject', 'lecturer'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $studentId = $student->id;

        $completedExamIds = ExamSession::where('user_id', $studentId)
            ->whereNotNull('end_time')
            ->pluck('exam_id')
            ->toArray();
            
        $inProgressExamIds = ExamSession::where('user_id', $studentId)
            ->whereNull('end_time')
            ->pluck('exam_id')
            ->toArray();

        return view('student.exams.index', compact('exams', 'completedExamIds', 'inProgressExamIds'));
    }

    public function results()
    {
        $completedSessions = ExamSession::where('user_id', Auth::id())
            ->whereNotNull('end_time')
            ->with([
                'exam.subject', 
                'exam.lecturer', 
                'exam' => function ($query) {
                    $query->withCount('questions');
                }
            ])
            ->latest('end_time')
            ->paginate(10);

        return view('student.results.index', compact('completedSessions'));
    }

    public function start(Request $request, Exam $exam)
    {
        $studentId = Auth::id();

        // Check if an in-progress session already exists.
        $session = ExamSession::where('user_id', $studentId)
            ->where('exam_id', $exam->id)
            ->whereNull('end_time')
            ->first();

        if ($session) {
            // If session exists, it's a "continue" action, bypass token check.
            return redirect()->route('student.exams.show', $session);
        }

        // If no session exists, it's a "start new" action, so validate the token.
        if ($exam->token !== $request->input('token')) {
            return redirect()->back()->withErrors(['token' => 'Token yang Anda masukkan tidak valid.'])->withInput();
        }

        // --- Create the shuffled question order ---
        // Separate questions by type, shuffle each group, then merge.
        $multipleChoiceIds = $exam->questions()->where('type', 'multiple_choice')->pluck('id')->shuffle();
        $essayIds = $exam->questions()->where('type', 'essay')->pluck('id')->shuffle();

        $questionIds = $multipleChoiceIds->merge($essayIds)->toArray();
        // --- End shuffle ---

        // Create a new session
        $newSession = ExamSession::create([
            'user_id' => $studentId,
            'exam_id' => $exam->id,
            'start_time' => Carbon::now(),
            'question_order' => $questionIds, // Save the shuffled order
        ]);

        return redirect()->route('student.exams.show', $newSession);
    }

    public function show(ExamSession $session)
    {
        // Authorize that the session belongs to the logged-in student
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load relationships
        $session->load(['exam.questions.options', 'answers']);

        // --- PERSISTENT SHUFFLE LOGIC ---
        $orderedIds = $session->question_order;

        if (empty($orderedIds)) {
            // Fallback for older sessions that might not have the order saved
            $orderedQuestions = $session->exam->questions->shuffle();
        } else {
            // Sort the loaded questions according to the saved order
            $questions = $session->exam->questions->keyBy('id');
            $orderedQuestions = new Collection();
            foreach ($orderedIds as $id) {
                if (isset($questions[$id])) {
                    $orderedQuestions->push($questions[$id]);
                }
            }
        }

        // Shuffle the options for each question
        $orderedQuestions->each(function ($question) {
            $question->setRelation('options', $question->options->shuffle());
        });

        // Set the sorted and option-shuffled questions back to the exam relationship
        $session->exam->setRelation('questions', $orderedQuestions);
        // --- END PERSISTENT SHUFFLE LOGIC ---

        // Create a key-value collection for easy lookup in the view
        $savedAnswers = $session->answers->keyBy('question_id');

        return view('student.exams.show', compact('session', 'savedAnswers'));
    }

    public function submit(Request $request, ExamSession $session)
    {
        // Authorize that the session belongs to the logged-in student
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $submittedMcqAnswers = $request->input('answers', []);
        $submittedEssayAnswers = $request->input('answers_text', []);
        
        // --- Scoring Logic ---
        $score = 0;
        $finalScore = 0; // Initialize score here
        $multipleChoiceQuestions = $session->exam->questions()->where('type', 'multiple_choice')->get();
        $totalMultipleChoice = $multipleChoiceQuestions->count();
        $correctOptions = Option::whereIn('question_id', $multipleChoiceQuestions->pluck('id'))
            ->where('is_correct', true)
            ->pluck('id', 'question_id');

        DB::transaction(function () use ($session, $submittedMcqAnswers, $submittedEssayAnswers) {
            // Process and save all answers first
            if (is_array($submittedMcqAnswers)) {
                foreach ($submittedMcqAnswers as $questionId => $optionId) {
                    if (!empty($optionId)) {
                        $session->answers()->updateOrCreate(
                            ['question_id' => $questionId],
                            ['option_id' => $optionId, 'answer_text' => null]
                        );
                    }
                }
            }

            if (is_array($submittedEssayAnswers)) {
                foreach ($submittedEssayAnswers as $questionId => $answerText) {
                    if (!is_null($answerText)) {
                        $session->answers()->updateOrCreate(
                            ['question_id' => $questionId],
                            ['option_id' => null, 'answer_text' => $answerText]
                        );
                    }
                }
            }

            // Set end time
            $session->end_time = Carbon::now();
            $session->save();

            // Recalculate score based on available data
            $session->recalculateScore();
        });

        // Check if the exam has essay questions to determine the redirect message
        $hasEssays = $session->exam->questions()->where('type', 'essay')->exists();

        if ($hasEssays) {
            return redirect()->route('dashboard')->with('status', 'Ujian telah selesai! Jawaban esai Anda akan dinilai oleh dosen. Hasil akhir akan tersedia di halaman "Hasil Ujian" setelah dinilai.');
        } else {
            return redirect()->route('dashboard')->with('status', 'Ujian telah selesai! Skor akhir Anda: ' . round($session->score));
        }
    }

    public function logCheating(Request $request, ExamSession $session)
    {
        Log::info('logCheating method hit for session: ' . $session->id);

        try {
            if ($session->user_id !== Auth::id()) {
                Log::warning('Unauthorized attempt to log cheating for session: ' . $session->id);
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'reason' => 'nullable|string|max:255',
            ]);

            $reason = $validated['reason'] ?? 'Mahasiswa terdeteksi meninggalkan halaman ujian.';

            CheatingLog::create([
                'exam_session_id' => $session->id,
                'user_id' => $session->user_id,
                'exam_id' => $session->exam->id,
                'message' => $reason,
            ]);

            Log::info('CheatingLog created successfully for session: ' . $session->id);
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Failed to create CheatingLog for session: ' . $session->id . ' - Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    public function autosave(Request $request, ExamSession $session)
    {
        if ($session->user_id !== Auth::id() || $session->end_time !== null) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or session ended'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'option_id' => 'nullable|integer|exists:options,id',
            'answer_text' => 'nullable|string',
        ]);

        $question = Question::find($validated['question_id']);

        if (!$question || $question->exam_id !== $session->exam_id) {
            return response()->json(['status' => 'error', 'message' => 'Invalid question for this exam.'], 422);
        }

        $session->answers()->updateOrCreate(
            [
                'question_id' => $validated['question_id'],
            ],
            [
                'option_id' => $question->type === 'multiple_choice' ? ($validated['option_id'] ?? null) : null,
                'answer_text' => $question->type === 'essay' ? ($validated['answer_text'] ?? null) : null,
            ]
        );

        return response()->json(['status' => 'success']);
    }
}
