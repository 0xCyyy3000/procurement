<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedOrders extends Model
{
    use HasFactory;
    protected $casts = [
        'purchased_items' => 'array'
    ];
}
