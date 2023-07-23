<?php

namespace App\Filament\Widgets\Dashboard\Cards;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ServicesCount extends BaseWidget
{
    protected function getCards(): array
    {
        $generalServices = Service::whereHas('house', function ($query) {
            return $query->whereHas('owner', function ($query) {
                return $query->where('is_client', 0);
            });
        });

        $periodicServices = Service::whereHas('house', function ($query) {
            return $query->whereHas('owner', function ($query) {
                return $query->whereIsClient(1);
            })->whereStatus('active');
        });

        return [
            Card::make('Services count', $generalServices->count())
                ->label('Total de serviços')
                ->icon('heroicon-o-clipboard-list'),
            Card::make('Periodics count', $periodicServices->count())
                ->label('Total de serviços periódicos')
                ->icon('heroicon-o-refresh')
        ];
    }
}
