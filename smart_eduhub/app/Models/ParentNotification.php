<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentNotification extends Model
{
    protected $fillable = [
        'parent_id',
        'type',
        'title',
        'message',
        'data',
        'read',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(ParentUser::class, 'parent_id');
    }
}