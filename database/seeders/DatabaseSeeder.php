<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Inquiry;
use App\Models\InquiryStatusLog;
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
            ['name' => 'Commercial', 'icon' => 'shopping-bag', 'status' => 'inactive'],
            ['name' => 'Rent', 'icon' => 'home', 'status' => 'active'],
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

        // 3. Locations (Official Company Locations list)
        $locationsData = [
            [
                'name' => 'Lovina',
                'description' => 'Famous for its black sand beaches, dolphin watching, and calm ocean atmosphere.',
                'image' => 'locations/lovina.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Kaliasem',
                'description' => 'A peaceful coastal area adjacent to Lovina beach, popular for villa development.',
                'image' => 'locations/kaliasem.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Celuk Buluh',
                'description' => 'A beautiful seaside area with direct beach access and calm waters.',
                'image' => 'locations/celuk-buluh.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Banjar',
                'description' => 'A peaceful mountainous area surrounded by lush tropical nature and famous hot springs.',
                'image' => 'locations/banjar.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Anturan',
                'description' => 'Peaceful location close to the beach and local amenities.',
                'image' => 'locations/anturan.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Kalibukbuk',
                'description' => 'Quiet residential area in Lovina with beautiful ocean views.',
                'image' => 'locations/kalibukbuk.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Singaraja',
                'description' => 'The main city of North Bali, offering full city facilities, top schools, and cultural heritage.',
                'image' => 'locations/singaraja.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Kayuputih',
                'description' => 'Lush green hillside area offering panoramic views of the Bali Sea.',
                'image' => 'locations/kayuputih.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sererit',
                'description' => 'A quiet coastal town with traditional markets and beautiful green rice field views.',
                'image' => 'locations/sererit.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Dencarik',
                'description' => 'A quiet village near Lovina with great potential for beachfront property developments.',
                'image' => 'locations/dencarik.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sambangan',
                'description' => 'Known as the Secret Garden of Sambangan, famous for waterfalls and lush valley views.',
                'image' => 'locations/sambangan.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Temukus',
                'description' => 'A serene coastal area known for beautiful beaches and premium luxury ocean villas.',
                'image' => 'locations/temukus.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Ume Anyar',
                'description' => 'A scenic coastal village near Sererit with pristine ocean views and quiet atmosphere.',
                'image' => 'locations/ume-anyar.jpg',
                'is_popular' => false,
                'status' => 'active',
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
                'is_featured' => false,
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
                'location' => 'Kalibukbuk',
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
                'location' => 'Sererit',
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
                'description' => 'Affordable modern tropical home in quiet Sererit residential area. Excellent opportunity for long-term retirement living or holiday home.',
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
                'location' => 'Kaliasem',
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
            [
                'name' => 'Oceanfront Cliff Villa',
                'category' => 'Villa',
                'location' => 'Anturan',
                'price' => 420000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'land_size' => 500,
                'building_size' => 240,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '5500 VA',
                'water_supply' => 'PDAM',
                'views_count' => 128,
                'description' => 'Beautiful modern cliff villa overlooking the beach in Anturan. Peace and luxury combined.',
            ],
            [
                'name' => 'Pemuteran Beachfront Plot',
                'category' => 'Land',
                'location' => 'Ume Anyar',
                'price' => 180000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'land_size' => 1000,
                'building_size' => 0,
                'garage' => 0,
                'swimming_pool' => false,
                'electricity' => 'Available',
                'water_supply' => 'Available',
                'views_count' => 84,
                'description' => 'Stunning beachfront land plot located in Ume Anyar, perfect for a private diving villa.',
            ],
            [
                'name' => 'Sunset Bay Villa',
                'category' => 'Villa',
                'location' => 'Lovina',
                'price' => 450000.00,
                'ownership_type' => 'Freehold',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'land_size' => 720,
                'building_size' => 380,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '7700 VA',
                'water_supply' => 'PDAM',
                'views_count' => 150,
                'description' => 'Spectacular sunset view villa located in the heart of Lovina Beach. Offers modern luxury design, large poolside deck, and beautiful tropical gardens.',
            ],
            [
                'name' => 'Lovina Beachfront Apartment',
                'category' => 'Rent',
                'location' => 'Lovina',
                'price' => 1500.00,
                'ownership_type' => 'Leasehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'land_size' => 120,
                'building_size' => 120,
                'garage' => 1,
                'swimming_pool' => true,
                'electricity' => '4400 VA',
                'water_supply' => 'PDAM',
                'views_count' => 95,
                'description' => 'Beautiful beachfront apartment for rent in Lovina. Fully furnished with stunning sea views and direct access to the beach.',
            ],
            [
                'name' => 'Hilltop View Studio',
                'category' => 'Rent',
                'location' => 'Banjar',
                'price' => 800.00,
                'ownership_type' => 'Leasehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'land_size' => 60,
                'building_size' => 60,
                'garage' => 1,
                'swimming_pool' => false,
                'electricity' => '2200 VA',
                'water_supply' => 'PDAM',
                'views_count' => 64,
                'description' => 'Cozy hilltop studio room for rent in Banjar. Quiet nature surroundings and scenic forest views.',
            ],
            [
                'name' => 'Cozy Town House',
                'category' => 'Rent',
                'location' => 'Singaraja',
                'price' => 1200.00,
                'ownership_type' => 'Leasehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'land_size' => 150,
                'building_size' => 130,
                'garage' => 1,
                'swimming_pool' => false,
                'electricity' => '3500 VA',
                'water_supply' => 'PDAM',
                'views_count' => 78,
                'description' => 'Modern town house for rent in central Singaraja. Close to shops, schools, and local amenities.',
            ],
            [
                'name' => 'Modern Rental Villa',
                'category' => 'Rent',
                'location' => 'Kaliasem',
                'price' => 2500.00,
                'ownership_type' => 'Leasehold',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'land_size' => 250,
                'building_size' => 200,
                'garage' => 2,
                'swimming_pool' => true,
                'electricity' => '5500 VA',
                'water_supply' => 'PDAM',
                'views_count' => 110,
                'description' => 'Luxurious 3-bedroom villa for rent in peaceful Kaliasem. Large swimming pool, landscaped garden, and daily staff service.',
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
                'price' => $propData['price'] * 15000,
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
        $sunsetBayProp = Property::where('name', 'Sunset Bay Villa')->first();
        $tropicalProp = Property::where('name', 'Tropical Bay Villa')->first();
        $familyHomeProp = Property::where('name', 'Singaraja Family Home')->first();

        $inquiries = [
            // Status: new (6 records)
            [
                'customer_name' => 'Graham Whitaker',
                'email' => 'graham.whitaker@example.com',
                'phone' => '+1 (555) 019-8234',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Inquiry regarding Azure Vista Residence',
                'message' => 'Hello, I am looking to purchase a property in North Bali and would like to arrange a private viewing of the Azure Vista Residence next week.',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(2),
            ],
            [
                'customer_name' => 'Ni Luh Pradnyani',
                'email' => 'nl.pradnyani@example.com',
                'phone' => '+62 812 3987 6543',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Viewing schedule for Harmony Pool Retreat',
                'message' => 'Selamat pagi, apakah saya bisa menjadwalkan kunjungan ke villa Harmony Pool Retreat hari Sabtu ini?',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(4),
            ],
            [
                'customer_name' => 'Pieter van Dijk',
                'email' => 'p.vandijk@example.com',
                'phone' => '+31 6 1234 5678',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Questions on Freehold title for Ocean Breeze Estate',
                'message' => 'Beste Lovina Agency, is the freehold certificate ready for transfer on the Ocean Breeze Estate? I am interested in purchasing this villa.',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(6),
            ],
            [
                'customer_name' => 'Elaine Mercer',
                'email' => 'elaine.mercer@example.com',
                'phone' => '+1 (555) 014-9382',
                'property_id' => null,
                'subject' => 'General inquiry about beachfront land plots',
                'message' => 'Hi, I am looking for a beachfront land plot in Lovina or Temukus. Please let me know what options you have available.',
                'source' => 'Contact Us Form',
                'status' => 'new',
                'created_at' => now()->subHours(8),
            ],
            [
                'customer_name' => 'I Made Wiratama',
                'email' => 'made.wiratama@example.com',
                'phone' => '+62 819 9988 7766',
                'property_id' => $sunsetBayProp ? $sunsetBayProp->id : null,
                'subject' => 'Villa Sunset Bay purchase inquiry',
                'message' => 'Halo, saya tertarik dengan Sunset Bay Villa. Apakah harganya masih bisa dinegosiasikan untuk sistem pembayaran tunai?',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(10),
            ],
            [
                'customer_name' => 'Marieke de Vries',
                'email' => 'marieke.devries@example.com',
                'phone' => '+31 6 9876 5432',
                'property_id' => $sunsetProp ? $sunsetProp->id : null,
                'subject' => 'Lease extension terms on Sunset View Villa',
                'message' => 'Hello, I see Sunset View Villa is leasehold. What are the options and costs for extending the lease beyond the initial 25 years?',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(12),
            ],

            // Status: in_progress (6 records)
            [
                'customer_name' => 'Douglas Hartwell',
                'email' => 'douglas.hartwell@example.com',
                'phone' => '+1 (555) 012-3456',
                'property_id' => $serenityProp ? $serenityProp->id : null,
                'subject' => 'Requesting documents for Serenity Hill Villa',
                'message' => 'Hello, please send the building permit (IMB) and land certificates for Serenity Hill Villa for my legal counsel to review.',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(15),
            ],
            [
                'customer_name' => 'Ni Made Suryani',
                'email' => 'made.suryani@example.com',
                'phone' => '+62 878 6123 4567',
                'property_id' => $tropicalProp ? $tropicalProp->id : null,
                'subject' => 'Rental yields for Tropical Bay Villa',
                'message' => 'Selamat siang, saya berencana investasi di Tropical Bay Villa. Bisa minta data history okupansi atau estimasi ROI tahunannya?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(18),
            ],
            [
                'customer_name' => 'Willem van der Meer',
                'email' => 'w.vandermeer@example.com',
                'phone' => '+31 6 4321 8765',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Water and power supply at Harmony Pool Retreat',
                'message' => 'Hello, I want to clarify the water source and backup generator capacity at Harmony Pool Retreat. Is there any issue during the dry season?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(21),
            ],
            [
                'customer_name' => 'Rebecca Callahan',
                'email' => 'rebecca.c@example.com',
                'phone' => '+44 7700 900077',
                'property_id' => null,
                'subject' => 'Retirement villa consulting',
                'message' => 'Hi, my husband and I are planning to retire in North Bali next year. We would like to schedule a call to discuss suitable villas under USD 400,000.',
                'source' => 'Contact Us Form',
                'status' => 'in_progress',
                'created_at' => now()->subDay(),
            ],
            [
                'customer_name' => 'I Wayan Suardika',
                'email' => 'wayan.suardika@example.com',
                'phone' => '+62 811 385 1234',
                'property_id' => $familyHomeProp ? $familyHomeProp->id : null,
                'subject' => 'Tanya legalitas Singaraja Family Home',
                'message' => 'Apakah status tanah Singaraja Family Home sudah SHM (Sertifikat Hak Milik) atas nama pemilik langsung?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subDays(2),
            ],
            [
                'customer_name' => 'Annelies Jansen',
                'email' => 'annelies.j@example.com',
                'phone' => '+31 6 5678 1234',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Furniture package details at Azure Vista',
                'message' => 'Hello, does the asking price for Azure Vista Residence include all the indoor and outdoor furniture shown in the photos?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subDays(3),
            ],

            // Status: responded (5 records)
            [
                'customer_name' => 'Martin Prescott',
                'email' => 'martin.prescott@example.com',
                'phone' => '+1 (555) 018-7241',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Property tax question for Ocean Breeze',
                'message' => 'Hi, what are the annual property maintenance taxes and local community fees for Ocean Breeze Estate?',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(4),
            ],
            [
                'customer_name' => 'Ni Luh Putu Arianti',
                'email' => 'putu.arianti@example.com',
                'phone' => '+62 812 3678 9900',
                'property_id' => null,
                'subject' => 'Looking for investment advice in Sererit',
                'message' => 'Hello, I am interested in building a guest house in Sererit. I would like to consult about local regulations for commercial licensing.',
                'source' => 'Contact Us Form',
                'status' => 'responded',
                'created_at' => now()->subDays(5),
            ],
            [
                'customer_name' => 'Hendrik de Boer',
                'email' => 'hendrik.deboer@example.com',
                'phone' => '+31 6 1122 3344',
                'property_id' => $serenityProp ? $serenityProp->id : null,
                'subject' => 'Access road details at Serenity Hill Villa',
                'message' => 'Beste, is the access road to Serenity Hill Villa paved and wide enough for two cars to pass? Thank you.',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(6),
            ],
            [
                'customer_name' => 'Judith Holloway',
                'email' => 'judith.h@example.com',
                'phone' => '+1 (555) 016-4422',
                'property_id' => $sunsetBayProp ? $sunsetBayProp->id : null,
                'subject' => 'Sunset Bay Villa site inspection',
                'message' => 'Hello, we would like to schedule a physical tour of the Sunset Bay Villa on August 15th around 4 PM. Please let us know if this works.',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(7),
            ],
            [
                'customer_name' => 'I Ketut Ardana',
                'email' => 'ketut.ardana@example.com',
                'phone' => '+62 813 3712 3456',
                'property_id' => $sunsetProp ? $sunsetProp->id : null,
                'subject' => 'Nego harga Sunset View Villa',
                'message' => 'Selamat sore, saya sudah mengunjungi lokasi Sunset View Villa. Apakah ada kelonggaran harga jika termin bayar bertahap 3 kali?',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(8),
            ],

            // Status: closed (5 records)
            [
                'customer_name' => 'Saskia Verhoeven',
                'email' => 'saskia.verhoeven@example.com',
                'phone' => '+31 6 7766 5544',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Survey request for Azure Vista',
                'message' => 'We would like to inspect the building quality of Azure Vista Residence before placing a formal offer.',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(10),
            ],
            [
                'customer_name' => 'Arthur Pendelton',
                'email' => 'arthur.p@example.com',
                'phone' => '+1 (555) 011-8899',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Asking if Ocean Breeze has ocean access',
                'message' => 'Hello, is there a pathway or direct access down to the beach from the Ocean Breeze Estate cliff garden?',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(12),
            ],
            [
                'customer_name' => 'I Nyoman Mahendra',
                'email' => 'nyoman.mahendra@example.com',
                'phone' => '+62 812 4567 8901',
                'property_id' => null,
                'subject' => 'Kemitraan agen properti lokal',
                'message' => 'Saya memiliki beberapa klien lokal yang mencari tanah di Lovina. Apakah ada skema bagi komisi kerja sama agen?',
                'source' => 'Contact Us Form',
                'status' => 'closed',
                'created_at' => now()->subDays(14),
            ],
            [
                'customer_name' => 'Geraldine Kraan',
                'email' => 'g.kraan@example.com',
                'phone' => '+31 6 8899 0011',
                'property_id' => $tropicalProp ? $tropicalProp->id : null,
                'subject' => 'Lease contract draft for Tropical Bay',
                'message' => 'Dear agent, please send the standard lease contract template you use for properties like Tropical Bay Villa.',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(16),
            ],
            [
                'customer_name' => 'Ni Made Asriati',
                'email' => 'made.asriati@example.com',
                'phone' => '+62 819 3344 5566',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Booking confirmation check',
                'message' => 'Halo, saya ingin menanyakan apakah deposit untuk sewa Harmony Pool Retreat sudah masuk ke rekening agen?',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(20),
            ],
        ];

        foreach ($inquiries as $inq) {
            $inquiry = Inquiry::create($inq);

            // Seed initial status log
            InquiryStatusLog::create([
                'inquiry_id' => $inquiry->id,
                'status' => 'new',
                'changed_at' => $inquiry->created_at,
            ]);

            // Add transition logs if status has progressed
            if ($inquiry->status === 'in_progress') {
                InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'in_progress',
                    'changed_at' => $inquiry->created_at->addMinutes(30),
                ]);
            } elseif ($inquiry->status === 'responded') {
                InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'in_progress',
                    'changed_at' => $inquiry->created_at->addMinutes(30),
                ]);
                InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'responded',
                    'changed_at' => $inquiry->created_at->addHours(3),
                ]);
            } elseif ($inquiry->status === 'closed') {
                InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'in_progress',
                    'changed_at' => $inquiry->created_at->addMinutes(30),
                ]);
                InquiryStatusLog::create([
                    'inquiry_id' => $inquiry->id,
                    'status' => 'closed',
                    'changed_at' => $inquiry->created_at->addHours(5),
                ]);
            }
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

        // 11. Property Seeder (Run custom properties list)
        $this->call(PropertySeeder::class);
    }
}
