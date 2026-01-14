<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class SettingsController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of all settings with localization.
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

        // Fetch all settings
        $settings = Setting::all();

        if ($settings->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.settings_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.settings_listed'),
            SettingResource::collection($settings)
        );
    }
}