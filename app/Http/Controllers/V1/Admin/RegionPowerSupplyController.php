<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\PowerSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PowerSupplyResource;

class RegionPowerSupplyController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $powerSupplies = PowerSupply::paginate(10);;
        return $this->successResponse([
            "powerSupplies" => PowerSupplyResource::collection($powerSupplies->load("user")),
            "links" => PowerSupplyResource::collection($powerSupplies)->response()->getData()->links,
            "meta" => PowerSupplyResource::collection($powerSupplies,)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "type" => "required|string",
            'unit' => 'required|numeric|gt:0',
            'description' => "required|string",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();

        $powerSupply = PowerSupply::create([
            "type" => $request->type,
            "unit" => $request->unit,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($powerSupply, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PowerSupply $powerSupply)
    {
        return $this->successResponse((new PowerSupplyResource($powerSupply)), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "type" => "required|string",
            'unit' => 'required|numeric|gt:0',
            'description' => "required|string",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $powerSupply = PowerSupply::findOrFail($id);
        $powerSupply->update([
            "type" => $request->type,
            "unit" => $request->unit,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($powerSupply, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $powerSupply = PowerSupply::findOrFail($id);
        $powerSupply->delete();
        return $this->successResponse(1, 200);
    }
}
