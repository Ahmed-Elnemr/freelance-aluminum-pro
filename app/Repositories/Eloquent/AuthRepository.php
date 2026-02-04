<?php

namespace App\Repositories\Eloquent;

use App\Models\Otp;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    public function findClientByEmail(string $email): ?User
    {
        return User::where('email', $email)->isClient()->first();
    }

    public function deleteOtps(int $userId): void
    {
        Otp::where('user_id', $userId)->delete();
    }

    public function createOtp(int $userId, string $otp)
    {
        return Otp::create([
            'user_id' => $userId,
            'otp' => $otp,
            'expired_at' => Carbon::now()->addMinutes(15),
            'status' => false
        ]);
    }

    public function checkOtp(int $userId, string $otp)
    {
        return Otp::where('user_id', $userId)
            ->where('otp', $otp)
            ->checkOtp()
            ->first();
    }

    public function markOtpAsUsed(int $otpId): void
    {
        Otp::where('id', $otpId)->update(['status' => true]);
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
