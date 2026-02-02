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

class AuthController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    //todo:login
    public function login(UserLoginRequest $request)
    {
        $validatedData = $request->validated();
        
        // Determine if login is email or mobile
        $loginField = filter_var($validatedData['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        
        // Find user by email or mobile
        $user = User::where($loginField, $validatedData['login'])
            ->where('is_active', 1)
            ->first();
        
        if (!$user) {
            return ApiResponder::failed(__('auth.invalid_credentials'), 401);
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
        
        return ApiResponder::success(__('auth.Logged in successfully'), [
            'user' => UserResource::make($user),
            'access_token' => $access_token,
        ]);
    }

    //todo:register
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        
        // Create new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'password' => Hash::make($validatedData['password']),
            'status' => 1,
            'is_active' => 1,
        ]);
        
        // Add device
        $this->userService->addDevice($user);
        
        // Send welcome notification
        $user->notify(new WelcomeNotification());
        
        // Create access token
        $access_token = $user->createToken('authToken')->plainTextToken;
        
        return ApiResponder::created([
            'user' => UserResource::make($user),
            'access_token' => $access_token,
        ], __('auth.Registration successful'), 201);
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
        return ApiResponder::loaded(['user' => UserResource::make(auth('sanctum')->user())]);
    }
////
    //todo: user editeProfile
    public function editeProfile(UserEditeProfile $request)
    {
        $user = auth('sanctum')->user();
        $validatedData = $request->validated();

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

        // Update user data
        $user->update($validatedData);
        
        return ApiResponder::success(__('auth.Profile updated successfully'), [
            'user' => UserResource::make($user->fresh())
        ]);
    }

    // ###
    public function deleteAccount(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->delete();
        return ApiResponder::deleted(200, __('Your account has been successfully deleted'));
    }
}
