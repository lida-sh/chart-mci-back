<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DirectorateTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "title" => $this->title,
            "slug" => $this->slug,
            "occupied" => $this->occupied,
            "office_manager_count" => $this->office_manager_count,
            "departments" => DepartmentTreeResource::collection($this->whenLoaded("departments"))
        ];
    }
}
