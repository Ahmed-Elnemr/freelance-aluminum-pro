<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\QuickOrderRequest;
use App\Models\QuickOrder;
use Illuminate\Http\JsonResponse;

class QuickOrderController extends Controller
{
    public function store(QuickOrderRequest $request): JsonResponse
    {
        $quickOrder = QuickOrder::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($request->hasFile('sound')) {
            foreach ($request->file('sound') as $file) {
                $quickOrder->addMedia($file)->toMediaCollection('sounds');
            }
        }

        return response()->json([
            'status' => true,
            'message' => __('dashboard.quick_order_created_successfully'),
            'data' => $quickOrder->load('media'),
        ], 201);
    }
}
