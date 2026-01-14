<?php

namespace App\Http\Controllers\Api\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    protected $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'area_id' => 'nullable|exists:areas,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $data = $validator->validated();

            // Handle image upload if present
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $this->userAuthService->uploadImage($request, 'profile_photo', 'profile_images');
            }

            $user = $this->userAuthService->register($data);

            return $this->successResponse(201, trans('messages.otp_sent_successfully'), [
                'user' => new UserResource($user),
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.registration_failed'), $e->getMessage());
        }
    }
    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|max:20',
                'otp_code' => 'required|string|size:4',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $user = $this->userAuthService->verifyOtp($request->phone, $request->otp_code);

            if (!$user) {
                return $this->errorResponse(401, trans('messages.otp_verification_failed'));
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(200, trans('messages.otp_verification_successful'), [
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.otp_verification_failed'), $e->getMessage());
        }
    }
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $token = $this->userAuthService->login($validator->validated());

            if (!$token) {
                return $this->errorResponse(401, trans('messages.invalid_credentials'));
            }

            // Use your custom 'user' guard to get the authenticated user
            $user = Auth::guard('user')->user();

            return $this->successResponse(200, trans('messages.login_successful'), [
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.login_failed'), $e->getMessage());
        }
    }
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->userAuthService->logout($request->user());
            return $this->successResponse(200, trans('messages.logout_successful'));
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.logout_failed'), $e->getMessage());
        }
    }
    public function forgetPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:11|exists:users,phone',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $this->userAuthService->forgetPassword($validator->validated());

            return $this->successResponse(200, trans('messages.otp_sent_successfully'));
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.forget_password_failed'), $e->getMessage());
        }
    }
    /**
     * Verify the OTP sent for password reset without setting a new password yet.
     */
    public function verifyPasswordResetOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'otp_code' => 'required|string|size:4',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $isValid = $this->userAuthService->verifyPasswordResetOtp($validator->validated());

            if (!$isValid) {
                return $this->errorResponse(422, trans('messages.validation_failed'), [
                    'phone' => [trans('messages.otp_verification_failed')],
                    'otp_code' => [trans('messages.otp_verification_failed')],
                    'verified' => false,
                ]);
            }

            return $this->successResponse(200, trans('messages.otp_verification_successful'), [
                'verified' => true,
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.otp_verification_failed'), $e->getMessage());
        }
    }

    /**
     * Set new password after OTP has been verified.
     */
    public function setNewPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $user = $this->userAuthService->setNewPassword($validator->validated());

            if (!$user) {
                return $this->errorResponse(401, trans('messages.reset_password_failed'));
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(200, trans('messages.password_reset_successfully'), [
                'user' => new \App\Http\Resources\UserResource($user),
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.reset_password_failed'), $e->getMessage());
        }
    }

    /**
     * Resend OTP for password reset.
     */
    public function resendOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
            }

            $this->userAuthService->resendOtp($validator->validated());

            return $this->successResponse(200, trans('messages.otp_sent_successfully'));
        } catch (ValidationException $e) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('messages.forget_password_failed'), $e->getMessage());
        }
    }
    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
        }

        $user = Auth::user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return $this->successResponse(200, trans('messages.fcm_token_updated'), new UserResource($user));
    }
    public function updateLanguage(Request $request)
    {
        $request->validate(['language' => 'required|in:en,ar']);
        $user = Auth::user();
        $user->language = $request->language;
        $user->save();
        return response()->json(['message' => 'Language updated']);
    }
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
        }

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->errorResponse(422, trans('messages.wrong_old_password'));
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return $this->successResponse(200, trans('messages.password_updated'));
    }
}
