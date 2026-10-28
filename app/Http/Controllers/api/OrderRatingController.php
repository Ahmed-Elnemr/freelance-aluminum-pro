<?php

namespace App\Http\Controllers\api;

use App\Enum\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\StoreOrderRatingRequest;
use App\Models\Order;
use App\Models\OrderRating;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderRatingController extends Controller
{
    public function store(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if ((int) $order->user_id !== (int) $user->id) {
            return $this->notRatable(403);
        }

        $order->load('rating');

        if ($order->rating) {
            return $this->alreadyRated($order->rating);
        }

        if ($order->status !== OrderStatusEnum::Completed) {
            return $this->notRatable(422);
        }

        $formRequest = StoreOrderRatingRequest::createFrom($request);
        $validator = Validator::make($request->all(), $formRequest->rules(), $formRequest->messages());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $comment = isset($data['comment']) ? trim((string) $data['comment']) : '';
        $tags = array_values(array_unique($data['tags'] ?? []));

        try {
            $rating = DB::transaction(fn () => $order->rating()->create([
                'user_id' => $user->id,
                'maintenance_id' => $order->maintenance_id,
                'stars' => $data['stars'],
                'comment' => $comment === '' ? null : $comment,
                'tags' => $tags,
            ]));
        } catch (QueryException $exception) {
            if (! $this->isDuplicateOrderRating($exception)) {
                throw $exception;
            }

            return $this->alreadyRated($order->rating()->first());
        }

        return response()->json([
            'status' => true,
            'message' => __('ratings.thanks'),
            'data' => $rating->toApiArray(),
        ], 201);
    }

    private function alreadyRated(?OrderRating $rating): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => 'order_already_rated',
            'message' => __('ratings.already_rated'),
            'data' => $rating?->toApiArray(),
        ], 409);
    }

    private function notRatable(int $status): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => 'order_not_ratable',
            'message' => __('ratings.not_ratable'),
        ], $status);
    }

    private function isDuplicateOrderRating(QueryException $exception): bool
    {
        $sqlState = (string) ($exception->errorInfo[0] ?? $exception->getCode());
        $driverCode = (string) ($exception->errorInfo[1] ?? '');

        $isUniqueViolation = in_array($sqlState, ['23000', '23505'], true)
            || in_array($driverCode, ['1062', '19'], true);

        return $isUniqueViolation && str_contains($exception->getMessage(), 'order_id');
    }
}
