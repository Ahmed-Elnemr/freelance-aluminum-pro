<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Throwable;

class PwaTestController extends Controller
{
    public function __invoke(Request $request, ?string $deviceToken = null): JsonResponse
    {
        $token = $deviceToken ?: $request->query('device_token');

        if (! filled($token)) {
            return response()->json([
                'status' => false,
                'message' => 'device_token is required. Example: /pwa-test/{device_token}',
            ], 422);
        }

        try {
            $messaging = app('firebase.messaging');

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create(
                    'PWA Test',
                    'Firebase + PWA test notification from Aluminum Pro'
                ))
                ->withData([
                    'type' => 'pwa_test',
                    'url' => url('/admin'),
                ])
                ->withWebPushConfig(WebPushConfig::fromArray([
                    'fcm_options' => [
                        'link' => url('/admin'),
                    ],
                ]))
                ->withHighestPossiblePriority();

            $messaging->send($message);

            return response()->json([
                'status' => true,
                'message' => 'Test notification sent.',
                'token_preview' => substr($token, 0, 20).'...',
            ]);
        } catch (Throwable $e) {
            $message = $e->getMessage();
            $hint = null;

            if (str_contains(strtolower($message), 'senderid mismatch')) {
                $hint = 'This device_token belongs to a different Firebase project than the server. '
                    .'Server project: '.config('services.firebase_web.project_id')
                    .' (senderId: '.config('services.firebase_web.messaging_sender_id').'). '
                    .'Open /admin, allow notifications again to register a new token from this project, then test with that new token.';
            }

            return response()->json([
                'status' => false,
                'message' => $message,
                'hint' => $hint,
                'server_project' => config('services.firebase_web.project_id'),
                'server_sender_id' => config('services.firebase_web.messaging_sender_id'),
            ], 500);
        }
    }
}
