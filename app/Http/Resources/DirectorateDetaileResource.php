<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hekmatinasser\Verta\Verta;
class DirectorateDetaileResource extends JsonResource
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
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "status" => $this->status,
            "occupied" => $this->occupied,
            "office_manager_count" => $this->office_manager_count,
            "architecture_id" => $this->architecture_id,
            "architecture" => new ArchitectureResource($this->whenLoaded("architecture")),
            "user" => new userBaseResource($this->whenLoaded("user")),
            "departments" => DepartmentTreeResource::collection($this->whenLoaded("departments"))
        ];
    }
}
