<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:home-banners.view')->only(['index','show']);
        $this->middleware('permission:manage home-banners')->only(['create','store','edit','update','destroy']);
    }
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) { return redirect()->route('admin.login'); }
        $banners = HomeBanner::orderBy('sort_order')->latest()->paginate(15);
        return view('dashboard.admin.home-banners.index', compact('banners', 'admin'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) { return redirect()->route('admin.login'); }
        return view('dashboard.admin.home-banners.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $path = $request->file('image')->store('home_banners', 'public');
        HomeBanner::create([
            'image' => $path,
            'status' => (bool)$request->status,
            'sort_order' => $request->sort_order ?? 0,
        ]);
        return redirect()->route('admin.home-banners.index')->with('success', 'Banner created.');
    }

    public function edit(HomeBanner $home_banner)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) { return redirect()->route('admin.login'); }
        return view('dashboard.admin.home-banners.edit', ['banner' => $home_banner, 'admin' => $admin]);
    }

    public function update(Request $request, HomeBanner $home_banner)
    {
        $request->validate([
            'image' => 'nullable|image|max:4096',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data = [
            'status' => (bool)$request->status,
            'sort_order' => $request->sort_order ?? 0,
        ];
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('home_banners', 'public');
        }
        $home_banner->update($data);
        return redirect()->route('admin.home-banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(HomeBanner $home_banner)
    {
        $home_banner->delete();
        return redirect()->route('admin.home-banners.index')->with('success', 'Banner deleted.');
    }
}


