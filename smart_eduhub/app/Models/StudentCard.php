<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCard extends Model
{
    protected $fillable = [
        'student_id',
        'card_number',
        'balance',
        'status',
        'pin',
    ];

    protected $hidden = [
        'pin',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StudentCardTransaction::class, 'card_id');
    }
}
