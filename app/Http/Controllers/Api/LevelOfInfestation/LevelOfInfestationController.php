<?php

namespace App\Http\Controllers\Api\LevelOfInfestation;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelOfInfestationResource;
use App\Models\LevelOfInfestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class LevelOfInfestationController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of level of infestation by service category ID with localization.
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

        // Fetch level of infestation by service_category_id
        $levelOfInfestations = LevelOfInfestation::where('service_category_id', $serviceCategoryId)->get();

        if ($levelOfInfestations->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.level_of_infestation_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.level_of_infestation_listed'),
            LevelOfInfestationResource::collection($levelOfInfestations)
        );
    }
}