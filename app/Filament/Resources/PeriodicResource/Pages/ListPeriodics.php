<?php

namespace App\Filament\Resources\PeriodicResource\Pages;

use App\Filament\Resources\PeriodicResource;
use App\Models\Periodic;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPeriodics extends ListRecords
{
    protected static string $resource = PeriodicResource::class;

    protected function getActions(): array
    {
        return [
            //
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Periodic::whereHas('house', fn ($query) => 
            $query
                ->where('status', 'active')
                ->whereHas('owner', fn ($query) => 
                    $query
                        ->where('status', 'active')
                        ->where('is_client', 1)
                )
        );
    }
}
