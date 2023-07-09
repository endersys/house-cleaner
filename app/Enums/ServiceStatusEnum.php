<?php

namespace App\Enums;

enum ServiceStatusEnum:string {
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';
    case DoneWithPendency = 'done_with_pendency';
    case Rescheduled = 'rescheduled';
    case Expired = 'expired';
    case Canceled = 'canceled';
}