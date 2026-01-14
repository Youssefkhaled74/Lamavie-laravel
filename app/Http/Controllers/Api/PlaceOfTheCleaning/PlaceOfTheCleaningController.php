<?php

namespace App\Http\Controllers\Api\PlaceOfTheCleaning;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceOfTheCleaningResource;
use App\Models\PlaceOfTheCleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class PlaceOfTheCleaningController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of place of the cleaning records by service category ID with localization.
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

        // Fetch place of the cleaning records by service_category_id
        $placeOfTheCleanings = PlaceOfTheCleaning::where('service_category_id', $serviceCategoryId)->get();

        if ($placeOfTheCleanings->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.place_of_the_cleanings_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.place_of_the_cleanings_listed'),
            PlaceOfTheCleaningResource::collection($placeOfTheCleanings)
        );
    }
}