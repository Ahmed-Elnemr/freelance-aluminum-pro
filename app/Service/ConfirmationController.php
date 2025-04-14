<?php

namespace App\Service;

//use App\Events\UserOnline;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
//use Modules\User\Models\Otp;
//use Modules\User\Models\User;
//use Modules\User\Requests\CheckMobileOtpRequest;
//use Modules\User\Requests\SendEmailOtpRequest;
//use Modules\User\Requests\SendMobileOtpRequest;
//use Modules\User\Resources\UserResource;
//use Modules\User\Services\UserService;
//use MshMsh\Helpers\ApiResponder;
use Illuminate\Validation\ValidationException;

class ConfirmationController extends Controller
{
//    public static function sendMail($user, $title = null, $message = null,$button_link = null, $confirmation = true)
//    {
//        $title = $title ?? __('Your Verification Code');
//        $message = $message ?? __('This is your confirmation code');
//        if ($confirmation) {
//            $code = rand(1000, 9999);
//            if(env('APP_ENV') == 'local'){
//                $code = 1234;
//            }
//            $user->otps()->whereStatus(true)->delete();
//            $user->otps()->updateOrCreate(['user_id' => $user['id'],'status'=>false], [
//                'expired_at' => Carbon::now()->addMinutes(10),
//                'otp' => $code,
//            ]);
//        }
//        $msg = [
//            'title' => $title,
//            'content' => $message,
//            'button_link' => $button_link,
//            'button_text' => __('press here'),
//            'code' => @$code,
//        ];
//
//        UserService::addDevice($user);
//        try {
//            \Mail::send('User::emails.default', ['user' => $user, 'msg' => json_decode(json_encode($msg))], function ($mail)
//            use ($user, $title) {
//                $mail->subject($title)
//                    ->from(app_setting('email'), app_setting('title'))
//                    ->to($user->email, $user->name);
//            });
//        } catch (\Throwable $th) {
//            //throw $th;
//        }
//        return $code;
//    }
//
//    public function activate(CheckMobileOtpRequest $request)
//    {
//        $token = Otp::where('otp', $request->code)->where('status', false)
//            ->whereRelation('user', 'mobile', '=', $request->mobile)->latest()
//            ->first();
//        throw_if(!$token, ValidationException::withMessages(['msg' => __('Activation code is not correct')]));
//        throw_if($token->expired_at < Carbon::now()->format('Y-m-d H:i:s'), ValidationException::withMessages(['msg' => __('Activation code is expired')]));
//
//        $user = $token->user;
//        $user->loginActivities()->updateOrCreate([
//            'user_id' => $user->id,
//            'date' => Carbon::now()->format('Y-m-d'),
//        ]);
//        $token->update(['status' => true]);
//        $user->update(['status' => true,'online' => true]);
//        $access_token = $user->createToken('authToken')->plainTextToken;
//        return ApiResponder::loaded([
//            'user' => UserResource::make($user),
//            'access_token' => $access_token,
//        ],200,'Your account activated successfully');
//    }

//    public function resend_code(SendEmailOtpRequest $request)
//    {
//        $user = User::where('email',$request->email)->first();
//        $code = self::sendMail($user);
//        $message = 'Confirmation code resent to your email';
//        return ApiResponder::loaded([
//            'email' => $user->email,
//            'code' => $code
//        ],200,$message);
//    }

    public static function sendCode($user,$new_mobile = null)
    {
        $code = rand(10000, 99999);
        if(env('APP_ENV') == 'local'){
            $code = 1234;
        }
        $user->otps()->whereStatus(true)->delete();
        $user->otps()->updateOrCreate(['user_id' => $user['id'],'status'=>false], [
            'expired_at' => Carbon::now()->addMinutes(2),
            'otp' => $code,
        ]);
        $message = __("Your confirmation code is : ") . $code;
        if(env('APP_ENV') != 'local') {
//            send_sms($new_mobile ?? $user->mobile, $message);
            return $code;
        }
        return $code;
    }
}
