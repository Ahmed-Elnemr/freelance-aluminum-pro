<?php

namespace App\Http\Controllers\api;

use App\Enum\CategoryEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function products()
    {
        $services = Service::active()->whereCategory(CategoryEnum::PRODUCTS)->get();
        return ApiResponder::loaded([
            'services' => ServiceListResource::collection($services)
        ]);
    }

    public function maintenance()
    {
        $services = Service::active()->whereCategory(CategoryEnum::MAINTENANCE)->get();
        return ApiResponder::loaded([
            'services' => ServiceListResource::collection($services)
        ]);
    }

    //todo:show

    public function show(Service $service)
    {
        if (!$service->isInactive()) {
            return ApiResponder::notFound();
        }
        return ApiResponder::get(
            '',
            [
                'service' => ServiceResource::make($service),
                'similar_services' => ServiceResource::collection($service->similar()),
            ]
        );
    }
}
