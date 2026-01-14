<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarWashDriver;
use App\Models\DriverVehicle;

class CarWashDriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:car-wash-drivers.view')->only(['index','show']);
        $this->middleware('permission:manage car-wash-drivers')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $drivers = CarWashDriver::with('vehicles')->orderByDesc('created_at')->paginate(20);
        return view('dashboard.admin.car_wash_drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('dashboard.admin.car_wash_drivers.create');
    }

    public function show(CarWashDriver $car_wash_driver)
    {
        $driver = $car_wash_driver->load('vehicles');
        return view('dashboard.admin.car_wash_drivers.show', compact('driver', 'car_wash_driver'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'license_number' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
            'plate_number' => 'nullable|string|max:191',
            'make' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'color' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer',
        ]);

        $driver = CarWashDriver::create($data);

        // create vehicle if plate provided and attach to driver
        if (!empty($data['plate_number'])) {
            $veh = DriverVehicle::create([
                'plate_number' => $data['plate_number'],
                'make' => $data['make'] ?? null,
                'model' => $data['model'] ?? null,
                'color' => $data['color'] ?? null,
                'capacity' => $data['capacity'] ?? null,
            ]);
            $driver->vehicles()->attach($veh->id);
        }

        return redirect()->route('admin.car-wash-drivers.index')->with('success', 'Driver created.');
    }

    public function edit(CarWashDriver $car_wash_driver)
    {
        $driver = $car_wash_driver->load('vehicles');
        return view('dashboard.admin.car_wash_drivers.edit', compact('driver', 'car_wash_driver'));
    }

    public function update(Request $request, CarWashDriver $car_wash_driver)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'license_number' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
            'plate_number' => 'nullable|string|max:191',
            'make' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'color' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer',
        ]);

        $car_wash_driver->update($data);

        // basic vehicle sync: if plate provided, update first attached vehicle or create+attach
        if (!empty($data['plate_number'])) {
            $firstVeh = $car_wash_driver->vehicles()->first();
            if ($firstVeh) {
                $firstVeh->update([
                    'plate_number' => $data['plate_number'] ?? $firstVeh->plate_number,
                    'make' => $data['make'] ?? $firstVeh->make,
                    'model' => $data['model'] ?? $firstVeh->model,
                    'color' => $data['color'] ?? $firstVeh->color,
                    'capacity' => $data['capacity'] ?? $firstVeh->capacity,
                ]);
            } else {
                $veh = DriverVehicle::create([
                    'plate_number' => $data['plate_number'],
                    'make' => $data['make'] ?? null,
                    'model' => $data['model'] ?? null,
                    'color' => $data['color'] ?? null,
                    'capacity' => $data['capacity'] ?? null,
                ]);
                $car_wash_driver->vehicles()->attach($veh->id);
            }
        }

        return redirect()->route('admin.car-wash-drivers.index')->with('success', 'Driver updated.');
    }

    public function destroy(CarWashDriver $car_wash_driver)
    {
        $car_wash_driver->delete();
        return redirect()->route('admin.car-wash-drivers.index')->with('success', 'Driver removed.');
    }
}
