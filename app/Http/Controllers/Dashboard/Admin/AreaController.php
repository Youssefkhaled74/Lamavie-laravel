<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:areas.view')->only(['index','show']);
        $this->middleware('permission:manage areas')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $areas = Area::orderBy('id', 'desc')->get();
        return view('dashboard.admin.areas.index', compact('areas', 'admin'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.areas.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:areas,slug',
            'description' => 'nullable|string',
            'price_increase_percentage' => 'nullable|numeric|min:0',
        ]);

        $slug = $request->slug ?: Str::slug($request->name_en);

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'slug' => $slug,
            'description' => $request->description,
            'price_increase_percentage' => $request->input('price_increase_percentage', 0),
        ];

        Area::create($data);

        return redirect()->route('admin.areas.index')->with('success', 'Area created successfully.');
    }

    public function show(Area $area)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.areas.show', compact('area', 'admin'));
    }

    public function edit(Area $area)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.areas.edit', compact('area', 'admin'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:areas,slug,' . $area->id,
            'description' => 'nullable|string',
            'price_increase_percentage' => 'nullable|numeric|min:0',
        ]);

        $slug = $request->slug ?: Str::slug($request->name_en);

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'slug' => $slug,
            'description' => $request->description,
            'price_increase_percentage' => $request->input('price_increase_percentage', 0),
        ];

        $area->update($data);

        return redirect()->route('admin.areas.index')->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area deleted successfully.');
    }
}
