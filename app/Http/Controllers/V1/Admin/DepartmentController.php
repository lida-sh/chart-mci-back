<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Department;
use App\DepartmentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Hekmatinasser\Verta\Verta;
use App\Services\DataConverter;
class DepartmentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input("search");
        $architecture_id = $request->input("architecture_id");
        $directorate_id = $request->input("directorate_id");
        $status = $request->input("status");
        $sortedBy = $request->input("sortedBy");
        $departments = Department::query()->with(['architecture', 'directorate', 'user'])->when($architecture_id, function ($query, $architecture_id) {
            return $query->where("architecture_id", $architecture_id);
        })->when($directorate_id, function ($query, $directorate_id) {
            return $query->where("directorate_id", $directorate_id);
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
            "departments" => DepartmentResource::collection($departments),
            "links" => DepartmentResource::collection($departments)->response()->getData()->links,
            "meta" => DepartmentResource::collection($departments)->response()->getData()->meta
        ], 200);
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
            "directorate_id" => "nullable|integer",
            "status" => "required|string",
            "occupied" => "required|string",
            "evaluated_expert_positions_count" => "required|integer|min:0",
            "old_permanent_experts_count" => "required|integer|min:0",
            "old_contracting_experts_count" => "required|integer|min:0",
            "old_below_expert_count" => "required|integer|min:0",
            "files.*" => "file|max:2048",
        ]);
        //"title" => "required|string|unique:sub_processes,title",
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

        $department = Department::create([
            "title" => $request->title,
            "status" => $request->status,
            "description" => $request->description,
            "occupied" => $request->occupied,
            "evaluated_expert_positions_count" => $request->evaluated_expert_positions_count,
            "old_permanent_experts_count" => $request->old_permanent_experts_count,
            "old_contracting_experts_count" => $request->old_contracting_experts_count,
            "old_below_expert_count" => $request->old_below_expert_count,
            "architecture_id" => $request->architecture_id,
            "directorate_id" => $request->directorate_id,
            "user_id" => auth()->user()->id,

        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                // $filePath = time() . '.' . $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('/files/sub-processes', $filePath, 'public');
                DepartmentFile::create([
                    "department_id" => $department->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath,
                    "status" => 1
                ]);
            }


        }
        DB::commit();
        // return response()->json($data, $code);
        return $this->successResponse($department, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return $this->successResponse((new DepartmentResource($department->load(["files", "architecture", "directorate"]))), 200);
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
            "directorate_id" => "nullable|integer",
            "status" => "required|string",
            "occupied" => "required|string",
            "evaluated_expert_positions_count" => "required|integer|min:0",
            "old_permanent_experts_count" => "required|integer|min:0",
            "old_contracting_experts_count" => "required|integer|min:0",
            "old_below_expert_count" => "required|integer|min:0",
            "files.*" => "file|max:2048",
        ]);
        //"title" => "string|unique:architectures,title," . $id,
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
        $department = Department::findOrFail($id);
        $department->slug = null;
        $department->update([
            "architecture_id" => $request->architecture_id,
            "directorate_id" => $request->directorate_id,
            "title" => $request->title,
            "status" => $request->status,
            "description" => $request->description,
            "occupied" => $request->occupied,
            "evaluated_expert_positions_count" => $request->evaluated_expert_positions_count,
            "old_permanent_experts_count" => $request->old_permanent_experts_count,
            "old_contracting_experts_count" => $request->old_contracting_experts_count,
            "old_below_expert_count" => $request->old_below_expert_count,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->has("fileIdsForDelete")) {
            foreach ($request->fileIdsForDelete as $fileId) {
                $file = DepartmentFile::findOrFail($fileId);
                if (file_exists($file->filePath)) {
                    unlink($file->filePath);
                }
                $file->delete();
            }
        }
        if ($request->has("files") && $request->file("files") !== null) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('/files/sub-processes', $filePath, 'public');
                DepartmentFile::create([
                    "department_id" => $department->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath
                ]);
            }
        }
        DB::commit();
        return $this->successResponse($department, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        foreach ($department->files as $file) {
            $fullPath = public_path('storage/files/departments/'.$file->filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            DepartmentFile::findOrFail($file->id)->delete();
        }
        $department->delete();
        return $this->successResponse(1, 200);
    }
    public function showBySlug($slug)
    {
        $department = Department::where('slug', $slug)->first();
        return $this->successResponse((new DepartmentResource($department->load(["files", "architecture", "directorate"]))), 200);

    }
}
