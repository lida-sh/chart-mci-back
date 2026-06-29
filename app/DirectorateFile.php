<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DirectorateFile extends Model
{
    protected $table = "directorate_files";
    protected $guarded = [];

    public function directorate(){
        return $this->belongsTo(Directorate::class, "directorate_id");
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
