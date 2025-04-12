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
}
