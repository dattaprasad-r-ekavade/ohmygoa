<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format date for display.
     */
    public static function format($date, string $format = 'd M Y'): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }

    /**
     * Format date with time.
     */
    public static function formatWithTime($date, string $format = 'd M Y, h:i A'): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }

    /**
     * Get human readable time difference.
     */
    public static function diffForHumans($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->diffForHumans();
    }

    /**
     * Check if date is past.
     */
    public static function isPast($date): bool
    {
        if (!$date) {
            return false;
        }

        return Carbon::parse($date)->isPast();
    }

    /**
     * Check if date is future.
     */
    public static function isFuture($date): bool
    {
        if (!$date) {
            return false;
        }

        return Carbon::parse($date)->isFuture();
    }

    /**
     * Check if date is today.
     */
    public static function isToday($date): bool
    {
        if (!$date) {
            return false;
        }

        return Carbon::parse($date)->isToday();
    }

    /**
     * Get days between two dates.
     */
    public static function daysBetween($startDate, $endDate): int
    {
        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
    }

    /**
     * Get date range string.
     */
    public static function dateRange($startDate, $endDate, string $format = 'd M Y'): string
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->isSameDay($end)) {
            return $start->format($format);
        }

        return $start->format($format) . ' - ' . $end->format($format);
    }

    /**
     * Get month year string.
     */
    public static function monthYear($date): string
    {
        return Carbon::parse($date)->format('F Y');
    }
}
