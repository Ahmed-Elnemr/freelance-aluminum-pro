<?php

namespace App\Http\Controllers\api;

use App\Enum\OrderStatusEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function store(StoreOrderRequest $request)
    {
        $user = auth('sanctum')->user();

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'location_data' => $request->location_data,
            'description' => $request->description,
            'status' => OrderStatusEnum::CURRENT,
            'is_active' => true,
        ]);

        return ApiResponder::created($order, __('Order created successfully'));
    }

    //todo:currentOrders
    public function currentOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::CURRENT)->get();
        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    //todo:expiredOrders
    public function expiredOrders()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders()->whereStatus(OrderStatusEnum::EXPIRED)->get();
        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    //todo:orderDetails

    public function show(Order $order)
    {
        return ApiResponder::loaded( OrderResource::make($order));
    }


}
