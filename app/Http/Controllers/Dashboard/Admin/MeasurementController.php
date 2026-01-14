<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:measurement.view')->only(['index','show']);
        $this->middleware('permission:manage measurement')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the measurements.
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
        $query = Measurement::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $measurements = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.measurement.partials.items-table', compact('measurements'))->render(),
                'pagination' => $measurements->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.measurement.index', compact('measurements', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new measurement.
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
        return view('dashboard.admin.measurement.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created measurement in storage.
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

        Measurement::create($data);

        return redirect()->route('admin.measurement.index')->with('success', 'Measurement created successfully.');
    }

    /**
     * Display the specified measurement.
     *
     * @param  \App\Models\Measurement  $measurement
     * @return \Illuminate\Http\Response
     */
    public function show(Measurement $measurement)
    {
        $measurement->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.measurement.show', compact('measurement', 'admin'));
    }

    /**
     * Show the form for editing the specified measurement.
     *
     * @param  \App\Models\Measurement  $measurement
     * @return \Illuminate\Http\Response
     */
    public function edit(Measurement $measurement)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.measurement.edit', compact('measurement', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified measurement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Measurement  $measurement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Measurement $measurement)
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

        $measurement->update($data);

        return redirect()->route('admin.measurement.index')->with('success', 'Measurement updated successfully.');
    }

    /**
     * Remove the specified measurement from storage.
     *
     * @param  \App\Models\Measurement  $measurement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Measurement $measurement)
    {
        $measurement->delete();
        return redirect()->route('admin.measurement.index')->with('success', 'Measurement deleted successfully.');
    }
}