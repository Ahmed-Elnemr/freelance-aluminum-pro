<?php

namespace App\Filament\Widgets;

use App\Enum\CategoryEnum;
use App\Enum\OrderStatusEnum;
use App\Enum\UserTypeEnum;
use App\Models\CategoryService;
use App\Models\Order;
use App\Models\Service;
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
            $this->getCategoryServicesStats(),
            $this->getMaintenanceServicesStats(),

            $this->getProductServicesStats(),
            $this->getCurrentOrdersStats(),

            $this->getExpiredOrdersStats(),
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

    protected function getMaintenanceServicesStats(): Stat
    {
        $currentCount = Service::where('category', CategoryEnum::MAINTENANCE->value)->count();
        $lastMonthCount = Service::where('category', CategoryEnum::MAINTENANCE->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.maintenance_services'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Service::class, ['category' => CategoryEnum::MAINTENANCE->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getProductServicesStats(): Stat
    {
        $currentCount = Service::where('category', CategoryEnum::PRODUCTS->value)->count();
        $lastMonthCount = Service::where('category', CategoryEnum::PRODUCTS->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.products_services'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Service::class, ['category' => CategoryEnum::PRODUCTS->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getCurrentOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::CURRENT->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::CURRENT->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.current_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::CURRENT->value]))
            ->color($this->getTrendColor($increase));
    }

    protected function getExpiredOrdersStats(): Stat
    {
        $currentCount = Order::where('status', OrderStatusEnum::EXPIRED->value)->count();
        $lastMonthCount = Order::where('status', OrderStatusEnum::EXPIRED->value)
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.expired_orders'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(Order::class, ['status' => OrderStatusEnum::EXPIRED->value]))
            ->color($this->getTrendColor($increase));
    }

    // ================ الدوال المساعدة ================ //

    protected function getTrendDescription(int $change, ?int $percentage = null): string
    {
        if ($percentage === null) {
            $percentage = $change != 0 ? round(abs($change) / ($change + $change) * 100) : 0;
        }

        return $change >= 0
            ? __("dashboard.increase") . " {$percentage}% (▲ {$change})"
            : __("dashboard.decrease") . " {$percentage}% (▼ " . abs($change) . ")";
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
        return Cache::remember("weekly_stats_{$model}_" . implode('_', $conditions), 3600, function() use ($model, $conditions) {
            return collect(range(6, 0)) // آخر 7 أيام
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

    protected function getCategoryServicesStats(): Stat
    {
        $currentCount = \App\Models\CategoryService::count();
        $lastMonthCount = \App\Models\CategoryService::where('created_at', '<', Carbon::now()->subMonth())->count();

        $increase = $currentCount - $lastMonthCount;

        return Stat::make(__('dashboard.category_services'), $currentCount)
            ->description($this->getTrendDescription($increase))
            ->descriptionIcon($this->getTrendIcon($increase))
            ->chart($this->getWeeklyData(\App\Models\CategoryService::class))
            ->color($this->getTrendColor($increase));
    }
}
