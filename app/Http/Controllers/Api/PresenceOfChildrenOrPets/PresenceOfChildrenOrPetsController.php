<?php

namespace App\Http\Controllers\Api\PresenceOfChildrenOrPets;

use App\Http\Controllers\Controller;
use App\Http\Resources\PresenceOfChildrenOrPetsResource;
use App\Models\PresenceOfChildrenOrPets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class PresenceOfChildrenOrPetsController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of presence of children or pets by service category ID with localization.
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

        // Fetch presence of children or pets by service_category_id
        $presenceOfChildrenOrPets = PresenceOfChildrenOrPets::where('service_category_id', $serviceCategoryId)->get();

        if ($presenceOfChildrenOrPets->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.presence_of_children_or_pets_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.presence_of_children_or_pets_listed'),
            PresenceOfChildrenOrPetsResource::collection($presenceOfChildrenOrPets)
        );
    }
}