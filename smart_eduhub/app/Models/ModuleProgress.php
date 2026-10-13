<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'module_id',
        'progress',
        'opened_at',
        'completed_at',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'opened_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
