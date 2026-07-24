<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\RegionDirectorate;
use App\RegionDirectorateFile;

class RegionDirectorateController extends ApiController
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
        $validator = Validator::make($request->all(), [
            "title" => "required|string|unique:region_directorates,title",
            "status" => "required|string",
            "files.*" => "file|max:2048",
        ]);
        
        $allowedExtensions = ['bpm', 'jpg', 'jpeg', 'png', 'tiff', 'docx', 'doc', 'gif', 'pdf'];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                    $validator->errors()->add('files', 'فایل ' . $file->getClientOriginalName() . ' معتبر نیست.');
                    return $this->errorResponse($validator->messages(), 422);
                }
            }
        }
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $regionDirectorate = RegionDirectorate::create([
            "title" => $request->title,
            "status" => $request->status,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
            

        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('/files/region-directorates', $filePath, 'public');
                RegionDirectorateFile::create([
                    "region_directorate_id" => $regionDirectorate->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath,
                    "status" => 1
                ]);
            }


        }
        DB::commit();
        // return response()->json($data, $code);
        return $this->successResponse($regionDirectorate, 201);
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
}
