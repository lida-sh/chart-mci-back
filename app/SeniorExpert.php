<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
class SeniorExpert extends Model
{
    use HasFactory;
    use Sluggable;
    protected $table = "senior_experts";
    protected $guarded = [];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    public function architecture(){
        return $this->belongsTo(Architecture::class, "architecture_id");
    }
    
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
