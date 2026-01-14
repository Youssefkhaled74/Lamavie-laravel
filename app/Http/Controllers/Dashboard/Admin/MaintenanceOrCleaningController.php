<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\MaintenanceOrCleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceOrCleaningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:maintenance-or-cleaning.view')->only(['index','show']);
        $this->middleware('permission:manage maintenance-or-cleaning')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the maintenance or cleaning records.
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
        $query = MaintenanceOrCleaning::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $maintenanceOrCleanings = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.maintenance-or-cleaning.partials.items-table', compact('maintenanceOrCleanings'))->render(),
                'pagination' => $maintenanceOrCleanings->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.maintenance-or-cleaning.index', compact('maintenanceOrCleanings', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new maintenance or cleaning record.
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
        return view('dashboard.admin.maintenance-or-cleaning.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created maintenance or cleaning record in storage.
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

        MaintenanceOrCleaning::create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.maintenance-or-cleaning.index')->with('success', 'Maintenance or cleaning record created successfully.');
    }

    /**
     * Display the specified maintenance or cleaning record.
     *
     * @param  \App\Models\MaintenanceOrCleaning  $maintenanceOrCleaning
     * @return \Illuminate\Http\Response
     */
    public function show(MaintenanceOrCleaning $maintenanceOrCleaning)
    {
        $maintenanceOrCleaning->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.maintenance-or-cleaning.show', compact('maintenanceOrCleaning', 'admin'));
    }

    /**
     * Show the form for editing the specified maintenance or cleaning record.
     *
     * @param  \App\Models\MaintenanceOrCleaning  $maintenanceOrCleaning
     * @return \Illuminate\Http\Response
     */
    public function edit(MaintenanceOrCleaning $maintenanceOrCleaning)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.maintenance-or-cleaning.edit', compact('maintenanceOrCleaning', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified maintenance or cleaning record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaintenanceOrCleaning  $maintenanceOrCleaning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaintenanceOrCleaning $maintenanceOrCleaning)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $maintenanceOrCleaning->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.maintenance-or-cleaning.index')->with('success', 'Maintenance or cleaning record updated successfully.');
    }

    /**
     * Remove the specified maintenance or cleaning record from storage.
     *
     * @param  \App\Models\MaintenanceOrCleaning  $maintenanceOrCleaning
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaintenanceOrCleaning $maintenanceOrCleaning)
    {
        $maintenanceOrCleaning->delete();
        return redirect()->route('admin.maintenance-or-cleaning.index')->with('success', 'Maintenance or cleaning record deleted successfully.');
    }
}