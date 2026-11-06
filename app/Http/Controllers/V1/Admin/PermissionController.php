<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PermissionResource;

class PermissionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        return  $this->successResponse($permissions, 200);
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
            "permission_name" => "required|string|unique:permissions,name",
            "permission_display_name" => "required|string",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $permission = Permission::create([
            "name" => $request->permission_name,
            "display_name" => $request->permission_display_name,
            "guard_name" => "api"

        ]);
        DB::commit();
        return $this->successResponse($permission, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return $this->successResponse((new PermissionResource($permission)), 200);
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
            "permission_name" => "required|string|unique:permissions,name," . $id,
            "permission_display_name" => "required|string",
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();
        $permission = Permission::findOrFail($id);
        $permission->update([
            "name" => $request->permission_name,
            "display_name" => $request->permission_display_name,
            "guard_name" => "api"
        ]);
        DB::commit();
        return $this->successResponse($permission, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return $this->successResponse(1, 200);
    }
}
