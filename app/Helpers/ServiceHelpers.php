<?php

namespace App\Helpers;

use App\Enums\ServiceStatusEnum;
use Carbon\Carbon;

class ServiceHelpers {
    public static function updateNextServiceDate($service, $serviceDate) {
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

                    $nextServiceDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($serviceDate)))->addDays($quantityOfDays);

                    $periodic->update([
                        'next_service_date' => $nextServiceDate
                    ]);
                }
            }
        }
    }
}