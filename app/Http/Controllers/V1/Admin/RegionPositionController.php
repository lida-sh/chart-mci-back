<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RegionPosition;

class RegionPositionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = RegionPosition::all();
        return $this->successResponse($positions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
        $position = RegionPosition::create([
            "title" => $request->title,
            "position_type"=>$request->position_type,
            "user_id" => auth()->user()->id,
        ]);
        return $this->successResponse($position, 201);
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
}
