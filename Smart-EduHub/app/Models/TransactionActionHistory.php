<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TransactionActionHistory extends Model
{
    protected $fillable = [
        'transaction_id',
        'action',
        'snapshot',
        'changes',
        'performed_by',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'changes'  => 'array',
    ];

    /**
     * Rekam riwayat perubahan transaksi.
     *
     * @param  StudentCardTransaction  $transaction
     * @param  string  $action  created|updated|deleted
     * @param  array|null  $changes  [field => ['old' => .., 'new' => ..]]
     * @param  int|null  $performedBy
     */
    public static function record(StudentCardTransaction $transaction, string $action, ?array $changes = null, ?int $performedBy = null): self
    {
        // Muat relasi agar snapshot menyimpan card_number dan nama siswa
        // (berguna untuk pencarian, terutama setelah transaksi dihapus)
        $transaction->loadMissing(['card.student']);

        $snapshot = $transaction->toArray();
        $snapshot['card_number']  = $transaction->card->card_number ?? null;
        $snapshot['student_name'] = $transaction->card->student->nama_lengkap ?? null;
        $snapshot['student_nis']  = $transaction->card->student->nis_siswa ?? null;

        return self::create([
            'transaction_id' => $transaction->id,
            'action'         => $action,
            'snapshot'       => $snapshot,
            'changes'        => $changes,
            'performed_by'   => $performedBy ?? (Auth::check() ? Auth::id() : null),
        ]);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(StudentCardTransaction::class, 'transaction_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            default   => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'emerald',
            'updated' => 'amber',
            'deleted' => 'rose',
            default   => 'slate',
        };
    }
}
