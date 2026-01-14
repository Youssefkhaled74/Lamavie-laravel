<?php

namespace App\Http\Controllers\Api\ServiceCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of service categories by service ID with localization.
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

        // Fetch service categories by service_id
        $serviceCategories = ServiceCategory::where('service_id', $serviceId)->get();

        if ($serviceCategories->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.service_categories_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.service_categories_listed'),
            ServiceCategoryResource::collection($serviceCategories)
        );
    }
}