<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PowerSupplyResource extends JsonResource
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
            "type" => $this->type,
            "description" => $this->description,
            "unit" => $this->unit,
            "count" => $this->pivot?->power_supply_count,
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
