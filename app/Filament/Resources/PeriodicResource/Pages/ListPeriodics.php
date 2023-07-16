<?php

namespace App\Filament\Resources\PeriodicResource\Pages;

use App\Filament\Resources\PeriodicResource;
use Filament\Resources\Pages\ListRecords;

class ListPeriodics extends ListRecords
{
    protected static string $resource = PeriodicResource::class;

    protected function getActions(): array
    {
        return [
            //
        ];
    }
}
