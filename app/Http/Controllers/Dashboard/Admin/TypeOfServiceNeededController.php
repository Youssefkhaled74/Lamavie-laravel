<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\TypeOfServiceNeeded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeOfServiceNeededController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:type-of-service-needed.view')->only(['index','show']);
        $this->middleware('permission:manage type-of-service-needed')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the type of service needed.
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
        $query = TypeOfServiceNeeded::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $typeOfServiceNeeded = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.type-of-service-needed.partials.items-table', compact('typeOfServiceNeeded'))->render(),
                'pagination' => $typeOfServiceNeeded->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.type-of-service-needed.index', compact('typeOfServiceNeeded', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new type of service needed.
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
        return view('dashboard.admin.type-of-service-needed.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created type of service needed in storage.
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

        TypeOfServiceNeeded::create($data);

        return redirect()->route('admin.type-of-service-needed.index')->with('success', 'Type of service needed created successfully.');
    }

    /**
     * Display the specified type of service needed.
     *
     * @param  \App\Models\TypeOfServiceNeeded  $typeOfServiceNeeded
     * @return \Illuminate\Http\Response
     */
    public function show(TypeOfServiceNeeded $typeOfServiceNeeded)
    {
        $typeOfServiceNeeded->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.type-of-service-needed.show', compact('typeOfServiceNeeded', 'admin'));
    }

    /**
     * Show the form for editing the specified type of service needed.
     *
     * @param  \App\Models\TypeOfServiceNeeded  $typeOfServiceNeeded
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeOfServiceNeeded $typeOfServiceNeeded)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.type-of-service-needed.edit', compact('typeOfServiceNeeded', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified type of service needed in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeOfServiceNeeded  $typeOfServiceNeeded
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeOfServiceNeeded $typeOfServiceNeeded)
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

        $typeOfServiceNeeded->update($data);

        return redirect()->route('admin.type-of-service-needed.index')->with('success', 'Type of service needed updated successfully.');
    }

    /**
     * Remove the specified type of service needed from storage.
     *
     * @param  \App\Models\TypeOfServiceNeeded  $typeOfServiceNeeded
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeOfServiceNeeded $typeOfServiceNeeded)
    {
        $typeOfServiceNeeded->delete();
        return redirect()->route('admin.type-of-service-needed.index')->with('success', 'Type of service needed deleted successfully.');
    }
}