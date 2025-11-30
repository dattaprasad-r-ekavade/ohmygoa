<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Business Categories
        $businessCategories = [
            'Restaurants & Cafes' => ['Fine Dining', 'Casual Dining', 'Cafes', 'Bars & Pubs', 'Beach Shacks', 'Fast Food', 'Bakeries'],
            'Hotels & Accommodation' => ['Luxury Hotels', 'Budget Hotels', 'Beach Resorts', 'Hostels', 'Guest Houses', 'Homestays', 'Villas'],
            'Travel & Tourism' => ['Travel Agencies', 'Tour Operators', 'Car Rentals', 'Bike Rentals', 'Water Sports', 'Cruise Services'],
            'Real Estate' => ['Property Sales', 'Property Rentals', 'Real Estate Agents', 'Property Management', 'Commercial Properties'],
            'Healthcare' => ['Hospitals', 'Clinics', 'Pharmacies', 'Diagnostic Centers', 'Dental Clinics', 'Physiotherapy'],
            'Education' => ['Schools', 'Colleges', 'Training Institutes', 'Coaching Classes', 'Language Schools'],
            'Shopping' => ['Clothing', 'Electronics', 'Handicrafts', 'Jewelry', 'Books', 'Groceries', 'Souvenirs'],
            'Beauty & Wellness' => ['Salons', 'Spas', 'Gyms', 'Yoga Centers', 'Beauty Parlours', 'Massage Centers'],
            'Entertainment' => ['Nightclubs', 'Casinos', 'Cinema Halls', 'Event Venues', 'Gaming Zones'],
            'Services' => ['Home Services', 'Repair Services', 'Cleaning Services', 'Event Management', 'Photography'],
            'Automotive' => ['Car Dealers', 'Bike Dealers', 'Service Centers', 'Spare Parts', 'Car Wash'],
            'Food & Beverages' => ['Grocery Stores', 'Wine Shops', 'Organic Stores', 'Seafood Markets'],
        ];

        foreach ($businessCategories as $parent => $children) {
            $parentCategory = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'type' => 'business',
                'parent_id' => null,
                'is_active' => true,
                'display_order' => 0,
            ]);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'type' => 'business',
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ]);
            }
        }

        // Event Categories
        $eventCategories = [
            'Music & Concerts' => ['Live Music', 'DJ Nights', 'Classical Concerts', 'Rock Shows'],
            'Food & Drink' => ['Food Festivals', 'Wine Tasting', 'Cooking Classes', 'Food Tours'],
            'Arts & Culture' => ['Art Exhibitions', 'Theatre', 'Dance Performances', 'Cultural Festivals'],
            'Sports & Fitness' => ['Yoga Retreats', 'Marathon', 'Water Sports Events', 'Beach Volleyball'],
            'Business & Networking' => ['Conferences', 'Workshops', 'Seminars', 'Networking Events'],
            'Festivals' => ['Carnival', 'Shigmo', 'Christmas', 'New Year', 'Diwali Celebrations'],
        ];

        foreach ($eventCategories as $parent => $children) {
            $parentCategory = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'type' => 'event',
                'parent_id' => null,
                'is_active' => true,
                'display_order' => 0,
            ]);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'type' => 'event',
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ]);
            }
        }

        // Job Categories
        $jobCategories = [
            'Hospitality' => ['Hotel Management', 'Chef', 'Waiter/Waitress', 'Housekeeping', 'Front Desk'],
            'Tourism' => ['Tour Guide', 'Travel Consultant', 'Driver', 'Tour Manager'],
            'IT & Technology' => ['Software Developer', 'Web Designer', 'Digital Marketing', 'Data Analyst'],
            'Sales & Marketing' => ['Sales Executive', 'Marketing Manager', 'Business Development'],
            'Healthcare' => ['Doctor', 'Nurse', 'Pharmacist', 'Medical Representative'],
            'Education' => ['Teacher', 'Professor', 'Trainer', 'Tutor'],
            'Real Estate' => ['Property Agent', 'Sales Manager', 'Marketing Executive'],
        ];

        foreach ($jobCategories as $parent => $children) {
            $parentCategory = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'type' => 'job',
                'parent_id' => null,
                'is_active' => true,
                'display_order' => 0,
            ]);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'type' => 'job',
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ]);
            }
        }

        // Product Categories
        $productCategories = [
            'Electronics' => ['Mobiles', 'Laptops', 'Cameras', 'Audio Systems'],
            'Fashion' => ['Clothing', 'Footwear', 'Accessories', 'Jewelry'],
            'Home & Living' => ['Furniture', 'Home Decor', 'Kitchen Appliances', 'Bedding'],
            'Sports & Outdoors' => ['Sports Equipment', 'Camping Gear', 'Fitness Equipment'],
            'Books & Media' => ['Books', 'Music', 'Movies', 'Magazines'],
            'Handicrafts' => ['Goan Handicrafts', 'Pottery', 'Art Pieces', 'Traditional Items'],
        ];

        foreach ($productCategories as $parent => $children) {
            $parentCategory = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'type' => 'product',
                'parent_id' => null,
                'is_active' => true,
                'display_order' => 0,
            ]);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'type' => 'product',
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ]);
            }
        }

        // Service Expert Categories
        $serviceCategories = [
            'Home Services' => ['Plumber', 'Electrician', 'Carpenter', 'Painter', 'AC Repair'],
            'Professional Services' => ['Lawyer', 'Accountant', 'Architect', 'Interior Designer'],
            'Personal Services' => ['Personal Trainer', 'Yoga Instructor', 'Beautician', 'Massage Therapist'],
            'Creative Services' => ['Photographer', 'Videographer', 'Graphic Designer', 'Content Writer'],
            'Event Services' => ['Event Planner', 'Caterer', 'DJ', 'Decorator'],
        ];

        foreach ($serviceCategories as $parent => $children) {
            $parentCategory = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'type' => 'service',
                'parent_id' => null,
                'is_active' => true,
                'display_order' => 0,
            ]);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'type' => 'service',
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ]);
            }
        }
    }
}
