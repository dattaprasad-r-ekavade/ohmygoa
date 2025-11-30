<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class GoaLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // India
        $india = Location::create([
            'name' => 'India',
            'slug' => 'india',
            'type' => 'country',
            'is_active' => true,
            'is_popular' => true,
            'display_order' => 1,
        ]);

        // Goa State
        $goa = Location::create([
            'name' => 'Goa',
            'slug' => 'goa',
            'type' => 'state',
            'parent_id' => $india->id,
            'latitude' => 15.2993,
            'longitude' => 74.1240,
            'is_active' => true,
            'is_popular' => true,
            'display_order' => 1,
        ]);

        // North Goa District
        $northGoa = Location::create([
            'name' => 'North Goa',
            'slug' => 'north-goa',
            'type' => 'city',
            'parent_id' => $goa->id,
            'latitude' => 15.5561,
            'longitude' => 73.7636,
            'is_active' => true,
            'is_popular' => true,
            'display_order' => 1,
        ]);

        // South Goa District
        $southGoa = Location::create([
            'name' => 'South Goa',
            'slug' => 'south-goa',
            'type' => 'city',
            'parent_id' => $goa->id,
            'latitude' => 15.1679,
            'longitude' => 74.0148,
            'is_active' => true,
            'is_popular' => true,
            'display_order' => 2,
        ]);

        // North Goa - Popular Areas
        $northGoaAreas = [
            ['name' => 'Panaji', 'latitude' => 15.4909, 'longitude' => 73.8278, 'popular' => true],
            ['name' => 'Mapusa', 'latitude' => 15.5901, 'longitude' => 73.8089, 'popular' => true],
            ['name' => 'Calangute', 'latitude' => 15.5436, 'longitude' => 73.7551, 'popular' => true],
            ['name' => 'Baga', 'latitude' => 15.5560, 'longitude' => 73.7517, 'popular' => true],
            ['name' => 'Candolim', 'latitude' => 15.5186, 'longitude' => 73.7622, 'popular' => true],
            ['name' => 'Anjuna', 'latitude' => 15.5735, 'longitude' => 73.7401, 'popular' => true],
            ['name' => 'Vagator', 'latitude' => 15.5991, 'longitude' => 73.7344, 'popular' => true],
            ['name' => 'Morjim', 'latitude' => 15.6311, 'longitude' => 73.7348, 'popular' => true],
            ['name' => 'Arambol', 'latitude' => 15.6868, 'longitude' => 73.7058, 'popular' => true],
            ['name' => 'Sinquerim', 'latitude' => 15.4897, 'longitude' => 73.7613, 'popular' => false],
            ['name' => 'Nerul', 'latitude' => 15.5575, 'longitude' => 73.7528, 'popular' => false],
            ['name' => 'Siolim', 'latitude' => 15.6000, 'longitude' => 73.7667, 'popular' => false],
            ['name' => 'Assagao', 'latitude' => 15.5833, 'longitude' => 73.7667, 'popular' => false],
            ['name' => 'Porvorim', 'latitude' => 15.5333, 'longitude' => 73.8167, 'popular' => true],
            ['name' => 'Reis Magos', 'latitude' => 15.5000, 'longitude' => 73.8167, 'popular' => false],
        ];

        foreach ($northGoaAreas as $index => $area) {
            Location::create([
                'name' => $area['name'],
                'slug' => strtolower(str_replace(' ', '-', $area['name'])),
                'type' => 'area',
                'parent_id' => $northGoa->id,
                'latitude' => $area['latitude'],
                'longitude' => $area['longitude'],
                'is_active' => true,
                'is_popular' => $area['popular'],
                'display_order' => $index + 1,
            ]);
        }

        // South Goa - Popular Areas
        $southGoaAreas = [
            ['name' => 'Margao', 'latitude' => 15.2708, 'longitude' => 73.9528, 'popular' => true],
            ['name' => 'Colva', 'latitude' => 15.2796, 'longitude' => 73.9114, 'popular' => true],
            ['name' => 'Benaulim', 'latitude' => 15.2553, 'longitude' => 73.9294, 'popular' => true],
            ['name' => 'Varca', 'latitude' => 15.2258, 'longitude' => 73.9394, 'popular' => true],
            ['name' => 'Cavelossim', 'latitude' => 15.1667, 'longitude' => 73.9389, 'popular' => true],
            ['name' => 'Mobor', 'latitude' => 15.1536, 'longitude' => 73.9397, 'popular' => true],
            ['name' => 'Palolem', 'latitude' => 15.0100, 'longitude' => 74.0233, 'popular' => true],
            ['name' => 'Agonda', 'latitude' => 15.0508, 'longitude' => 74.0033, 'popular' => true],
            ['name' => 'Betalbatim', 'latitude' => 15.2667, 'longitude' => 73.9167, 'popular' => false],
            ['name' => 'Majorda', 'latitude' => 15.2833, 'longitude' => 73.9333, 'popular' => false],
            ['name' => 'Utorda', 'latitude' => 15.2917, 'longitude' => 73.9250, 'popular' => false],
            ['name' => 'Vasco da Gama', 'latitude' => 15.3989, 'longitude' => 73.8158, 'popular' => true],
            ['name' => 'Bogmalo', 'latitude' => 15.3667, 'longitude' => 73.8333, 'popular' => false],
            ['name' => 'Canacona', 'latitude' => 15.0083, 'longitude' => 74.0500, 'popular' => false],
            ['name' => 'Patnem', 'latitude' => 15.0042, 'longitude' => 74.0308, 'popular' => false],
        ];

        foreach ($southGoaAreas as $index => $area) {
            Location::create([
                'name' => $area['name'],
                'slug' => strtolower(str_replace(' ', '-', $area['name'])),
                'type' => 'area',
                'parent_id' => $southGoa->id,
                'latitude' => $area['latitude'],
                'longitude' => $area['longitude'],
                'is_active' => true,
                'is_popular' => $area['popular'],
                'display_order' => $index + 1,
            ]);
        }

        $this->command->info('Goa locations seeded successfully!');
    }
}
