<?php

namespace App\Http\Controllers\Api\NumberOfCleaners;

use App\Http\Controllers\Controller;
use App\Http\Resources\NumberOfCleanersResource;
use App\Models\NumberOfCleaners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class NumberOfCleanersController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of number of cleaners by service category ID with localization.
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

        // Fetch number of cleaners by service_category_id
        $numberOfCleaners = NumberOfCleaners::where('service_category_id', $serviceCategoryId)->get();

        if ($numberOfCleaners->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.number_of_cleaners_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.number_of_cleaners_listed'),
            NumberOfCleanersResource::collection($numberOfCleaners)
        );
    }
}