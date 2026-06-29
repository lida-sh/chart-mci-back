<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class Department extends Model
{
    use Sluggable;
    protected $table = "departments";
    protected $guarded = [];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    public function directorate(){
        return $this->belongsTo(Directorate::class, "directorate_id");
    }
    public function architecture(){
        return $this->belongsTo(Architecture::class, "architecture_id");
    }
    public function files(){
        return $this->hasMany(DepartmentFile::class, "department_id");
    }
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
