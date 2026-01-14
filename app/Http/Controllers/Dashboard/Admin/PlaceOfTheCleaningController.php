<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\PlaceOfTheCleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaceOfTheCleaningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:place-of-the-cleaning.view')->only(['index','show']);
        $this->middleware('permission:manage place-of-the-cleaning')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the place of the cleaning records.
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
        $query = PlaceOfTheCleaning::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $placeOfTheCleanings = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.place-of-the-cleaning.partials.items-table', compact('placeOfTheCleanings'))->render(),
                'pagination' => $placeOfTheCleanings->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.place-of-the-cleaning.index', compact('placeOfTheCleanings', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new place of the cleaning record.
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
        return view('dashboard.admin.place-of-the-cleaning.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created place of the cleaning record in storage.
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

        PlaceOfTheCleaning::create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.place-of-the-cleaning.index')->with('success', 'Place of the cleaning created successfully.');
    }

    /**
     * Display the specified place of the cleaning record.
     *
     * @param  \App\Models\PlaceOfTheCleaning  $placeOfTheCleaning
     * @return \Illuminate\Http\Response
     */
    public function show(PlaceOfTheCleaning $placeOfTheCleaning)
    {
        $placeOfTheCleaning->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.place-of-the-cleaning.show', compact('placeOfTheCleaning', 'admin'));
    }

    /**
     * Show the form for editing the specified place of the cleaning record.
     *
     * @param  \App\Models\PlaceOfTheCleaning  $placeOfTheCleaning
     * @return \Illuminate\Http\Response
     */
    public function edit(PlaceOfTheCleaning $placeOfTheCleaning)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.place-of-the-cleaning.edit', compact('placeOfTheCleaning', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified place of the cleaning record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlaceOfTheCleaning  $placeOfTheCleaning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlaceOfTheCleaning $placeOfTheCleaning)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $placeOfTheCleaning->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.place-of-the-cleaning.index')->with('success', 'Place of the cleaning updated successfully.');
    }

    /**
     * Remove the specified place of the cleaning record from storage.
     *
     * @param  \App\Models\PlaceOfTheCleaning  $placeOfTheCleaning
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlaceOfTheCleaning $placeOfTheCleaning)
    {
        $placeOfTheCleaning->delete();
        return redirect()->route('admin.place-of-the-cleaning.index')->with('success', 'Place of the cleaning deleted successfully.');
    }
}