<?php

namespace App\Http\Controllers\Api\SizeOfStain;

use App\Http\Controllers\Controller;
use App\Http\Resources\SizeOfStainResource;
use App\Models\SizeOfStain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class SizeOfStainController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of size of stains by service category ID with localization.
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

        // Fetch size of stains by service_category_id
        $sizeOfStains = SizeOfStain::where('service_category_id', $serviceCategoryId)->get();

        if ($sizeOfStains->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.size_of_stains_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.size_of_stains_listed'),
            SizeOfStainResource::collection($sizeOfStains)
        );
    }
}