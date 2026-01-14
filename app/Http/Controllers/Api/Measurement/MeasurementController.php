<?php

namespace App\Http\Controllers\Api\Measurement;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class MeasurementController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of measurements by service category ID with localization.
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

        // Fetch measurements by service_category_id
        $measurements = Measurement::where('service_category_id', $serviceCategoryId)->get();

        if ($measurements->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.measurements_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.measurements_listed'),
            MeasurementResource::collection($measurements)
        );
    }
}