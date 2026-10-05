<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TelecomCenterResource;
use App\TelecomCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegionTelecomCenterController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->all());

        $search = $request->input("search");
        $capacity = $request->input("capacity");
        $provinceId = $request->input("province_id");
        $center_pivot_id = $request->input("center_pivot_id");
        $sortedBy = $request->input("sortedBy");

        $telecomCenters = TelecomCenter::query()->with(['centerPivot', 'user', 'centerPivot.cities',])->when($provinceId, function ($query, $provinceId) {
            return $query->whereHas('centerPivot', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        })->when($center_pivot_id, function ($query, $center_pivot_id) {
            return $query->where("center_pivot_id", $center_pivot_id);
        })->when($capacity, function ($query, $capacity) {
            if ($capacity == 1) {
                return $query->where("capacity", $capacity);
            } else {
                return $query->where("capacity", 0);
            }
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
            "telecomCenters" => TelecomCenterResource::collection($telecomCenters),
            "links" => TelecomCenterResource::collection($telecomCenters)->response()->getData()->links,
            "meta" => TelecomCenterResource::collection($telecomCenters)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "center_pivot_id" => "required|integer",
            'in_service_count' =>  "required|integer",
            "title" => "required|string",
            "capacity" => "required|integer",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $telecomCenter = TelecomCenter::create([
            "title" => $request->title,
            "center_pivot_id" => $request->center_pivot_id,
            "in_service_count" => $request->in_service_count,
            "capacity" => $request->capacity,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($telecomCenter, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TelecomCenter $telecomCenter)
    {
        return $this->successResponse((new TelecomCenterResource($telecomCenter)), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "center_pivot_id" => "required|integer",
            'in_service_count' =>  "required|integer",
            "title" => "required|string",
            "capacity" => "required|integer",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $telecomCenter = TelecomCenter::findOrFail($id);
        $telecomCenter->update([
            "title" => $request->title,
            "center_pivot_id" => $request->center_pivot_id,
            "in_service_count" => $request->in_service_count,
            "capacity" => $request->capacity,
            "user_id" => auth()->user()->id,
        ]);
        DB::commit();
        return $this->successResponse($telecomCenter, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $telecomCenter = TelecomCenter::findOrFail($id);
        $telecomCenter->delete();
        return $this->successResponse(1, 200);
    }
}
