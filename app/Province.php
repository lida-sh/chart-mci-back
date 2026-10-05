<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = "provinces";

    public function centerPivots(){
        return $this->hasMany(CenterPivot::class, "province_id");
    }
    public function cities(){
        return $this->hasMany(City::class, "province_id");
    }
    public function powerSupplies(){
        return $this->belongsToMany(PowerSupply::class,
            'region_power_supply_pivots',
            'province_id',
            'power_supply_id')->withPivot('power_supply_count');
    }
    public function facilities(){
        return $this->belongsToMany(Facility::class,
            'region_facility_pivots',
            'province_id',
            'facility_id')->withPivot('facility_count');
    }
    public function regionTechnicalInfo(){
        return $this->hasOne(RegionTechnicalInfo::class, "province_id");
    }
}
