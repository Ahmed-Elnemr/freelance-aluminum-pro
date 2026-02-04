<?php

namespace App\Service;

use App\Helpers\Response\ApiResponder;
use App\Models\User;
use App\Notifications\ResetPasswordOtpNotification;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function forgotPassword(string $email)
    {
        $user = $this->authRepository->findClientByEmail($email);

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        if ($user->is_active == 0) {
            return ApiResponder::failed(__('auth.Your account is blocked'), 403);
        }

        // $otp = rand(100000, 999999);
        $otp = 1111; // Fixed for testing as requested

        // Invalidate old OTPs
        $this->authRepository->deleteOtps($user->id);

        // Create new OTP
        $this->authRepository->createOtp($user->id, (string)$otp);

        // Send Email (Sync for testing)
        try {
            // Using notifyNow to send immediately (synchronously) instead of queuing
            $user->notifyNow(new ResetPasswordOtpNotification($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
            return ApiResponder::failed(__('auth.failed_to_send_email'), 500);
        }

        return ApiResponder::success(__('auth.otp_sent_successfully'));
    }

    public function verifyOtp(string $email, string $otp)
    {
        $user = $this->authRepository->findClientByEmail($email);

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        $otpRecord = $this->authRepository->checkOtp($user->id, $otp);

        if (!$otpRecord) {
            return ApiResponder::failed(__('auth.invalid_or_expired_otp'), 400);
        }

        return ApiResponder::success(__('auth.otp_verified_successfully'));
    }

    public function resetPassword(string $email, string $otp, string $password)
    {
        $user = $this->authRepository->findClientByEmail($email);

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        $otpRecord = $this->authRepository->checkOtp($user->id, $otp);

        if (!$otpRecord) {
            return ApiResponder::failed(__('auth.invalid_or_expired_otp'), 400);
        }

        // Update Password
        $this->authRepository->updatePassword($user, $password);

        // Mark OTP as used
        $this->authRepository->markOtpAsUsed($otpRecord->id);

        return ApiResponder::success(__('auth.password_reset_successfully'));
    }

    public function resendOtp(string $email)
    {
        $user = $this->authRepository->findClientByEmail($email);

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        // $otp = rand(100000, 999999);
        $otp = 1111; // Fixed for testing

        $this->authRepository->deleteOtps($user->id);
        $this->authRepository->createOtp($user->id, (string)$otp);

        try {
            // Using notifyNow to send immediately (synchronously)
            $user->notifyNow(new ResetPasswordOtpNotification($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
            return ApiResponder::failed(__('auth.failed_to_send_email'), 500);
        }

        return ApiResponder::success(__('auth.otp_resent_successfully'));
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return ApiResponder::failed(__('auth.current_password_incorrect'), 422);
        }

        $this->authRepository->updatePassword($user, $newPassword);

        return ApiResponder::success(__('auth.password_changed_successfully'));
    }

    public function sendVerificationOtp(User $user)
    {
        // $otp = rand(100000, 999999);
        $otp = 1111; // Fixed for testing

        $this->authRepository->deleteOtps($user->id);
        $this->authRepository->createOtp($user->id, (string)$otp);

        try {
            $user->notifyNow(new ResetPasswordOtpNotification($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
            // We suppress error here to not break the profile update response, 
            // or we could throw specific exception.
        }
    }
}
