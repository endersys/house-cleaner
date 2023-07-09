<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterial extends EditRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterValidate(): void
    {
        $this->record->stock()->update([
            'quantity' => data_get($this->data, 'stock')
        ]);
    }

    protected function afterFill(): void
    {
        data_set($this->data, 'stock', $this->record->stock->quantity);
    }
}