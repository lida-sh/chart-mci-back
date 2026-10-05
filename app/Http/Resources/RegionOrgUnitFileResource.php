<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionOrgUnitFileResource extends JsonResource
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
            "fileName" => $this->fileName,
            "status" => $this->status,
            "filePath" => url("/storage/files/region-org-units") . "/" . $this->filePath,
        ];
    }
}
