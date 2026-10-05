<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionUnitTitle extends Model
{
    use HasFactory;
    protected $table = "region_organizational_unit_titles";
    protected $guarded = [];
    public function units(){
        return $this->hasMany(RegionOrganizationalUnit::class, "title_id");
    }
}

