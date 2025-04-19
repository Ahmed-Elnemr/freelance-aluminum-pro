<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function storeRate(StoreRateRequest $request, Service $service)
    {
        if (!$service->isInactive()) {
            return ApiResponder::notFound();
        }
        $user = auth()->user();

        $rating = $service->ratings()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'rating' => $request->rating,
            ]
        );
        return  ApiResponder::success( __('Rate created successfully'),$rating);
    }
}
