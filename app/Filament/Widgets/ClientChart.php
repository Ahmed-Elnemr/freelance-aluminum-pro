<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ClientChart extends ChartWidget
{
    protected static ?string $heading = 'Clients';
    protected static string $color = 'info';
    protected static ?string $maxHeight = '10000px';
    public function getDescription(): ?string
    {
        return __('dashboard.Timeline of in-app customer growth rate');
    }
    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('clients'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];


    }

    protected function getType(): string
    {
        return 'line';
    }

}
