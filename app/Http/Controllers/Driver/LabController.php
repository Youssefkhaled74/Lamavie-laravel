<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lab;

class LabController extends Controller
{
    public function show(Lab $lab)
    {
        $driver = auth()->guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        return view('driver.labs.show', compact('lab', 'driver'));
    }
}
