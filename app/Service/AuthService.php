<?php

namespace App\Service;

use App\Helpers\Response\ApiResponder;
use App\Models\User;
use App\Notifications\ResetPasswordOtpNotification;
use App\Notifications\EmailVerificationOtpNotification;
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

        $otp = rand(1000, 9999);

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
        $user = User::where('email', $email)->orWhere('new_email', $email)->isClient()->first();

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        $otpRecord = $this->authRepository->checkOtp($user->id, $otp);

        if (!$otpRecord) {
            return ApiResponder::failed(__('auth.invalid_or_expired_otp'), 400);
        }

        // Handle Email Change Verification
        if ($user->new_email === $email) {
            $user->update([
                'email' => $user->new_email,
                'new_email' => null,
                'email_verified_at' => now(),
                'is_active' => 1
            ]);
            $this->authRepository->markOtpAsUsed($otpRecord->id);
            return ApiResponder::success(__('auth.email_verified_successfully'), [
                'need_token' => false,
                'user' => new \App\Http\Resources\user\UserResource($user)
            ]);
        }

        // Handle Initial Registration Verification
        if ($user->email_verified_at === null) {
            $user->update([
                'email_verified_at' => now(),
                'is_active' => 1
            ]);
            $this->authRepository->markOtpAsUsed($otpRecord->id);
            
            // Create access token for newly verified user
            $access_token = $user->createToken('authToken')->plainTextToken;
            $user->access_token = $access_token;
            
            return ApiResponder::success(__('auth.account_activated_successfully'), [
                'need_token' => false,
                'user' => new \App\Http\Resources\user\UserResource($user)
            ]);
        }

        // For password reset verification (email already verified)
        $this->authRepository->markOtpAsUsed($otpRecord->id);
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
        $user = User::where('email', $email)->orWhere('new_email', $email)->isClient()->first();

        if (!$user) {
            return ApiResponder::failed(__('auth.user_not_found'), 404);
        }

        $otp = rand(1000, 9999);

        $this->authRepository->deleteOtps($user->id);
        $this->authRepository->createOtp($user->id, (string)$otp);

        try {
            if ($user->new_email === $email) {
                // It's an email verification resend (email change)
                \Illuminate\Support\Facades\Notification::route('mail', $email)
                    ->notifyNow(new EmailVerificationOtpNotification($otp));
            } elseif ($user->email_verified_at === null) {
                // It's a registration verification resend
                $user->notifyNow(new EmailVerificationOtpNotification($otp));
            } else {
                // It's a password reset resend
                $user->notifyNow(new ResetPasswordOtpNotification($otp));
            }
        } catch (\Exception $e) {
            Log::error("Failed to resend OTP email: " . $e->getMessage());
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
        $otp = rand(1000, 9999);

        $this->authRepository->deleteOtps($user->id);
        $this->authRepository->createOtp($user->id, (string)$otp);

        $targetEmail = $user->new_email ?? $user->email;

        try {
            // Using Notification facade to route specifically to the target email
            \Illuminate\Support\Facades\Notification::route('mail', $targetEmail)
                ->notifyNow(new EmailVerificationOtpNotification($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
        }
    }
}
