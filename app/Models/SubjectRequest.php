<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
        'name',
        'code',
        'description',
    ];

    /**
     * Get the lecturer that owns the request.
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
