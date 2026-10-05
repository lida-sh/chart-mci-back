<?php

namespace App\Http\Resources;

use App\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
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
            "powerSupplies" => PowerSupplyResource::collection(
                $this->whenLoaded("powerSupplies")
            ),
            "facilities" => FacilityResource::collection($this->whenLoaded("facilities"))
        ];
    }
}
