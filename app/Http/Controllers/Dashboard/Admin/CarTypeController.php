<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\CarType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:car_type.view')->only(['index','show']);
        $this->middleware('permission:manage car type')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the car types.
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
        $query = CarType::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $carTypes = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.car_type.partials.car-types-table', compact('carTypes'))->render(),
                'pagination' => $carTypes->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.car_type.index', compact('carTypes', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new car type.
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
        return view('dashboard.admin.car_type.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created car type in storage.
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

        CarType::create($data);

        return redirect()->route('admin.car_type.index')->with('success', 'Car type created successfully.');
    }

    /**
     * Display the specified car type.
     *
     * @param  \App\Models\CarType  $carType
     * @return \Illuminate\Http\Response
     */
    public function show(CarType $carType)
    {
        $carType->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.car_type.show', compact('carType', 'admin'));
    }

    /**
     * Show the form for editing the specified car type.
     *
     * @param  \App\Models\CarType  $carType
     * @return \Illuminate\Http\Response
     */
    public function edit(CarType $carType)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.car_type.edit', compact('carType', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified car type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarType  $carType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarType $carType)
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

        $carType->update($data);

        return redirect()->route('admin.car_type.index')->with('success', 'Car type updated successfully.');
    }

    /**
     * Remove the specified car type from storage.
     *
     * @param  \App\Models\CarType  $carType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarType $carType)
    {
        $carType->delete();
        return redirect()->route('admin.car_type.index')->with('success', 'Car type deleted successfully.');
    }
}
