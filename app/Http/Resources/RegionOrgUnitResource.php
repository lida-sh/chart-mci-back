<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionOrgUnitResource extends JsonResource
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
            "title_id"=>$this->title_id,
            "title" => $this->title->title,
            "is_active" => $this->is_active,
            "description" => $this->description,
            "sort_order" => $this->sort_order,
            "province_id" => $this->province_id,
            "city_id" => $this->city_id,
            "parent_id" => $this->parent_id,
            "province" => $this->whenLoaded("province"),
            "city" => $this->whenLoaded("city", function () {
                return $this->city?->title;
            }, "ستاد"),
            "orgType" => $this->whenLoaded("orgType"),
            "parent" => $this->parent ? $this->parent->title->title : "مدیر منطقه",
            "children"=> RegionOrgUnitResource::collection($this->whenLoaded("children")),
            "files" => RegionOrgUnitFileResource::collection($this->whenLoaded("files")),
            'positions' => $this->positions->map(function ($position) {
                return [
                    'id' => $position->id,
                    'title' => $position->title,
                    'position_type' => $position->position_type,
                    'approved_count' => $position->pivot->approved_count,
                    'description' => $position->pivot->description,
                ];
            }),
            "user" => new userBaseResource($this->whenLoaded("user"))
        ];
    }
}
