<?php

namespace App\Http\Controllers\Api\Frequency;

use App\Http\Controllers\Controller;
use App\Http\Resources\FrequencyResource;
use App\Models\Frequency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class FrequencyController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of frequencies by service category ID with localization.
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

        // Fetch frequencies by service_category_id
        $frequencies = Frequency::where('service_category_id', $serviceCategoryId)->get();

        if ($frequencies->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.frequencies_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.frequencies_listed'),
            FrequencyResource::collection($frequencies)
        );
    }
}