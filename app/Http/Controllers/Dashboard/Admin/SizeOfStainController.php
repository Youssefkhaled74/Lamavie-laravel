<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\SizeOfStain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SizeOfStainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:size-of-stain.view')->only(['index','show']);
        $this->middleware('permission:manage size-of-stain')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the size of stains.
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
        $query = SizeOfStain::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $sizeOfStains = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.size-of-stain.partials.items-table', compact('sizeOfStains'))->render(),
                'pagination' => $sizeOfStains->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.size-of-stain.index', compact('sizeOfStains', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new size of stain.
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
        return view('dashboard.admin.size-of-stain.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created size of stain in storage.
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

        SizeOfStain::create($data);

        return redirect()->route('admin.size-of-stain.index')->with('success', 'Size of stain created successfully.');
    }

    /**
     * Display the specified size of stain.
     *
     * @param  \App\Models\SizeOfStain  $sizeOfStain
     * @return \Illuminate\Http\Response
     */
    public function show(SizeOfStain $sizeOfStain)
    {
        $sizeOfStain->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.size-of-stain.show', compact('sizeOfStain', 'admin'));
    }

    /**
     * Show the form for editing the specified size of stain.
     *
     * @param  \App\Models\SizeOfStain  $sizeOfStain
     * @return \Illuminate\Http\Response
     */
    public function edit(SizeOfStain $sizeOfStain)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.size-of-stain.edit', compact('sizeOfStain', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified size of stain in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SizeOfStain  $sizeOfStain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SizeOfStain $sizeOfStain)
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

        $sizeOfStain->update($data);

        return redirect()->route('admin.size-of-stain.index')->with('success', 'Size of stain updated successfully.');
    }

    /**
     * Remove the specified size of stain from storage.
     *
     * @param  \App\Models\SizeOfStain  $sizeOfStain
     * @return \Illuminate\Http\Response
     */
    public function destroy(SizeOfStain $sizeOfStain)
    {
        $sizeOfStain->delete();
        return redirect()->route('admin.size-of-stain.index')->with('success', 'Size of stain deleted successfully.');
    }
}