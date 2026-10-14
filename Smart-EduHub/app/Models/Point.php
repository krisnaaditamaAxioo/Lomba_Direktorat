<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = ['student_id', 'type', 'score', 'reason', 'proof_photo', 'date', 'academic_year'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
