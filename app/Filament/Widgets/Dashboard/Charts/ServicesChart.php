<?php

namespace App\Filament\Widgets\Dashboard\Charts;

use App\Models\Service;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ServicesChart extends LineChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getHeading(): string
    {
        return 'Serviços';
    }

    protected function getData(): array
    {
        $generalServices = Trend::query(
            Service::whereHas('house', function ($query) {
                return $query->whereHas('owner', function ($query) {
                    return $query->where('is_client', 0);
                });
            })
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->dateColumn('service_date')
            ->count();

        $periodicServices = Trend::query(
            Service::whereHas('house', function ($query) {
                return $query->whereHas('owner', function ($query) {
                    return $query->where('is_client', 1);
                });
            })
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->dateColumn('service_date')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Serviços gerais',
                    'data' => $generalServices->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(0, 192, 0)',
                    'borderWidth' => '2',
                    'pointHoverBorderColor' => 'rgb(0, 192, 0)',
                    'pointHoverBackgroundColor' => 'rgb(0, 192, 0)',
                    'tension' => '0.2',
                ],
                [
                    'label' => 'Serviços periódicos',
                    'data' => $periodicServices->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(0, 0, 192)',
                    'borderWidth' => '2',
                    'pointHoverBorderColor' => 'rgb(0, 0, 192)',
                    'pointHoverBackgroundColor' => 'rgb(0, 0, 192)',
                    'tension' => '0.2',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}