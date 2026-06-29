<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchitectureTreeResource extends JsonResource
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
            "status" => $this->status  ,
            "slug" => $this->slug ?  $this->slug : "",
            "type" => $this->type,
            "old_positions_count"=> $this->old_positions_count,
            "old_expert_positions_count"=> $this->old_expert_positions_count,
            "old_directorates_count"=> $this->old_directorates_count,
            "old_departments_count"=> $this->old_departments_count,
            "office_manager_count"=> $this->office_manager_count,
            "directorates"=> DirectorateTreeResource::collection($this->whenLoaded("directorates", function(){
                return $this->directorates->load("departments");
            })),
            "rootDepartments"=> DepartmentTreeResource::collection($this->rootDepartments),
            "senior_experts"=> SeniorExpertTreeResource::collection($this->seniorExperts),
            "user" => new userBaseResource($this->whenLoaded("user"))
            
        ];
    }
}
