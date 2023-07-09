<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->before(function (CreateAction $action) {
                    dd($action);
                }),
        ];
    }
}
