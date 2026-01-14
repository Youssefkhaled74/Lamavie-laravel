<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\TypeOfStain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeOfStainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:type-of-stain.view')->only(['index','show']);
        $this->middleware('permission:manage type-of-stain')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the type of stains.
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
        $query = TypeOfStain::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $typeOfStains = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.type-of-stain.partials.items-table', compact('typeOfStains'))->render(),
                'pagination' => $typeOfStains->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.type-of-stain.index', compact('typeOfStains', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new type of stain.
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
        return view('dashboard.admin.type-of-stain.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created type of stain in storage.
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

        TypeOfStain::create($data);

        return redirect()->route('admin.type-of-stain.index')->with('success', 'Type of stain created successfully.');
    }

    /**
     * Display the specified type of stain.
     *
     * @param  \App\Models\TypeOfStain  $typeOfStain
     * @return \Illuminate\Http\Response
     */
    public function show(TypeOfStain $typeOfStain)
    {
        $typeOfStain->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.type-of-stain.show', compact('typeOfStain', 'admin'));
    }

    /**
     * Show the form for editing the specified type of stain.
     *
     * @param  \App\Models\TypeOfStain  $typeOfStain
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeOfStain $typeOfStain)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.type-of-stain.edit', compact('typeOfStain', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified type of stain in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeOfStain  $typeOfStain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeOfStain $typeOfStain)
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

        $typeOfStain->update($data);

        return redirect()->route('admin.type-of-stain.index')->with('success', 'Type of stain updated successfully.');
    }

    /**
     * Remove the specified type of stain from storage.
     *
     * @param  \App\Models\TypeOfStain  $typeOfStain
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeOfStain $typeOfStain)
    {
        $typeOfStain->delete();
        return redirect()->route('admin.type-of-stain.index')->with('success', 'Type of stain deleted successfully.');
    }
}