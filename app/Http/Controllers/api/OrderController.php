<?php

namespace App\Http\Controllers\api;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Helpers\Response\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Models\WorkingDaySetting;
use App\Models\WorkingHourBlockedSlot;
use App\Notifications\AdminOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $user = auth('sanctum')->user();
        $date = $request->date;
        $time = $request->time;

        // Validation: Prevent booking in the past
        if (Carbon::parse($date)->isPast() && ! Carbon::parse($date)->isToday()) {
            return ApiResponder::failed('Cannot book in the past.');
        }

        // Strict validation using the helper
        $slotsData = $this->getAvailableSlotsForDate($date);

        $allAvailableSlots = array_merge($slotsData['am'], $slotsData['pm']);

        // Normalize input time to 12h format (h:i)
        $normalizedTime = Carbon::parse($time)->format('h:i');

        if (! in_array($normalizedTime, $allAvailableSlots)) {
            return ApiResponder::failed(__('dashboard.slot_already_booked_error'));
        }

        // Convert back to 24h for DB
        $final24hTime = null;
        if (in_array($normalizedTime, $slotsData['am'])) {
            $final24hTime = Carbon::parse($normalizedTime.' AM')->format('H:i');
        } elseif (in_array($normalizedTime, $slotsData['pm'])) {
            $final24hTime = Carbon::parse($normalizedTime.' PM')->format('H:i');
        }

        if (! $final24hTime) {
            return ApiResponder::failed(__('dashboard.slot_already_booked_error'));
        }

        $order = Order::create([
            'user_id' => $user->id,
            'maintenance_id' => $request->maintenance_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'description' => $request->description,
            'internal_note' => $request->internal_note ?? null,
            'scheduled_date' => $date,
            'scheduled_time' => $final24hTime,
            'status' => OrderStatusEnum::New,
            'is_active' => true,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $order->addMedia($image)->toMediaCollection('media');
            }
        }

        if ($request->hasFile('sounds')) {
            foreach ($request->file('sounds') as $sound) {
                $order->addMedia($sound)->toMediaCollection('sounds');
            }
        }

        $admins = User::where('type', UserTypeEnum::ADMIN->value)->active()->get();
        \Illuminate\Support\Facades\Notification::send($admins, new AdminOrderNotification($order, 'created'));

        return ApiResponder::created(OrderResource::make($order), __('dashboard.order_created_successfully'));
    }

    public function cancel(Order $order)
    {
        $user = auth('sanctum')->user();

        if ($order->user_id !== $user->id) {
            return ApiResponder::failed('Unauthorized', 403);
        }

        if (! in_array($order->status, [OrderStatusEnum::New, OrderStatusEnum::Approved])) {
            return ApiResponder::failed(__('dashboard.cannot_cancel_order_error'));
        }

        $order->update(['status' => OrderStatusEnum::Cancelled]);

        $admins = User::where('type', UserTypeEnum::ADMIN->value)->active()->get();
        \Illuminate\Support\Facades\Notification::send($admins, new AdminOrderNotification($order, 'cancelled'));

        return ApiResponder::success(__('dashboard.order_cancelled_successfully'));
    }

    public function availableSlots(Request $request)
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        if (Carbon::parse($request->date)->isPast() && ! Carbon::parse($request->date)->isToday()) {
            return ApiResponder::loaded(['am' => [], 'pm' => []]);
        }

        return ApiResponder::loaded($this->getAvailableSlotsForDate($request->date));
    }

    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $status = $request->status; // current, completed, cancelled

        $query = $user->orders();

        if ($status === 'current') {
            $query->whereIn('status', [OrderStatusEnum::New, OrderStatusEnum::Approved]);
        } elseif ($status === 'completed') {
            $query->where('status', OrderStatusEnum::Completed);
        } elseif ($status === 'cancelled') {
            $query->where('status', OrderStatusEnum::Cancelled);
        }

        $orders = $query->latest()->paginate(20);

        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    public function currentOrders()
    {
        return $this->index(new Request(['status' => 'current']));
    }

    public function expiredOrders()
    {
        // For backward compatibility, but index is preferred
        $user = auth('sanctum')->user();
        $orders = $user->orders()
            ->whereIn('status', [OrderStatusEnum::Completed, OrderStatusEnum::Cancelled])
            ->latest()
            ->paginate(20);

        return ApiResponder::loaded(OrderListResource::collection($orders));
    }

    public function show(Order $order)
    {
        $authUser = auth('sanctum')->user();
        if ((int) $order->user_id !== $authUser->id) {
            return ApiResponder::failed('Unauthorized', 403);
        }

        return ApiResponder::loaded(OrderResource::make($order));
    }

    public function availableDays(Request $request)
    {
        $startDate = $request->date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->date)
            ? Carbon::parse($request->date)
            : today();

        // Prevent viewing past dates
        if ($startDate->isPast() && ! $startDate->isToday()) {
            $startDate = today();
        }

        $days = [];
        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $slotsData = $this->getAvailableSlotsForDate($date);

            if (! empty($slotsData['am']) || ! empty($slotsData['pm'])) {
                $days[] = [
                    'date' => $date,
                    'day_name' => Carbon::parse($date)->translatedFormat('l'),
                    'slots' => $slotsData,
                ];
            }
        }

        return ApiResponder::loaded($days);
    }

    private function getAvailableSlotsForDate(string $date): array
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));

        $setting = WorkingDaySetting::where('day', $dayName)->where('is_active', true)->first();
        if (! $setting) {
            return ['am' => [], 'pm' => []];
        }

        $allSlots = $setting->generateSlots();
        $blockedSlots = WorkingHourBlockedSlot::where('day', $dayName)
            ->pluck('slot_time')
            ->map(fn ($t) => substr($t, 0, 5))
            ->toArray();

        $bookedOrders = Order::where('scheduled_date', $date)
            ->whereIn('status', [OrderStatusEnum::New, OrderStatusEnum::Approved])
            ->get(['scheduled_time', 'end_time']);

        $am = [];
        $pm = [];

        foreach ($allSlots as $slot) {
            $time = $slot['time'];
            $isAvailable = ! in_array($time, $blockedSlots);

            if ($isAvailable) {
                foreach ($bookedOrders as $order) {
                    $orderStart = substr($order->scheduled_time, 0, 5);
                    if ($time === $orderStart) {
                        $isAvailable = false;
                        break;
                    }
                    if ($order->end_time) {
                        $orderEnd = substr($order->end_time, 0, 5);
                        if ($time >= $orderStart && $time < $orderEnd) {
                            $isAvailable = false;
                            break;
                        }
                    }
                }
            }

            if ($isAvailable) {
                $time12h = Carbon::parse($time)->format('h:i');
                if ($slot['period'] === 'AM') {
                    $am[] = $time12h;
                } else {
                    $pm[] = $time12h;
                }
            }
        }

        return ['am' => $am, 'pm' => $pm];
    }
}
