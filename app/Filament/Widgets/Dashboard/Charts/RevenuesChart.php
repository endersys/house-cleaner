<?php

namespace App\Filament\Widgets\Dashboard\Charts;

use App\Models\Service;
use Filament\Widgets\BarChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenuesChart extends BarChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getHeading(): string
    {
        return 'Rendimentos';
    }

    protected function getData(): array
    {
        $generalServices = Trend::model(Service::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->dateColumn('service_date')
            ->sum('price');

        return [
            'datasets' => [
                [
                    'label' => 'Rendimentos',
                    'data' => $generalServices->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(0, 192, 0)',
                    'borderWidth' => '2',
                    'pointHoverBorderColor' => 'rgb(0, 192, 0)',
                    'pointHoverBackgroundColor' => 'rgb(0, 192, 0)',
                    'tension' => '0.2',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getOptions(): array
    {
        $totalRevenue = 0;

        foreach (Service::all() as $service) {
            $totalRevenue += $service->price;
        }

        $totalRevenue = 'R$ ' . $totalRevenue;

        return [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "Total: $totalRevenue"
                ],
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
