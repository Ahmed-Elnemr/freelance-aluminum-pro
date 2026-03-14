<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Order;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function paymentPage(Request $request)
    {
        $userId = $request->user_id;

        $cacheKey = 'pending_order_'.$userId;
        $cached = Cache::get($cacheKey);

        if (! $cached) {
            abort(404, 'Order not found or expired.');
        }

        $maintenance = Maintenance::find($cached['order_data']['maintenance_id']);

        $finalPrice = $maintenance->final_price;

        if ($finalPrice < 100) {
            $finalPrice += 50;
        }

        return view('payments.payments-moyasar', [
            'maintenance' => $maintenance,
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
        $cacheKey = 'pending_order_'.$userId;
        $cached = Cache::get($cacheKey);

        if ($request->status === 'paid') {
            $order = Order::create($cached['order_data']);
            Cache::forget($cacheKey);

            if (! empty($cached['images'])) {
                foreach ($cached['images'] as $imagePath) {
                    $fullPath = storage_path("app/{$imagePath}");
                    if (file_exists($fullPath)) {
                        $order->addMedia($fullPath)->toMediaCollection('media');
                    }
                }
            }
            if (! empty($cached['sounds'])) {
                foreach ($cached['sounds'] as $soundPath) {
                    $fullPath = storage_path("app/{$soundPath}");
                    if (file_exists($fullPath)) {
                        $order->addMedia($fullPath)->toMediaCollection('sounds');
                    }
                }
            }

            $order->user->notify(new OrderCreatedNotification($order));

            // Notify Admin
            $admin = \App\Models\User::where('type', 'admin')->first();
            if ($admin) {
                $admin->notify(new OrderCreatedNotification($order));
            }

            return ApiResponder::success('The service has been booked successfully.');
        }

        $errorMessage = $request->message ?? 'An error occurred while booking the service. Try again.';

        return ApiResponder::failed($errorMessage);
    }
}
