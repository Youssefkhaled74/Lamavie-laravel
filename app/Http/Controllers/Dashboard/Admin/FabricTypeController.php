<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\FabricType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FabricTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:fabric-type.view')->only(['index','show']);
        $this->middleware('permission:manage fabric-type')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the fabric types.
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
        $query = FabricType::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $fabricTypes = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.fabric-type.partials.items-table', compact('fabricTypes'))->render(),
                'pagination' => $fabricTypes->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.fabric-type.index', compact('fabricTypes', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new fabric type.
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
        return view('dashboard.admin.fabric-type.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created fabric type in storage.
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

        FabricType::create($data);

        return redirect()->route('admin.fabric-type.index')->with('success', 'Fabric type created successfully.');
    }

    /**
     * Display the specified fabric type.
     *
     * @param  \App\Models\FabricType  $fabricType
     * @return \Illuminate\Http\Response
     */
    public function show(FabricType $fabricType)
    {
        $fabricType->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.fabric-type.show', compact('fabricType', 'admin'));
    }

    /**
     * Show the form for editing the specified fabric type.
     *
     * @param  \App\Models\FabricType  $fabricType
     * @return \Illuminate\Http\Response
     */
    public function edit(FabricType $fabricType)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.fabric-type.edit', compact('fabricType', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified fabric type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FabricType  $fabricType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FabricType $fabricType)
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

        $fabricType->update($data);

        return redirect()->route('admin.fabric-type.index')->with('success', 'Fabric type updated successfully.');
    }

    /**
     * Remove the specified fabric type from storage.
     *
     * @param  \App\Models\FabricType  $fabricType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FabricType $fabricType)
    {
        $fabricType->delete();
        return redirect()->route('admin.fabric-type.index')->with('success', 'Fabric type deleted successfully.');
    }
}
