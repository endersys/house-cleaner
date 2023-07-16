<?php

namespace App\Observers;

use App\Enums\ServiceStatusEnum;
use App\Models\Service;
use Carbon\Carbon;

class ServiceObserver
{
    public function updated(Service $service): void
    {
        $houseOwner = $service->house->owner;

        if ($houseOwner->is_client) {
            if ($service->isLastHouseService()) {
                if ($service->status === ServiceStatusEnum::Done) {
                    $periodic = $service->house->periodicity;

                    $quantityOfDays = match ($periodic->periodicity) {
                        'daily' => 1,
                        'bimonthly' => 15,
                        'monthly' => 30
                    };

                    $nextServiceDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($service->getOriginal('service_date'))))->addDays($quantityOfDays);

                    $periodic->update([
                        'next_service_date' => $nextServiceDate
                    ]);
                }
            }
        }
    }
}
