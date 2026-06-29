<?php

namespace App\Http\Controllers\V1\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\DirectorateResource;
use App\Http\Resources\DirectorateDetaileResource ;
use App\Directorate;
use App\DirectorateFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\DataConverter;

class DirectorateController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $search = $request->input("search");
        $architecture_id = $request->input("architecture_id");
        $status = $request->input("status");
        $sortedBy = $request->input("sortedBy");
        $directorates = Directorate::query()->with("architecture", "user", "departments")->when($architecture_id, function ($query, $architecture_id) {
            return $query->where("architecture_id", $architecture_id);
        })->when($status, function ($query, $status) {
            if ($status == 1) {
                return $query->where("status", $status);
            } else {
                return $query->where("status", 0);
            }
        })->when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', "%{$search}%");
        })->when($sortedBy, function ($query, $sortedBy) {
            if ($sortedBy == "newest") {
                return $query->latest();
            } else if ($sortedBy == "oldest")
                return $query->oldest();
        })->paginate(10);
        return $this->successResponse([
            "directorates" => DirectorateDetaileResource ::collection($directorates),
            "links" => DirectorateDetaileResource ::collection($directorates)->response()->getData()->links,
            "meta" => DirectorateDetaileResource ::collection($directorates)->response()->getData()->meta
        ], 200);
        // return response()->json($processes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            "architecture_id" => "required|integer",
            "status" => "required|string",
            "occupied" => "required|string",
            "office_manager_count"=>"required|integer|min:0",
            "files.*" => "file|max:2048",
        ]);
        //"title" => "required|string|unique:processes,title",
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

        $directorate = Directorate::create([
            "title" => $request->title,
            "status" => $request->status,
            "occupied" => $request->occupied,
            "office_manager_count" => $request->office_manager_count,
            "description" => $request->description,
            "architecture_id" => $request->architecture_id,
            "user_id" => auth()->user()->id,
            

        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('/files/directorates', $filePath, 'public');
                DirectorateFile::create([
                    "directorate_id" => $directorate->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath,
                    "status" => 1
                ]);
            }


        }
        DB::commit();
        // return response()->json($data, $code);
        return $this->successResponse($directorate, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Directorate $directorate)
    {
        
        return $this->successResponse((new DirectorateResource($directorate->load("files")->load("architecture"))), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            "architecture_id" => "required|integer",
            "occupied" => "required|string",
            "status" => "required|string",
            "office_manager_count"=>"required|integer|min:0",
            "files.*" => "file|max:2048",
        ]);
       //"title" => "string|unique:processes,title," . $id,
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
        $directorate = Directorate::findOrFail($id);
        $directorate->slug = null;
        $directorate->update([
            "architecture_id" => $request->architecture_id,
            "title" => $request->title,
            "occupied" => $request->occupied,
            "office_manager_count" => $request->office_manager_count,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->has("fileIdsForDelete")) {
            foreach ($request->fileIdsForDelete as $fileId) {
                $file = DirectorateFile::findOrFail($fileId);
                if (file_exists($file->filePath)) {
                    unlink($file->filePath);
                }
                $file->delete();
            }
        }
        if ($request->has("files") && $request->file("files") !== null) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalName();
                $file->storeAs('/files/processes', $filePath, 'public');
                DirectorateFile::create([
                    "directorate_id" => $directorate->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath
                ]);
            }
        }
        DB::commit();
        return $this->successResponse($directorate, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $directorate = Directorate::findOrFail($id);
        foreach ($directorate->files as $file) {
            $fullPath = public_path('storage/files/processes/'.$file->filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            DirectorateFile::findOrFail($file->id)->delete();
        }
        $directorate->delete();
        return $this->successResponse(1, 200);
    }
    public function showBySlug($slug)
    {
        $directorate = Directorate::where('slug', $slug)->first();
        return $this->successResponse((new DirectorateResource($directorate->load("files")->load("architecture"))), 200);

    }
}
