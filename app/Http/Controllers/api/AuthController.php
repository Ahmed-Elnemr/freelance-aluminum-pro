<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\auth\RegisterRequest;
use App\Http\Requests\auth\StoreUserNameRequest;
use App\Http\Requests\auth\UserEditeProfile;
use App\Http\Requests\auth\UserLoginRequest;
use App\Http\Resources\user\UserResource;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Otp;
use Carbon\Carbon;
use App\Notifications\ResetPasswordOtpNotification;
use Illuminate\Support\Facades\DB;
use App\Service\AuthService;
use App\Http\Requests\auth\ForgotPasswordRequest;
use App\Http\Requests\auth\VerifyOtpRequest;
use App\Http\Requests\auth\ResetPasswordRequest;
use App\Http\Requests\auth\ResendOtpRequest;
use App\Http\Requests\auth\ChangePasswordRequest;

class AuthController extends Controller
{
    protected $userService;
    protected $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }



    //todo:login
    public function login(UserLoginRequest $request)
    {
        $validatedData = $request->validated();
        
        // Determine if login is email or mobile
        // $loginField = filter_var($validatedData['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        
        //  Restricting login to Email Only as per request (since mobile is not verified/unique)
        $loginField = 'email';

        // Find user by email
        $user = User::where($loginField, $validatedData['login'])
            ->first();
        
        if (!$user) {
            return ApiResponder::failed(__('auth.invalid_credentials'), 401);
        }
        
        // Check if email is verified
        if ($user->email_verified_at === null) {
            return ApiResponder::failed(__('auth.account_not_verified'), 403, [
                'need_token' => true,
                'user' => UserResource::make($user)
            ]);
        }
        
        // Check if account is active
        if ($user->is_active == 0) {
            return ApiResponder::failed(__('auth.Your account is blocked'), 403);
        }
        
        // Verify password
        if (!Hash::check($validatedData['password'], $user->password)) {
            return ApiResponder::failed(__('auth.invalid_credentials'), 401);
        }
        
        // Add device
        $this->userService->addDevice($user);
        
        // Create access token
        $access_token = $user->createToken('authToken')->plainTextToken;
        
        // Set token on user model for resource
        $user->access_token = $access_token;
        
        return ApiResponder::success(__('auth.Logged in successfully'), [
            'need_token' => false,
            'user' => UserResource::make($user),
        ]);
    }

    //todo:register
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        
        // Check if email already exists
        $existingUser = User::where('email', $validatedData['email'])->first();
        
        if ($existingUser) {
            // If user exists and is verified (email_verified_at is not null)
            if ($existingUser->email_verified_at !== null) {
                return ApiResponder::failed(__('validation.unique', ['attribute' => __('validation.attributes.email')]), 422);
            }
            
            // If user exists but NOT verified, resend OTP
            // Update user data in case they changed name/mobile/password
            $existingUser->update([
                'name' => $validatedData['name'],
                'mobile' => $validatedData['mobile'],
                'password' => Hash::make($validatedData['password']),
            ]);
            
            // Update device info
            $this->userService->addDevice($existingUser);
            
            // Send OTP
            $this->authService->sendVerificationOtp($existingUser);
            
            return ApiResponder::success(__('auth.verification_code_sent'), [
                'need_token' => true,
                'user' => UserResource::make($existingUser)
            ]);
        }
        
        // Create new user (inactive by default)
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'password' => Hash::make($validatedData['password']),
            'status' => 1,
            'is_active' => 0, // Inactive until email is verified
            'email_verified_at' => null, // Not verified yet
        ]);
        
        // Add device
        $this->userService->addDevice($user);
        
        // Send OTP for email verification
        $this->authService->sendVerificationOtp($user);
        
        return ApiResponder::success(__('auth.verification_code_sent'), [
            'need_token' => true,
            'user' => UserResource::make($user)
        ]);
    }

//todo::storeName
    public function storeName(StoreUserNameRequest $request)
    {
        $user = auth()->user();
        $user->update([
            'name' => $request->name
        ]);
        return ApiResponder::success( __('auth.Name created successfully'),[
            'user' => UserResource::make($user),
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->devices()->whereUuid($request->uuid)->delete();
        auth('sanctum')->user()->currentAccessToken()->delete();
        return ApiResponder::success(__('auth.Logged out successfully'));
    }
    ####
//
    public function profile(): \Illuminate\Http\JsonResponse
    {
        // Data is null because the user is already in the top-level 'user' key
        return ApiResponder::loaded();
    }
////
    //todo: user editeProfile
    public function editeProfile(UserEditeProfile $request)
    {
        $user = auth('sanctum')->user();
        $validatedData = $request->validated();
        $needOtp = false;

        // If email is changing
        if (isset($validatedData['email']) && $validatedData['email'] !== $user->email) {
            $user->new_email = $validatedData['email'];
            $user->save();
            
            // Remove email from validated data so it's not updated immediately
            unset($validatedData['email']);
            $needOtp = true;
        }

        // If password is being changed, verify current password
        if (!empty($validatedData['password'])) {
            if (empty($validatedData['current_password'])) {
                return ApiResponder::failed(__('auth.current_password_required'), 422);
            }
            
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return ApiResponder::failed(__('auth.current_password_incorrect'), 422);
            }
            
            $validatedData['password'] = Hash::make($validatedData['password']);
            unset($validatedData['current_password']);
            unset($validatedData['password_confirmation']);
        } else {
            unset($validatedData['password']);
            unset($validatedData['current_password']);
            unset($validatedData['password_confirmation']);
        }

        // Update user data (name, mobile, etc.)
        $user->update($validatedData);
        
        $responseData = [
            'need_token' => $needOtp
        ];

        if ($needOtp) {
            $this->authService->sendVerificationOtp($user);
        }
        
        return ApiResponder::success(__('auth.Profile updated successfully'), $responseData);
    }

    // ###
    public function deleteAccount(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->delete();
        return ApiResponder::deleted(200, __('Your account has been successfully deleted'));
    }
    //todo: forgot password & otp
    //todo: forgot password & otp
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->authService->forgotPassword($request->email);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyOtp($request->email, $request->otp);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->authService->resetPassword($request->email, $request->otp, $request->password);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->authService->resendOtp($request->email);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->authService->changePassword($request->user(), $request->current_password, $request->password);
    }
}
