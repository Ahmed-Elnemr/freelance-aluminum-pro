<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\ServiceInspection;
use Illuminate\Http\Request;

class ServiceInspectionController extends Controller
{
    public function requestInspection(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $user = auth('sanctum')->user();

        $alreadyRequested = ServiceInspection::where('user_id', $user->id)
            ->where('service_id', $request->service_id)
            ->exists();

        if ($alreadyRequested) {
            return ApiResponder::failed(__('You have already requested a preview of this service.'));
        }

        ServiceInspection::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id,
        ]);
        return ApiResponder::success(__('Your preview request has been sent successfully.'));

    }
}
