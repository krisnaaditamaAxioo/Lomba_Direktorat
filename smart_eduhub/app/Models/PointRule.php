<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $table = 'point_rules'; // Pastikan nama tabel benar

    protected $fillable = [
        'name',
        'condition_type',
        'attendance_status',
        'threshold',
        'point_type',
        'point_score',
        'reason_template',
        'sort_order',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
