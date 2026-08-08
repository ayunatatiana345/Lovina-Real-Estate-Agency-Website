<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Inquiry;
use App\Models\CompanySetting;
use App\Models\CmsContent;
use App\Models\Benefit;
use App\Models\Statistic;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@lovinanorthbali.com'],
            [
                'name' => 'Lovina Agency Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Property Categories
        $categoriesData = [
            ['name' => 'Villa', 'icon' => 'home', 'status' => 'active'],
            ['name' => 'House', 'icon' => 'building', 'status' => 'active'],
            ['name' => 'Land', 'icon' => 'map-pin', 'status' => 'active'],
            ['name' => 'Hotel', 'icon' => 'briefcase', 'status' => 'active'],
            ['name' => 'Restaurant', 'icon' => 'coffee', 'status' => 'active'],
            ['name' => 'Commercial', 'icon' => 'shopping-bag', 'status' => 'active'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = PropertyCategory::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'status' => $cat['status'],
            ]);
        }

        // 3. Locations (Matching reference images & prompt: Lovina, Temukus, Singaraja, Seririt, Banjar, Gerokgak)
        $locationsData = [
            [
                'name' => 'Lovina',
                'description' => 'Famous for its black sand beaches, dolphin watching, and calm ocean atmosphere.',
                'image' => 'locations/lovina.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Temukus',
                'description' => 'A serene coastal area known for beautiful beaches and premium luxury ocean villas.',
                'image' => 'locations/temukus.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Singaraja',
                'description' => 'The main city of North Bali, offering full city facilities, top schools, and cultural heritage.',
                'image' => 'locations/singaraja.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Seririt',
                'description' => 'A quiet coastal town with traditional markets and beautiful green rice field views.',
                'image' => 'locations/seririt.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Banjar',
                'description' => 'A peaceful mountainous area surrounded by lush tropical nature and famous hot springs.',
                'image' => 'locations/banjar.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Gerokgak',
                'description' => 'A rapidly growing area in West-North Bali with large land opportunities and scenic bay views.',
                'image' => 'locations/gerokgak.jpg',
                'is_popular' => false,
                'status' => 'inactive',
            ],
        ];

        $locations = [];
        foreach ($locationsData as $loc) {
            $locations[$loc['name']] = Location::create([
                'name' => $loc['name'],
                'slug' => Str::slug($loc['name']),
                'description' => $loc['description'],
                'image' => $loc['image'],
                'is_popular' => $loc['is_popular'],
                'status' => $loc['status'],
            ]);
        }

        // 4. Properties (Realistic North Bali listings matching requirements)
        $propertiesData = [
            [
                'name' => 'Azure Vista Residence',
                'category' => 'Villa',
                'location' => 'Lovina',
                'price' => 450000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'land_size' => 450,
                'building_size' => 280,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '7700 VA',
                'water_supply' => 'PDAM & Deep Well',
                'views_count' => 326,
                'description' => 'Stunning oceanfront luxury villa located in prime Lovina. Features panoramic ocean sunset views, private infinity pool, fully furnished modern minimalist interior, spacious tropical garden, and direct beach access.',
            ],
            [
                'name' => 'Harmony Pool Retreat',
                'category' => 'Villa',
                'location' => 'Banjar',
                'price' => 320000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'land_size' => 600,
                'building_size' => 320,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '5500 VA',
                'water_supply' => 'Fresh Spring Water',
                'views_count' => 287,
                'description' => 'Peaceful sanctuary set amidst lush green hills near Banjar Hot Springs. Features open-plan living rooms, large private pool, gazebo, and manicured tropical gardens.',
            ],
            [
                'name' => 'Ocean Breeze Estate',
                'category' => 'Villa',
                'location' => 'Temukus',
                'price' => 580000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 5,
                'bathrooms' => 4,
                'land_size' => 850,
                'building_size' => 420,
                'garage' => 3,
                'swimming_pool' => true,
                'electricity' => '11000 VA',
                'water_supply' => 'PDAM',
                'views_count' => 256,
                'description' => 'Ultra-exclusive cliffside estate overlooking the tranquil waters of Temukus. Designed by renowned Balinese architects with sustainable teak wood and stone.',
            ],
            [
                'name' => 'Sunset View Villa',
                'category' => 'Villa',
                'location' => 'Lovina',
                'price' => 275000.00,
                'ownership_type' => 'Leasehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'land_size' => 300,
                'building_size' => 180,
                'garage' => 1,
                'swimming_pool' => true,
                'electricity' => '4400 VA',
                'water_supply' => 'PDAM',
                'views_count' => 221,
                'description' => 'Charming 2-bedroom vacation villa with solid rental yields. Convenient location 5 minutes walk to Lovina center, restaurants, and dolphin tour points.',
            ],
            [
                'name' => 'Tropical Bay Villa',
                'category' => 'Villa',
                'location' => 'Seririt',
                'price' => 210000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'land_size' => 400,
                'building_size' => 200,
                'garage' => 1,
                'swimming_pool' => true,
                'electricity' => '3500 VA',
                'water_supply' => 'Deep Well',
                'views_count' => 198,
                'description' => 'Affordable modern tropical home in quiet Seririt residential area. Excellent opportunity for long-term retirement living or holiday home.',
            ],
            [
                'name' => 'Serenity Hill Villa',
                'category' => 'Villa',
                'location' => 'Singaraja',
                'price' => 390000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'land_size' => 550,
                'building_size' => 310,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '5500 VA',
                'water_supply' => 'PDAM',
                'views_count' => 174,
                'description' => 'Hillside villa commanding sweeping views over Singaraja harbor and sea. Cool climate, breeze, and top-tier security.',
            ],
            [
                'name' => 'Prime Beachfront Land Plot',
                'category' => 'Land',
                'location' => 'Lovina',
                'price' => 195000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'land_size' => 1200,
                'building_size' => 0,
                'garage' => 0,
                'swimming_pool' => false,
                'electricity' => 'Available on street',
                'water_supply' => 'Available on street',
                'views_count' => 142,
                'description' => 'Rare flat beachfront land suitable for boutique resort, luxury villa compound, or commercial beach club project in Lovina.',
            ],
            [
                'name' => 'Sunset Ridge House Draft',
                'category' => 'House',
                'location' => 'Singaraja',
                'price' => 150000.00,
                'ownership_type' => 'Freehold',
                'status' => 'draft',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'land_size' => 250,
                'building_size' => 150,
                'garage' => 1,
                'swimming_pool' => false,
                'electricity' => '2200 VA',
                'water_supply' => 'PDAM',
                'views_count' => 45,
                'description' => 'Draft listing for a suburban family home in Singaraja town.',
            ],
        ];

        foreach ($propertiesData as $propData) {
            $cat = $categories[$propData['category']];
            $loc = $locations[$propData['location']];

            $property = Property::create([
                'name' => $propData['name'],
                'slug' => Str::slug($propData['name']),
                'category_id' => $cat->id,
                'location_id' => $loc->id,
                'price' => $propData['price'],
                'ownership_type' => $propData['ownership_type'],
                'status' => $propData['status'],
                'is_featured' => $propData['is_featured'],
                'description' => $propData['description'],
                'bedrooms' => $propData['bedrooms'],
                'bathrooms' => $propData['bathrooms'],
                'land_size' => $propData['land_size'],
                'building_size' => $propData['building_size'],
                'garage' => $propData['garage'],
                'swimming_pool' => $propData['swimming_pool'],
                'electricity' => $propData['electricity'],
                'water_supply' => $propData['water_supply'],
                'views_count' => $propData['views_count'],
            ]);

            // Add placeholder images
            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => 'properties/sample-' . strtolower(str_replace(' ', '-', $propData['category'])) . '-1.jpg',
                'is_cover' => true,
                'sort_order' => 1,
            ]);

            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => 'properties/sample-' . strtolower(str_replace(' ', '-', $propData['category'])) . '-2.jpg',
                'is_cover' => false,
                'sort_order' => 2,
            ]);
        }

        // 5. Inquiries (Matching dashboard recent inquiries table from prompt reference image 5)
        $azureProp = Property::where('name', 'Azure Vista Residence')->first();
        $harmonyProp = Property::where('name', 'Harmony Pool Retreat')->first();
        $oceanProp = Property::where('name', 'Ocean Breeze Estate')->first();
        $serenityProp = Property::where('name', 'Serenity Hill Villa')->first();
        $sunsetProp = Property::where('name', 'Sunset View Villa')->first();

        $inquiries = [
            [
                'customer_name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+62 812 3456 7890',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Inquiry regarding Azure Vista Residence',
                'message' => 'Hello, I am interested in viewing Azure Vista Residence next week. Is it available for private inspection on Tuesday?',
                'status' => 'new',
                'created_at' => now()->subHours(2),
            ],
            [
                'customer_name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'phone' => '+62 815 9676 5432',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Investment inquiry for Harmony Pool Retreat',
                'message' => 'Could you please send me the expected rental yield history for Harmony Pool Retreat in Banjar?',
                'status' => 'new',
                'created_at' => now()->subHours(4),
            ],
            [
                'customer_name' => 'Michael Brown',
                'email' => 'm.brown@example.com',
                'phone' => '+62 812 2345 6780',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Price Negotiation Ocean Breeze Estate',
                'message' => 'We are very interested in Ocean Breeze Estate. Is the asking price negotiable for cash buyers?',
                'status' => 'in_progress',
                'created_at' => now()->subHours(8),
            ],
            [
                'customer_name' => 'Emma Wilson',
                'email' => 'emma.w@example.com',
                'phone' => '+62 811 2233 4455',
                'property_id' => $serenityProp ? $serenityProp->id : null,
                'subject' => 'Freehold Ownership Details',
                'message' => 'I would like to clarify the exact land title certificate type for Serenity Hill Villa.',
                'status' => 'in_progress',
                'created_at' => now()->subHours(12),
            ],
            [
                'customer_name' => 'David Lee',
                'email' => 'david.lee@example.com',
                'phone' => '+62 878 1234 3344',
                'property_id' => $sunsetProp ? $sunsetProp->id : null,
                'subject' => 'Lease Extension Options',
                'message' => 'Hi, can the 25-year lease on Sunset View Villa be extended prior to purchase contract sign?',
                'status' => 'responded',
                'created_at' => now()->subDay(),
            ],
        ];

        foreach ($inquiries as $inq) {
            Inquiry::create($inq);
        }

        // 6. Company Settings
        CompanySetting::getSettings();

        // 7. Benefits (Why Choose Us)
        $benefits = [
            [
                'page' => 'homepage',
                'title' => 'North Bali Specialists',
                'description' => 'Over 15 years of exclusive focus and deep expertise in Lovina and North Bali property market.',
                'icon' => 'compass',
                'sort_order' => 1,
            ],
            [
                'page' => 'homepage',
                'title' => 'Verified Legal Titles',
                'description' => 'Every listing undergoes rigorous legal verification and land title checking before publishing.',
                'icon' => 'shield-check',
                'sort_order' => 2,
            ],
            [
                'page' => 'homepage',
                'title' => 'Transparent Pricing',
                'description' => 'Direct owner prices with zero hidden markups or surprise fees for international buyers.',
                'icon' => 'tag',
                'sort_order' => 3,
            ],
            [
                'page' => 'homepage',
                'title' => 'Full Ownership Support',
                'description' => 'End-to-end guidance from initial viewing, notary coordination, tax setup, to villa management.',
                'icon' => 'users',
                'sort_order' => 4,
            ],
        ];

        foreach ($benefits as $b) {
            Benefit::create($b);
        }

        // 8. Statistics
        $statistics = [
            ['page' => 'homepage', 'number' => '120+', 'label' => 'Properties Listed', 'icon' => 'home', 'is_visible' => true, 'sort_order' => 1],
            ['page' => 'homepage', 'number' => '15+', 'label' => 'Years Experience', 'icon' => 'award', 'is_visible' => true, 'sort_order' => 2],
            ['page' => 'homepage', 'number' => '450+', 'label' => 'Happy Clients', 'icon' => 'smile', 'is_visible' => true, 'sort_order' => 3],
            ['page' => 'homepage', 'number' => '99%', 'label' => 'Customer Satisfaction', 'icon' => 'star', 'is_visible' => true, 'sort_order' => 4],
        ];

        foreach ($statistics as $stat) {
            Statistic::create($stat);
        }

        // 9. CMS Contents Default
        CmsContent::create([
            'page' => 'homepage',
            'section_key' => 'hero',
            'content' => [
                'heading' => 'Discover Premier Luxury Real Estate in Beautiful North Bali',
                'subheading' => 'Explore beachfront luxury villas, ocean view land plots, and prime investments in Lovina, Temukus, and Singaraja.',
                'background_image' => 'cms/hero-bg.jpg',
            ],
        ]);

        CmsContent::create([
            'page' => 'homepage',
            'section_key' => 'cta',
            'content' => [
                'heading' => 'Ready to Find Your Dream Property in North Bali?',
                'description' => 'Speak directly with our experienced property advisors today and schedule a private villa inspection.',
                'button_text' => 'Contact Us Today',
                'button_link' => '/contact',
            ],
        ]);

        CmsContent::create([
            'page' => 'about_us',
            'section_key' => 'story',
            'content' => [
                'title' => 'Our Story',
                'description' => 'Founded in Lovina, PT Lovina North Bali Real Estate Agency has established itself as the leading property agency dedicated to North Bali real estate. We specialize in luxury villas, residential homes, beachfront land plots, and commercial opportunities for both local and foreign buyers.',
                'vision' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
                'mission' => [
                    'Deliver uncompromised legal integrity and title verification for every transaction.',
                    'Provide personalized consultation tailored to international buyer requirements.',
                    'Promote sustainable, community-respecting property developments across Buleleng Regency.',
                ],
            ],
        ]);

        // 10. Activity Logs
        ActivityLog::create(['user_id' => $admin->id, 'description' => 'Published new property: Azure Vista Residence']);
        ActivityLog::create(['user_id' => $admin->id, 'description' => 'Updated company contact settings']);
        ActivityLog::create(['user_id' => $admin->id, 'description' => 'Added new location: Temukus']);
    }
}
