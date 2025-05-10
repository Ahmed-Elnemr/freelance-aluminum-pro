<?php

namespace App\Http\Controllers\api;

use App\Enum\PaymentMethodEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Service;
use App\Models\ServicePaymentMethod;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymentPage($id)
    {
        $service = Service::find($id);

        return view('payments.payments-moyasar', get_defined_vars());
    }


    //todo: add payment method
    public function createPayment(StorePaymentRequest $request)
    {
        $user = auth('sanctum')->user();
        $userId = $user->id;
        $service = Service::findOrFail($request->service_id);
        if ((int)$request->paymentmethod === PaymentMethodEnum::moyasar->value) {
            return ApiResponder::get(
                '',
                ["payment_url" => route('payment-page', ['id' => $service->id, 'user_id' => $userId])]
            );
        } else {
            return ApiResponder::failed('payment method not found');
        }
    }

    //todo: paymentCallback
    public function paymentCallback(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id,deleted_at,NULL',
            'user_id' => 'required|exists:users,id,deleted_at,NULL',
        ]);
        if ($request->status == 'paid') {
            $userReservation = ServicePaymentMethod::create([
                'user_id' => $request->user_id,
                'service_id' => $request->service_id,
                'paymentmethod' => PaymentMethodEnum::moyasar->value,
            ]);
            return ApiResponder::success(
                'The service has been booked successfully.'
            );
        }

        return ApiResponder::failed('An error occurred while booking the service. Try again.');
    }
}
