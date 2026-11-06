<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class RegionDirectorate extends Model
{
    use HasFactory;
    use Sluggable;
    protected $table = "region_directorates";
    protected $guarded = [];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    public function files(){
        return $this->hasMany(RegionDirectorateFile::class, "region_directorate_id");
    }
}
