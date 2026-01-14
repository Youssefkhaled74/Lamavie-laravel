<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DriversController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        // Viewing drivers
        $this->middleware('permission:drivers.view')->only(['index', 'show']);

        // Manage drivers (create/edit/delete) and service assignments
        $this->middleware('permission:manage drivers')->only(['create', 'store', 'edit', 'update', 'destroy', 'assignService', 'removeService']);
    }
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $query = Driver::query();
        if ($request->has('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%');
        }

        $drivers = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.drivers.partials.items-table', compact('drivers'))->render(),
                'pagination' => $drivers->appends(['q' => $request->q])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.drivers.index', compact('drivers', 'admin'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        return view('dashboard.admin.drivers.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        Driver::create($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $allServices = \App\Models\Service::all();
        $driver->load('services');
        return view('dashboard.admin.drivers.show', compact('driver', 'admin', 'allServices'));
    }

    /**
     * Attach a service to a driver
     */
    public function assignService(Request $request, Driver $driver)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $driver->services()->syncWithoutDetaching([$request->service_id]);

        return redirect()->route('admin.drivers.show', $driver)->with('success', 'Service assigned to driver.');
    }

    /**
     * Remove a service from a driver
     */
    public function removeService(Driver $driver, $serviceId)
    {
        $driver->services()->detach($serviceId);
        return redirect()->route('admin.drivers.show', $driver)->with('success', 'Service removed from driver.');
    }

    public function edit(Driver $driver)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.drivers.edit', compact('driver', 'admin'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers,email,' . $driver->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
