<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['student_id', 'date', 'check_in', 'status', 'location', 'academic_year'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
