<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\CheatingLog;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lecturerId = Auth::id();

        // Base query for the lecturer's exams
        $examsQuery = Exam::where('user_id', $lecturerId)->with('subject')->withCount('questions');

        // --- Inefficient Stats Calculation ---
        // Calculate stats before any filtering for an accurate overview
        $stats = [
            'total' => $examsQuery->clone()->count(),
            'draft' => $examsQuery->clone()->where('status', 'draft')->count(),
            'published' => $examsQuery->clone()->where('status', 'published')->count(),
            'finished' => $examsQuery->clone()->where('status', 'finished')->count(),
        ];

        // --- Apply Filters ---
        // 1. Filter by search term
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $examsQuery->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%")
                      ->orWhereHas('subject', function ($q) use ($searchTerm) {
                          $q->where('name', 'like', "%{$searchTerm}%");
                      });
            });
        }

        // 2. Filter by status
        if ($request->filled('status') && in_array($request->input('status'), ['draft', 'published', 'finished'])) {
            $examsQuery->where('status', $request->input('status'));
        }

        // --- Paginate the final query ---
        $exams = $examsQuery->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('lecturer.exams._exam-list', compact('exams'))->render();
        }
        
        $subjects = Subject::where('user_id', $lecturerId)->get();
            
        return view('lecturer.exams.index', compact('exams', 'subjects', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
        ]);

        $validated['user_id'] = Auth::id();

        Exam::create($validated);

        return redirect()->route('lecturer.exams.index')->with('status', 'Ujian berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
        ]);

        $exam->update($validated);

        return redirect()->route('lecturer.exams.index')->with('status', 'Ujian berhasil diperbarui!');
    }

    public function publish(Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($exam->questions()->count() === 0) {
            return redirect()->route('lecturer.exams.index')->with('error', 'Ujian tidak dapat dipublikasikan karena belum memiliki soal.');
        }

        // Generate a unique 6-character uppercase token
        do {
            $token = strtoupper(Str::random(6));
        } while (Exam::where('token', $token)->exists());

        $exam->update([
            'status' => 'published',
            'token' => $token,
            'published_at' => Carbon::now(),
        ]);

        return redirect()->route('lecturer.exams.index')->with('status', 'Ujian berhasil dipublikasikan! Token: ' . $token);
    }

    public function finish(Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->update([
            'status' => 'finished',
            'end_time' => Carbon::now(),
        ]);

        return redirect()->route('lecturer.exams.index')->with('status', 'Ujian telah berhasil diakhiri.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->delete();

        return redirect()->route('lecturer.exams.index')->with('status', 'Ujian berhasil dihapus!');
    }

    public function showLogs(Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $logs = CheatingLog::where('exam_id', $exam->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('lecturer.exams.logs', compact('exam', 'logs'));
    }

    public function results(Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = $exam->examSessions()
            ->whereNotNull('end_time')
            ->with('student')
            ->latest('end_time')
            ->paginate(10);

        return view('lecturer.exams.results', compact('exam', 'sessions'));
    }

    public function showAnswers(ExamSession $session)
    {
        // Eager load necessary relationships
        $session->load(['student', 'exam.questions.options', 'answers']);

        // Authorize that the lecturer owns the exam this session belongs to
        if ($session->exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get all questions for the exam, ordered as the student saw them
        $orderedIds = $session->question_order;
        $questions = $session->exam->questions->keyBy('id');
        $orderedQuestions = collect($orderedIds)->map(function ($id) use ($questions) {
            return $questions[$id] ?? null;
        })->filter();

        // Create a map of student's answers for easy lookup
        $studentAnswers = $session->answers->keyBy('question_id');

        return view('lecturer.exams.answers', compact('session', 'orderedQuestions', 'studentAnswers'));
    }

    public function gradeEssay(Request $request, Answer $answer)
    {
        $validated = $request->validate([
            'is_correct' => 'required|boolean',
        ]);

        // Authorize that the lecturer owns the exam this answer belongs to
        if ($answer->examSession->exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure we are only grading essays
        if ($answer->question->type !== 'essay') {
            return back()->withErrors(['error' => 'Hanya jawaban esai yang bisa dinilai.']);
        }

        $answer->update([
            'is_correct' => $validated['is_correct'],
        ]);

        // Recalculate the total score for the session
        $answer->examSession->recalculateScore();

        return back()->with('status', 'Jawaban esai berhasil dinilai.');
    }

    public function assign(Exam $exam)
    {
        // Authorization: ensure the lecturer owns the exam
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get all students from the system
        $students = User::where('role', 'mahasiswa')->orderBy('name')->get();

        // Get IDs of students already assigned to this exam
        $assignedStudentIds = $exam->assignedStudents()->pluck('users.id')->toArray();

        return view('lecturer.exams.assign', compact('exam', 'students', 'assignedStudentIds'));
    }

    public function syncStudents(Request $request, Exam $exam)
    {
        // Authorization: ensure the lecturer owns the exam
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'students' => 'nullable|array',
            'students.*' => 'integer|exists:users,id',
        ]);

        $exam->assignedStudents()->sync($validated['students'] ?? []);

        return redirect()->route('lecturer.exams.index')->with('status', 'Daftar mahasiswa untuk ujian berhasil diperbarui.');
    }
}
