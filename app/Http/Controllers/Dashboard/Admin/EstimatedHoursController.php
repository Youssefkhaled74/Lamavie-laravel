<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\EstimatedHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstimatedHoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:estimated-hours.view')->only(['index','show']);
        $this->middleware('permission:manage estimated-hours')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the estimated hours.
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
        $query = EstimatedHours::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $estimatedHours = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.estimated-hours.partials.items-table', compact('estimatedHours'))->render(),
                'pagination' => $estimatedHours->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.estimated-hours.index', compact('estimatedHours', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new estimated hours.
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
        return view('dashboard.admin.estimated-hours.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created estimated hours in storage.
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

        EstimatedHours::create($data);

        return redirect()->route('admin.estimated-hours.index')->with('success', 'Estimated hours created successfully.');
    }

    /**
     * Display the specified estimated hours.
     *
     * @param  \App\Models\EstimatedHours  $estimatedHours
     * @return \Illuminate\Http\Response
     */
    public function show(EstimatedHours $estimatedHours)
    {
        $estimatedHours->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.estimated-hours.show', compact('estimatedHours', 'admin'));
    }

    /**
     * Show the form for editing the specified estimated hours.
     *
     * @param  \App\Models\EstimatedHours  $estimatedHours
     * @return \Illuminate\Http\Response
     */
    public function edit(EstimatedHours $estimatedHours)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.estimated-hours.edit', compact('estimatedHours', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified estimated hours in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EstimatedHours  $estimatedHours
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EstimatedHours $estimatedHours)
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

        $estimatedHours->update($data);

        return redirect()->route('admin.estimated-hours.index')->with('success', 'Estimated hours updated successfully.');
    }

    /**
     * Remove the specified estimated hours from storage.
     *
     * @param  \App\Models\EstimatedHours  $estimatedHours
     * @return \Illuminate\Http\Response
     */
    public function destroy(EstimatedHours $estimatedHours)
    {
        $estimatedHours->delete();
        return redirect()->route('admin.estimated-hours.index')->with('success', 'Estimated hours deleted successfully.');
    }
}