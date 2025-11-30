<?php

namespace App\View\Composers;

use App\Models\Location;
use Illuminate\View\View;

class LocationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $countries = cache()->remember('global_countries', 3600, function () {
            return Location::where('type', 'country')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        });

        $states = cache()->remember('global_states', 3600, function () {
            return Location::where('type', 'state')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        });

        $cities = cache()->remember('global_cities', 3600, function () {
            return Location::where('type', 'city')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        });

        $view->with([
            'globalCountries' => $countries,
            'globalStates' => $states,
            'globalCities' => $cities,
        ]);
    }
}
