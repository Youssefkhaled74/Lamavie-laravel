<?php

namespace App\Http\Controllers\Api\MaintenanceOrCleaning;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceOrCleaningResource;
use App\Models\MaintenanceOrCleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class MaintenanceOrCleaningController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of maintenance or cleaning records by service category ID with localization.
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

        // Fetch maintenance or cleaning records by service_category_id
        $maintenanceOrCleanings = MaintenanceOrCleaning::where('service_category_id', $serviceCategoryId)->get();

        if ($maintenanceOrCleanings->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.maintenance_or_cleanings_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.maintenance_or_cleanings_listed'),
            MaintenanceOrCleaningResource::collection($maintenanceOrCleanings)
        );
    }
}