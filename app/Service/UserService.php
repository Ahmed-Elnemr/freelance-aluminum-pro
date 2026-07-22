<?php

namespace App\Service;

use App\Models\UserDevice;

class UserService
{
    public static function addDevice(
        $user,
        ?string $token = null,
        ?string $platform = null,
        ?string $uuid = null,
    ): void {
        $token ??= request('device_token');
        $platform ??= request('device_type');
        $uuid ??= request('uuid');

        if (! $token) {
            return;
        }

        $device = $user->devices()->firstOrCreate([
            'token' => $token,
            'platform' => $platform,
            'uuid' => $uuid,
        ]);

        UserDevice::query()
            ->where('user_id', $user->id)
            ->where('uuid', $uuid)
            ->where('platform', $platform)
            ->where('id', '!=', $device->id)
            ->delete();
    }
}
