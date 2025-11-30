<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper
{
    /**
     * Truncate string with read more link.
     */
    public static function excerpt(string $text, int $length = 150, string $more = '...'): string
    {
        return Str::limit(strip_tags($text), $length, $more);
    }

    /**
     * Convert string to title case.
     */
    public static function titleCase(string $text): string
    {
        return Str::title($text);
    }

    /**
     * Extract phone numbers from text.
     */
    public static function extractPhone(string $text): array
    {
        preg_match_all('/(\+91|0)?[789]\d{9}/', $text, $matches);
        return $matches[0];
    }

    /**
     * Extract email addresses from text.
     */
    public static function extractEmail(string $text): array
    {
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);
        return $matches[0];
    }

    /**
     * Mask email address.
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];
        
        $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        return $maskedName . '@' . $domain;
    }

    /**
     * Mask phone number.
     */
    public static function maskPhone(string $phone): string
    {
        return substr($phone, 0, 2) . str_repeat('*', strlen($phone) - 4) . substr($phone, -2);
    }

    /**
     * Generate random string.
     */
    public static function random(int $length = 10): string
    {
        return Str::random($length);
    }

    /**
     * Convert HTML to plain text.
     */
    public static function toPlainText(string $html): string
    {
        return strip_tags($html);
    }

    /**
     * Clean and sanitize input.
     */
    public static function clean(string $text): string
    {
        return trim(strip_tags($text));
    }

    /**
     * Convert line breaks to <br> tags.
     */
    public static function nl2br(string $text): string
    {
        return nl2br($text);
    }

    /**
     * Count words in text.
     */
    public static function wordCount(string $text): int
    {
        return str_word_count(strip_tags($text));
    }

    /**
     * Estimate reading time in minutes.
     */
    public static function readingTime(string $text, int $wordsPerMinute = 200): int
    {
        $words = self::wordCount($text);
        return max(1, ceil($words / $wordsPerMinute));
    }

    /**
     * Highlight search terms in text.
     */
    public static function highlight(string $text, string $search): string
    {
        if (empty($search)) {
            return $text;
        }

        return preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
