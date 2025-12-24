<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $lecturerId = Auth::id();

        $questionsQuery = Question::whereHas('exam', function ($query) use ($lecturerId) {
            $query->where('user_id', $lecturerId);
        })->with('exam.subject');

        if ($request->has('subject') && $request->subject != '') {
            $questionsQuery->whereHas('exam', function ($query) use ($request) {
                $query->where('subject_id', $request->subject);
            });
        }

        $questions = $questionsQuery->latest()->paginate(10);
        $subjects = Subject::where('user_id', $lecturerId)->get();

        return view('lecturer.question-bank.index', compact('questions', 'subjects'));
    }
}
