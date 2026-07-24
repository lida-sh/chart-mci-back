<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionDirectorateFile extends Model
{
    use HasFactory;
    protected $table = "region_directorate_files";
    protected $guarded = [];

    public function directorate(){
        return $this->belongsTo(RegionDirectorate::class, "region_directorate_id");
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
