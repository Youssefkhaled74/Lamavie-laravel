<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:service-types.view')->only(['index','show']);
        $this->middleware('permission:manage service-types')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the service types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $serviceTypes = ServiceType::with('service')->get();
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.service-types.index', compact('serviceTypes', 'admin'));
    }

    /**
     * Show the form for creating a new service type.
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
        return view('dashboard.admin.service-types.create', compact('admin', 'services'));
    }

    /**
     * Store a newly created service type in storage.
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
            $data['logo'] = $request->file('logo')->store('service-types/logos', 'public');
        }

        ServiceType::create($data);

        return redirect()->route('admin.service-types.index')->with('success', 'Service type created successfully.');
    }

    /**
     * Display the specified service type.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceType $serviceType)
    {
        $serviceType->load('service');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.service-types.show', compact('serviceType', 'admin'));
    }

    /**
     * Show the form for editing the specified service type.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceType $serviceType)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $services = Service::all();
        return view('dashboard.admin.service-types.edit', compact('serviceType', 'admin', 'services'));
    }

    /**
     * Update the specified service type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceType $serviceType)
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
            if ($serviceType->logo) {
                Storage::disk('public')->delete($serviceType->logo);
            }
            $data['logo'] = $request->file('logo')->store('service-types/logos', 'public');
        }

        $serviceType->update($data);

        return redirect()->route('admin.service-types.index')->with('success', 'Service type updated successfully.');
    }

    /**
     * Remove the specified service type from storage.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->logo) {
            Storage::disk('public')->delete($serviceType->logo);
        }

        $serviceType->delete();
        return redirect()->route('admin.service-types.index')->with('success', 'Service type deleted successfully.');
    }
}