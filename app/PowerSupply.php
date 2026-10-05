<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerSupply extends Model
{
    use HasFactory;
    protected $table = "power_supplies";
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function provinces()
    {
        return $this->belongsToMany(
            Province::class,
            'region_power_supply_pivots',
            'power_supply_id',
            'province_id'
        );
    }
}
