<?php

namespace App\Http\Controllers\Api\CarsAdditionalService;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarsAdditionalServiceResource;
use App\Models\CarsAdditionalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class CarsAdditionalServiceController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of cars additional services by service category ID with localization.
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

        // Fetch cars additional services by service_category_id
        $carsAdditionalServices = CarsAdditionalService::where('service_category_id', $serviceCategoryId)->get();

        if ($carsAdditionalServices->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.cars_additional_services_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.cars_additional_services_listed'),
            CarsAdditionalServiceResource::collection($carsAdditionalServices)
        );
    }
}