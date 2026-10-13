<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYearStudent extends Model
{
    protected $table = 'academic_year_student';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_room_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
