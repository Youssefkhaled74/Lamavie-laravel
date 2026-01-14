<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\PackagesOptional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackagesOptionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:packages-optional.view')->only(['index','show']);
        $this->middleware('permission:manage packages-optional')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the packages optional.
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
        $query = PackagesOptional::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $packagesOptional = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.packages-optional.partials.items-table', compact('packagesOptional'))->render(),
                'pagination' => $packagesOptional->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.packages-optional.index', compact('packagesOptional', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new packages optional.
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
        return view('dashboard.admin.packages-optional.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created packages optional in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        PackagesOptional::create($data);

        return redirect()->route('admin.packages-optional.index')->with('success', 'Packages optional created successfully.');
    }

    /**
     * Display the specified packages optional.
     *
     * @param  \App\Models\PackagesOptional  $packagesOptional
     * @return \Illuminate\Http\Response
     */
    public function show(PackagesOptional $packagesOptional)
    {
        $packagesOptional->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.packages-optional.show', compact('packagesOptional', 'admin'));
    }

    /**
     * Show the form for editing the specified packages optional.
     *
     * @param  \App\Models\PackagesOptional  $packagesOptional
     * @return \Illuminate\Http\Response
     */
    public function edit(PackagesOptional $packagesOptional)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.packages-optional.edit', compact('packagesOptional', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified packages optional in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PackagesOptional  $packagesOptional
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PackagesOptional $packagesOptional)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        $packagesOptional->update($data);

        return redirect()->route('admin.packages-optional.index')->with('success', 'Packages optional updated successfully.');
    }

    /**
     * Remove the specified packages optional from storage.
     *
     * @param  \App\Models\PackagesOptional  $packagesOptional
     * @return \Illuminate\Http\Response
     */
    public function destroy(PackagesOptional $packagesOptional)
    {
        $packagesOptional->delete();
        return redirect()->route('admin.packages-optional.index')->with('success', 'Packages optional deleted successfully.');
    }
}