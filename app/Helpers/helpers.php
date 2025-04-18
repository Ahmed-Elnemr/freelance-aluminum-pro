<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;

if (! function_exists('getSettingMediaUrl')) {
    function getSettingMediaUrl(string $key, string $collection = 'default', string $conversion = ''): ?string
    {
        if (App::runningInConsole()) {
            return null;
        }

        if (Schema::hasTable('settings')) {
            $setting = \App\Models\Setting::where('key', $key)->first();

            if ($setting && $setting->hasMedia($collection)) {
                return $setting->getFirstMediaUrl($collection, $conversion);
            }
        }

        return null;
    }


    if (! function_exists('getDefaultImageUrl')) {
        function getDefaultImageUrl(?string $path, string $default = 'defaultImage/ChatGPTImageApr18, 2025, 07_07_18 PM.png', string $disk = 'public'): string
        {
            if ($path && Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->url($path);
            }
            return asset($default);
        }
    }

}
