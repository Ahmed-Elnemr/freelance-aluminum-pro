<?php

namespace App\Http\Controllers\api;

use App\Enum\OrderStatusEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function store(StoreOrderRequest $request)
    {
        $user = auth('sanctum')->user();

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'description' => $request->description,
            'status' => OrderStatusEnum::CURRENT,
            'is_active' => true,
        ]);
        $user->notify(new OrderCreatedNotification($order));
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $media) {
                $order->addMedia($media)->toMediaCollection('media');
            }
        }


        return ApiResponder::created($order, __('Order created successfully'));
    }

    //todo:currentOrders
    public function currentOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::CURRENT)->latest()->get();
        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    //todo:expiredOrders
    public function expiredOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::EXPIRED)->latest()->get();
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
