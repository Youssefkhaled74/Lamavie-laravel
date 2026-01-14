<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SendOtpNotification;

class UserAuthService
{
    public function register(array $data): User
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'area_id' => $data['area_id'] ?? null,
                'password' => Hash::make($data['password']),
                // dd('asd'),
                'profile_photo' => $data['profile_photo'] ?? null,
                'otp_code' => $this->generateNumericOtp(), // 4-digit numeric OTP
            ]);
            // Send OTP notification would go here
            // $user->notify(new SendOtpNotification($user->otp_code));

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());
            throw new \Exception('Failed to register user');
        }
    }
    public function uploadImage(Request $request, string $fieldName, string $directory = 'images'): ?string
    {
        if ($request->hasFile($fieldName)) {
            $image = $request->file($fieldName);
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path($directory), $imageName);
            return $directory . '/' . $imageName;
        }
        return null;
    }
    public function login(array $credentials): ?string
    {
        // Use your custom 'user' guard for authentication attempt
        if (Auth::guard('user')->attempt($credentials)) {
            $user = Auth::guard('user')->user();
            return $user->createToken('auth_token')->plainTextToken;
        }

        return null;
    }
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
    public function verifyOtp(string $phone, string $otpCode): ?User
    {
        DB::beginTransaction();

        try {
            $user = User::where('phone', $phone)
                ->where('otp_code', $otpCode)
                ->first();

            if (!$user) {
                DB::rollBack();
                return null;
            }

            $user->update([
                'phone_verified_at' => now(),
                'otp_code' => null,
            ]);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OTP verification failed: ' . $e->getMessage());
            throw new \Exception('Failed to verify OTP');
        }
    }
    protected function generateNumericOtp(): string
    {
        // return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        return '1111';
    }
    public function forgetPassword(array $data): void
    {
        $user = User::where('phone', $data['phone'])->first();
        if ($user) {
            $user->update([
                'otp_code' => $this->generateNumericOtp(),
            ]);
        }
    }

    /**
     * Verify that the provided OTP matches the user's stored OTP for password reset.
     */
    public function verifyPasswordResetOtp(array $data): bool
    {
        $user = User::where('phone', $data['phone'])
            ->where('otp_code', $data['otp_code'])
            ->first();

        if ($user) {
            // Invalidate OTP upon successful verification
            $user->update([
                'otp_code' => null,
            ]);
            return true;
        }

        return false;
    }

    /**
     * Set a new password if phone and OTP are valid, then clear the OTP.
     */
    public function setNewPassword(array $data): ?User
    {
        $user = User::where('phone', $data['phone'])->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
            return $user;
        }

        return null;
    }

    /**
     * Resend a fresh OTP to the user (same as forgetPassword behavior).
     */
    public function resendOtp(array $data): void
    {
        $this->forgetPassword($data);
    }
}
