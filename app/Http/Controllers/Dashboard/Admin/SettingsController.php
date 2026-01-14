<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        // Restrict settings to super-admins (also allow users with manage settings permission)
        $this->middleware('role:super-admin')->only(['index','create','store','edit','update','destroy','show']);
        $this->middleware('permission:manage settings')->only(['index','create','store','edit','update','destroy','show']);
    }
    /**
     * Display a listing of the settings.
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

        $settings = Setting::paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('dashboard.admin.settings.partials.items-table', compact('settings'))->render(),
                'pagination' => $settings->links('vendor.pagination.bootstrap-5')->toHtml(),
            ]);
        }

        return view('dashboard.admin.settings.index', compact('settings', 'admin'));
    }

    /**
     * Show the form for creating a new setting.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.settings.create', compact('admin'));
    }

    /**
     * Store a newly created setting in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $data = [
            'key' => $request->key,
            'value' => $request->value,
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
        ];

        Setting::create($data);

        return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified setting.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.settings.show', compact('setting', 'admin'));
    }

    /**
     * Show the form for editing the specified setting.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('dashboard.admin.settings.edit', compact('setting', 'admin'));
    }

    /**
     * Update the specified setting in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $data = [
            'key' => $request->key,
            'value' => $request->value,
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
        ];

        $setting->update($data);

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
    }
}