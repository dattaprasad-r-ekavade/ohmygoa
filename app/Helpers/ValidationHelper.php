<?php

namespace App\Helpers;

class ValidationHelper
{
    /**
     * Validate Indian phone number.
     */
    public static function isValidPhone(string $phone): bool
    {
        return (bool) preg_match('/^(\+91|0)?[789]\d{9}$/', $phone);
    }

    /**
     * Validate Indian pincode.
     */
    public static function isValidPincode(string $pincode): bool
    {
        return (bool) preg_match('/^[1-9][0-9]{5}$/', $pincode);
    }

    /**
     * Validate GST number.
     */
    public static function isValidGst(string $gst): bool
    {
        return (bool) preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gst);
    }

    /**
     * Validate PAN number.
     */
    public static function isValidPan(string $pan): bool
    {
        return (bool) preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan);
    }

    /**
     * Validate URL.
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate image file.
     */
    public static function isValidImage($file): bool
    {
        if (!$file) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        return in_array($file->getMimeType(), $allowedTypes);
    }

    /**
     * Validate file size.
     */
    public static function isValidSize($file, int $maxSizeMB = 5): bool
    {
        if (!$file) {
            return false;
        }

        return $file->getSize() <= ($maxSizeMB * 1024 * 1024);
    }

    /**
     * Sanitize input.
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Check if string contains only alphanumeric.
     */
    public static function isAlphanumeric(string $string): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', $string);
    }

    /**
     * Check if age is valid (18+).
     */
    public static function isValidAge(string $dateOfBirth, int $minAge = 18): bool
    {
        $dob = new \DateTime($dateOfBirth);
        $today = new \DateTime();
        $age = $today->diff($dob)->y;

        return $age >= $minAge;
    }
}
