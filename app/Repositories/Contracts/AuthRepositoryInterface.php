<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findClientByEmail(string $email): ?User;
    public function deleteOtps(int $userId): void;
    public function createOtp(int $userId, string $otp);
    public function checkOtp(int $userId, string $otp);
    public function markOtpAsUsed(int $otpId): void;
    public function updatePassword(User $user, string $newPassword): void;
}
