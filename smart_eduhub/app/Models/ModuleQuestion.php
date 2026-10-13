<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'type',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'score',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}