<?php

namespace App\Filament\Widgets\Dashboard\Cards;

use App\Models\Owner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ClientsCount extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Clients count', Owner::whereIsClient(1)->whereStatus('active')->count())
                ->label('Total de clientes')
                ->icon('heroicon-o-user-group'),
            Card::make('Non clients count', Owner::whereIsClient(0)->whereStatus('active')->count())
                ->label('Total de não clientes')
                ->icon('heroicon-o-user-group')
        ];
    }
}