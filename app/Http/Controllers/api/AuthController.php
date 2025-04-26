<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ConfirmNewMobileRequest;
use App\Http\Requests\auth\StoreUserNameRequest;
use App\Http\Requests\auth\UserEditeProfile;
use App\Http\Requests\auth\UserLoginRequest;
use App\Http\Requests\auth\UserRegisterRequest;
use App\Http\Resources\user\UserResource;
use App\Models\Otp;
use App\Models\User;
use App\Service\ConfirmationController;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $user = User::whereMobile($request->mobile)->whereIsActive(1)->first();
        if (!$user) {
            $user = User::create([
                'mobile' => $validatedData['mobile'],
                'status' => 0,
            ]);
            $code = ConfirmationController::sendCode($user);
            return ApiResponder::created([
                'mobile' => $user->mobile,
                'code' => $code,
                'is_new' => true
            ], __('auth.Verification code sent to your mobile'), 302);
        }
        if ($user->is_active == 0) {
            return ApiResponder::failed(__('auth.Your account is blocked'));
        }
        $code = ConfirmationController::sendCode($user);
        $this->userService->addDevice($user);
        return ApiResponder::created([
            'mobile' => $user->mobile,
            'code' => $code,
            'is_new' => false

        ], __('auth.Verification code sent to your mobile'), 302);
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
////    //todo: user editeProfile
    public function editeProfile(UserEditeProfile $request)
    {
        $user = auth('sanctum')->user();
        $validatedData = $request->validated();

        if (!empty($validatedData['mobile']) && $validatedData['mobile'] !== $user->mobile) {
            $user->update(['new_mobile' => $validatedData['mobile']]);
            $code= ConfirmationController::sendCode($user,$validatedData['mobile']);
            return ApiResponder::created([
                'new_mobile' => $validatedData['mobile'],
                'code' => $code
            ], __('Verification code sent to your mobile'), 302);
        }
        $user->update($validatedData);
        return ApiResponder::loaded(['user'=>UserResource::make($user)]);
    }
//
//
    public function confirmMobileChange(ConfirmNewMobileRequest $request)
    {
        $user = auth('sanctum')->user();

        $token = Otp::where('otp', $request->code)
            ->where('status', false)
            ->whereRelation('user', 'new_mobile', $request->mobile)
            ->latest()
            ->first();

        throw_if(!$token, ValidationException::withMessages(['msg' => __('Activation code is not correct')]));
        throw_if($token->expired_at < now(), ValidationException::withMessages(['msg' => __('Activation code is expired')]));

        $token->update(['status' => true]);

        $user->update([
            'mobile' => $request->mobile,
            'new_mobile' => null,
            'name' => $request->name ?? $user->name,
        ]);

        return ApiResponder::loaded([
            'user' => UserResource::make($user),
        ], 200, __('Mobile number updated successfully'));
    }
//###
    public function deleteAccount(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->delete();
        return ApiResponder::deleted(200, __('Your account has been successfully deleted'));
    }
}
