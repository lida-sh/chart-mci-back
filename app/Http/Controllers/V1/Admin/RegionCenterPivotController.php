<?php

namespace App\Http\Controllers\V1\Admin;

use App\CenterPivot;
use App\City;
use App\Http\Controllers\Controller;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Resources\CenterPivotResource;

class RegionCenterPivotController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->input("search");
        $provinceId = $request->input("province_id");
        $cityId = $request->input("city_id");
        $sortedBy = $request->input("sortedBy");
        $centerPivots = CenterPivot::query()->with(['province', 'user', 'cities',])->when($provinceId, function ($query, $provinceId) {
            return $query->where("province_id", $provinceId);
        })->when($cityId, function ($query, $cityId) {
            return $query->whereHas('cities', function ($q) use ($cityId) {
                $q->where('id', $cityId);
            });
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
            "centerPivots" => CenterPivotResource::collection($centerPivots),
            "links" => CenterPivotResource::collection($centerPivots)->response()->getData()->links,
            "meta" => CenterPivotResource::collection($centerPivots)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "province_id" => "required|integer",
            'city_id' => 'required|array|min:1',
            'city_id.*' => 'required|integer|exists:cities,id',
            'number_pivot' => [
                'required',
                'string',
                Rule::unique('center_pivots', 'number_pivot')
                    ->where('province_id', $request->province_id),
            ],
            "active_access_count" => "required|integer|min:0",
            "active_fttx_count" => "required|integer|min:0",
            "fault_count" => "required|integer|min:0",
            "installed_and_displacement_count" => "required|integer|min:0",
            "access_fiber_length_km" => "required|integer|min:0",
            "transport_fiber_length_km" => "required|integer|min:0",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $province = Province::findOrFail($request->province_id);
        $center_pivot = CenterPivot::create([
            "province_id" => $request->province_id,
            "number_pivot" => $request->number_pivot,
            "slug" => $province->title . "-" . $request->number_pivot,
            "active_access_count" => $request->active_access_count,
            "active_fttx_count" => $request->active_fttx_count,
            "fault_count" => $request->fault_count,
            "installed_and_displacement_count" => $request->installed_and_displacement_count,
            "access_fiber_length_km" => $request->access_fiber_length_km,
            "transport_fiber_length_km" => $request->transport_fiber_length_km,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        foreach ($request->city_id as $city_id) {
            $city = City::findOrFail($city_id);
            $city->update([
                'center_pivot_id' => $center_pivot->id
            ]);
        }

        DB::commit();
        // return response()->json($data, $code);
        return $this->successResponse($center_pivot, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CenterPivot $centerPivot)
    {
        return $this->successResponse((new CenterPivotResource($centerPivot->load(["cities", "province"]))), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "province_id" => "required|integer",
            'city_id' => 'required|array|min:1',
            'city_id.*' => 'required|integer|exists:cities,id',
            'number_pivot' => [
                'required',
                'string',
                Rule::unique('center_pivots', 'number_pivot')
                    ->where('province_id', $request->province_id)
                    ->ignore($id),
            ],
            "active_access_count" => "required|integer|min:0",
            "active_fttx_count" => "required|integer|min:0",
            "fault_count" => "required|integer|min:0",
            "installed_and_displacement_count" => "required|integer|min:0",
            "access_fiber_length_km" => "required|integer|min:0",
            "transport_fiber_length_km" => "required|integer|min:0",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $province = Province::findOrFail($request->province_id);
        $centerPivot = CenterPivot::findOrFail($id);
        $centerPivot->update([
            "province_id" => $request->province_id,
            "number_pivot" => $request->number_pivot,
            "slug" => $province->title . "-" . $request->number_pivot,
            "active_access_count" => $request->active_access_count,
            "active_fttx_count" => $request->active_fttx_count,
            "fault_count" => $request->fault_count,
            "installed_and_displacement_count" => $request->installed_and_displacement_count,
            "access_fiber_length_km" => $request->access_fiber_length_km,
            "transport_fiber_length_km" => $request->transport_fiber_length_km,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        // حذف ارتباط شهرهایی که دیگر انتخاب نشده‌اند
        City::where('center_pivot_id', $centerPivot->id)
            ->whereNotIn('id', $request->city_id)
            ->update([
                'center_pivot_id' => null
            ]);

        // اضافه کردن/به‌روزرسانی شهرهای انتخاب‌شده
        City::whereIn('id', $request->city_id)
            ->update([
                'center_pivot_id' => $centerPivot->id
            ]);
        // foreach ($request->city_id as $city_id) {
        //     $city = City::findOrFail($city_id);
        //     $city->update([
        //         'center_pivot_id' => $centerPivot->id
        //     ]);
        // }

        DB::commit();
        return $this->successResponse($centerPivot, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getcitiesByProvince(string $id)
    {

        $cities = City::query()
            ->where('province_id', $id)
            ->whereNotNull('center_pivot_id')
            ->select('id', 'title')
            ->get();

        return $this->successResponse($cities, 200);
    }
    
}
