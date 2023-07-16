<?php

namespace App\Filament\Resources\PeriodicResource\Pages;

use App\Filament\Resources\PeriodicResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodic extends EditRecord
{
    protected static string $resource = PeriodicResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
