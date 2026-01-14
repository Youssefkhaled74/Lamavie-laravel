<?php

namespace App\Http\Controllers\Api\CarType;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarTypeResource;
use App\Models\CarType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class CarTypeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of car types by service category ID with localization.
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

        // Fetch car types by service_category_id
        $carTypes = CarType::where('service_category_id', $serviceCategoryId)->get();

        if ($carTypes->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.car_types_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.car_types_listed'),
            CarTypeResource::collection($carTypes)
        );
    }
}
