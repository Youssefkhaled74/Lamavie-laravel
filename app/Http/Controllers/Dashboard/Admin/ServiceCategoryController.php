<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:service-categories.view')->only(['index','show']);
        $this->middleware('permission:manage service-categories')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the service categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $serviceCategories = ServiceCategory::with('service')->get();
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.service-categories.index', compact('serviceCategories', 'admin'));
    }

    /**
     * Show the form for creating a new service category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $services = Service::all();
        return view('dashboard.admin.service-categories.create', compact('admin', 'services'));
    }

    /**
     * Store a newly created service category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_id' => $request->service_id,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('service-categories/logos', 'public');
        }

        ServiceCategory::create($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Service category created successfully.');
    }

    /**
     * Display the specified service category.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceCategory $serviceCategory)
    {
        $serviceCategory->load('service', 'maintenanceOrCleaning', 'carpetMaterial', 'typeOfStain', 'sizeOfStain', 'carpetSize', 'yourItems');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.service-categories.show', compact('serviceCategory', 'admin'));
    }

    /**
     * Show the form for editing the specified service category.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $services = Service::all();
        return view('dashboard.admin.service-categories.edit', compact('serviceCategory', 'admin', 'services'));
    }

    /**
     * Update the specified service category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_id' => $request->service_id,
        ];

        if ($request->hasFile('logo')) {
            if ($serviceCategory->logo) {
                Storage::disk('public')->delete($serviceCategory->logo);
            }
            $data['logo'] = $request->file('logo')->store('service-categories/logos', 'public');
        }

        $serviceCategory->update($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Service category updated successfully.');
    }

    /**
     * Remove the specified service category from storage.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->logo) {
            Storage::disk('public')->delete($serviceCategory->logo);
        }

        $serviceCategory->delete();
        return redirect()->route('admin.service-categories.index')->with('success', 'Service category deleted successfully.');
    }
}