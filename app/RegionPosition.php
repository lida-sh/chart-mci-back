<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionPosition extends Model
{
    use HasFactory;
    protected $table = "region_positions";
    protected $guarded = [];
}
