<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $table = "facilities";
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function provinces()
    {
        return $this->belongsToMany(
            Province::class,
            'region_facility_pivots',
            'facility_id',
            'province_id'
        );
    }
}
