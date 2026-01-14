<?php

namespace App\Http\Controllers\Api\Area;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class AreaController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of areas with localization and optional pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Check if pagination is requested
        $perPage = $request->query('per_page', null);
        
        if ($perPage) {
            // Return paginated results
            $areas = Area::paginate((int) $perPage);
            
            return $this->successResponse(
                200,
                trans('messages.areas_listed'),
                [
                    'data' => AreaResource::collection($areas->items()),
                    'pagination' => [
                        'current_page' => $areas->currentPage(),
                        'last_page' => $areas->lastPage(),
                        'per_page' => $areas->perPage(),
                        'total' => $areas->total(),
                        'from' => $areas->firstItem(),
                        'to' => $areas->lastItem(),
                    ]
                ]
            );
        }

        // Return all areas without pagination
        $areas = Area::all();

        if ($areas->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.areas_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.areas_listed'),
            AreaResource::collection($areas)
        );
    }
}
