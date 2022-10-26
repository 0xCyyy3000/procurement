<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItems extends Model
{
    use HasFactory;

    protected $casts = [
        'units' => 'array',
        'qtys' => 'array',
        'prices' => 'array'
    ];
}
