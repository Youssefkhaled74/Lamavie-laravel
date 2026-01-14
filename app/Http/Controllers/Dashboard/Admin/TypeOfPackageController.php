<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\TypeOfPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeOfPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:type-of-package.view')->only(['index','show']);
        $this->middleware('permission:manage type-of-package')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the package types.
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
        $query = TypeOfPackage::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $typeOfPackages = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.type-of-package.partials.packages-table', compact('typeOfPackages'))->render(),
                'pagination' => $typeOfPackages->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.type-of-package.index', compact('typeOfPackages', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new package type.
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
        return view('dashboard.admin.type-of-package.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created package type in storage.
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

        TypeOfPackage::create($data);

        return redirect()->route('admin.type-of-package.index')->with('success', 'Package type created successfully.');
    }

    /**
     * Display the specified package type.
     *
     * @param  \App\Models\TypeOfPackage  $typeOfPackage
     * @return \Illuminate\Http\Response
     */
    public function show(TypeOfPackage $typeOfPackage)
    {
        $typeOfPackage->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.type-of-package.show', compact('typeOfPackage', 'admin'));
    }

    /**
     * Show the form for editing the specified package type.
     *
     * @param  \App\Models\TypeOfPackage  $typeOfPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeOfPackage $typeOfPackage)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.type-of-package.edit', compact('typeOfPackage', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified package type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeOfPackage  $typeOfPackage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeOfPackage $typeOfPackage)
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

        $typeOfPackage->update($data);

        return redirect()->route('admin.type-of-package.index')->with('success', 'Package type updated successfully.');
    }

    /**
     * Remove the specified package type from storage.
     *
     * @param  \App\Models\TypeOfPackage  $typeOfPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeOfPackage $typeOfPackage)
    {
        $typeOfPackage->delete();
        return redirect()->route('admin.type-of-package.index')->with('success', 'Package type deleted successfully.');
    }
}