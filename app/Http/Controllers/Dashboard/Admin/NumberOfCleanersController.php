<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\NumberOfCleaners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NumberOfCleanersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:number-of-cleaners.view')->only(['index','show']);
        $this->middleware('permission:manage number-of-cleaners')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the number of cleaners.
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
        $query = NumberOfCleaners::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $numberOfCleaners = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.number-of-cleaners.partials.items-table', compact('numberOfCleaners'))->render(),
                'pagination' => $numberOfCleaners->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->render(),
            ]);
        }

        return view('dashboard.admin.number-of-cleaners.index', compact('numberOfCleaners', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new number of cleaners.
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
        return view('dashboard.admin.number-of-cleaners.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created number of cleaners in storage.
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

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        NumberOfCleaners::create($data);

        return redirect()->route('admin.number-of-cleaners.index')->with('success', 'Number of cleaners created successfully.');
    }

    /**
     * Display the specified number of cleaners.
     *
     * @param  \App\Models\NumberOfCleaners  $numberOfCleaners
     * @return \Illuminate\Http\Response
     */
    public function show(NumberOfCleaners $numberOfCleaners)
    {
        $numberOfCleaners->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.number-of-cleaners.show', compact('numberOfCleaners', 'admin'));
    }

    /**
     * Show the form for editing the specified number of cleaners.
     *
     * @param  \App\Models\NumberOfCleaners  $numberOfCleaners
     * @return \Illuminate\Http\Response
     */
    public function edit(NumberOfCleaners $numberOfCleaners)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.number-of-cleaners.edit', compact('numberOfCleaners', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified number of cleaners in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NumberOfCleaners  $numberOfCleaners
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NumberOfCleaners $numberOfCleaners)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'service_category_id' => $request->service_category_id,
            'price' => $request->price,
        ];

        $numberOfCleaners->update($data);

        return redirect()->route('admin.number-of-cleaners.index')->with('success', 'Number of cleaners updated successfully.');
    }

    /**
     * Remove the specified number of cleaners from storage.
     *
     * @param  \App\Models\NumberOfCleaners  $numberOfCleaners
     * @return \Illuminate\Http\Response
     */
    public function destroy(NumberOfCleaners $numberOfCleaners)
    {
        $numberOfCleaners->delete();
        return redirect()->route('admin.number-of-cleaners.index')->with('success', 'Number of cleaners deleted successfully.');
    }
}