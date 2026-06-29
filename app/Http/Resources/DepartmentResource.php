<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hekmatinasser\Verta\Verta;
class DepartmentResource extends JsonResource
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
            "evaluated_expert_positions_count" => $this->evaluated_expert_positions_count,
            "old_permanent_experts_count" => $this->old_permanent_experts_count,
            "old_contracting_experts_count" => $this->old_contracting_experts_count,
            "old_below_expert_count" => $this->old_below_expert_count,
            "architecture_id" => $this->architecture_id,
            "directorate_id" => $this->directorate_id,
            "description" => $this->description,
            "files" => DepartmentFileResource::collection($this->whenLoaded("files")),
            "architecture" => new ArchitectureResource($this->whenLoaded("architecture")),
            "directorate" => new DirectorateResource($this->whenLoaded("directorate")),
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
