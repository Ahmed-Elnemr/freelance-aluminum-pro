<?php

namespace App\Service;

use App\Models\UserDevice;

class UserService
{
    public static function addDevice($user): void
    {
        if (request('device_token')) {
            $device = $user->devices()->firstOrCreate([
                'token' => request('device_token'),
                'platform' => request('device_type'),
                'uuid' => request('uuid'),
            ]);
            UserDevice::where('user_id', $user->id)->where('uuid', request('uuid'))->where('platform', request('device_type'))->where('id', '!=', $device->id)->delete();
        }
    }
}
