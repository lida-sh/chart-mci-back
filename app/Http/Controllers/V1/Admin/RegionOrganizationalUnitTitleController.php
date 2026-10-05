<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\RegionUnitTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class RegionOrganizationalUnitTitleController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unitTitles = RegionUnitTitle::all();
        return $this->successResponse($unitTitles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $unitTitle = RegionUnitTitle::create([
            "title"=>$request->title,
            "region_organizational_unit_type_id"=>$request->typeId,
            "user_id"=>auth()->user()->id
        ]);
        return $this->successResponse($unitTitle, 201);
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
    public function getUnitTitlesByTypeId(string $id){
        $unitTitles = RegionUnitTitle::where('region_organizational_unit_type_id', $id)->get();
        return $this->successResponse($unitTitles, 200);
    }
}
