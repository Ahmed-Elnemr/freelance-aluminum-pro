<?php

namespace App\Http\Controllers\api;

use App\Enum\OrderStatusEnum;
use App\Enum\PaymentMethodEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    public function store(StoreOrderRequest $request)
    {
        $user = auth('sanctum')->user();

        $orderData = [
            'user_id' => $user->id,
            'service_id' => $request->service_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'description' => $request->description,
            'status' => OrderStatusEnum::CURRENT,
            'is_active' => true,
        ];

        $imagePaths = [];

        if ($request->hasFile('images')) {
            $tempPath = storage_path("app/temp-user-uploads/{$user->id}/");
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            foreach ($request->file('images') as $index => $image) {
                $filename = "image_$index." . $image->getClientOriginalExtension();
                $image->move($tempPath, $filename);
                $imagePaths[] = "temp-user-uploads/{$user->id}/{$filename}";
            }
        }

        $cacheKey = 'pending_order_' . $user->id;
        Cache::put($cacheKey, [
            'order_data' => $orderData,
            'images' => $imagePaths
        ], now()->addMinutes(5));

        if ((int)$request->paymentmethod === PaymentMethodEnum::moyasar->value) {
            return ApiResponder::get(
                '',
                ['payment_url' => route('payment-page', ['user_id' => $user->id])]
            );
        }
        return ApiResponder::failed('Payment method not supported');
    }


    //todo:currentOrders
    public function currentOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::CURRENT)->latest()->paginate(10);
        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    //todo:expiredOrders
    public function expiredOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::EXPIRED)->latest()->paginate(10);
        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    //todo:orderDetails

    public function show(Order $order)
    {
        $authUser = auth('sanctum')->user();

        if ($order->user_id !== $authUser->id) {
            return ApiResponder::failed('Unauthorized', 403);
        }
        return ApiResponder::loaded(OrderResource::make($order));

    }


}
