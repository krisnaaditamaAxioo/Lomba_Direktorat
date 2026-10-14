<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'tahun_masuk',
        'sort_order',
        'is_active',
        'wali_kelas_id',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(\App\Models\User::class, 'wali_kelas_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
