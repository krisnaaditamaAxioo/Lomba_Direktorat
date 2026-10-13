<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'no_wa',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
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
    
    public function isTeller()
    {
        return $this->role === 'teller';
    }

    public function isBums()
    {
        return $this->role === 'bums';
    }

    public function isFinanceStaff()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canAccessFinanceDashboard()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canAccessFinanceRekap()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canManageFinanceRequests()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canViewFinanceCards()
    {
        return $this->isFinanceStaff();
    }

    public function canViewFinanceTransactions()
    {
        return $this->isFinanceStaff();
    }

    public function canManageFinanceTransactions()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canTopupFinance()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canProcessFinancePayment()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canUpdateFinanceCardStatus()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canViewFinancePaymentHistory()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canViewFinanceRekap()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canApproveFinanceRequests()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canWithdrawFinance()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canManageFinanceCards()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canEditFinanceTransactions()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function canDeleteFinanceTransactions()
    {
        return $this->isAdmin() || $this->isTeller() || $this->isBums();
    }

    public function roleLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->role) {
                'admin'      => 'Administrator',
                'guru'       => 'Guru',
                'wali_kelas' => 'Wali Kelas',
                'wakasis'    => 'Waka Kesiswaan',
                'bk'         => 'BK',
                'teller'     => 'Teller',
                'bums'       => 'BUMS',
                default      => ucwords(str_replace('_', ' ', $this->role ?? '')),
            }
        );
    }

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complainant');
    }

    public function adminNotifications()
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function unreadAdminNotifications()
    {
        return $this->adminNotifications()->whereNull('read_at');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
