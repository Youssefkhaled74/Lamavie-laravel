<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\CarsAdditionalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarsAdditionalServiceController extends Controller
{
    /**
     * Display a listing of the cars additional services.
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
        $query = CarsAdditionalService::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $carsAdditionalServices = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.cars-additional-service.partials.items-table', compact('carsAdditionalServices'))->render(),
                'pagination' => $carsAdditionalServices->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.cars-additional-service.index', compact('carsAdditionalServices', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new cars additional service.
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
        return view('dashboard.admin.cars-additional-service.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created cars additional service in storage.
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

        CarsAdditionalService::create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.cars-additional-service.index')->with('success', 'Cars additional service created successfully.');
    }

    /**
     * Display the specified cars additional service.
     *
     * @param  \App\Models\CarsAdditionalService  $carsAdditionalService
     * @return \Illuminate\Http\Response
     */
    public function show(CarsAdditionalService $carsAdditionalService)
    {
        $carsAdditionalService->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.cars-additional-service.show', compact('carsAdditionalService', 'admin'));
    }

    /**
     * Show the form for editing the specified cars additional service.
     *
     * @param  \App\Models\CarsAdditionalService  $carsAdditionalService
     * @return \Illuminate\Http\Response
     */
    public function edit(CarsAdditionalService $carsAdditionalService)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.cars-additional-service.edit', compact('carsAdditionalService', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified cars additional service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarsAdditionalService  $carsAdditionalService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarsAdditionalService $carsAdditionalService)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $carsAdditionalService->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.cars-additional-service.index')->with('success', 'Cars additional service updated successfully.');
    }

    /**
     * Remove the specified cars additional service from storage.
     *
     * @param  \App\Models\CarsAdditionalService  $carsAdditionalService
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarsAdditionalService $carsAdditionalService)
    {
        $carsAdditionalService->delete();
        return redirect()->route('admin.cars-additional-service.index')->with('success', 'Cars additional service deleted successfully.');
    }
}