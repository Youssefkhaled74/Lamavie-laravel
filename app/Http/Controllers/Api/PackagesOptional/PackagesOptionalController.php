<?php

namespace App\Http\Controllers\Api\PackagesOptional;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackagesOptionalResource;
use App\Models\PackagesOptional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class PackagesOptionalController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of packages optional by service category ID with localization.
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

        // Fetch packages optional by service_category_id
        $packagesOptional = PackagesOptional::where('service_category_id', $serviceCategoryId)->get();

        if ($packagesOptional->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.packages_optional_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.packages_optional_listed'),
            PackagesOptionalResource::collection($packagesOptional)
        );
    }
}