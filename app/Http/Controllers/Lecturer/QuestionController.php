<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->load('questions.options');

        return view('lecturer.questions.index', compact('exam'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used, handled by modal
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Exam $exam)
    {
        if ($exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:1024', // Max 1MB
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string|max:255',
            'correct_option' => 'required_if:type,multiple_choice|integer',
        ]);

        DB::transaction(function () use ($exam, $validated, $request) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $manager = new ImageManager(new GdDriver());
                $image = $request->file('image');
                $filename = Str::uuid() . '.jpg';
                $path = 'question_images/' . $filename;

                $img = $manager->read($image->getRealPath());
                $img->scale(width: 800);
                
                Storage::disk('public')->put($path, (string) $img->toJpeg(75));
                $imagePath = $path;
            }

            $question = $exam->questions()->create([
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'image_path' => $imagePath,
            ]);

            if ($validated['type'] === 'multiple_choice') {
                if (isset($validated['options'])) {
                    foreach ($validated['options'] as $index => $optionText) {
                        $question->options()->create([
                            'option_text' => $optionText,
                            'is_correct' => ($index == $validated['correct_option']),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('lecturer.exams.questions.index', $exam)->with('status', 'Soal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        // Not used
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        // Not used, handled by modal
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        if ($question->exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:1024', // Max 1MB
            'delete_image' => 'nullable|boolean',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string|max:255',
            'correct_option' => 'required_if:type,multiple_choice|integer',
        ]);

        DB::transaction(function () use ($question, $validated, $request) {
            $imagePath = $question->image_path;

            // Handle image deletion
            if ($request->boolean('delete_image') && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            // Handle new image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $manager = new ImageManager(new GdDriver());
                $image = $request->file('image');
                $filename = Str::uuid() . '.jpg';
                $path = 'question_images/' . $filename;

                $img = $manager->read($image->getRealPath());
                $img->scale(width: 800);
                
                Storage::disk('public')->put($path, (string) $img->toJpeg(75));
                $imagePath = $path;
            }

            $question->update([
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'image_path' => $imagePath,
            ]);

            // Delete old options first
            $question->options()->delete();

            // If the new type is multiple choice, create new options
            if ($validated['type'] === 'multiple_choice') {
                if (isset($validated['options'])) {
                    foreach ($validated['options'] as $index => $optionText) {
                        $question->options()->create([
                            'option_text' => $optionText,
                            'is_correct' => ($index == $validated['correct_option']),
                        ]);
                    }
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Soal berhasil diperbarui!']);
        }

        return redirect()->route('lecturer.exams.questions.index', $question->exam)->with('status', 'Soal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        if ($question->exam->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam = $question->exam;
        DB::transaction(function () use ($question) {
            // Delete associated image if exists
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $question->delete();
        });

        return redirect()->route('lecturer.exams.questions.index', $exam)->with('status', 'Soal berhasil dihapus!');
    }
}
