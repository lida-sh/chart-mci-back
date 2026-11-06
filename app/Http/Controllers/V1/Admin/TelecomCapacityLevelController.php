<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TelecomCapacityLevelResource;
use App\TelecomCapacityLevel;
use Illuminate\Http\Request;

class TelecomCapacityLevelController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function getTelecomCapacitiesLevels(){
        $capacityLevels = TelecomCapacityLevel::all();
        return $this->successResponse(TelecomCapacityLevelResource::collection($capacityLevels), 200);
    }
}
