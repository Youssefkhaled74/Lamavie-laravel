<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\CarpetSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarpetSizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:carpet-size.view')->only(['index','show']);
        $this->middleware('permission:manage carpet-size')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the carpet sizes.
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
        $query = CarpetSize::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $carpetSizes = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.carpet-size.partials.items-table', compact('carpetSizes'))->render(),
                'pagination' => $carpetSizes->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.carpet-size.index', compact('carpetSizes', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new carpet size.
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
        return view('dashboard.admin.carpet-size.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created carpet size in storage.
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

        CarpetSize::create($data);

        return redirect()->route('admin.carpet-size.index')->with('success', 'Carpet size created successfully.');
    }

    /**
     * Display the specified carpet size.
     *
     * @param  \App\Models\CarpetSize  $carpetSize
     * @return \Illuminate\Http\Response
     */
    public function show(CarpetSize $carpetSize)
    {
        $carpetSize->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.carpet-size.show', compact('carpetSize', 'admin'));
    }

    /**
     * Show the form for editing the specified carpet size.
     *
     * @param  \App\Models\CarpetSize  $carpetSize
     * @return \Illuminate\Http\Response
     */
    public function edit(CarpetSize $carpetSize)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.carpet-size.edit', compact('carpetSize', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified carpet size in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarpetSize  $carpetSize
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetSize $carpetSize)
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

        $carpetSize->update($data);

        return redirect()->route('admin.carpet-size.index')->with('success', 'Carpet size updated successfully.');
    }

    /**
     * Remove the specified carpet size from storage.
     *
     * @param  \App\Models\CarpetSize  $carpetSize
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarpetSize $carpetSize)
    {
        $carpetSize->delete();
        return redirect()->route('admin.carpet-size.index')->with('success', 'Carpet size deleted successfully.');
    }
}