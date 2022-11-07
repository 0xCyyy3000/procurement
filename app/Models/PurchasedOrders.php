<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchasedOrders extends Model
{
    use HasFactory;

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
