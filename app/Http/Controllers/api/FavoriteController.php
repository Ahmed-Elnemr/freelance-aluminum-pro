<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Favorite;
use App\Models\Service;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id,deleted_at,NULL,is_active,1',
        ]);

        $user = auth('sanctum')->user();
        $service = Service::findOrFail($request->service_id);

        $favorite = $service->favoritedByUsers()
            ->where('user_id', $user->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return ApiResponder::success(__('Removed from favorites successfully'));
        }

        $service->favoritedByUsers()->create([
            'user_id' => $user->id,
        ]);

        return ApiResponder::success(__('Added to favorites successfully'));
    }

    //todo::getFavorites

    public function myFavoriteServices()
    {
        $user = auth('sanctum')->user();

        $favorites = Favorite::with('favouritable')
            ->where('user_id', $user->id)
            ->where('favouritable_type', Service::class)
            ->latest()
            ->get()
            ->filter(fn($fav) => $fav->favouritable)
            ->map(fn($fav) => $fav->favouritable)
       ;
        return ApiResponder::loaded(ServiceResource::collection($favorites));
    }
}
