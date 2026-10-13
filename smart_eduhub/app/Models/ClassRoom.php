<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'name',
        'major_id',
        'level',
        'wali_kelas_id',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function academicYearStudents()
    {
        return $this->hasMany(AcademicYearStudent::class);
    }
}
