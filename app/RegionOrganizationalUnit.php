<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\OrganizationalUnitType;

class RegionOrganizationalUnit extends Model
{
    use HasFactory;
    protected $table = "region_organizational_units";
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(
            RegionOrganizationalUnit::class,
            'parent_id'
        );
    }

    // public function children()
    // {
    //     return $this->hasMany(
    //         OrganizationalUnit::class,
    //         'parent_id'
    //     );
    // }
    public function children()
    {
        return $this->hasMany(
            RegionOrganizationalUnit::class,
            'parent_id'
        )->with('children',
            'province',
            'city',
            'orgType',
            'user');
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function orgType()
    {
        return $this->belongsTo(OrganizationalUnitType::class, "region_organizational_unit_type_id");
    }
    public function files()
    {
        return $this->hasMany(RegionOrgUnitFile::class, "region_organizational_unit_id");
    }
    public function positions(){
        return $this->belongsToMany(RegionPosition::class,
            'region_organizational_unit_positions',
            'region_organizational_unit_id',
            'region_position_id'
        )->withPivot('approved_count', 'description');
    }
    public function title(){
        return $this->belongsTo(RegionUnitTitle::class, "title_id");
    }
}
