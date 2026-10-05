<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationalUnitType extends Model
{
    use HasFactory;
    protected $table = "region_organizational_unit_types";
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function orgType()
    {
        return $this->belongsTo(OrganizationalUnitType::class, "region_organizational_unit_type_id");
    }
}
