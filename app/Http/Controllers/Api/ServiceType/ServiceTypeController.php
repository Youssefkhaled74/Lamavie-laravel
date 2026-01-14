<?php

namespace App\Http\Controllers\Api\ServiceType;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class ServiceTypeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of service types by service ID with localization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $serviceId)
    {
        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Fetch service types by service_id
        $serviceTypes = ServiceType::where('service_id', $serviceId)->get();

        if ($serviceTypes->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.service_types_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.service_types_listed'),
            ServiceTypeResource::collection($serviceTypes)
        );
    }
}