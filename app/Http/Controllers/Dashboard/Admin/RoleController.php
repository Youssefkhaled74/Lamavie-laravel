<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('role:super-admin');
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return view('dashboard.admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('dashboard.admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|unique:roles,name"]);
        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($request->input('permissions', []));
        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('dashboard.admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate(["name" => "required|string|unique:roles,name,{$role->id}"]);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($request->input('permissions', []));
        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    // Return JSON with all permissions and which are assigned to this role
    public function permissions(Role $role)
    {
        $all = Permission::all()->map(fn($p) => ['id' => $p->id, 'name' => $p->name]);
        $assigned = $role->permissions->pluck('name')->toArray();
        return response()->json(['permissions' => $all, 'assigned' => $assigned]);
    }

    // Update role permissions via AJAX
    public function updatePermissions(Request $request, Role $role)
    {
        $perms = $request->input('permissions', []);
        // Accept array of permission names
        $role->syncPermissions($perms);
        return response()->json(['success' => true, 'message' => 'Permissions updated.', 'count' => $role->permissions()->count()]);
    }
}
