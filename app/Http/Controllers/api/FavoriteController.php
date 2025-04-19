<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
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
}
