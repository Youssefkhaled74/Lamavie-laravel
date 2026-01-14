<?php

namespace App\Http\Controllers\Api\TypeOfStain;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeOfStainResource;
use App\Models\TypeOfStain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class TypeOfStainController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of type of stains by service category ID with localization.
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

        // Fetch type of stains by service_category_id
        $typeOfStains = TypeOfStain::where('service_category_id', $serviceCategoryId)->get();

        if ($typeOfStains->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.type_of_stains_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.type_of_stains_listed'),
            TypeOfStainResource::collection($typeOfStains)
        );
    }
}