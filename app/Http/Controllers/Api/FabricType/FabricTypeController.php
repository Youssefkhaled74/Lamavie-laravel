<?php

namespace App\Http\Controllers\Api\FabricType;

use App\Http\Controllers\Controller;
use App\Http\Resources\FabricTypeResource;
use App\Models\FabricType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class FabricTypeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of fabric types by service category ID with localization.
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

        // Fetch fabric types by service_category_id
        $fabricTypes = FabricType::where('service_category_id', $serviceCategoryId)->get();

        if ($fabricTypes->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.fabric_types_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.fabric_types_listed'),
            FabricTypeResource::collection($fabricTypes)
        );
    }
}