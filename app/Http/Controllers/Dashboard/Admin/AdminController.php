<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        // Admins management restricted to super-admin
        $this->middleware('role:super-admin')->only(['index','create','store','edit','update','destroy','show']);
        $this->middleware('permission:manage admins')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        $admins = Admin::all();
        return view('dashboard.admin.admins.index', compact('admins', 'currentAdmin'));
    }

    public function create()
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        $roles = \Spatie\Permission\Models\Role::all();
        return view('dashboard.admin.admins.create', compact('currentAdmin', 'roles'));
    }

    public function store(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('admins', 'public');
            $data['photo'] = $path;
        }

        Admin::create($data);

        // assign roles if provided
        if ($request->filled('roles')) {
            $admin = Admin::where('email', $request->email)->first();
            if ($admin && method_exists($admin, 'assignRole')) {
                $admin->assignRole($request->input('roles'));
            }
        }

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        $roles = \Spatie\Permission\Models\Role::all();
        $adminRoles = $admin->roles->pluck('name')->toArray();
        return view('dashboard.admin.admins.edit', compact('admin', 'currentAdmin', 'roles', 'adminRoles'));
    }

    public function update(Request $request, Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
        ];

        if ($request->hasFile('photo')) {
            // delete old photo if exists
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }
            $data['photo'] = $request->file('photo')->store('admins', 'public');
        }

        $admin->update($data);

        // sync roles if provided and current user is allowed
        if ($request->has('roles') && method_exists($admin, 'syncRoles')) {
            $admin->syncRoles($request->input('roles', []));
        }

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin) {
            return redirect()->route('admin.login');
        }

        if ($admin->id === $currentAdmin->id) {
            return redirect()->route('admin.admins.index')->with('error', 'You cannot delete yourself.');
        }

        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }
}
