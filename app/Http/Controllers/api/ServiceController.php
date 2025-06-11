<?php

namespace App\Http\Controllers\api;

use App\Enum\CategoryEnum;
use App\Enum\SliderTypeEnum;
use App\Enum\TypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\MainServiceResource;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SliderResource;
use App\Models\MainService;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function mainServices(Request $request)
    {
        $mainServices = MainService::whereType(TypeEnum::SERVICES)->active()->paginate(10);
        return ApiResponder::loaded([
            'main_services'=> MainServiceResource::collection($mainServices)
        ]);
    }

    public function ServicesByMian(Request $request, $id)
    {

        $search = $request->get('search') ;
        $locale = app()->getLocale();
        $allServices = Service::whereMainServiceId($id)->active()
            ->whereType(TypeEnum::SERVICES)
            ->whereCategory(CategoryEnum::PRODUCTS->value)
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
    public function services()
    {
        $services = Service::active()
            ->whereType(TypeEnum::SERVICES)
            ->whereCategory(CategoryEnum::PRODUCTS->value)->latest()->paginate(10);
        return ApiResponder::loaded([
            'services' => ServiceListResource::collection($services)
        ]);
    }

//    public function products()
//    {
//        $services = Service::active()->whereCategory(CategoryEnum::PRODUCTS)->latest()->paginate(10);
//        return ApiResponder::loaded([
//            'services' => ServiceListResource::collection($services)
//        ]);
//    }
//
//    public function maintenance()
//    {
//        $services = Service::active()->whereCategory(CategoryEnum::MAINTENANCE)->latest()->paginate(10);
//        return ApiResponder::loaded([
//            'services' => ServiceListResource::collection($services)
//        ]);
//    }

    //todo:show

    public function show(Service $service)
    {
        if (!$service->isInactive()) {
            return ApiResponder::notFound();
        }

        $service->loadCount('ratings')
            ->loadAvg('ratings', 'rating');
        return ApiResponder::get(
            '',
            [
                'service' => ServiceResource::make($service),
//                'similar_services' => ServiceResource::collection($service->similar()),
            ]
        );
    }

    //todo:list two categories for services
    public function listProducts()
    {
        $services = Service::active()
            ->whereCategory(CategoryEnum::PRODUCTS->value)
            ->latest()
            ->get(['id', 'name'])
            ->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->getTranslation('name', app()->getLocale()),
            ]);

        return ApiResponder::loaded($services);
    }

    public function listMaintenance()
    {
        $services = Service::active()
            ->whereCategory(CategoryEnum::MAINTENANCE->value)
            ->latest()
            ->get(['id', 'name'])
            ->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->getTranslation('name', app()->getLocale()),
            ])
            ->toArray();

        return ApiResponder::loaded($services);
    }
    //todo:search

    public function search(Request $request)
    {
        $search = $request->input('search');

        $locale = app()->getLocale();

        $allServices = Service::active()
            ->whereCategory(CategoryEnum::PRODUCTS->value)
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
