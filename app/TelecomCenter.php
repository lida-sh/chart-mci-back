<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelecomCenter extends Model
{
    use HasFactory;
    protected $table = "telecom_centers";
    protected $guarded = [];
    public function centerPivot()
    {
        return $this->belongsTo(CenterPivot::class, "center_pivot_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
