<?php

namespace App\Http\Controllers\V1\Admin;

use App\RegionOrganizationalUnit;
use App\Province;
use App\Http\Controllers\Controller;
use App\Http\Resources\RegionOrgUnitResource;
use App\RegionPosition;
use App\RegionOrganizationalUnitPosition;
use App\RegionOrgUnitFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegionOrganizationalUnitController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->input("search");
        $status = $request->input("status");
        $provinceId = $request->input("province_id");
        $cityId = $request->input("city_id");
        $unitTypeId = $request->input("type");
        $sortedBy = $request->input("sortedBy");

        $org_units = RegionOrganizationalUnit::query()->with(['province', 'user', 'city', 'orgType', 'parent', 'title'])->when($provinceId, function ($query, $provinceId) {
            return $query->where('province_id', $provinceId);
        })->when($cityId, function ($query, $cityId) {
            return $query->where("city_id", $cityId);
        })->when($unitTypeId, function ($query, $unitTypeId) {
            return $query->where("region_organizational_unit_type_id", $unitTypeId);
        })->when($status, function ($query, $status) {
            if ($status == 1) {
                return $query->where("is_active", $status);
            } else {
                return $query->where("is_active", 0);
            }
        })->when($search, function ($query, $search) {
            return $query->whereHas('title', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        })->when($sortedBy, function ($query, $sortedBy) {
            return match ($sortedBy) {
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                default => $query,
            };
        })->paginate(20);
        return $this->successResponse([
            "regionOrgUnits" => RegionOrgUnitResource::collection($org_units),
            "links" => RegionOrgUnitResource::collection($org_units)->response()->getData()->links,
            "meta" => RegionOrgUnitResource::collection($org_units)->response()->getData()->meta
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            "province_id" => "required|integer",
            "city_id" => "nullable|integer",
            'parent_id' =>  "nullable|integer|exists:region_organizational_units,id",
            "title_id" => "required|integer",
            "status" => "required|integer",
            "type" => "required|integer",
            "files" => "nullable|array",
            "files.*" => "file|max:2048",
            "posts" => "array",
            "posts.*.region_position_id" => "required|integer|exists:region_positions,id",
            "posts.*.approved_count" => "required|integer|min:0",
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
        $maxSortOrder = RegionOrganizationalUnit::where(
            'province_id',
            $request->province_id
        )
            ->where('parent_id', $request->parent_id)
            ->max('sort_order');
        $region_org_unit = RegionOrganizationalUnit::create([
            "title_id" => $request->title_id,
            "province_id" => $request->province_id,
            "parent_id" => $request->parent_id,
            "city_id" => $request->city_id,
            "is_active" => $request->status,
            'sort_order' => ($maxSortOrder ?? 0) + 1,
            "region_organizational_unit_type_id" => $request->type,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file("files") as $file) {
                $fileName = $file->getClientOriginalName();
                // $filePath = time() . '.' . $file->getClientOriginalName();
                $filePath = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('/files/region-org-units', $filePath, 'public');
                RegionOrgUnitFile::create([
                    "region_organizational_unit_id" => $region_org_unit->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath,
                    "status" => 1
                ]);
            }
        }
        if (!empty($request->posts)) {
            foreach ($request->posts as $post) {
                $position = RegionPosition::findOrFail($post["region_position_id"]);
                if ($position) {
                    RegionOrganizationalUnitPosition::create([
                        "region_position_id" => $position->id,
                        "region_organizational_unit_id" => $region_org_unit->id,
                        "approved_count" => $post["approved_count"],
                        "description" => $post["description"],
                        "user_id" => auth()->user()->id,
                    ]);
                }
            }
        }
        DB::commit();
        return $this->successResponse($region_org_unit, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orgUnit = RegionOrganizationalUnit::with(['province', 'city', 'orgType', 'parent', 'files','positions'])->findOrFail($id);
        return $this->successResponse((new RegionOrgUnitResource($orgUnit)), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all(), $id);
        $validator = Validator::make($request->all(), [
            'parent_id' =>  "nullable|integer|exists:region_organizational_units,id",
            "title_id" => "required|integer",
            "status" => "required|integer",
            "sort_order" => "nullable|integer",
            "type" => "required|integer",
            "files" => "nullable|array",
            "files.*" => "file|max:2048",
            "posts" => "array",
            "posts.*.region_position_id" => "required|integer|exists:region_positions,id",
            "posts.*.approved_count" => "required|integer|min:0",
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
        $orgUnit = RegionOrganizationalUnit::findOrFail($id);
        $orgUnit->update([
            "title_id" => $request->title_id,
            "parent_id" => $request->parent_id,
            "is_active" => $request->status,
            'sort_order' => $request->sort_order,
            "region_organizational_unit_type_id" => $request->type,
            "description" => $request->description,
            "user_id" => auth()->user()->id,
        ]);
        if ($request->has("fileIdsForDelete")) {
            foreach ($request->fileIdsForDelete as $fileId) {
                $file = RegionOrgUnitFile::findOrFail($fileId);
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
                $file->storeAs('/files/region-org-units', $filePath, 'public');
                RegionOrgUnitFile::create([
                    "region_organizational_unit_id" => $orgUnit->id,
                    "fileName" => $fileName,
                    "filePath" => $filePath
                ]);
            }
        }

        if (!empty($request->posts)) {
            
            foreach ($request->posts as $post) {

                $position = RegionPosition::findOrFail($post["region_position_id"]);

                $org_position = RegionOrganizationalUnitPosition::where(
                    'region_position_id',
                    $position->id
                )
                    ->where(
                        'region_organizational_unit_id',
                        $orgUnit->id
                    )->first();
                if ($org_position) {
                    $org_position->update([
                        'approved_count' => $post['approved_count'],
                        "description" => $post['description_post'],
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    RegionOrganizationalUnitPosition::create([
                        'region_position_id' => $position->id,
                        'region_organizational_unit_id' => $orgUnit->id,
                        'approved_count' => $post['approved_count'],
                        "description" => $post['description_post'],
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }

        foreach ($request->postIdsForDelete ?? [] as $positionId) {

            $pivot = RegionOrganizationalUnitPosition::where(
                'region_position_id',
                $positionId
            )
                ->where(
                    'region_organizational_unit_id',
                    $orgUnit->id
                )
                ->first();

            if (!$pivot) {
                continue;
            }
            $pivot->delete();
        }

        DB::commit();
        return $this->successResponse($orgUnit, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getTree(string  $provinceId, ?string $cityId = null)
    {
        // dd($provinceId, $cityId);
        $query = RegionOrganizationalUnit::query()->with([
            "province",
            "city",
            "orgType",
            "user",
            "parent",
            "children",
        ])
            ->where('province_id', $provinceId)
            ->whereNull('parent_id');

        if ($cityId === null || $cityId === 'null') {
            // واحدهای ستادی بدون شهر
            $query->whereNull('city_id');
        } else {
            // واحدهای مربوط به یک شهر
            $query->where('city_id', $cityId);
        }

        $region_org_units = $query
            ->get();
        //  dd($region_org_units);
        // return $this->successResponse(($region_org_units), 200);
        return $this->successResponse(RegionOrgUnitResource::collection($region_org_units), 200);
    }
    public function getTreeById(string  $provinceId, string $unitId)
    {
        // dd($provinceId, $cityId);
        $query = RegionOrganizationalUnit::query()->with([
            "province",
            "city",
            "orgType",
            "user",
            "parent",
            "children",
        ])
            ->where('province_id', $provinceId)
            ->where('id', $unitId);

        $region_org_units = $query
            ->first();
        //  dd($region_org_units);
        return $this->successResponse(new RegionOrgUnitResource($region_org_units), 200);
    }
}
