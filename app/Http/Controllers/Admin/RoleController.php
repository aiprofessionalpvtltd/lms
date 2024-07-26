<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
//        $this->middleware(['permission:view-roles'])->only(['index', 'show', 'view']);
//        $this->middleware(['permission:create-roles']);
//        $this->middleware(['permission:edit-roles'])->only(['edit', 'update']);
//        $this->middleware(['permission:delete-roles'])->only('destroy');
    }

    public function show()
    {
         $title = 'Roles';
        $roles = Role::all();
        return view("admin.role.index", compact('roles', 'title'));
    }


    public function getAllPermissions()
    {
        // Get all modules with their permissions
        $modules = Module::with('permissions')->get();

        $formattedPermissions = [];

        foreach ($modules as $module) {
            $moduleName = $module->name;
            $modulePermissions = $module->permissions->pluck('short_name', 'id')->toArray();

            // Format the permissions for the module
            $formattedPermissions[] = [
                'name' => $moduleName,
                'permissions' => $modulePermissions,
            ];
        }

        return $formattedPermissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Roles';
        $roles = Role::all();
        $permissions = $this->getAllPermissions();
//        dd($permissions);
        return view('admin.role.create', compact('title', 'roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();

        }

        $role = Role::create(['name' => $request->input('name')]);

        if ($request->has('permission') && count($request->permission) > 0) {
            foreach ($request->permission as $permission) {
//                $role->givePermissionTo($permission);
                $role->syncPermissions($permission);
            }
        }

        if ($role) {

            return redirect()->route('show-role')->with('success', 'Role and Permission added Successfully');

        } else {

            return redirect()->route('show-role')->with('error', 'Something went wrong');

        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Roles';
        $roles = Role::all();
        $role = Role::find($id);

        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('name', 'id')
            ->toArray();
        foreach ($this->getAllPermissions() as $key => $module) {
            $moduleName = $module['name'];
            $modulePermissions = $module['permissions'];

            // Check if any permission exists in $rolePermissions for this module
            $permissionsExistInRole = array_intersect_key($modulePermissions, $rolePermissions);

            $permissions[$key]['name'] = $moduleName;
            // If there are permissions for this module in $rolePermissions, mark them as checked
            foreach ($modulePermissions as $permissionId => $permissionName) {
                $permissions[$key]['permissions'][$permissionId] = [
                    'name' => $permissionName,
                    'checked' => isset($permissionsExistInRole[$permissionId]),
                ];
            }
        }

//        dd($permissions);

        return view('admin.role.edit', compact('title','roles','permissions','role', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();

        }
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();


        if ($request->has('permission') && count($request->permission) > 0) {
            $role->syncPermissions($request->permission);
        } else {
            $role->syncPermissions([]);
        }
        if ($role) {

            return redirect()->route('show-role')->with('success', 'Role and Permission Updated Successfully');

        } else {

            return redirect()->route('show-role')->with('error', 'Something went wrong');

        }
    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Role $role)
//    {
//        $role->delete();
//        return redirect('roles');
//    }
}
