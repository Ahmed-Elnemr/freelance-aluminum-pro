<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettings(Request $request)
    {
        $settings = Setting::whereIn('key', ['about_app', 'terms_conditions'])
            ->pluck('value', 'key');
        return ApiResponder::get('', $settings);
    }
}
