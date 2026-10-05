<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\OrganizationalUnitType;
use App\Http\Resources\RegionOrgTypeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegionOrganizationalUnitTypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $region_org_unit_types = OrganizationalUnitType::with("user")->get();
        return $this->successResponse(RegionOrgTypeResource::collection($region_org_unit_types), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "code" => "required|string",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $region_org_type = OrganizationalUnitType::create([
            "title" => $request->title,
            "code" => $request->code,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($region_org_type, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orgType = OrganizationalUnitType::findOrFail($id);
        return $this->successResponse((new RegionOrgTypeResource($orgType)), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "code" => "required|string",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $orgType = OrganizationalUnitType::findOrFail($id);
        $orgType->update([
            "title" => $request->title,
            "code" => $request->code,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($orgType, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orgType = OrganizationalUnitType::findOrFail($id);
        $orgType->delete();
        return $this->successResponse(1, 200);
    }
}
