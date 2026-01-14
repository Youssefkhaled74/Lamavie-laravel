<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\CarpetMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:carpet-material.view')->only(['index','show']);
        $this->middleware('permission:manage carpet-material')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the carpet materials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $serviceCategories = ServiceCategory::all();
        $query = CarpetMaterial::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $carpetMaterials = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.carpet-material.partials.items-table', compact('carpetMaterials'))->render(),
                'pagination' => $carpetMaterials->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.carpet-material.index', compact('carpetMaterials', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new carpet material.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.carpet-material.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created carpet material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        CarpetMaterial::create($data);

        return redirect()->route('admin.carpet-material.index')->with('success', 'Carpet material created successfully.');
    }

    /**
     * Display the specified carpet material.
     *
     * @param  \App\Models\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetMaterial $carpetMaterial)
    {
        $carpetMaterial->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.carpet-material.show', compact('carpetMaterial', 'admin'));
    }

    /**
     * Show the form for editing the specified carpet material.
     *
     * @param  \App\Models\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetMaterial $carpetMaterial)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.carpet-material.edit', compact('carpetMaterial', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified carpet material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetMaterial $carpetMaterial)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        $carpetMaterial->update($data);

        return redirect()->route('admin.carpet-material.index')->with('success', 'Carpet material updated successfully.');
    }

    /**
     * Remove the specified carpet material from storage.
     *
     * @param  \App\Models\CarpetMaterial  $carpetMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetMaterial $carpetMaterial)
    {
        $carpetMaterial->delete();
        return redirect()->route('admin.carpet-material.index')->with('success', 'Carpet material deleted successfully.');
    }
}