<?php

namespace App\Http\Controllers\api;

use App\Enum\SliderTypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\SliderResource;
use App\Models\Service;
use App\Models\Slider;

class HomeController extends Controller
{
    public function home()
    {
        $slidersInternal=Slider::active()->whereType(SliderTypeEnum::INTERNAL)->get();
        $allServices=Service::active()->get();
        return ApiResponder::loaded([
            'sliders'=>SliderResource::collection($slidersInternal),
            'services'=>ServiceListResource::collection($allServices),
        ]);
    }
}
