<?php

namespace App\Http\Controllers\Api\EstimatedHours;

use App\Http\Controllers\Controller;
use App\Http\Resources\EstimatedHoursResource;
use App\Models\EstimatedHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class EstimatedHoursController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of estimated hours by service category ID with localization.
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

        // Fetch estimated hours by service_category_id
        $estimatedHours = EstimatedHours::where('service_category_id', $serviceCategoryId)->get();

        if ($estimatedHours->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.estimated_hours_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.estimated_hours_listed'),
            EstimatedHoursResource::collection($estimatedHours)
        );
    }
}