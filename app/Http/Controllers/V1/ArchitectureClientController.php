<?php

namespace App\Http\Controllers\V1;

use App\Architecture;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArchitectureDetailsClientResource;
use App\Http\Resources\ArchitectureTreeResource;
use App\Http\Resources\ArchitectureTopResource;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\Admin\ApiController;
class ArchitectureClientController extends ApiController
{
    public function showBySlug($slug)
    {
        $architecture = Architecture::where('slug', $slug)->first();
        return $this->successResponse((new ArchitectureDetailsClientResource($architecture->load(["files" => function ($query) {
            $query->withAllowedExtensions(['pdf', 'jpeg', 'png']); // فیلتر فایل‌ها بر اساس پسوند
        }, "architecture", "process"]))), 200);

    }
    public function getTreeStructure($slug){
        $architecture = Architecture::where('slug', $slug)->first();
        // dd(new ArchitectureTreeResource($architecture->load(["directorates", "rootDepartments", "seniorExperts"])));
        return $this->successResponse(new ArchitectureTreeResource($architecture->load(["directorates", "rootDepartments", "seniorExperts"])),200);
    }
    public function getTopChart(){
        $deputy_architectures = Architecture::where('type', 'deputy')->get();
        $directorate_architectures = Architecture::where('type', 'directorate')->get();
        return $this->successResponse([
            "deputy_architectures" => ArchitectureTopResource::collection($deputy_architectures),
            "directorate_architectures" => ArchitectureTopResource::collection($directorate_architectures),
        ], 200);
    }
}
