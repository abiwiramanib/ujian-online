<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\SubjectRequestController as AdminSubjectRequestController;
use App\Http\Controllers\Lecturer\DashboardController as LecturerDashboardController;
use App\Http\Controllers\Lecturer\ExamController as LecturerExamController;
use App\Http\Controllers\Lecturer\QuestionController as LecturerQuestionController;
use App\Http\Controllers\Lecturer\QuestionBankController as LecturerQuestionBankController;
use App\Http\Controllers\Lecturer\SubjectController as LecturerSubjectController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Main Student Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:mahasiswa'])->name('dashboard');

// Other Student Routes
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->prefix('student')->name('student.')->group(function () {
    Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('/results', [StudentExamController::class, 'results'])->name('results.index');
    Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('/exam-sessions/{session}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exam-sessions/{session}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    Route::post('/exam-sessions/{session}/log-cheating', [StudentExamController::class, 'logCheating'])->name('exams.log_cheating');
    Route::post('/exam-sessions/{session}/autosave', [StudentExamController::class, 'autosave'])->name('exams.autosave');
});

// Admin Dashboard
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('subjects', AdminSubjectController::class);

    Route::get('/subject-requests', [AdminSubjectRequestController::class, 'index'])->name('subject-requests.index');
    Route::patch('/subject-requests/{subject_request}/approve', [AdminSubjectRequestController::class, 'approve'])->name('subject-requests.approve');
    Route::patch('/subject-requests/{subject_request}/reject', [AdminSubjectRequestController::class, 'reject'])->name('subject-requests.reject');
});

// Lecturer Dashboard
Route::middleware(['auth', 'verified', 'role:dosen'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/question-bank', [LecturerQuestionBankController::class, 'index'])->name('question-bank.index');
    
    // Subject & Enrollment Management
    Route::get('/subjects/request', [LecturerSubjectController::class, 'createRequestForm'])->name('subjects.request');
    Route::post('/subjects/request', [LecturerSubjectController::class, 'storeRequest'])->name('subjects.request.store');
    Route::get('/subjects', [LecturerSubjectController::class, 'index'])->name('subjects.index');

    Route::resource('exams', LecturerExamController::class);
    Route::get('exams/{exam}/logs', [LecturerExamController::class, 'showLogs'])->name('exams.logs');
    Route::get('exams/{exam}/results', [LecturerExamController::class, 'results'])->name('exams.results');
    Route::get('exam-sessions/{session}/answers', [LecturerExamController::class, 'showAnswers'])->name('exams.answers');
    Route::post('answers/{answer}/grade', [LecturerExamController::class, 'gradeEssay'])->name('answers.grade');
    Route::patch('exams/{exam}/publish', [LecturerExamController::class, 'publish'])->name('exams.publish');
    Route::patch('exams/{exam}/finish', [LecturerExamController::class, 'finish'])->name('exams.finish');
    Route::get('exams/{exam}/assign', [LecturerExamController::class, 'assign'])->name('exams.assign');
    Route::post('exams/{exam}/assign', [LecturerExamController::class, 'syncStudents'])->name('exams.assign.store');
    Route::resource('exams.questions', LecturerQuestionController::class)->shallow();
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
