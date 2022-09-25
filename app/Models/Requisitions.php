<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisitions extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'created_at' => 'datetime:D, d M Y-g:i A',
    //     'updated_at' => 'datetime:D, d M Y-g:i A'
    // ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone(env("APP_TIMEZONE"))
            ->toDayDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone(env("APP_TIMEZONE"))
            ->toDayDateTimeString();
    }
}
