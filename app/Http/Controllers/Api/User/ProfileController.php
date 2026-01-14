<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\UserPoints;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ApiResponse;
    protected $userService;

    public function __construct(UserProfileService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Update user name and profile photo
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'area_id' => 'sometimes|nullable|exists:areas,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $user = Auth::user();
            $data = $request->only('name', 'area_id');

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo if exists
                if ($user->profile_photo) {
                    $this->userService->deleteOldImage($user->profile_photo);
                }

                $photoPath = $this->userService->uploadImage($request->file('profile_photo'));
                $data['profile_photo'] = $photoPath;
            }

            $updatedUser = $this->userService->updateProfile($user, $data);

            return $this->successResponse(200, trans('messages.profile_updated_successfully'), [
                'user' => new UserResource($updatedUser)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.profile_update_failed'), $e->getMessage());
        }
    }

    public function updatePhone(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|max:20|unique:users,phone,' . Auth::id(),
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $user = Auth::user();
            $updatedUser = $this->userService->updatePhone($user, $request->phone);

            return $this->successResponse(200, trans('messages.phone_updated_successfully'), [
                'user' => new UserResource($updatedUser)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.phone_update_failed'), $e->getMessage());
        }
    }

    /**
     * Get user profile
     */
    public function getProfile(): JsonResponse
    {
        try {
            $user = Auth::user();

            return $this->successResponse(200, trans('messages.profile_retrieved_successfully'), [
                'user' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.profile_retrieval_failed'), $e->getMessage());
        }
    }
    /**
     * Verify new phone number with OTP
     */
    public function verifyNewPhone(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp_code' => 'required|string|size:4',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $user = Auth::user();

            if (!$user->new_phone) {
                return $this->errorResponse(400, trans('messages.no_pending_phone'));
            }

            $isVerified = $this->userService->verifyNewPhone($user, $request->otp_code);

            if (!$isVerified) {
                return $this->errorResponse(400, trans('messages.invalid_otp'));
            }

            return $this->successResponse(200, trans('messages.phone_verified_successfully'), [
                'user' => new UserResource($user->fresh())
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.phone_verification_failed'), $e->getMessage());
        }
    }
    public function getPoints(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get points transaction history without booking relationship
            $pointsHistory = UserPoints::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'booking_id', 'points', 'created_at']);

            // Format the points history
            $formattedHistory = $pointsHistory->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'points' => $transaction->points,
                    'type' => $transaction->points > 0 ? 'earned' : 'spent',
                    'booking_id' => $transaction->booking_id,
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'description' => $transaction->points > 0
                        ? 'Points earned from booking'
                        : 'Points spent on booking'
                ];
            });

            return $this->successResponse(200, trans('messages.points_retrieved_successfully'), [
                'current_points' => $user->points,
                'points_history' => $formattedHistory
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.points_retrieval_failed'), $e->getMessage());
        }
    }

    /**
     * Get all bookings for the authenticated user
     */
    public function getBookings(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get status from query params (can be array or single value)
            $status = $request->input('status');
            $perPage = (int) $request->input('per_page', 10);

            $query = $user->bookings()->with(['service', 'paymentMethod'])->orderByDesc('created_at');

            if ($status) {
                if (is_array($status)) {
                    $query->whereIn('status', $status);
                } else {
                    $query->where('status', $status);
                }
            }

            $bookings = $query->paginate($perPage);

            // Return paginated bookings using BookingResource
            return $this->successResponse(200, trans('messages.bookings_retrieved_successfully'), [
                'bookings' => \App\Http\Resources\BookingResource::collection($bookings),
                'pagination' => [
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.bookings_retrieval_failed'), $e->getMessage());
        }
    }
}
