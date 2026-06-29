<?php

namespace App\Http\Controllers\V1\Admin;

use App\Architecture;




use App\ArchitectureFile;
use App\Http\Resources\ArchitectureResource;
use App\Http\Resources\ArchitectureTreeResource;
use App\Http\Resources\DirectorateResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Environment\Console;


class ArchitectureController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $architectures = Architecture::all();
        
        return response()->json($architectures, 200);
        // return $this->successResponse($architectures, 200);
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
            "title" => "required|string|unique:architectures,title",
            "type" => "required",
            "status" => "required",
            "office_manager_count"=>"required|integer|min:0",
            "old_positions_count"=>"required|integer|min:0",
            "old_expert_positions_count"=>"required|integer|min:0",
            "old_directorates_count"=>"required|integer|min:0",
            "old_departments_count"=>"required|integer|min:0",
            "files.*" => "file|max:2048",
    
        ]);

        $allowedExtensions = ['bpm', 'jpg', 'jpeg', 'png', 'tiff', 'docx', 'doc', 'gif', 'pdf', 'pptx'];
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

        $architecture = Architecture::create([
            "title" => $request->title,
            "description" => $request->description,
            "type" => $request->type,
            "status" => $request->status,
            "office_manager_count" => $request->office_manager_count,
            "old_positions_count" => $request->old_positions_count,
            "old_expert_positions_count" => $request->old_expert_positions_count,
            "old_directorates_count" => $request->old_directorates_count,
            "old_departments_count" => $request->old_departments_count,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalName();
                $file->storeAs('/files/architectures', $filePath, 'public');
                ArchitectureFile::create([
                    "architecture_id" => $architecture->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath
                ]);
            }


        }
        DB::commit();
        // return response()->json($data, $code);
        return $this->successResponse($architecture, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Architecture $architecture)
    {
        
        return $this->successResponse((new ArchitectureResource($architecture->load("files"))), 200);
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
            "title" => "string|unique:architectures,title," . $id,
            "type" => "required",
            "status" => "required",
            "office_manager_count"=>"required|integer|min:0",
            "old_positions_count"=>"required|integer|min:0",
            "old_expert_positions_count"=>"required|integer|min:0",
            "old_directorates_count"=>"required|integer|min:0",
            "old_departments_count"=>"required|integer|min:0",
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
        $architecture = Architecture::findOrFail(($id));
        $architecture->update([
            "title" => $request->title,
            "type" => $request->type,
            "description" => $request->description,
            "status" => $request->status,
            "office_manager_count" => $request->office_manager_count,
            "old_positions_count" => $request->old_positions_count,
            "old_expert_positions_count" => $request->old_expert_positions_count,
            "old_directorates_count" => $request->old_directorates_count,
            "old_departments_count" => $request->old_departments_count,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->has("fileIdsForDelete")) {
            foreach ($request->fileIdsForDelete as $fileId) {
                $file = ArchitectureFile::findOrFail($fileId);
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
                $file->storeAs('/files/architectures', $filePath, 'public');
                ArchitectureFile::create([
                    "architecture_id" => $architecture->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath
                ]);
            }
        }
        DB::commit();
        return $this->successResponse($architecture, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $architecture = Architecture::findOrFail($id);
        foreach ($architecture->files as $file) {
            $fullPath = public_path('storage/files/architectures/'.$file->filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            ArchitectureFile::findOrFail($file->id)->delete();
        }
        $architecture->delete();
        return $this->successResponse(1, 200);
    }
    
    public function getDirectoratesOfArchitecture(Architecture $architecture)
    {
        
        $directorates = $architecture->directorates;
        return $this->successResponse(DirectorateResource::collection($directorates), 200);
    }
    //گرفتن معماری ها

    public function getArchitectures(){    
        $architectures = Architecture::paginate(10);
           return $this->successResponse([
            "architectures" => ArchitectureTreeResource::collection($architectures->load(["user", "directorates", "rootDepartments", "seniorExperts"])),
            "links" => ArchitectureTreeResource::collection($architectures)->response()->getData()->links,
            "meta" => ArchitectureTreeResource::collection($architectures)->response()->getData()->meta
        ], 200);
   
    }
    public function showBySlug($slug)
    {
        
        $architecture = Architecture::where('slug', $slug)->first();
        // dd($architecture);
        return $this->successResponse((new ArchitectureResource($architecture->load(["files", "user"]))), 200);

    }

}
