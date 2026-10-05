<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionPowerSupplyPivot extends Model
{
    use HasFactory;
    protected $table = "region_power_supply_pivots";
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
