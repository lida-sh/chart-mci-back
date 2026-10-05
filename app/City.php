<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = "cities";
    protected $guarded = [];
    public $timestamps = false;
    public function province()
    {
        return $this->belongsTo(Province::class, "province_id");
    }
    public function centerPivot()
    {
        return $this->belongsTo(CenterPivot::class, 'center_pivot_id');
    }
}
