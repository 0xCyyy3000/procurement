<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSavedItems extends Model
{
    use HasFactory;

    protected $casts = [
        'items' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
