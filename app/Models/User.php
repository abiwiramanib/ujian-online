<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'npm',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'user_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'user_id');
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }

    public function enrolledSubjects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_user');
    }

    public function assignedExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_user');
    }

    public function subjectRequests(): HasMany
    {
        return $this->hasMany(SubjectRequest::class, 'lecturer_id');
    }
}
