<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\House;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function afterFill(): void
    {
        if (request()->query('record')) {
            $house = House::findOrFail(request()->query('record'));
            
            if ($house) {
                $this->form->fill([
                    'house_id' => $house->id,
                    'status' => 'done',
                    'service_date' => $house->periodicity->next_service_date
                ]);
            }
        }
    }
}