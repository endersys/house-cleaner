<?php

namespace App\Filament\Widgets\Dashboard\Cards;

use App\Models\Owner;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ClientsAndServicesCount extends BaseWidget
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
            Card::make('Clients count', Owner::whereIsClient(1)->whereStatus('active')->count())
                ->label('Total de clientes')
                ->icon('heroicon-o-user-group'),
            Card::make('Non clients count', Owner::whereIsClient(0)->whereStatus('active')->count())
                ->label('Total de não clientes')
                ->icon('heroicon-o-user-group'),
            Card::make('Services count', $generalServices->count())
                ->label('Total de serviços não periódicos')
                ->icon('heroicon-o-clipboard-list'),
            Card::make('Periodics count', $periodicServices->count())
                ->label('Total de serviços periódicos')
                ->icon('heroicon-o-refresh')
        ];
    }
}