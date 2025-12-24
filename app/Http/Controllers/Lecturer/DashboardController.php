<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lecturerId = Auth::id();

        // Stats Cards Data
        $totalExams = Exam::where('user_id', $lecturerId)->count();
        $activeExams = Exam::where('user_id', $lecturerId)
            ->where('status', 'published')
            ->where(function($query) {
                $query->whereNull('end_time')
                      ->orWhere('end_time', '>=', now());
            })
            ->count();
        $totalSubjects = Subject::where('user_id', $lecturerId)->count();
        $totalQuestions = Question::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })->count();
        
        // Students statistics
        $totalStudents = ExamSession::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })->distinct('user_id')->count('user_id');

        $completedSessions = ExamSession::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })->whereNotNull('end_time')->count();
        
        // Recent activity: students who finished exams in the last 7 days
        $recentStudentActivity = ExamSession::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })
        ->whereNotNull('end_time')
        ->where('end_time', '>=', Carbon::now()->subDays(7))
        ->distinct('user_id')
        ->count();

        // Chart data - Exam submissions last 7 days
        $examSubmissionsChart = ExamSession::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })
        ->whereNotNull('end_time')
        ->where('end_time', '>=', Carbon::now()->subDays(6))
        ->selectRaw('DATE(end_time) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('d M');
            $submission = $examSubmissionsChart->firstWhere('date', $date);
            $chartData[] = $submission ? $submission->count : 0;
        }

        // Recent Exams Table with more info
        $recentExams = Exam::where('user_id', $lecturerId)
            ->with(['subject', 'questions'])
            ->withCount(['examSessions', 'examSessions as completed_sessions_count' => function ($query) {
                $query->whereNotNull('end_time');
            }])
            ->latest()
            ->take(5)
            ->get();

        // Upcoming exams (recently published)
        $upcomingExams = Exam::where('user_id', $lecturerId)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        // Recent Student Submissions Table
        $recentSessions = ExamSession::whereIn('exam_id', function ($query) use ($lecturerId) {
            $query->select('id')->from('exams')->where('user_id', $lecturerId);
        })
        ->whereNotNull('end_time')
        ->with(['student', 'exam', 'answers'])
        ->latest('end_time')
        ->take(5)
        ->get();

        // Calculate average scores for recent sessions
        foreach ($recentSessions as $session) {
            $correctAnswers = $session->answers->where('is_correct', true)->count();
            $totalQuestions = $session->exam->questions->count();
            $session->score_percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        }

        // Exam status distribution
        $examStatusDistribution = Exam::where('user_id', $lecturerId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('lecturer.dashboard', compact(
            'totalExams',
            'activeExams',
            'totalSubjects',
            'totalQuestions',
            'totalStudents',
            'completedSessions',
            'recentStudentActivity',
            'recentExams',
            'recentSessions',
            'upcomingExams',
            'chartLabels',
            'chartData',
            'examStatusDistribution'
        ));
    }
}