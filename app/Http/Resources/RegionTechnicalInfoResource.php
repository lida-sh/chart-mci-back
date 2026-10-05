<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionTechnicalInfoResource extends JsonResource
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
            "region_coefficient" => $this->region_coefficient,
            "provisioned_lines_count" => $this->provisioned_lines_count,
            "tower_count" => $this->tower_count,
            "province" => new ProvinceResource(
                $this->whenLoaded("province")
            ),
            "user" => new userBaseResource($this->whenLoaded("user"))
        ]; 
    }
}
