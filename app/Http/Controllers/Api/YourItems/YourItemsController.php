<?php

namespace App\Http\Controllers\Api\YourItems;

use App\Http\Controllers\Controller;
use App\Http\Resources\YourItemsResource;
use App\Models\YourItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class YourItemsController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of items by service category ID with localization.
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

        // Fetch items by service_category_id
        $yourItems = YourItems::where('service_category_id', $serviceCategoryId)->get();

        if ($yourItems->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.your_items_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.your_items_listed'),
            YourItemsResource::collection($yourItems)
        );
    }
}