<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = ['page', 'number', 'label', 'icon', 'is_visible', 'sort_order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];
}
