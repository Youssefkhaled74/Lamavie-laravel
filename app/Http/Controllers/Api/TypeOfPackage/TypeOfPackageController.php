<?php

namespace App\Http\Controllers\Api\TypeOfPackage;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeOfPackageResource;
use App\Models\TypeOfPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class TypeOfPackageController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of package types by service category ID with localization.
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

        // Fetch package types by service_category_id
        $typeOfPackages = TypeOfPackage::where('service_category_id', $serviceCategoryId)->get();

        if ($typeOfPackages->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.type_of_package_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.type_of_package_listed'),
            TypeOfPackageResource::collection($typeOfPackages)
        );
    }
}
