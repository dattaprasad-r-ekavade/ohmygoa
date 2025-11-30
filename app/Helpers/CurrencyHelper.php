<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Default currency symbol.
     */
    const DEFAULT_SYMBOL = '₹';
    const DEFAULT_CODE = 'INR';

    /**
     * Format amount with currency symbol.
     */
    public static function format(float $amount, string $symbol = null): string
    {
        $symbol = $symbol ?? self::DEFAULT_SYMBOL;
        return $symbol . number_format($amount, 2);
    }

    /**
     * Format amount without decimals.
     */
    public static function formatWhole(float $amount, string $symbol = null): string
    {
        $symbol = $symbol ?? self::DEFAULT_SYMBOL;
        return $symbol . number_format($amount, 0);
    }

    /**
     * Format amount in short form (K, L, Cr).
     */
    public static function formatShort(float $amount, string $symbol = null): string
    {
        $symbol = $symbol ?? self::DEFAULT_SYMBOL;

        if ($amount >= 10000000) {
            return $symbol . number_format($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return $symbol . number_format($amount / 100000, 2) . ' L';
        } elseif ($amount >= 1000) {
            return $symbol . number_format($amount / 1000, 2) . ' K';
        }

        return $symbol . number_format($amount, 2);
    }

    /**
     * Convert amount to words (Indian system).
     */
    public static function toWords(float $amount): string
    {
        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
            18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];

        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        
        $number = floor($amount);
        $decimal = round(($amount - $number) * 100);
        
        if ($number == 0) {
            return 'Zero Rupees';
        }

        $result = '';
        $i = 0;

        while ($number > 0) {
            $divider = ($i == 2) ? 10 : 100;
            $remainder = $number % $divider;
            $number = floor($number / $divider);

            if ($remainder) {
                $result = self::convertNumber($remainder, $words) . ' ' . $digits[$i] . ' ' . $result;
            }
            $i++;
        }

        $result = trim($result) . ' Rupees';

        if ($decimal > 0) {
            $result .= ' and ' . self::convertNumber($decimal, $words) . ' Paise';
        }

        return $result;
    }

    /**
     * Helper to convert number to words.
     */
    private static function convertNumber(int $number, array $words): string
    {
        if ($number < 20) {
            return $words[$number];
        }

        if ($number < 100) {
            $tens = floor($number / 10) * 10;
            $units = $number % 10;
            return $words[$tens] . ($units ? ' ' . $words[$units] : '');
        }

        return '';
    }

    /**
     * Parse currency string to float.
     */
    public static function parse(string $currency): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $currency);
    }
}
