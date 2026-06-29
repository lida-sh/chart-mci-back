<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class Directorate extends Model
{
    use Sluggable;
    protected $table = "directorates";
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
        return $this->hasMany(DirectorateFile::class, "directorate_id");
    }
    public function architecture(){
        return $this->belongsTo(Architecture::class, "architecture_id");
    }
    public function departments(){
        return $this->hasMany(Department::class, "directorate_id");
    }
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
