<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentFile extends Model
{
    protected $table = "department_files";
    protected $guarded = [];

    public function department(){
        return $this->belongsTo(Department::class, "department_id");
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
