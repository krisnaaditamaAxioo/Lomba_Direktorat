<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCardRequest extends Model
{
    protected $fillable = [
        'student_id',
        'parent_name',
        'parent_phone',
        'statement_file',
        'status',
        'admin_note',
        'approved_by',
        'approved_at',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
