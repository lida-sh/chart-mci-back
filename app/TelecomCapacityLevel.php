<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelecomCapacityLevel extends Model
{
    use HasFactory;
    protected $table = "telecom_capacity_levels";
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
