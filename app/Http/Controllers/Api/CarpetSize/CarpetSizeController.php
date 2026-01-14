<?php

namespace App\Http\Controllers\Api\CarpetSize;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarpetSizeResource;
use App\Models\CarpetSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class CarpetSizeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of carpet sizes by service category ID with localization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceCategoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $serviceCategoryId)
    {
        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Fetch carpet sizes by service_category_id
        $carpetSizes = CarpetSize::where('service_category_id', $serviceCategoryId)->get();

        if ($carpetSizes->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.carpet_sizes_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.carpet_sizes_listed'),
            CarpetSizeResource::collection($carpetSizes)
        );
    }
}