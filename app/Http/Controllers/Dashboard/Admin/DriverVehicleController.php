<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverVehicle;
use App\Models\CarWashDriver;

class DriverVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:driver-vehicles.view')->only(['index','show']);
        $this->middleware('permission:manage driver-vehicles')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $vehicles = DriverVehicle::with('drivers')->orderByDesc('created_at')->paginate(20);
        return view('dashboard.admin.driver_vehicles.index', compact('vehicles'));
    }

    public function show(DriverVehicle $driver_vehicle)
    {
        $vehicle = $driver_vehicle->load('drivers');
        return view('dashboard.admin.driver_vehicles.show', compact('vehicle'));
    }

    public function create()
    {
        $drivers = CarWashDriver::orderBy('name')->get();
        return view('dashboard.admin.driver_vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_ids' => 'nullable|array',
            'driver_ids.*' => 'exists:car_wash_drivers,id',
            'plate_number' => 'nullable|string|max:191',
            'make' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'color' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer',
        ]);

        $vehicle = DriverVehicle::create([
            'plate_number' => $data['plate_number'] ?? null,
            'make' => $data['make'] ?? null,
            'model' => $data['model'] ?? null,
            'color' => $data['color'] ?? null,
            'capacity' => $data['capacity'] ?? null,
        ]);

        if (!empty($data['driver_ids'])) {
            $vehicle->drivers()->sync($data['driver_ids']);
        }

        return redirect()->route('admin.driver-vehicles.index')->with('success', 'Vehicle created.');
    }

    public function edit(DriverVehicle $driver_vehicle)
    {
        $vehicle = $driver_vehicle;
        $drivers = CarWashDriver::orderBy('name')->get();
        return view('dashboard.admin.driver_vehicles.edit', compact('vehicle','drivers'));
    }

    public function update(Request $request, DriverVehicle $driver_vehicle)
    {
        $data = $request->validate([
            'driver_ids' => 'nullable|array',
            'driver_ids.*' => 'exists:car_wash_drivers,id',
            'plate_number' => 'nullable|string|max:191',
            'make' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'color' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer',
        ]);

        $driver_vehicle->update([
            'plate_number' => $data['plate_number'] ?? $driver_vehicle->plate_number,
            'make' => $data['make'] ?? $driver_vehicle->make,
            'model' => $data['model'] ?? $driver_vehicle->model,
            'color' => $data['color'] ?? $driver_vehicle->color,
            'capacity' => $data['capacity'] ?? $driver_vehicle->capacity,
        ]);

        if (isset($data['driver_ids'])) {
            $driver_vehicle->drivers()->sync($data['driver_ids']);
        }

        return redirect()->route('admin.driver-vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function destroy(DriverVehicle $driver_vehicle)
    {
        $driver_vehicle->delete();
        return redirect()->route('admin.driver-vehicles.index')->with('success', 'Vehicle removed.');
    }
}
