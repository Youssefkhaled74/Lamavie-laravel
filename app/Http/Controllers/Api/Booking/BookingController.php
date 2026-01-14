<?php

namespace App\Http\Controllers\Api\Booking;

use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Setting;
use App\Models\UserPoints;
use App\Models\ServiceType;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Validator;
use App\Notifications\CanceledBookingNotification;

class BookingController extends Controller
{
    use ApiResponse;

    /**
     * Create a new booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        Log::info('📥 BookingController::create - Request received', [
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'payment_method_id' => $request->payment_method_id,
            'ip' => $request->ip(),
        ]);

        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Check if user is authenticated
        if (!auth()->check()) {
            Log::warning('❌ BookingController::create - Unauthenticated', [
                'ip' => $request->ip(),
            ]);
            return $this->errorResponse(
                401,
                trans('messages.unauthenticated')
            );
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'service_type_id' => 'nullable|exists:service_types,id',
            'payload_data' => 'required|array',
            'payment_method_id' => 'required|exists:payments_method,id',
            'total' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::warning('❌ BookingController::create - Validation failed', [
                'user_id' => auth()->id(),
                'errors' => $validator->errors()->toArray(),
            ]);
            return $this->errorResponse(
                422,
                trans('messages.validation_failed'),
                $validator->errors()
            );
        }

        Log::info('✅ BookingController::create - Validation passed', [
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
        ]);

        // Verify relationships
        $service = Service::find($request->service_id);
        $serviceCategory = ServiceCategory::where('id', $request->service_category_id)
            ->where('service_id', $request->service_id)
            ->first();
        $serviceType = ServiceType::where('id', $request->service_type_id)
            ->where('service_id', $request->service_id)
            ->first();
        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        Log::info('🔍 BookingController::create - Relationships loaded', [
            'service_exists' => $service !== null,
            'category_exists' => $serviceCategory !== null,
            'type_exists' => $serviceType !== null,
            'payment_method_exists' => $paymentMethod !== null,
        ]);

        // Ensure the selected payment method exists and is active
        if (!$paymentMethod || !$paymentMethod->status) {
            return $this->errorResponse(
                422,
                'The selected payment method is not available.'
            );
        }

        // Payment method cycle logic
        $paymentMethodId = (int) $request->payment_method_id;
        $photoRequired = in_array($paymentMethodId, [1, 3]);
        $pointsRequired = ($paymentMethodId === 5);
        $addTwenty = ($paymentMethodId === 2);

        // Validate photo if required
        if ($photoRequired) {
            if (!$request->hasFile('photo')) {
                return $this->errorResponse(422, 'Photo is required for this payment method.');
            }
            // Optionally, validate file type/size here
        }

        // Validate points if required
        if ($pointsRequired) {
            $user = Auth::user();
            $pointsValue = (float) \App\Models\Setting::getValue('points_value', 1);
            $pointsCount = (int) $request->input('points');
            $total = 100.00;
            if ($pointsCount <= 0) {
                return $this->errorResponse(422, 'Points count is required and must be greater than 0.');
            }
            $total = $pointsCount * $pointsValue;
            if ($user->points < $total) {
                return $this->errorResponse(422, 'You do not have enough points. Required: ' . $total . ', Your points: ' . $user->points);
            }
        } else {
            $total = $request->input('total', 100.00);
        }
        if ($addTwenty) {
            // $total += 20;
            $total = $request->input('total');
        }

        // Generate unique order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        while (Booking::where('order_number', $orderNumber)->exists()) {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        }

        // Create the booking
        Log::info('💾 BookingController::create - Creating booking in database', [
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'order_number' => $orderNumber,
            'total' => $total,
            'payment_method_id' => $paymentMethodId,
            'status' => 'pending',
        ]);
        
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'service_category_id' => $request->service_category_id,
            'service_type_id' => $request->service_type_id,
            'payload_data' => $request->payload_data,
            'status' => 'pending',
            'order_number' => $orderNumber,
            'total' => $total,
            'payment_method_id' => $paymentMethodId,
            'is_unseen' => true,
        ]);
        
        Log::info('✅ BookingController::create - Booking created in database', [
            'booking_id' => $booking->id,
            'order_number' => $booking->order_number,
            'user_id' => $booking->user_id,
            'status' => $booking->status,
        ]);
        
        Log::info('🔔 BookingController::create - Observer should trigger now', [
            'booking_id' => $booking->id,
            'observer_registered' => class_exists('App\Observers\BookingObserver'),
        ]);

        // Handle photo upload if required
        if ($photoRequired && $request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->store('booking_photos', 'public');
            $booking->payload_data = array_merge($booking->payload_data ?? [], ['photo' => $photoPath]);
            $booking->save();
        }

        $user = Auth::user();
        $pointsEarned = 0;

        // Handle points deduction if required (points payment)
        if ($pointsRequired) {
            $user->points -= $total;
            $user->save();
        } else {
            // Add points for non-points payment methods
            $pointsPerOrder = (int) \App\Models\Setting::getValue('points_per_order', 10);
            $user->increment('points', $pointsPerOrder);
            $pointsEarned = $pointsPerOrder;

            // Store points history
            UserPoints::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'points' => $pointsEarned
            ]);
        }

        // Load relationships for the resource
        $booking->load('service', 'serviceCategory', 'serviceType', 'paymentMethod');

        return $this->successResponse(
            201,
            trans('messages.booking_created'),
            [
                'booking' => new BookingResource($booking),
                'user_points' => $user->points,
                'points_earned' => $pointsEarned
            ]
        );
    }

    public function cancel(Request $request, $id)
    {
        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->errorResponse(
                401,
                trans('messages.unauthenticated')
            );
        }

        // Find the booking
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->errorResponse(
                404,
                trans('messages.booking_not_found')
            );
        }

        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return $this->errorResponse(
                403,
                trans('messages.unauthorized')
            );
        }

        // Check if the booking status is pending
        if ($booking->status !== 'pending') {
            return $this->errorResponse(
                403,
                trans('messages.contact_support_to_cancel')
            );
        }

        // Update the booking status to canceled
        $booking->status = 'canceled';
        $booking->save();

        // Notify the user about the cancellation
        $user = auth()->user();
        $user->notify(new CanceledBookingNotification($booking));

        // Load relationships for the resource
        $booking->load('service', 'serviceCategory', 'serviceType', 'paymentMethod');

        return $this->successResponse(
            200,
            trans('messages.booking_canceled'),
            [
                'booking' => new BookingResource($booking)
            ]
        );
    }
}
