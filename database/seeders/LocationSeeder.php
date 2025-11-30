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
            'type' => 'country',
            'parent_id' => null,
            'status' => 'active',
        ]);

        // State
        $goa = Location::create([
            'name' => 'Goa',
            'type' => 'state',
            'parent_id' => $india->id,
            'status' => 'active',
        ]);

        // Districts
        $northGoa = Location::create([
            'name' => 'North Goa',
            'type' => 'district',
            'parent_id' => $goa->id,
            'status' => 'active',
        ]);

        $southGoa = Location::create([
            'name' => 'South Goa',
            'type' => 'district',
            'parent_id' => $goa->id,
            'status' => 'active',
        ]);

        // North Goa Cities/Towns
        $northGoaCities = [
            'Panaji', 'Mapusa', 'Bicholim', 'Pernem', 'Ponda', 
            'Valpoi', 'Tivim', 'Saligao', 'Anjuna', 'Arambol',
            'Assagao', 'Baga', 'Calangute', 'Candolim', 'Morjim',
            'Siolim', 'Vagator', 'Aldona', 'Reis Magos', 'Nerul'
        ];

        foreach ($northGoaCities as $city) {
            Location::create([
                'name' => $city,
                'type' => 'city',
                'parent_id' => $northGoa->id,
                'status' => 'active',
            ]);
        }

        // South Goa Cities/Towns
        $southGoaCities = [
            'Margao', 'Vasco da Gama', 'Ponda', 'Quepem', 'Curchorem',
            'Sancoale', 'Cortalim', 'Benaulim', 'Colva', 'Cavelossim',
            'Varca', 'Betalbatim', 'Majorda', 'Palolem', 'Agonda',
            'Canacona', 'Cuncolim', 'Loutolim', 'Chandor', 'Raia'
        ];

        foreach ($southGoaCities as $city) {
            Location::create([
                'name' => $city,
                'type' => 'city',
                'parent_id' => $southGoa->id,
                'status' => 'active',
            ]);
        }

        // Popular Beaches (as locations for attractions)
        $beaches = [
            'Anjuna Beach', 'Baga Beach', 'Calangute Beach', 'Candolim Beach',
            'Vagator Beach', 'Morjim Beach', 'Arambol Beach', 'Palolem Beach',
            'Agonda Beach', 'Colva Beach', 'Benaulim Beach', 'Cavelossim Beach',
            'Varca Beach', 'Majorda Beach', 'Miramar Beach', 'Dona Paula Beach'
        ];

        foreach ($beaches as $beach) {
            $district = (str_contains($beach, ['Palolem', 'Agonda', 'Colva', 'Benaulim', 'Cavelossim', 'Varca', 'Majorda'])) 
                ? $southGoa->id 
                : $northGoa->id;

            Location::create([
                'name' => $beach,
                'type' => 'area',
                'parent_id' => $district,
                'status' => 'active',
            ]);
        }
    }
}
