<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Country
        $india = Location::create([
            'name' => 'India',
            'slug' => 'india',
            'type' => 'country',
            'parent_id' => null,
            'is_active' => true,
        ]);

        // State
        $goa = Location::create([
            'name' => 'Goa',
            'slug' => 'goa',
            'type' => 'state',
            'parent_id' => $india->id,
            'is_active' => true,
        ]);

        // Districts
        $northGoa = Location::create([
            'name' => 'North Goa',
            'slug' => 'north-goa',
            'type' => 'district',
            'parent_id' => $goa->id,
            'is_active' => true,
        ]);

        $southGoa = Location::create([
            'name' => 'South Goa',
            'slug' => 'south-goa',
            'type' => 'district',
            'parent_id' => $goa->id,
            'is_active' => true,
        ]);

        // North Goa Cities/Towns
        $northGoaCities = [
            'Panaji', 'Mapusa', 'Bicholim', 'Pernem', 
            'Valpoi', 'Tivim', 'Saligao', 'Anjuna', 'Arambol',
            'Assagao', 'Baga', 'Calangute', 'Candolim', 'Morjim',
            'Siolim', 'Vagator', 'Aldona', 'Reis Magos', 'Nerul'
        ];

        foreach ($northGoaCities as $city) {
            Location::create([
                'name' => $city,
                'slug' => strtolower(str_replace(' ', '-', $city)),
                'type' => 'city',
                'parent_id' => $northGoa->id,
                'is_active' => true,
            ]);
        }

        // South Goa Cities/Towns (removed Ponda - duplicate, Benaulim/Colva/Cavelossim/Varca/Betalbatim/Majorda/Palolem/Agonda as they're beaches in GoaLocationsSeeder)
        $southGoaCities = [
            'Margao', 'Vasco da Gama', 'Quepem', 'Curchorem',
            'Sancoale', 'Cortalim', 'Canacona', 'Cuncolim', 'Loutolim', 'Chandor', 'Raia'
        ];

        foreach ($southGoaCities as $city) {
            Location::create([
                'name' => $city,
                'slug' => strtolower(str_replace(' ', '-', $city)),
                'type' => 'city',
                'parent_id' => $southGoa->id,
                'is_active' => true,
            ]);
        }

        // Additional locations
        $additionalLocations = [
            'Miramar Beach' => $northGoa->id,
            'Dona Paula Beach' => $northGoa->id,
        ];

        foreach ($additionalLocations as $location => $parentId) {
            Location::create([
                'name' => $location,
                'slug' => strtolower(str_replace(' ', '-', $location)),
                'type' => 'area',
                'parent_id' => $parentId,
                'is_active' => true,
            ]);
        }
    }
}
