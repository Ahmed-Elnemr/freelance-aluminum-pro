<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceResource;
use App\Models\Favorite;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'maintenance_id' => 'required|exists:maintenances,id,deleted_at,NULL,is_active,1',
        ]);

        $user = auth('sanctum')->user();
        $maintenance = Maintenance::findOrFail($request->maintenance_id);

        $favorite = $maintenance->favoritedByUsers()
            ->where('user_id', $user->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return ApiResponder::success(__('Removed from favorites successfully'));
        }

        $maintenance->favoritedByUsers()->create([
            'user_id' => $user->id,
        ]);

        return ApiResponder::success(__('Added to favorites successfully'));
    }

    public function myFavoriteMaintenances()
    {
        $user = auth('sanctum')->user();

        $favorites = Favorite::with('favouritable')
            ->where('user_id', $user->id)
            ->where('favouritable_type', Maintenance::class)
            ->latest()
            ->get()
            ->filter(fn ($fav) => $fav->favouritable)
            ->map(fn ($fav) => $fav->favouritable);

        return ApiResponder::loaded(MaintenanceResource::collection($favorites));
    }
}
