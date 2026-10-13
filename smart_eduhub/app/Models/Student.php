<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_siswa',
        'username',
        'pin',
        'status',
        'nama_lengkap',
        'angkatan_id',
        'foto',
        'nisn',
        'no_nik',
        'gender',
        'mother_name',
        'religion',
        'height',
        'weight',
        'place_of_birth',
        'dateofbirth',
        'address',
        'nis_siswa',
        'role',
        'email',
        'phone',
        'parent_phone',
        'gpa',
        'previous_school',
    ];

    public function angkatanModel()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id');
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id');
    }

    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_student')
                    ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(AcademicYearStudent::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function permits()
    {
        return $this->hasMany(StudentPermit::class);
    }

    public function cards()
    {
        return $this->hasMany(StudentCard::class);
    }

    public function cardRequests()
    {
        return $this->hasMany(StudentCardRequest::class);
    }

    public function notifications()
    {
        return $this->hasMany(StudentNotification::class);
    }

    public function moduleProgress()
    {
        return $this->hasMany(ModuleProgress::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complainant');
    }

    public function parents()
    {
        return $this->hasMany(ParentUser::class, 'student_id');
    }

    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }

    public function getAngkatanAttribute()
    {
        return $this->angkatanModel?->code;
    }

    // Accessor untuk gender dalam format teks
    public function getGenderTextAttribute()
    {
        return $this->gender === 1 ? 'Perempuan' : 'Laki-laki';
    }

    // Accessor untuk gender dalam format L/P
    public function getGenderShortAttribute()
    {
        return $this->gender === 1 ? 'P' : 'L';
    }

    // Accessor untuk status dalam format teks
    public function getStatusTextAttribute()
    {
        return $this->status === 1 ? 'Nonaktif' : 'Aktif';
    }

    // Accessor untuk status dalam format active/inactive
    public function getStatusShortAttribute()
    {
        return $this->status === 1 ? 'inactive' : 'active';
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 0)->orWhere('status', '0')->orWhere('status', 'active');
        });
    }
}
