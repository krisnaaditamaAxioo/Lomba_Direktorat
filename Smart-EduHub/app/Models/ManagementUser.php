<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ManagementUser extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'no_wa',
        'role',
        'foto'
    ];

    protected $hidden = [
        'password',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isWaliKelas()
    {
        return $this->role === 'wali_kelas';
    }

    public function isWakasis()
    {
        return $this->role === 'wakasis';
    }

    public function isBK()
    {
        return $this->role === 'bk';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }
}
