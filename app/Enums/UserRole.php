<?php

namespace App\Enums;

enum UserRole: string
{
    case FREE = 'free';
    case BUSINESS = 'business';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Free User',
            self::BUSINESS => 'Business User',
            self::ADMIN => 'Administrator',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::FREE => [
                'view_listings',
                'create_enquiry',
                'write_review',
                'bookmark_content',
                'apply_job',
                'redeem_coupon',
            ],
            self::BUSINESS => [
                'view_listings',
                'create_listing',
                'edit_listing',
                'create_event',
                'create_job',
                'create_product',
                'create_coupon',
                'manage_bookings',
                'view_analytics',
            ],
            self::ADMIN => [
                'manage_users',
                'manage_listings',
                'manage_categories',
                'manage_locations',
                'manage_settings',
                'view_all_analytics',
                'approve_content',
                'manage_payments',
            ],
        };
    }
}
