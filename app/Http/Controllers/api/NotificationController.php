<?php

namespace App\Http\Controllers\api;

use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
         $user=auth('sanctum')->user();
        $notifications = $user->notifications()->paginate(10);
        $user->unreadNotifications->markAsRead();
        return ApiResponder::loaded([ 'notifications' => NotificationResource::collection($notifications)->response()->getData(true)]);
    }


    /**
     * Get the count of unread notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function unreadCount()
    {
        $unreadCount = Auth::user()->unreadNotifications()->count();
        return ApiResponder::success('Unread notifications count retrieved.', ['unread_count' => $unreadCount]);
    }


    public function deleteNotification($uuid)
    {
        $user = auth('sanctum')->user();

        $notification = $user->notifications()->where('id', $uuid)->first();

        if (!$notification) {
            return ApiResponder::failed('Notification not found.', 404);
        }

        $notification->delete();

        return ApiResponder::deleted(200,__('Notification deleted successfully.'));
    }

    public function deleteAllNotifications()
    {
        $user = auth('sanctum')->user();

        $user->notifications()->delete();

        return ApiResponder::deleted(200,'All notifications deleted successfully.');
    }
}
