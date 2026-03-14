<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceListResource;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $maintenances = Maintenance::active()
            ->when($request->get('search'), function ($query) use ($request) {
                $locale = app()->getLocale();
                $search = $request->get('search');
                $query->where(function ($q) use ($search, $locale) {
                    $q->where("name->{$locale}", 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($request->input('per_page', 20));

        return ApiResponder::loaded(MaintenanceListResource::collection($maintenances));
    }

    public function list(Request $request)
    {
        $maintenances = Maintenance::active()
            ->latest()
            ->get(['id', 'name'])
            ->map(fn ($maintenance) => [
                'id' => $maintenance->id,
                'name' => $maintenance->getTranslation('name', app()->getLocale()),
            ])
            ->values()
            ->toArray();

        return ApiResponder::loaded($maintenances);
    }

    public function show(Maintenance $maintenance)
    {
        if ($maintenance->is_active === false) {
            return ApiResponder::notFound();
        }

        $maintenance->loadCount('ratings')
            ->loadAvg('ratings', 'rating');

        return ApiResponder::loaded(MaintenanceResource::make($maintenance));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $locale = app()->getLocale();

        $maintenances = Maintenance::active()
            ->where(function ($q) use ($search, $locale) {
                $q->where("name->{$locale}", 'like', "%{$search}%")
                    ->orWhere("content->{$locale}", 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($request->input('per_page', 20));

        return ApiResponder::loaded(MaintenanceListResource::collection($maintenances));
    }
}
