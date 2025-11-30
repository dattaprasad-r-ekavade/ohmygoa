<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class SettingsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $settings = cache()->remember('global_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        $view->with('siteSettings', $settings);
    }
}
