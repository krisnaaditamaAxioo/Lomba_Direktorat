<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'title',
        'message',
        'data',
        'read',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public static function sendTo(int $studentId, string $type, string $title, string $message, array $data = []): self
    {
        return self::create([
            'student_id' => $studentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'read' => false,
        ]);
    }

    public static function sendToMultiple(array $studentIds, string $type, string $title, string $message, array $data = []): void
    {
        foreach ($studentIds as $studentId) {
            self::sendTo($studentId, $type, $title, $message, $data);
        }
    }
}
