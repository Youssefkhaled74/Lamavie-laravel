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
        Log::info('BookingController::create - Request received', [
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
            Log::warning('BookingController::create - Unauthenticated', [
                'ip' => $request->ip(),
            ]);
            return $this->errorResponse(
                401,
                trans('messages.unauthenticated')
            );
        }

        $servicesData = $request->input('services_data');
        $hasMultiple = is_array($servicesData) && count($servicesData) > 0;

        // Validate the request
        $baseRules = [
            'payment_method_id' => 'required|exists:payments_method,id',
            'total' => 'nullable|numeric|min:0',
        ];
        if ($hasMultiple) {
            $rules = array_merge($baseRules, [
                'services_data' => 'required|array|min:1',
                'services_data.*.service_id' => 'required|exists:services,id',
                'services_data.*.service_category_id' => 'required|exists:service_categories,id',
                'services_data.*.service_type_id' => 'nullable|exists:service_types,id',
            ]);
        } else {
            $rules = array_merge($baseRules, [
                'service_id' => 'required|exists:services,id',
                'service_category_id' => 'required|exists:service_categories,id',
                'service_type_id' => 'nullable|exists:service_types,id',
                'payload_data' => 'required|array',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('BookingController::create - Validation failed', [
                'user_id' => auth()->id(),
                'errors' => $validator->errors()->toArray(),
            ]);
            return $this->errorResponse(
                422,
                trans('messages.validation_failed'),
                $validator->errors()
            );
        }

        Log::info('BookingController::create - Validation passed', [
            'user_id' => auth()->id(),
            'service_id' => $hasMultiple ? null : $request->service_id,
            'services_count' => $hasMultiple ? count($servicesData) : 1,
        ]);

        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        Log::info('BookingController::create - Relationships loaded', [
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

        $normalizePayload = function (array $entry): array {
            if (isset($entry['payload_data']) && is_array($entry['payload_data'])) {
                return $entry['payload_data'];
            }
            $payload = [];
            foreach ($entry as $key => $value) {
                if (!is_string($key)) {
                    continue;
                }
                if (strpos($key, 'payload_data[') !== 0) {
                    continue;
                }
                $path = trim(substr($key, strlen('payload_data[')), ']');
                if ($path == '') {
                    continue;
                }
                $segments = explode('][', $path);
                $ref = &$payload;
                foreach ($segments as $idx => $segment) {
                    if ($idx === count($segments) - 1) {
                        $ref[$segment] = $value;
                        break;
                    }
                    if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                        $ref[$segment] = [];
                    }
                    $ref = &$ref[$segment];
                }
                unset($ref);
            }
            return $payload;
        };

        $services = [];
        if ($hasMultiple) {
            foreach ($servicesData as $index => $entry) {
                if (!is_array($entry)) {
                    continue;
                }
                $payload = $normalizePayload($entry);
                $services[] = [
                    'index' => $index,
                    'service_id' => $entry['service_id'] ?? null,
                    'service_category_id' => $entry['service_category_id'] ?? null,
                    'service_type_id' => $entry['service_type_id'] ?? null,
                    'payload_data' => $payload,
                    'sub_total' => $entry['sub_total'] ?? null,
                ];
            }
        } else {
            $services[] = [
                'index' => 0,
                'service_id' => $request->service_id,
                'service_category_id' => $request->service_category_id,
                'service_type_id' => $request->service_type_id,
                'payload_data' => $request->payload_data ?? [],
                'sub_total' => $request->input('total'),
            ];
        }

        foreach ($services as $serviceEntry) {
            if (empty($serviceEntry['payload_data']) || !is_array($serviceEntry['payload_data'])) {
                return $this->errorResponse(
                    422,
                    trans('messages.validation_failed'),
                    ['payload_data' => ['payload_data is required for each service request.']]
                );
            }
            $service = Service::find($serviceEntry['service_id']);
            $serviceCategory = ServiceCategory::where('id', $serviceEntry['service_category_id'])
                ->where('service_id', $serviceEntry['service_id'])
                ->first();
            $serviceType = ServiceType::where('id', $serviceEntry['service_type_id'])
                ->where('service_id', $serviceEntry['service_id'])
                ->first();
            if (!$service || !$serviceCategory || ($serviceEntry['service_type_id'] && !$serviceType)) {
                return $this->errorResponse(
                    422,
                    trans('messages.validation_failed'),
                    ['service' => ['Invalid service relationships for item index ' . $serviceEntry['index'] . '.']]
                );
            }
        }

        // Validate photo if required
        if ($photoRequired) {
            if (!$request->hasFile('photo')) {
                return $this->errorResponse(422, 'Photo is required for this payment method.');
            }
            // Optionally, validate file type/size here
        }

        $user = Auth::user();
        $pointsValue = (float) Setting::getValue('points_value', 1);
        $pointsCount = (int) $request->input('points');
        $sumSubTotals = 0.0;
        foreach ($services as $serviceEntry) {
            $sumSubTotals += is_numeric($serviceEntry['sub_total']) ? (float) $serviceEntry['sub_total'] : 0.0;
        }

        $grandTotal = null;
        if ($pointsRequired) {
            if ($pointsCount <= 0 && $sumSubTotals <= 0) {
                return $this->errorResponse(422, 'Points count is required and must be greater than 0.');
            }
            $grandTotal = $pointsCount > 0 ? $pointsCount * $pointsValue : $sumSubTotals;
            if ($user->points < $grandTotal) {
                return $this->errorResponse(422, 'You do not have enough points. Required: ' . $grandTotal . ', Your points: ' . $user->points);
            }
        } else {
            $grandTotal = $request->input('total');
            if (!is_numeric($grandTotal) || $grandTotal <= 0) {
                $grandTotal = $sumSubTotals > 0 ? $sumSubTotals : 100.00;
            }
        }
        if ($addTwenty) {
            // $grandTotal += 20;
            $grandTotal = $request->input('total');
        }

        $perBookingTotals = [];
        $count = count($services);
        if ($count === 1) {
            $perBookingTotals[] = (float) $grandTotal;
        } else {
            if ($sumSubTotals > 0) {
                $allocated = 0.0;
                foreach ($services as $index => $serviceEntry) {
                    $raw = is_numeric($serviceEntry['sub_total']) ? (float) $serviceEntry['sub_total'] : 0.0;
                    if ($index === $count - 1) {
                        $perBookingTotals[$index] = round(((float) $grandTotal) - $allocated, 2);
                    } else {
                        $portion = round(($raw / $sumSubTotals) * (float) $grandTotal, 2);
                        $perBookingTotals[$index] = $portion;
                        $allocated += $portion;
                    }
                }
            } else {
                $even = round(((float) $grandTotal) / $count, 2);
                for ($i = 0; $i < $count; $i++) {
                    $perBookingTotals[$i] = $even;
                }
                $remainder = round(((float) $grandTotal) - ($even * $count), 2);
                if ($remainder != 0.0) {
                    $perBookingTotals[$count - 1] = round($perBookingTotals[$count - 1] + $remainder, 2);
                }
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            if (!$photo->isValid()) {
                return $this->errorResponse(422, 'Uploaded payment photo is invalid.');
            }
            $photoPath = $photo->store('booking_photos', 'public');
        }

        $pointsEarned = 0;
        $bookings = [];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($services as $idx => $serviceEntry) {
                // Generate unique order number
                $orderNumber = 'ORD-' . strtoupper(Str::random(8));
                while (Booking::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = 'ORD-' . strtoupper(Str::random(8));
                }

                $payload = $serviceEntry['payload_data'];
                if ($photoPath) {
                    $payload = array_merge($payload ?? [], ['photo' => $photoPath]);
                }

                // Create the booking
                Log::info('BookingController::create - Creating booking in database', [
                    'user_id' => auth()->id(),
                    'service_id' => $serviceEntry['service_id'],
                    'order_number' => $orderNumber,
                    'total' => $perBookingTotals[$idx] ?? $grandTotal,
                    'payment_method_id' => $paymentMethodId,
                    'status' => 'pending',
                ]);

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'service_id' => $serviceEntry['service_id'],
                    'service_category_id' => $serviceEntry['service_category_id'],
                    'service_type_id' => $serviceEntry['service_type_id'],
                    'payload_data' => $payload,
                    'status' => 'pending',
                    'order_number' => $orderNumber,
                    'total' => $perBookingTotals[$idx] ?? $grandTotal,
                    'payment_method_id' => $paymentMethodId,
                    'is_unseen' => true,
                ]);

                Log::info('BookingController::create - Booking created in database', [
                    'booking_id' => $booking->id,
                    'order_number' => $booking->order_number,
                    'user_id' => $booking->user_id,
                    'status' => $booking->status,
                ]);

                Log::info('BookingController::create - Observer should trigger now', [
                    'booking_id' => $booking->id,
                    'observer_registered' => class_exists('App\\Observers\\BookingObserver'),
                ]);

                $bookings[] = $booking;
            }

            // Handle points deduction if required (points payment)
            if ($pointsRequired) {
                $user->points -= $grandTotal;
                $user->save();
            } else {
                // Add points for non-points payment methods
                $pointsPerOrder = (int) Setting::getValue('points_per_order', 10);
                foreach ($bookings as $createdBooking) {
                    $user->increment('points', $pointsPerOrder);
                    $pointsEarned += $pointsPerOrder;
                    UserPoints::create([
                        'user_id' => $user->id,
                        'booking_id' => $createdBooking->id,
                        'points' => $pointsPerOrder
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('BookingController::create - Exception caught', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);
            return $this->errorResponse(500, trans('messages.booking_creation_failed'), $e->getMessage());
        }

        foreach ($bookings as $createdBooking) {
            $createdBooking->load('service', 'serviceCategory', 'serviceType', 'paymentMethod');
        }

        return $this->successResponse(
            201,
            trans('messages.booking_created'),
            [
                'booking' => count($bookings) === 1
                    ? new BookingResource($bookings[0])
                    : BookingResource::collection(collect($bookings)),
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
