<?php

namespace App\Http\Controllers\Api\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponse;

class PaymentMethodController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of payment methods with localization.
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

    // Fetch only active payment methods
    $paymentMethods = PaymentMethod::where('status', true)->get();

        if ($paymentMethods->isEmpty()) {
            return $this->errorResponse(
                404,
                trans('messages.payment_methods_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.payment_methods_listed'),
            PaymentMethodResource::collection($paymentMethods)
        );
    }
}
