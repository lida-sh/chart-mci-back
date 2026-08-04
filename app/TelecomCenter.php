<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelecomCenter extends Model
{
    use HasFactory;
    protected $table = "telecom_centers";
    protected $guarded = [];
    public function centerPivot(){
        return $this->belongsTo(CenterPivot::class, "center_pivote_id");
    }
}
