<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_active'];

    public static function active()
    {
        return self::where('is_active', true)->first();
    }

    public static function getIdByName($name)
    {
        return self::where('name', $name)->value('id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'academic_year_student')
                    ->withTimestamps();
    }
}
