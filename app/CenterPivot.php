<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterPivot extends Model
{
    use HasFactory;
    protected $table = "center_pivots";
    protected $guarded = [];
    public function province(){
        
        return $this->belongsTo(Province::class, "province_id");
    }
    public function telecomCenters(){
        return $this->hasMany(TelecomCenter::class, "center_pivot_id");
    }

    
}
