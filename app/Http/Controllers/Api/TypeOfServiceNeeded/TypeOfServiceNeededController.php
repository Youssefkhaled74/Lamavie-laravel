<?php

namespace App\Http\Controllers\Api\TypeOfServiceNeeded;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeOfServiceNeededResource;
use App\Models\TypeOfServiceNeeded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class TypeOfServiceNeededController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of type of service needed by service category ID with localization.
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

        // Fetch type of service needed by service_category_id
        $typeOfServiceNeeded = TypeOfServiceNeeded::where('service_category_id', $serviceCategoryId)->get();

        if ($typeOfServiceNeeded->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.type_of_service_needed_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.type_of_service_needed_listed'),
            TypeOfServiceNeededResource::collection($typeOfServiceNeeded)
        );
    }
}