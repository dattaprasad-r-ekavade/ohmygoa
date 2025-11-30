<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Site Information
            ['key' => 'site_name', 'value' => 'OhMyGoa', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Your Gateway to Goa', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Discover businesses, events, jobs, and more in Goa', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'info@ohmygoa.com', 'group' => 'general'],
            ['key' => 'site_phone', 'value' => '+91 832 123 4567', 'group' => 'general'],
            ['key' => 'site_address', 'value' => 'Panaji, Goa, India', 'group' => 'general'],
            
            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/ohmygoa', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/ohmygoa', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/ohmygoa', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/ohmygoa', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@ohmygoa', 'group' => 'social'],
            
            // Payment Settings
            ['key' => 'commission_rate', 'value' => '0.10', 'group' => 'payment'],
            ['key' => 'minimum_payout', 'value' => '1000', 'group' => 'payment'],
            ['key' => 'subscription_price', 'value' => '499', 'group' => 'payment'],
            ['key' => 'currency', 'value' => 'INR', 'group' => 'payment'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'payment'],
            
            // Features
            ['key' => 'registration_enabled', 'value' => '1', 'group' => 'features'],
            ['key' => 'business_registration_enabled', 'value' => '1', 'group' => 'features'],
            ['key' => 'review_moderation', 'value' => '0', 'group' => 'features'],
            ['key' => 'listing_auto_approve', 'value' => '0', 'group' => 'features'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'features'],
            ['key' => 'maintenance_message', 'value' => 'We are currently performing scheduled maintenance. Please check back soon.', 'group' => 'features'],
            
            // Email Settings
            ['key' => 'email_from_name', 'value' => 'OhMyGoa', 'group' => 'email'],
            ['key' => 'email_from_address', 'value' => 'noreply@ohmygoa.com', 'group' => 'email'],
            ['key' => 'email_notifications_enabled', 'value' => '1', 'group' => 'email'],
            
            // SEO Settings
            ['key' => 'meta_keywords', 'value' => 'Goa, business directory, events, jobs, tourism, beach, india', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'google_maps_api_key', 'value' => '', 'group' => 'seo'],
            
            // Limits
            ['key' => 'max_images_per_listing', 'value' => '10', 'group' => 'limits'],
            ['key' => 'max_file_size_mb', 'value' => '5', 'group' => 'limits'],
            ['key' => 'listing_expiry_days', 'value' => '365', 'group' => 'limits'],
            ['key' => 'classified_expiry_days', 'value' => '30', 'group' => 'limits'],
            
            // Points System
            ['key' => 'points_per_rupee', 'value' => '10', 'group' => 'points'],
            ['key' => 'signup_bonus_points', 'value' => '100', 'group' => 'points'],
            ['key' => 'review_points', 'value' => '10', 'group' => 'points'],
            ['key' => 'listing_creation_points', 'value' => '50', 'group' => 'points'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
