<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'parents';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'password',
        'student_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complainant');
    }

    public function notifications()
    {
        return $this->hasMany(ParentNotification::class, 'parent_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }
}
