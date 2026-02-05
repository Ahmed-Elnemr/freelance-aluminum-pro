<?php

namespace App\Helpers\Response;

class ResponseFactory
{
    public static array $response;

    public static function get($status, $message, $data)
    {
        $user = auth('sanctum')->user();
        
        // Inject user into data if authenticated and data is an array or null
        if ($user) {
            $userResource = \App\Http\Resources\user\UserResource::make($user);
            if (is_null($data)) {
                $data = ['user' => $userResource];
            } elseif (is_array($data)) {
                // Merge user if not already present to avoid double processing
                if (!isset($data['user'])) {
                    $data['user'] = $userResource;
                }
            }
        }

        self::$response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return Paginator::finalResponse(self::$response);
    }
}
