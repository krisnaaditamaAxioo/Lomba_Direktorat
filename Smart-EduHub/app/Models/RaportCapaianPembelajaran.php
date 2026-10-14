<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaportCapaianPembelajaran extends Model
{
    protected $table = 'raport_capaian_pembelajaran';

    protected $fillable = [
        'raport_id',
        'no',
        'aspek_penilaian',
        'predikat',
        'keterangan',
    ];

    public function raport(): BelongsTo
    {
        return $this->belongsTo(RaportIndustri::class, 'raport_id');
    }

    /**
     * Returns keterangan auto-generated from predikat if not set.
     */
    public function getKeteranganLabelAttribute(): string
    {
        if ($this->keterangan) {
            return $this->keterangan;
        }
        return match (strtoupper($this->predikat ?? '')) {
            'A'     => 'Sangat Baik',
            'B'     => 'Baik',
            'C'     => 'Cukup',
            'D'     => 'Kurang',
            default => '-',
        };
    }
}
