<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lab;

class LabsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:labs.view')->only(['index','show']);
        $this->middleware('permission:manage labs')->only(['create','store','edit','update','destroy']);
    }
    public function index(Request $request)
    {
        $labs = Lab::paginate(15);
        return view('dashboard.admin.labs.index', compact('labs'));
    }

    public function create()
    {
        return view('dashboard.admin.labs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        Lab::create($data);
        return redirect()->route('admin.labs.index')->with('success', 'Lab created successfully.');
    }

    public function edit(Lab $lab)
    {
        return view('dashboard.admin.labs.edit', compact('lab'));
    }

    public function show(Lab $lab)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $lab->load('bookings');
        return view('dashboard.admin.labs.show', compact('lab', 'admin'));
    }

    public function update(Request $request, Lab $lab)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $lab->update($data);
        return redirect()->route('admin.labs.index')->with('success', 'Lab updated successfully.');
    }

    public function destroy(Lab $lab)
    {
        $lab->delete();
        return redirect()->route('admin.labs.index')->with('success', 'Lab deleted successfully.');
    }
}
