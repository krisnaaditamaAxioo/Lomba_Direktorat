<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'registrations';
    protected $fillable = [
        'application_id',
        'name',
        'email',
        'phone',
        'nisn',
        'no_nik',
        'gender',
        'mother_name',
        'religion',
        'height',
        'weight',
        'place_of_birth',
        'dateofbirth',
        'nis_siswa',
        'angkatan',
        'previous_school',
        'address',
        'status',
        'academic_year',
    ];

    public function examResult()
    {
        return $this->hasOne(ExamResult::class);
    }
}
