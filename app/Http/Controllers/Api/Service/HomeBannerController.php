<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;

class HomeBannerController extends Controller
{
    public function index()
    {
        $banners = HomeBanner::where('status', true)
            ->orderBy('sort_order')
            ->latest()
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'image_url' => url('storage/' . ltrim($b->image, '/')),
                    'sort_order' => $b->sort_order,
                ];
            });
        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }
}


