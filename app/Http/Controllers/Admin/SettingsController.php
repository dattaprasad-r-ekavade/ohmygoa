<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $settings = $this->getSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update SEO settings.
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_site_verification' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'SEO settings updated successfully.');
    }

    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'Email settings updated successfully.');
    }

    /**
     * Update payment settings.
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'stripe_enabled' => 'boolean',
            'stripe_public_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'paypal_enabled' => 'boolean',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'razorpay_enabled' => 'boolean',
            'razorpay_key_id' => 'nullable|string',
            'razorpay_key_secret' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Update social media links.
     */
    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'Social media links updated successfully.');
    }

    /**
     * Update business settings.
     */
    public function updateBusiness(Request $request)
    {
        $validated = $request->validate([
            'listings_require_approval' => 'boolean',
            'products_require_approval' => 'boolean',
            'classifieds_require_approval' => 'boolean',
            'service_experts_require_approval' => 'boolean',
            'auto_approve_verified_users' => 'boolean',
            'listing_expiry_days' => 'required|integer|min:1',
            'classified_expiry_days' => 'required|integer|min:1',
            'max_images_per_listing' => 'required|integer|min:1|max:20',
            'max_images_per_product' => 'required|integer|min:1|max:20',
        ]);

        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()
            ->with('success', 'Business settings updated successfully.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return redirect()->back()
            ->with('success', 'All caches cleared successfully.');
    }

    /**
     * Clear specific cache.
     */
    public function clearSpecificCache(Request $request)
    {
        $request->validate([
            'type' => 'required|in:config,route,view,cache',
        ]);

        Artisan::call("{$request->type}:clear");

        return redirect()->back()
            ->with('success', ucfirst($request->type) . ' cache cleared successfully.');
    }

    /**
     * Run database backup.
     */
    public function backupDatabase()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Create backups directory if it doesn't exist
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }

            // SQLite backup
            if (Config::get('database.default') === 'sqlite') {
                $dbPath = Config::get('database.connections.sqlite.database');
                copy($dbPath, storage_path('app/backups/backup_' . date('Y-m-d_His') . '.sqlite'));
            }

            return redirect()->back()
                ->with('success', 'Database backup created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Get all settings from database.
     */
    private function getSettings(): array
    {
        try {
            $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            $settings = [];
        }

        return array_merge($this->getDefaultSettings(), $settings);
    }

    /**
     * Set a setting value.
     */
    private function setSetting(string $key, $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );

        Cache::forget('settings');
    }

    /**
     * Get default settings.
     */
    private function getDefaultSettings(): array
    {
        return [
            'site_name' => 'OhMyGoa',
            'timezone' => 'Asia/Kolkata',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'listings_require_approval' => true,
            'products_require_approval' => true,
            'classifieds_require_approval' => true,
            'service_experts_require_approval' => true,
            'listing_expiry_days' => 90,
            'classified_expiry_days' => 30,
            'max_images_per_listing' => 10,
            'max_images_per_product' => 8,
        ];
    }
}
