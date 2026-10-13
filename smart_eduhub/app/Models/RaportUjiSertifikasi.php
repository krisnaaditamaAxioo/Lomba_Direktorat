<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaportUjiSertifikasi extends Model
{
    protected $table = 'raport_uji_sertifikasi';

    protected $fillable = [
        'raport_id',
        'no',
        'nama_ujian',
        'no_serti',
        'status',
    ];

    public function raport(): BelongsTo
    {
        return $this->belongsTo(RaportIndustri::class, 'raport_id');
    }
}
