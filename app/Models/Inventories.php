<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventories extends Model
{
    use HasFactory;

    public function units()
    {
        return $this->hasMany(Units::class, 'unit_id');
    }
}
