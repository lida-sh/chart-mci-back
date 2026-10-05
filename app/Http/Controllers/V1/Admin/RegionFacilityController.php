<?php

namespace App\Http\Controllers\V1\Admin;



use App\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\FacilityResource;

class RegionFacilityController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::paginate(10);;
        return $this->successResponse([
            "facilities" => FacilityResource::collection($facilities->load("user")),
            "links" => FacilityResource::collection($facilities)->response()->getData()->links,
            "meta" => FacilityResource::collection($facilities)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        $facility = Facility::create([
            "type" => $request->type,
            "unit" => $request->unit,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($facility, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return $this->successResponse((new FacilityResource($facility)), 200);
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
        $facility = Facility::findOrFail($id);
        $facility->update([
            "type" => $request->type,
            "unit" => $request->unit,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($facility, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();
        return $this->successResponse(1, 200);
    }
}
