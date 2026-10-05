<?php

namespace App\Http\Controllers\V1\Admin;

use App\Facility;
use App\OutsourcingActivity;
use App\PowerSupply;
use App\RegionFacilityPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Province;
use App\RegionPowerSupplyPivot;
use App\RegionTechnicalInfo;
use App\Http\Resources\RegionTechnicalInfoResource;

class RegionTechnicalInfoController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input("search");
        $provinceId = $request->input("province_id");
        $sortedBy = $request->input("sortedBy");

        $technicalInfo = RegionTechnicalInfo::query()->with(['province', 'user'])->when($provinceId, function ($query, $provinceId) {
            return $query->where("province_id", $provinceId);
        })->when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', "%{$search}%");
        })->when($sortedBy, function ($query, $sortedBy) {
            return match ($sortedBy) {
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                default => $query,
            };
        })->paginate(20);
        return $this->successResponse([
            "regionTechnicalInfo" => RegionTechnicalInfoResource::collection($technicalInfo),
            "links" => RegionTechnicalInfoResource::collection($technicalInfo)->response()->getData()->links,
            "meta" => RegionTechnicalInfoResource::collection($technicalInfo)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "province_id" => "required|integer",
            "region_coefficient" => "required|numeric|between:0,1",
            "provisioned_lines_count" => "required|integer|min:0",
            "tower_count" => "required|integer|min:0",
            "powerSupplies" => "required|array",
            "powerSupplies.*" => "required|integer|min:0",
            "facilities" => "required|array",
            "facilities.*" => "required|integer|min:0",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $province = Province::findOrFail($request->province_id);
        $region_technical_info = RegionTechnicalInfo::create([
            "province_id" => $request->province_id,
            "region_coefficient" => $request->region_coefficient,
            "provisioned_lines_count" => $request->provisioned_lines_count,
            "tower_count" => $request->tower_count,
            "user_id" => auth()->user()->id,
        ]);
        foreach ($request->powerSupplies as $type => $count) {
            $powerSupply = PowerSupply::where("type", $type)->firstOrFail();
            $regionPowerSupplyPivot = RegionPowerSupplyPivot::create([
                "province_id" => $province->id,
                "power_supply_id" => $powerSupply->id,
                "power_supply_count" => $count,
                "user_id" => auth()->user()->id,
            ]);
        }
        foreach ($request->facilities as $type => $count) {
            $facility = Facility::where("type", $type)->firstOrFail();
            $regionFacilityPivot = RegionFacilityPivot::create([
                "province_id" => $province->id,
                "facility_id" => $facility->id,
                "facility_count" => $count,
                "user_id" => auth()->user()->id,
            ]);
        }
        DB::commit();
        return $this->successResponse([
            "region_technical_info" => (new RegionTechnicalInfoResource($region_technical_info))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RegionTechnicalInfo $technicalInfo)
    {
        // $powerSupplies = [];
        // $facilities = [];
        // foreach (PowerSupply::all() as $powerSupply) {
        //     $powerSupplies[] = [
        //         $powerSupply->power_supply_id => $powerSupply->power_supply_count
        //     ];
        // }
        // foreach (Facility::all() as $facility) {
        //     $facilities[] = [
        //         $facility->facility_id => $facility->facility_count
        //     ];
        // }
        return $this->successResponse((new RegionTechnicalInfoResource($technicalInfo->load([
            'province.powerSupplies',
            'province.facilities',
            'user',
        ]))), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "region_coefficient" => "required|numeric|between:0,1",
            "provisioned_lines_count" => "required|integer|min:0",
            "tower_count" => "required|integer|min:0",
            "powerSupplies" => "required|array",
            "powerSupplies.*" => "required|integer|min:0",
            "facilities" => "required|array",
            "facilities.*" => "required|integer|min:0",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $region_technical_info = RegionTechnicalInfo::findOrFail($id);
        $province = $region_technical_info->province;
        $region_technical_info->update([
            "region_coefficient" => $request->region_coefficient,
            "provisioned_lines_count" => $request->provisioned_lines_count,
            "tower_count" => $request->tower_count,
            "user_id" => auth()->user()->id,
        ]);
        foreach ($request->powerSupplies as $type => $count) {
            $powerSupply = PowerSupply::where("type", $type)->firstOrFail();
            $regionPowerSupplyPivot = RegionPowerSupplyPivot::where([
                ['province_id', $province->id],
                ['power_supply_id', $powerSupply->id],
            ])->firstOrFail();
            $regionPowerSupplyPivot->update([
                "power_supply_count" => $count,
                "user_id" => auth()->user()->id,
            ]);
        }
        foreach ($request->facilities as $type => $count) {
            $facility = Facility::where("type", $type)->firstOrFail();
            $regionFacilityPivot = RegionFacilityPivot::where([
                ['province_id', $province->id],
                ['facility_id', $facility->id],
            ])->firstOrFail();
            $regionFacilityPivot->update([
                "facility_count" => $count,
                "user_id" => auth()->user()->id,
            ]);
        }
        DB::commit();
        return $this->successResponse([
            "region_technical_info" => (new RegionTechnicalInfoResource($region_technical_info))
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getTechnicalParameters()
    {
        $powerSupplies = PowerSupply::all();
        $facilities = Facility::all();
        $outsourcingActivities = OutsourcingActivity::all();
        return $this->successResponse([
            "powerSupplies" => $powerSupplies,
            "facilities" => $facilities,
            "outsourcingActivities" => $outsourcingActivities,
        ], 200);
    }
}
