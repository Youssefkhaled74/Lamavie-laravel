<?php

namespace App\Http\Controllers\Api\CarpetMaterial;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarpetMaterialResource;
use App\Models\CarpetMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class CarpetMaterialController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of carpet materials by service category ID with localization.
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

        // Fetch carpet materials by service_category_id
        $carpetMaterials = CarpetMaterial::where('service_category_id', $serviceCategoryId)->get();

        if ($carpetMaterials->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.carpet_materials_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.carpet_materials_listed'),
            CarpetMaterialResource::collection($carpetMaterials)
        );
    }
}