<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['profile_photo'])) {
            // store into `profile_photo` only (DB column). Avoid assigning non-existent `photo`.
            $user->profile_photo = $data['profile_photo'];
        }

        if (isset($data['area_id'])) {
            $user->area_id = $data['area_id'];
        }

        $user->save();
        return $user->fresh();
    }

    public function updateEmail(User $user, string $email): User
    {
        $user->email = $email;
        $user->email_verified_at = null; // Reset verification if email changed
        $user->save();

        // Here you would typically send a verification email
        return $user->fresh();
    }

    public function uploadImage($image, $directory = 'profile_photos'): string
    {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $path = $image->storeAs($directory, $imageName, 'public');

        return $path;
    }

    public function deleteOldImage($imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
    protected function generateNumericOtp(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
    public function updatePhone(User $user, string $phone): User
    {
        // Store the new phone in temporary field
        $user->new_phone = $phone;
        $user->otp_code = $this->generateNumericOtp();
        $user->save();

        // Here you would typically send OTP to the new phone number
        $this->sendOtpToPhone($user->new_phone, $user->otp_code);

        return $user->fresh();
    }

    public function verifyNewPhone(User $user, string $otpCode): bool
    {
        if ($user->otp_code === $otpCode && $user->new_phone) {
            // Update the actual phone and clear temporary fields
            $user->phone = $user->new_phone;
            $user->phone_verified_at = now();
            $user->new_phone = null;
            $user->new_phone_verified_at = now();
            $user->otp_code = null;
            $user->save();

            return true;
        }

        return false;
    }

    private function sendOtpToPhone(string $phone, string $otp): void
    {
        // Implement your SMS service here
        // Example: Twilio, Nexmo, etc.
        // This is just a placeholder
        logger("Sending OTP {$otp} to phone: {$phone}");
    }
}
