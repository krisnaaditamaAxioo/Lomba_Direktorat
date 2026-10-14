<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'title',
        'description',
        'proof_photo',
        'status',
        'response',
        'handled_by',
        'handled_at',
        'complainant_id',
        'complainant_type'
    ];

    protected $casts = [
        'handled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to the complainant (Student or User)
    public function complainant(): MorphTo
    {
        return $this->morphTo();
    }

    // Relationship to the admin who handled the complaint
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Get status text for display
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Menunggu'
        };
    }
}
