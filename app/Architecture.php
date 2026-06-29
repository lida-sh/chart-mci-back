<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class Architecture extends Model
{
    use Sluggable;
    protected $table = "architectures";
    protected $guarded = [];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    public function files()
    {
        return $this->hasMany(ArchitectureFile::class, "architecture_id");
    }
    public function directorates(){
        return $this->hasMany(Directorate::class, "architecture_id");
    }
    public function seniorExperts(){
        return $this->hasMany(SeniorExpert::class, "architecture_id");
    }
    public function departments(){
        return $this->hasMany(Department::class, "architecture_id");
    }
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function rootDepartments()
    {
    return $this->hasMany(Department::class)
        ->whereNull('directorate_id');
    }
}
