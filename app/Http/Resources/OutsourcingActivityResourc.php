<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutsourcingActivityResourc extends JsonResource
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
            "activity_title" => $this->activity_title,
            "unit" => $this->unit,
            "type" => $this->type,
            "description" => $this->description,
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
