<?php

namespace App\Http\Controllers\api;

use App\Enum\SliderTypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function listIntro()
    {
      $slider=  Slider::active()->whereType(SliderTypeEnum::INTRODUCTION)->get();
      return ApiResponder::loaded([
          SliderResource::collection($slider)
      ]);

    }
}
