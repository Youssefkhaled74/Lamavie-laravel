<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:users.view')->only(['index','show','bookings']);
        $this->middleware('permission:users.export')->only(['export']);
        $this->middleware('permission:manage users')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('area')->orderBy('created_at', 'desc');

        // Search by name or phone
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        // Filter by area
        $areas = \App\Models\Area::orderBy('name')->get();
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $users = $query->paginate(25)->withQueryString();

        return view('dashboard.admin.users.index', compact('users', 'areas'));
    }

    /**
     * Show bookings for a single user.
     */
    public function bookings(User $user)
    {
        $bookings = $user->bookings()->with(['service','driver','lab'])->orderBy('created_at','desc')->get();
        return view('dashboard.admin.users.bookings', compact('user','bookings'));
    }

    /**
     * Export users to Excel
     */
    public function export(Request $request)
    {
        $filename = 'users-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new UsersExport($request), $filename);
    }

    /**
     * Show user profile
     */
    public function show(User $user)
    {
        return view('dashboard.admin.users.show', compact('user'));
    }

    /**
     * Edit user profile (admin)
     */
    public function edit(User $user)
    {
        return view('dashboard.admin.users.edit', compact('user'));
    }

    /**
     * Update user profile (admin)
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|max:2048'
        ]);

        try {
            $user->name = $data['name'];
            $user->phone = $data['phone'] ?? $user->phone;

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                // store files in `profile_photos` directory on the public disk
                $path = $file->store('profile_photos', 'public');
                // delete old photo if exists
                // delete any existing image stored in either `photo` or `profile_photo`
                $old = $user->photo ?? $user->profile_photo ?? null;
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }

                // store into `profile_photo` (DB column). Do NOT set `photo` (no DB column),
                // prefer the official `profile_photo` column to avoid SQL errors.
                $user->profile_photo = $path;
            }

            $user->save();
            return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update user by admin', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update user.')->withInput();
        }
    }

    /**
     * Download or stream a small PDF containing the user's unique code.
     */
    public function codePdf(User $user)
    {
        $user->load('area');
        $viewData = compact('user');
        $html = view('dashboard.admin.users.code_pdf', $viewData)->render();

        // Prefer dompdf if available
        try {
            if (function_exists('app') && app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
                return $pdf->stream('user-code-' . ($user->unique_code ?? $user->id) . '.pdf');
            }
        } catch (\Exception $e) {
            // fallback to returning HTML
        }

        return response($html);
    }
}
