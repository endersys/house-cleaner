<?php

use Carbon\Carbon;

if (!function_exists('subtract_dates')) {
    function subtract_date_with_days($date, $daysToSubtract, $returnFormat = null) {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        
        $date->subDays($daysToSubtract);

        if ($returnFormat) {
            return $date->format($returnFormat);
        }

        return $date;
    }
}

if (!function_exists('count_days_between_now_and_date')) {
    function count_days_between_now_and_date($endDate): int {
        $startDate = Carbon::createFromFormat('Y-m-d', Carbon::now()->format('Y-m-d'));
        $finishDate = Carbon::createFromFormat('Y-m-d', $endDate);

        $diffInDays = $startDate->diffInDays($finishDate);

        return $diffInDays;
    }
}
