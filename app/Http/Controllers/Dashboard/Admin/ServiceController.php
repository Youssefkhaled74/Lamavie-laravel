<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Service;
use App\Models\PhotoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:services.view')->only(['index','show']);
        $this->middleware('permission:manage services')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $services = Service::with('serviceTypes')->get();
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.services.index', compact('services', 'admin'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.services.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'about_ar' => 'required|string',
            'about_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'about' => [
                'ar' => $request->about_ar,
                'en' => $request->about_en,
            ],
            'description' => [
                'ar' => $request->description_ar,
                'en' => $request->description_en,
            ],
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('services/logos', 'public');
        }

        $service = Service::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('services/photos', 'public');
                PhotoService::create([
                    'service_id' => $service->id,
                    'file_path' => $path,
                    'photo_name' => $photo->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load('serviceTypes', 'photoServices');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.services.show', compact('service', 'admin'));
    }

    public function edit(Service $service)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        // Load relations used by the edit view to avoid lazy-loading issues
        $service->load('serviceTypes', 'photoServices');

        // Clear any leftover validation errors or old input so the form opens clean
        // without spurious error messages from previous redirects
        if (session()->has('_old_input')) {
            session()->forget('_old_input');
        }
        if (session()->has('errors')) {
            session()->forget('errors');
        }

        return view('dashboard.admin.services.edit', compact('service', 'admin'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'about_ar' => 'required|string',
            'about_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'about' => [
                'ar' => $request->about_ar,
                'en' => $request->about_en,
            ],
            'description' => [
                'ar' => $request->description_ar,
                'en' => $request->description_en,
            ],
        ];

        if ($request->hasFile('logo')) {
            if ($service->logo) {
                Storage::disk('public')->delete($service->logo);
            }
            $data['logo'] = $request->file('logo')->store('services/logos', 'public');
        }

        $service->update($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('services/photos', 'public');
                PhotoService::create([
                    'service_id' => $service->id,
                    'file_path' => $path,
                    'photo_name' => $photo->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->logo) {
            Storage::disk('public')->delete($service->logo);
        }

        foreach ($service->photoServices as $photo) {
            Storage::disk('public')->delete($photo->file_path);
            $photo->delete();
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    public function destroyPhoto(PhotoService $photo)
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();
        return back()->with('success', 'Photo deleted successfully.');
    }
}