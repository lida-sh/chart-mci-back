<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchitectureResource extends JsonResource
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
            "status" => $this->status,
            "slug" => $this->slug,
            "type" => $this->type,
            "description" => $this->description,
            "office_manager_count" => $this->office_manager_count,
            "old_positions_count" => $this->old_positions_count,
            "old_expert_positions_count" => $this->old_expert_positions_count,
            "old_directorates_count" => $this->old_directorates_count,
            "old_departments_count" => $this->old_departments_count,
            "files" => ArchitectureFileResource::collection($this->whenLoaded("files")),
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
