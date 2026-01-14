<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\PresenceOfChildrenOrPets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceOfChildrenOrPetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:presence-of-children-or-pets.view')->only(['index','show']);
        $this->middleware('permission:manage presence-of-children-or-pets')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the presence of children or pets.
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
        $query = PresenceOfChildrenOrPets::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $presenceOfChildrenOrPets = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.presence-of-children-or-pets.partials.items-table', compact('presenceOfChildrenOrPets'))->render(),
                'pagination' => $presenceOfChildrenOrPets->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.presence-of-children-or-pets.index', compact('presenceOfChildrenOrPets', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new presence of children or pets.
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
        return view('dashboard.admin.presence-of-children-or-pets.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created presence of children or pets in storage.
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

        PresenceOfChildrenOrPets::create($data);

        return redirect()->route('admin.presence-of-children-or-pets.index')->with('success', 'Presence of children or pets created successfully.');
    }

    /**
     * Display the specified presence of children or pets.
     *
     * @param  \App\Models\PresenceOfChildrenOrPets  $presenceOfChildrenOrPets
     * @return \Illuminate\Http\Response
     */
    public function show(PresenceOfChildrenOrPets $presenceOfChildrenOrPets)
    {
        $presenceOfChildrenOrPets->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.presence-of-children-or-pets.show', compact('presenceOfChildrenOrPets', 'admin'));
    }

    /**
     * Show the form for editing the specified presence of children or pets.
     *
     * @param  \App\Models\PresenceOfChildrenOrPets  $presenceOfChildrenOrPets
     * @return \Illuminate\Http\Response
     */
    public function edit(PresenceOfChildrenOrPets $presenceOfChildrenOrPets)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.presence-of-children-or-pets.edit', compact('presenceOfChildrenOrPets', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified presence of children or pets in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PresenceOfChildrenOrPets  $presenceOfChildrenOrPets
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PresenceOfChildrenOrPets $presenceOfChildrenOrPets)
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

        $presenceOfChildrenOrPets->update($data);

        return redirect()->route('admin.presence-of-children-or-pets.index')->with('success', 'Presence of children or pets updated successfully.');
    }

    /**
     * Remove the specified presence of children or pets from storage.
     *
     * @param  \App\Models\PresenceOfChildrenOrPets  $presenceOfChildrenOrPets
     * @return \Illuminate\Http\Response
     */
    public function destroy(PresenceOfChildrenOrPets $presenceOfChildrenOrPets)
    {
        $presenceOfChildrenOrPets->delete();
        return redirect()->route('admin.presence-of-children-or-pets.index')->with('success', 'Presence of children or pets deleted successfully.');
    }
}