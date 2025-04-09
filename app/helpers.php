<?php

use App\Models\Setting;

if (! function_exists('getSettingMediaUrl')) {
    function getSettingMediaUrl(string $key, string $collection = 'default', string $conversion = ''): ?string
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->hasMedia($collection)) {
            return $setting->getFirstMediaUrl($collection, $conversion);
        }

        return null; // or a default image URL
    }
}
