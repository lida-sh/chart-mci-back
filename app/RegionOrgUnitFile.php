<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionOrgUnitFile extends Model
{
    use HasFactory;
    protected $table = "region_org_unit_files";
    protected $guarded = [];

    public function regionOrgUnit()
    {
        return $this->belongsTo(RegionOrganizationalUnit::class, "region_organizational_unit_id");
    }
    public function scopeWithAllowedExtensions($query, $extensions = ['pdf', 'jpeg', 'png', 'jpg'])
    {
        return $query->where(function ($query) use ($extensions) {
            foreach ($extensions as $extension) {
                $query->orWhereRaw("LOWER(SUBSTRING_INDEX(filePath, '.', -1)) = ?", [$extension]);
            }
        });
    }
}
