<?php

namespace App\Filament\Widgets;

use App\Enum\OrderStatusEnum;
use App\Enum\OrderTypeEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrderChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('dashboard.orders_by_type');
    }

    public function getDescription(): ?string
    {
        return __('dashboard.Timeline of request types');
    }

    protected function getData(): array
    {
        $monthlyData = Trend::model(Order::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('dashboard.orders_count'),
                    'data' => $monthlyData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $monthlyData->map(function (TrendValue $value) {
                // تحويل التاريخ من نص إلى كائن Carbon أولاً
                return now()->parse($value->date)->translatedFormat('M'); // إرجاع اسم الشهر المختصر
            }),
        ];
    }

    protected function getType(): string
    {
        return 'polarArea'; // أو 'line' إذا أردت رسم بياني خطي
    }
}
