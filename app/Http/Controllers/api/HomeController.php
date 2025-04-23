<?php

namespace App\Http\Controllers\api;

use App\Enum\SliderTypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\SliderResource;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $search = $request->get('search') ;
            $locale = app()->getLocale();

        $slidersInternal = Slider::active()->whereType(SliderTypeEnum::INTERNAL)->get();

        $allServices = Service::active()
            ->when($search, function ($query) use ($search, $locale) {
                $query->where(function ($q) use ($search, $locale) {
                    $q->where("name->{$locale}", 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->get();

        return ApiResponder::loaded([
            'sliders' => SliderResource::collection($slidersInternal),
            'services' => ServiceListResource::collection($allServices),
        ]);
    }
}
