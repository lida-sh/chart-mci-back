<?php

namespace App\Http\Controllers\V1\Admin;



use App\OutsourcingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OutsourcingActivityResourc;

class RegionOutsourcingActivityController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = OutsourcingActivity::paginate(10);;
        return $this->successResponse([
            "outsourcingActivities" => OutsourcingActivityResourc::collection($facilities->load("user")),
            "links" => OutsourcingActivityResourc::collection($facilities)->response()->getData()->links,
            "meta" => OutsourcingActivityResourc::collection($facilities)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "activity_title" => "required|string",
            'unit' => "required|string",
            'type' => "required|string",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();

        $facility = OutsourcingActivity::create([
            "activity_title" => $request->activity_title,
            "unit" => $request->unit,
            "type" => $request->type,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($facility, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OutsourcingActivity $outsourcingActivity)
    {
        return $this->successResponse((new OutsourcingActivityResourc($outsourcingActivity)), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "activity_title" => "required|string",
            'unit' => "required|string",
            'type' => "required|string",

        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $outsourcingActivity = OutsourcingActivity::findOrFail($id);
        $outsourcingActivity->update([
            "activity_title" => $request->activity_title,
            "unit" => $request->unit,
            "type" => $request->type,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($outsourcingActivity, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $outsourcingActivity = OutsourcingActivity::findOrFail($id);
        $outsourcingActivity->delete();
        return $this->successResponse(1, 200);
    }
}
