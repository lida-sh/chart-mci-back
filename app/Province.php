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
}
