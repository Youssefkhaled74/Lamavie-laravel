<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Frequency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrequencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:frequency.view')->only(['index','show']);
        $this->middleware('permission:manage frequency')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the frequencies.
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
        $query = Frequency::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $frequencies = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.frequency.partials.frequencies-table', compact('frequencies'))->render(),
                'pagination' => $frequencies->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.frequency.index', compact('frequencies', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new frequency.
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
        return view('dashboard.admin.frequency.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created frequency in storage.
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

        Frequency::create($data);

        return redirect()->route('admin.frequency.index')->with('success', 'Frequency created successfully.');
    }

    /**
     * Display the specified frequency.
     *
     * @param  \App\Models\Frequency  $frequency
     * @return \Illuminate\Http\Response
     */
    public function show(Frequency $frequency)
    {
        $frequency->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.frequency.show', compact('frequency', 'admin'));
    }

    /**
     * Show the form for editing the specified frequency.
     *
     * @param  \App\Models\Frequency  $frequency
     * @return \Illuminate\Http\Response
     */
    public function edit(Frequency $frequency)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.frequency.edit', compact('frequency', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified frequency in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Frequency  $frequency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Frequency $frequency)
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

        $frequency->update($data);

        return redirect()->route('admin.frequency.index')->with('success', 'Frequency updated successfully.');
    }

    /**
     * Remove the specified frequency from storage.
     *
     * @param  \App\Models\Frequency  $frequency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Frequency $frequency)
    {
        $frequency->delete();
        return redirect()->route('admin.frequency.index')->with('success', 'Frequency deleted successfully.');
    }
}