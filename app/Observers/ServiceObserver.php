<?php

namespace App\Observers;

use App\Helpers\ServiceHelpers;
use App\Models\Service;

class ServiceObserver
{
    public function created(Service $service): void {
        ServiceHelpers::updateNextServiceDate($service, $service->service_date);
    }

    public function updated(Service $service): void
    {
        ServiceHelpers::updateNextServiceDate($service, $service->getOriginal('service_date'));
    }
}