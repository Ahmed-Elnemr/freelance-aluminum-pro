<?php

namespace App\Filament\Widgets;

use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Models\Maintenance;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class CategoryServiceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getUserStats(),
            $this->getMaintenanceStats(),
            $this->getCurrentOrdersStats(),
            $this->getApprovedOrdersStats(),
            $this->getExpiredOrdersStats(),
            $this->getCancelledOrdersStats(),
        ];
    }

    protected function getUserStats(): Stat
    {
        $currentCount = User::where('type', UserTypeEnum::CLIENT->value)->count();
        $lastMonthCount = User::where('type', UserTypeEnum::CLIENT->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;
        $increasePercentage = $lastMonthCount > 0 ? round(($increase / $lastMonthCount) * 100) : 100;

        return Stat::make(__('dashboard.clients'), $currentCount)
            ->description($this->getTrendDescription($increase, $increasePercentage))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(User::class, ['type' => UserTypeEnum::CLIENT->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getMaintenanceStats(): Stat
    {
        $currentCount = Maintenance::count();
        $lastMonthCount = Maintenance::where('created_at', '<', Carbon::now()->subMonth())->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.maintenance_services'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Maintenance::class))
            ->color($this->getTrendColor($increase));
    }

    protected function getCurrentOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::New->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::New->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.current_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::New->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getExpiredOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::Completed->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::Completed->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.expired_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::Completed->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getApprovedOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::Approved->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::Approved->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.approved_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::Approved->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getCancelledOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::Cancelled->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::Cancelled->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.cancelled_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::Cancelled->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getTrendDescription(int $change, ?int $percentage = null): string
    {
        if ($percentage === null) {
            $percentage = $change != 0 ? round(abs($change) / ($change + $change) * 100) : 0;
        }

        return $change >= 0
            ? __('dashboard.increase')." {$percentage}% (▲ {$change})"
            : __('dashboard.decrease')." {$percentage}% (▼ ".abs($change).')';
    }

    protected function getTrendIcon(int $change): string
    {
        return $change >= 0
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
    }

    protected function getTrendColor(int $change): string
    {
        return $change >= 0 ? 'success' : 'danger';
    }

    protected function getWeeklyData(string $model, array $conditions = []): array
    {
        return Cache::remember("weekly_stats_{$model}_".implode('_', $conditions), 3600, function () use ($model, $conditions) {
            return collect(range(6, 0))
                ->map(function ($days) use ($model, $conditions) {
                    $query = $model::query();

                    foreach ($conditions as $column => $value) {
                        $query->where($column, $value);
                    }

                    return $query->whereDate('created_at', today()->subDays($days))
                        ->count();
                })
                ->toArray();
        });
    }
}
