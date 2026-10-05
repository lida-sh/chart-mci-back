<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\OrganizationalUnitType;
use App\RegionPosition;

class RegionOrganizationalUnitPosition extends Model
{
    use HasFactory;
    protected $table = "region_organizational_unit_positions";
    protected $guarded = [];
    public function position()
    {
        return $this->belongsTo(RegionPosition::class, "region_position_id");
    }
    public function regionOrgUnit()
    {
        return $this->belongsTo(RegionOrganizationalUnit::class, "region_organizational_unit_id");
    }
}
