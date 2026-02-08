<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\YourItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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

        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(function ($sub) use ($term) {
                $like = '%' . $term . '%';
                $sub->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", [$like])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", [$like]);
            });
        }

        $yourItems = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.your-items.partials.items-table', compact('yourItems'))->render(),
                'pagination' => $yourItems->appends([
                    'service_category_id' => $request->service_category_id,
                    'q' => $request->q,
                ])->links('vendor.pagination.bootstrap-5')->toHtml(),
                'meta' => [
                    'total' => $yourItems->total(),
                    'count' => $yourItems->count(),
                    'from' => $yourItems->firstItem(),
                    'to' => $yourItems->lastItem(),
                ],
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
            'washing_price' => 'nullable|numeric|min:0|max:999999.99',
            'ironing_price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_category_id' => $request->service_category_id,
            'washing_price' => $request->washing_price,
            'ironing_price' => $request->ironing_price,
            'price' => $request->washing_price ?? $request->ironing_price,
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
            'washing_price' => 'nullable|numeric|min:0|max:999999.99',
            'ironing_price' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        $data = [
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'service_category_id' => $request->service_category_id,
            'washing_price' => $request->washing_price,
            'ironing_price' => $request->ironing_price,
            'price' => $request->washing_price ?? $request->ironing_price,
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

    /**
     * Bulk delete items.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : [];
        }

        $request->merge(['ids' => $ids]);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:your_items,id',
        ]);
        $items = YourItems::whereIn('id', $ids)->get();
        foreach ($items as $item) {
            if ($item->logo) {
                Storage::disk('public')->delete($item->logo);
            }
        }
        YourItems::whereIn('id', $ids)->delete();

        return redirect()->route('admin.your-items.index')->with('success', 'Items deleted successfully.');
    }

    /**
     * Export items as CSV.
     */
    public function export(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : [];
        }
        $query = YourItems::with('serviceCategory');
        if (is_array($ids) && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }
        $items = $query->get();

        $filename = 'your_items_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name (EN)', 'Name (AR)', 'Category (EN)', 'Category (AR)', 'Washing Price', 'Ironing Price', 'Created At']);
            foreach ($items as $item) {
                $nameEn = is_array($item->name) ? ($item->name['en'] ?? '') : $item->name;
                $nameAr = is_array($item->name) ? ($item->name['ar'] ?? '') : '';
                $catEn = $item->serviceCategory && is_array($item->serviceCategory->name) ? ($item->serviceCategory->name['en'] ?? '') : '';
                $catAr = $item->serviceCategory && is_array($item->serviceCategory->name) ? ($item->serviceCategory->name['ar'] ?? '') : '';
                fputcsv($out, [
                    $item->id,
                    $nameEn,
                    $nameAr,
                    $catEn,
                    $catAr,
                    $item->washing_price ?? $item->price,
                    $item->ironing_price,
                    $item->created_at,
                ]);
            }
            fclose($out);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
