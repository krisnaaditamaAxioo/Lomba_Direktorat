<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'total_questions',
        'correct_answers',
        'score',
        'started_at',
        'finished_at',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
