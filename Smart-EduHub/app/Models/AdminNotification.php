<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function sendTo(int $userId, string $type, string $title, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function sendToAdmins(string $type, string $title, string $message, array $data = []): void
    {
        // Kirim ke semua user dengan role admin (dan juga mungkin wakasis, guru, dll sesuai kebutuhan)
        $admins = User::whereIn('role', ['admin', 'wakasis', 'guru', 'bk', 'teller', 'bums'])->get();
        foreach ($admins as $admin) {
            self::sendTo($admin->id, $type, $title, $message, $data);
        }
    }
}
