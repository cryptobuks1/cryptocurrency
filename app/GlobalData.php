<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalData extends Model
{
    public $incrementing = false;

    protected $fillable = [
        "id",
        "name",
        "symbol",
        "rank",
        "price_usd",
        "price_btc",
        "24h_volume_usd",
        "market_cap_usd",
        "available_supply",
        "total_supply",
        "percent_change_1h",
        "percent_change_24h",
        "percent_change_7d",
        "last_updated"
    ];
}
