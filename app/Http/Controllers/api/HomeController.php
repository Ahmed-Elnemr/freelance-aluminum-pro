<?php

namespace App\Http\Controllers\api;

use App\Enum\CategoryEnum;
use App\Enum\SliderTypeEnum;
use App\Enum\TypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainServiceResource;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\SliderResource;
use App\Models\MainService;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function home(Request $request)
    {
        $mainServices = MainService::whereType(TypeEnum::HOME)->active()->paginate(10);
        $slidersInternal = Slider::active()->whereType(SliderTypeEnum::INTERNAL)->get();
        return ApiResponder::loaded([
            'sliders' => SliderResource::collection($slidersInternal),
            'main_services'=> MainServiceResource::collection($mainServices)
        ]);
    }

    public function ServicesByMian(Request $request, $id)
    {

        $search = $request->get('search') ;
            $locale = app()->getLocale();



        $allServices = Service::whereMainServiceId($id)->active()
            ->whereType(TypeEnum::HOME)
            ->whereCategory(CategoryEnum::PRODUCTS)
            ->when($search, function ($query) use ($search, $locale) {
                $query->where(function ($q) use ($search, $locale) {
                    $q->where("name->{$locale}", 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return ApiResponder::loaded([
            'services' => ServiceListResource::collection($allServices),
        ]);
    }
}
