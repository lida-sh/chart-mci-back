<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterPivotResource extends JsonResource
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
            "number_pivot" => $this->number_pivot,
            "slug" => $this->slug,
            "active_access_count" => $this->active_access_count,
            "active_fttx_count" => $this->active_fttx_count,
            "fault_count" => $this->fault_count,
            "installed_and_displacement_count" => $this->installed_and_displacement_count,
            "access_fiber_length_km" => $this->access_fiber_length_km,
            "transport_fiber_length_km" => $this->transport_fiber_length_km,
            "description" => $this->description,
            "province_id" => $this->province_id,
            "cities" => CityResource::collection($this->whenLoaded("cities")),
            "province"=> $this->whenLoaded("province"),
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
