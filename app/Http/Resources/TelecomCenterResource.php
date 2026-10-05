<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelecomCenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "in_service_count" => $this->in_service_count,
            "center_pivot_id" => $this->center_pivot_id,
            "capacity" => $this->capacity,
            "centerPivot" => new CenterPivotResource($this->whenLoaded("centerPivot")),
            "province" => $this->centerPivot?->province,
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
