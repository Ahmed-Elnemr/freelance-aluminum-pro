<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateRequest;
use App\Models\Maintenance;

class RateController extends Controller
{
    public function storeRate(StoreRateRequest $request, Maintenance $maintenance)
    {
        if ($maintenance->is_active === false) {
            return ApiResponder::notFound();
        }

        $user = auth()->user();

        $rating = $maintenance->ratings()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return ApiResponder::success(__('Rate created successfully'), $rating);
    }
}
