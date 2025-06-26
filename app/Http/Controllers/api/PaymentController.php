<?php

namespace App\Http\Controllers\api;

use App\Enum\PaymentMethodEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePaymentMethod;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
class PaymentController extends Controller
{
    public function paymentPage(Request $request)
    {
        $userId = $request->user_id;

        $cacheKey = 'pending_order_' . $userId;
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            abort(404, 'Order not found or expired.');
        }

        $service = Service::find($cached['order_data']['service_id']);

        $finalPrice = $service->final_price;
      $x=  $finalPrice<100;
        if ($service->category === \App\Enum\CategoryEnum::MAINTENANCE) {

            dd($service->category,\App\Enum\CategoryEnum::MAINTENANCE , $x);
        }
        if ($service->category == \App\Enum\CategoryEnum::MAINTENANCE->value ) {
            dd('hh');
        }
        if ($finalPrice<100) {
            dd('ii');
        }

//        if ($service->category === \App\Enum\CategoryEnum::MAINTENANCE->value && $finalPrice < 100) {
//            $finalPrice += 50;
//        }

        return view('payments.payments-moyasar', [
            'service' => $service,
            'userId' => $userId,
            'finalPrice' => $finalPrice,
        ]);
    }


    public function paymentCallback(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id,deleted_at,NULL',
            'status' => 'required|in:paid,failed',
        ]);

        $userId = $request->user_id;
        $cacheKey = 'pending_order_' . $userId;
        $cached = Cache::pull($cacheKey);

        if ($request->status === 'paid') {
            $order = Order::create($cached['order_data']);

            if (!empty($cached['images'])) {
                foreach ($cached['images'] as $imagePath) {
                    $fullPath = storage_path("app/{$imagePath}");
                    if (file_exists($fullPath)) {
                        $order->addMedia($fullPath)->toMediaCollection('media');
//                        unlink($fullPath); // حذف الصورة من المجلد المؤقت
                    }
                }
            }
            if (!empty($cached['sounds'])) {
                foreach ($cached['sounds'] as $soundPath) {
                    $fullPath = storage_path("app/{$soundPath}");
                    if (file_exists($fullPath)) {
                        $order->addMedia($fullPath)->toMediaCollection('sounds');
                        // unlink($fullPath); // احذف الملف بعد التخزين لو حابب
                    }
                }
            }


            $order->user->notify(new OrderCreatedNotification($order));

            return ApiResponder::success('The service has been booked successfully.');
        }

        return ApiResponder::failed('An error occurred while booking the service. Try again.');
    }
}
