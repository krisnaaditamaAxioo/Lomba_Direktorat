<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCardTransaction extends Model
{
    protected $fillable = [
        'card_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_number',
        'description',
        'related_card_id',
        'performed_by',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(StudentCard::class, 'card_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    public function relatedCard(): BelongsTo
    {
        return $this->belongsTo(StudentCard::class, 'related_card_id');
    }

    public function getPerformerNameAttribute()
    {
        if ($this->performedBy) {
            return $this->performedBy->name;
        }

        // For transfer transactions, show the student's name
        if ($this->type === 'transfer') {
            return $this->card->student->nama_lengkap ?? 'Siswa';
        }

        if ($this->type === 'transfer_receive' && $this->relatedCard) {
            return $this->relatedCard->student->nama_lengkap ?? 'Siswa';
        }

        return 'Sistem';
    }
}
