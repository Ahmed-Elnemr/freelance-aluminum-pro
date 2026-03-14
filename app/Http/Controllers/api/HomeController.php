<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceListResource;
use App\Models\Maintenance;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $maintenances = Maintenance::active()->latest()->paginate(10);
        $slidersInternal = Slider::active()->whereType(\App\Enum\SliderTypeEnum::INTERNAL)->get();

        return ApiResponder::loaded([
            'sliders' => \App\Http\Resources\SliderResource::collection($slidersInternal),
            'maintenances' => MaintenanceListResource::collection($maintenances),
        ]);
    }
}
