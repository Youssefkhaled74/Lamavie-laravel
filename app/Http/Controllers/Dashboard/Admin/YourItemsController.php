<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\YourItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class YourItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:your-items.view')->only(['index','show']);
        $this->middleware('permission:manage your-items')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the items.
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
        $query = YourItems::with('serviceCategory');

        if ($request->has('service_category_id') && $request->service_category_id) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $yourItems = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.your-items.partials.items-table', compact('yourItems'))->render(),
                'pagination' => $yourItems->appends(['service_category_id' => $request->service_category_id])->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.your-items.index', compact('yourItems', 'admin', 'serviceCategories'));
    }

    /**
     * Show the form for creating a new item.
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
        return view('dashboard.admin.your-items.create', compact('admin', 'serviceCategories'));
    }

    /**
     * Store a newly created item in storage.
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('your-items/logos', 'public');
        }

        YourItems::create($data);

        return redirect()->route('admin.your-items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified item.
     *
     * @param  \App\Models\YourItems  $yourItem
     * @return \Illuminate\Http\Response
     */
    public function show(YourItems $yourItem)
    {
        $yourItem->load('serviceCategory');
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.your-items.show', compact('yourItem', 'admin'));
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  \App\Models\YourItems  $yourItem
     * @return \Illuminate\Http\Response
     */
    public function edit(YourItems $yourItem)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $serviceCategories = ServiceCategory::all();
        return view('dashboard.admin.your-items.edit', compact('yourItem', 'admin', 'serviceCategories'));
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\YourItems  $yourItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, YourItems $yourItem)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($request->hasFile('logo')) {
            if ($yourItem->logo) {
                Storage::disk('public')->delete($yourItem->logo);
            }
            $data['logo'] = $request->file('logo')->store('your-items/logos', 'public');
        }

        $yourItem->update($data);

        return redirect()->route('admin.your-items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  \App\Models\YourItems  $yourItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(YourItems $yourItem)
    {
        if ($yourItem->logo) {
            Storage::disk('public')->delete($yourItem->logo);
        }

        $yourItem->delete();
        return redirect()->route('admin.your-items.index')->with('success', 'Item deleted successfully.');
    }
}