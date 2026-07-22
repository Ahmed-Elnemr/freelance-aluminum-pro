<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreDeviceTokenRequest;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;

class AdminDeviceTokenController extends Controller
{
    public function store(StoreDeviceTokenRequest $request): JsonResponse
    {
        UserService::addDevice(
            $request->user(),
            $request->validated('device_token'),
            $request->validated('device_type'),
            $request->validated('uuid'),
        );

        return response()->json([
            'message' => 'Device token registered.',
        ]);
    }
}
