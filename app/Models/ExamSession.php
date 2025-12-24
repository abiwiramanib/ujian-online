<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'start_time',
        'end_time',
        'score',
        'question_order',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'question_order' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function recalculateScore(): void
    {
        $this->load('answers.question', 'exam.questions');

        $totalQuestions = $this->exam->questions->count();
        if ($totalQuestions === 0) {
            $this->score = 0;
            $this->save();
            return;
        }

        $correctMcqCount = $this->answers
            ->where('question.type', 'multiple_choice')
            ->filter(fn($answer) => $answer->option && $answer->option->is_correct)
            ->count();

        $correctEssayCount = $this->answers
            ->where('question.type', 'essay')
            ->where('is_correct', true)
            ->count();

        $totalCorrect = $correctMcqCount + $correctEssayCount;

        $this->score = ($totalCorrect / $totalQuestions) * 100;
        $this->save();
    }

    public function isGradingComplete(): bool
    {
        $this->load('answers.question', 'exam.questions');

        // Get all essay question IDs for this exam
        $essayQuestionIds = $this->exam->questions()
            ->where('type', 'essay')
            ->pluck('id');

        // If there are no essay questions, grading is always complete.
        if ($essayQuestionIds->isEmpty()) {
            return true;
        }

        // Check if there are any submitted answers for these essays that are not yet graded.
        $pendingEssayAnswers = $this->answers()
            ->whereIn('question_id', $essayQuestionIds)
            ->whereNull('is_correct')
            ->count();

        // If there are pending essays, grading is not complete.
        return $pendingEssayAnswers === 0;
    }
}
