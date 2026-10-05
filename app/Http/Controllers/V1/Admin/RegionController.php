<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CenterPivotResource;
use App\Province;
use Illuminate\Http\Request;

class RegionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getProvinces()
    {
        $provinces = Province::all();
        return $this->successResponse($provinces, 200);
    }
    public function getCities(Province $province)
    {
        $cities = $province->cities;
        return $this->successResponse($cities, 200);
    }
    public function getCenterPivots(Province $province)
    {
        $centerPivots = $province->centerPivots;
        return $this->successResponse(CenterPivotResource::collection($centerPivots->load(["user", "cities"])), 200);
    }
}
