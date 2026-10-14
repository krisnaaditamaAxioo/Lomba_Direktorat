<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'module_id',
        'answer',
        'file_path',
        'answer_files',
        'submitted_at',
        'status',
        'feedback',
        'score',
        'max_score',
        'question_scores',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'score' => 'integer',
            'max_score' => 'integer',
            'answer_files' => 'array',
            'question_scores' => 'array',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
