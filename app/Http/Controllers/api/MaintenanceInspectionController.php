<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceInspection;
use App\Notifications\MaintenanceInspectionRequestNotification;
use Illuminate\Http\Request;

class MaintenanceInspectionController extends Controller
{
    public function requestInspection(Request $request)
    {
        $request->validate([
            'maintenance_id' => 'required|exists:maintenances,id',
        ]);

        $user = auth('sanctum')->user();

        $alreadyRequested = MaintenanceInspection::where('user_id', $user->id)
            ->where('maintenance_id', $request->maintenance_id)
            ->exists();

        if ($alreadyRequested) {
            return ApiResponder::failed(__('You have already requested a preview of this maintenance.'));
        }

        $inspection = MaintenanceInspection::create([
            'user_id' => $user->id,
            'maintenance_id' => $request->maintenance_id,
        ]);

        $admin = \App\Models\User::where('type', 'admin')->first();
        if ($admin) {
            $admin->notify(new MaintenanceInspectionRequestNotification($inspection));
        }

        return ApiResponder::success(__('Your preview request has been sent successfully.'));
    }
}
