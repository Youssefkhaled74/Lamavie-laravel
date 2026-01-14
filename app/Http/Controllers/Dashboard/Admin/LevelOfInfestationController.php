<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\LevelOfInfestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelOfInfestationController extends Controller
{
    /**
     * Display a listing of the level of infestation.
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
        $query = LevelOfInfestation::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $levelOfInfestation = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.level-of-infestation.partials.items-table', compact('levelOfInfestation'))->render(),
                'pagination' => $levelOfInfestation->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.level-of-infestation.index', compact('levelOfInfestation', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new level of infestation.
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
        return view('dashboard.admin.level-of-infestation.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created level of infestation in storage.
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

        LevelOfInfestation::create($data);

        return redirect()->route('admin.level-of-infestation.index')->with('success', 'Level of infestation created successfully.');
    }

    /**
     * Display the specified level of infestation.
     *
     * @param  \App\Models\LevelOfInfestation  $levelOfInfestation
     * @return \Illuminate\Http\Response
     */
    public function show(LevelOfInfestation $levelOfInfestation)
    {
        $levelOfInfestation->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.level-of-infestation.show', compact('levelOfInfestation', 'admin'));
    }

    /**
     * Show the form for editing the specified level of infestation.
     *
     * @param  \App\Models\LevelOfInfestation  $levelOfInfestation
     * @return \Illuminate\Http\Response
     */
    public function edit(LevelOfInfestation $levelOfInfestation)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.level-of-infestation.edit', compact('levelOfInfestation', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified level of infestation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LevelOfInfestation  $levelOfInfestation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LevelOfInfestation $levelOfInfestation)
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

        $levelOfInfestation->update($data);

        return redirect()->route('admin.level-of-infestation.index')->with('success', 'Level of infestation updated successfully.');
    }

    /**
     * Remove the specified level of infestation from storage.
     *
     * @param  \App\Models\LevelOfInfestation  $levelOfInfestation
     * @return \Illuminate\Http\Response
     */
    public function destroy(LevelOfInfestation $levelOfInfestation)
    {
        $levelOfInfestation->delete();
        return redirect()->route('admin.level-of-infestation.index')->with('success', 'Level of infestation deleted successfully.');
    }
}
