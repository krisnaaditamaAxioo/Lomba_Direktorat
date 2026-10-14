<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaportIndustri extends Model
{
    protected $table = 'raport_industri';

    protected $fillable = [
        'student_id',
        'angkatan_id',
        'tahun_pelajaran',
        'tingkat',
        'program_keahlian',
        'trainer_name',
        'trainer_no_serti',
        'trainer_nbm',
        'catatan',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function capaianPembelajaran(): HasMany
    {
        return $this->hasMany(RaportCapaianPembelajaran::class, 'raport_id')->orderBy('no');
    }

    public function ujiSertifikasi(): HasMany
    {
        return $this->hasMany(RaportUjiSertifikasi::class, 'raport_id')->orderBy('no');
    }

    /**
     * Get the Wali Kelas for this raport via the angkatan relationship.
     */
    public function getWaliKelasAttribute()
    {
        return $this->angkatan?->waliKelas;
    }
}
