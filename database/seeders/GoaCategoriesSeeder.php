<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class GoaCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Business Listings Categories
        $businessCategories = [
            [
                'name' => 'Hotels & Resorts',
                'icon' => 'fa-hotel',
                'children' => ['Beach Resorts', 'Luxury Hotels', 'Budget Hotels', 'Boutique Hotels', 'Hostels', 'Guest Houses']
            ],
            [
                'name' => 'Restaurants & Cafes',
                'icon' => 'fa-utensils',
                'children' => ['Beach Shacks', 'Fine Dining', 'Seafood Restaurants', 'Goan Cuisine', 'International Cuisine', 'Cafes & Bakeries', 'Bars & Pubs']
            ],
            [
                'name' => 'Water Sports',
                'icon' => 'fa-water',
                'children' => ['Jet Skiing', 'Parasailing', 'Scuba Diving', 'Kayaking', 'Banana Boat Rides', 'Snorkeling']
            ],
            [
                'name' => 'Tours & Travel',
                'icon' => 'fa-bus',
                'children' => ['Tour Operators', 'Taxi Services', 'Bike Rentals', 'Car Rentals', 'Boat Cruises', 'Travel Agencies']
            ],
            [
                'name' => 'Spa & Wellness',
                'icon' => 'fa-spa',
                'children' => ['Ayurvedic Spas', 'Massage Centers', 'Yoga Studios', 'Wellness Centers', 'Beauty Salons']
            ],
            [
                'name' => 'Nightlife & Entertainment',
                'icon' => 'fa-music',
                'children' => ['Night Clubs', 'Beach Clubs', 'Live Music Venues', 'Casinos', 'Beach Parties']
            ],
            [
                'name' => 'Shopping',
                'icon' => 'fa-shopping-bag',
                'children' => ['Flea Markets', 'Handicrafts Stores', 'Souvenir Shops', 'Spice Shops', 'Jewelry Stores', 'Clothing Boutiques']
            ],
            [
                'name' => 'Real Estate',
                'icon' => 'fa-home',
                'children' => ['Property Sales', 'Property Rentals', 'Real Estate Agents', 'Vacation Rentals']
            ],
        ];

        foreach ($businessCategories as $index => $category) {
            $parent = Category::create([
                'name' => $category['name'],
                'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $category['name'])),
                'type' => 'business',
                'icon' => $category['icon'],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => $index + 1,
            ]);

            foreach ($category['children'] as $childIndex => $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $childName)),
                    'type' => 'business',
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'display_order' => $childIndex + 1,
                ]);
            }
        }

        // Job Categories
        $jobCategories = [
            [
                'name' => 'Hospitality & Tourism',
                'icon' => 'fa-concierge-bell',
                'children' => ['Hotel Management', 'Front Desk', 'Housekeeping', 'Chef & Cooks', 'Waiters', 'Tour Guides']
            ],
            [
                'name' => 'Information Technology',
                'icon' => 'fa-laptop',
                'children' => ['Software Development', 'Web Development', 'Digital Marketing', 'IT Support', 'Data Entry']
            ],
            [
                'name' => 'Healthcare',
                'icon' => 'fa-heartbeat',
                'children' => ['Doctors', 'Nurses', 'Pharmacists', 'Lab Technicians', 'Healthcare Assistants']
            ],
            [
                'name' => 'Education',
                'icon' => 'fa-graduation-cap',
                'children' => ['Teachers', 'Tutors', 'School Administration', 'Education Counselors']
            ],
            [
                'name' => 'Sales & Marketing',
                'icon' => 'fa-chart-line',
                'children' => ['Sales Executives', 'Marketing Managers', 'Business Development', 'Customer Service']
            ],
        ];

        foreach ($jobCategories as $index => $category) {
            $parent = Category::create([
                'name' => $category['name'],
                'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $category['name'])),
                'type' => 'job',
                'icon' => $category['icon'],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => $index + 1,
            ]);

            foreach ($category['children'] as $childIndex => $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $childName)),
                    'type' => 'job',
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'display_order' => $childIndex + 1,
                ]);
            }
        }

        // Event Categories
        $eventCategories = [
            ['name' => 'Music Festivals', 'icon' => 'fa-music'],
            ['name' => 'Beach Party Events', 'icon' => 'fa-umbrella-beach'],
            ['name' => 'Cultural Events', 'icon' => 'fa-theater-masks'],
            ['name' => 'Sports Events', 'icon' => 'fa-trophy'],
            ['name' => 'Food Festivals', 'icon' => 'fa-utensils'],
            ['name' => 'Art Exhibitions', 'icon' => 'fa-palette'],
        ];

        foreach ($eventCategories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => strtolower(str_replace([' '], ['-'], $category['name'])),
                'type' => 'event',
                'icon' => $category['icon'],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => $index + 1,
            ]);
        }

        // Product Categories
        $productCategories = [
            [
                'name' => 'Handicrafts & Souvenirs',
                'icon' => 'fa-gift',
                'children' => ['Wooden Crafts', 'Pottery', 'Paintings', 'Decorative Items']
            ],
            [
                'name' => 'Goan Specialties',
                'icon' => 'fa-pepper-hot',
                'children' => ['Cashews', 'Spices', 'Feni (Local Spirit)', 'Kokum Products', 'Pickles']
            ],
            [
                'name' => 'Fashion & Accessories',
                'icon' => 'fa-tshirt',
                'children' => ['Beachwear', 'Jewelry', 'Bags', 'Footwear']
            ],
        ];

        foreach ($productCategories as $index => $category) {
            $parent = Category::create([
                'name' => $category['name'],
                'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $category['name'])),
                'type' => 'product',
                'icon' => $category['icon'],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => $index + 1,
            ]);

            if (isset($category['children'])) {
                foreach ($category['children'] as $childIndex => $childName) {
                    Category::create([
                        'name' => $childName,
                        'slug' => strtolower(str_replace(['(', ')', ' '], ['', '', '-'], $childName)),
                        'type' => 'product',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'display_order' => $childIndex + 1,
                    ]);
                }
            }
        }

        // Service Categories
        $serviceCategories = [
            ['name' => 'Photography & Videography', 'icon' => 'fa-camera'],
            ['name' => 'Event Planning', 'icon' => 'fa-calendar-check'],
            ['name' => 'Home Services', 'icon' => 'fa-tools'],
            ['name' => 'Legal Services', 'icon' => 'fa-balance-scale'],
            ['name' => 'Financial Services', 'icon' => 'fa-coins'],
        ];

        foreach ($serviceCategories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => strtolower(str_replace(['&', ' '], ['and', '-'], $category['name'])),
                'type' => 'service',
                'icon' => $category['icon'],
                'is_active' => true,
                'is_featured' => true,
                'display_order' => $index + 1,
            ]);
        }

        $this->command->info('Goa categories seeded successfully!');
    }
}
