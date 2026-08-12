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
            // Status: new (3 records)
            [
                'customer_name' => 'Thomas Alexander Reed',
                'email' => 'thomas.reed78@gmail.com',
                'phone' => '+1 (555) 019-8234',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Inquiry regarding Azure Vista Residence',
                'message' => 'Hello, I am looking to purchase a property in North Bali and would like to arrange a private viewing of the Azure Vista Residence next week.',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(2),
            ],
            [
                'customer_name' => 'Ni Luh Made Ayu Prameswari',
                'email' => 'ayu.prameswari@gmail.com',
                'phone' => '+62 812-3987-6543',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Viewing schedule for Harmony Pool Retreat',
                'message' => 'Selamat pagi, apakah saya bisa menjadwalkan kunjungan ke villa Harmony Pool Retreat hari Sabtu ini?',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(4),
            ],
            [
                'customer_name' => 'Pieter Willem van der Linden',
                'email' => 'pietervdl.linden@icloud.com',
                'phone' => '+31 6 1234 5678',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Questions on Freehold title for Ocean Breeze Estate',
                'message' => 'Beste Lovina Agency, is the freehold certificate ready for transfer on the Ocean Breeze Estate? I am interested in purchasing this villa.',
                'source' => 'Property Detail Page',
                'status' => 'new',
                'created_at' => now()->subHours(6),
            ],

            // Status: in_progress (3 records)
            [
                'customer_name' => 'Arthur James Pendelton',
                'email' => 'arthur.pendelton53@gmail.com',
                'phone' => '+44 7700 900456',
                'property_id' => $serenityProp ? $serenityProp->id : null,
                'subject' => 'Requesting documents for Serenity Hill Villa',
                'message' => 'Hello, please send the building permit (IMB) and land certificates for Serenity Hill Villa for my legal counsel to review.',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(15),
            ],
            [
                'customer_name' => 'Ni Putu Ratih Lestari',
                'email' => 'ratih.lestari22@gmail.com',
                'phone' => '+62 878-6123-4567',
                'property_id' => $tropicalProp ? $tropicalProp->id : null,
                'subject' => 'Rental yields for Tropical Bay Villa',
                'message' => 'Selamat siang, saya berencana investasi di Tropical Bay Villa. Bisa minta data history okupansi atau estimasi ROI tahunannya?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(18),
            ],
            [
                'customer_name' => 'Hendrik Jan de Boer',
                'email' => 'hendrik.deboer@icloud.com',
                'phone' => '+31 6 4321 8765',
                'property_id' => $harmonyProp ? $harmonyProp->id : null,
                'subject' => 'Water and power supply at Harmony Pool Retreat',
                'message' => 'Hello, I want to clarify the water source and backup generator capacity at Harmony Pool Retreat. Is there any issue during the dry season?',
                'source' => 'Property Detail Page',
                'status' => 'in_progress',
                'created_at' => now()->subHours(21),
            ],

            // Status: responded (3 records)
            [
                'customer_name' => 'Jean-Pierre Dubois',
                'email' => 'jpdubois.81@gmail.com',
                'phone' => '+33 6 12 34 56 78',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Property tax question for Ocean Breeze',
                'message' => 'Hi, what are the annual property maintenance taxes and local community fees for Ocean Breeze Estate?',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(4),
            ],
            [
                'customer_name' => 'Komang Ayu Sri Wahyuni',
                'email' => 'komang.sri87@gmail.com',
                'phone' => '+62 812-3678-9900',
                'property_id' => null,
                'subject' => 'Looking for investment advice in Sererit',
                'message' => 'Hello, I am interested in building a guest house in Sererit. I would like to consult about local regulations for commercial licensing.',
                'source' => 'Contact Us Form',
                'status' => 'responded',
                'created_at' => now()->subDays(5),
            ],
            [
                'customer_name' => 'Yosef Andreas Wibowo',
                'email' => 'yosef.wibowo31@gmail.com',
                'phone' => '+62 811-385-1234',
                'property_id' => $serenityProp ? $serenityProp->id : null,
                'subject' => 'Access road details at Serenity Hill Villa',
                'message' => 'Beste, is the access road to Serenity Hill Villa paved and wide enough for two cars to pass? Thank you.',
                'source' => 'Property Detail Page',
                'status' => 'responded',
                'created_at' => now()->subDays(6),
            ],

            // Status: closed (3 records)
            [
                'customer_name' => 'Siti Aminah Rahmawati',
                'email' => 'aminah.rahmawati@icloud.com',
                'phone' => '+62 821-4567-9032',
                'property_id' => $azureProp ? $azureProp->id : null,
                'subject' => 'Survey request for Azure Vista',
                'message' => 'We would like to inspect the building quality of Azure Vista Residence before placing a formal offer.',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(10),
            ],
            [
                'customer_name' => 'I Made Raka Pranata',
                'email' => 'made.raka78@gmail.com',
                'phone' => '+62 819-9988-7766',
                'property_id' => $oceanProp ? $oceanProp->id : null,
                'subject' => 'Asking if Ocean Breeze has ocean access',
                'message' => 'Hello, is there a pathway or direct access down to the beach from the Ocean Breeze Estate cliff garden?',
                'source' => 'Property Detail Page',
                'status' => 'closed',
                'created_at' => now()->subDays(12),
            ],
            [
                'customer_name' => 'I Nyoman Gede Mahendra',
                'email' => 'nyoman.mahendra@gmail.com',
                'phone' => '+62 812-4567-8901',
                'property_id' => null,
                'subject' => 'Kemitraan agen properti lokal',
                'message' => 'Saya memiliki beberapa klien lokal yang mencari tanah di Lovina. Apakah ada skema bagi komisi kerja sama agen?',
                'source' => 'Contact Us Form',
                'status' => 'closed',
                'created_at' => now()->subDays(14),
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
                'title' => 'North Bali Property Focus',
                'description' => 'We specialize in villas, houses, land, hotels, and restaurants across beautiful North Bali.',
                'icon' => 'home',
                'sort_order' => 1,
            ],
            [
                'page' => 'homepage',
                'title' => 'Tailored Property Search',
                'description' => 'On request, we can specifically search for properties based on what you are looking for and what your wishes are.',
                'icon' => 'search',
                'sort_order' => 2,
            ],
            [
                'page' => 'homepage',
                'title' => 'Local Property Support',
                'description' => 'We can help you find a property that suits your requirements and provide support based on your specific needs.',
                'icon' => 'shield',
                'sort_order' => 3,
            ],
        ];

        foreach ($benefits as $b) {
            Benefit::create($b);
        }

        // 8. Statistics
        $statistics = [
            [
                'page' => 'homepage',
                'number' => '120+',
                'label' => 'Carefully curated properties across North Bali.',
                'icon' => 'Properties Listed',
                'is_visible' => true,
                'sort_order' => 1
            ],
            [
                'page' => 'homepage',
                'number' => '3+',
                'label' => 'Proudly serving North Bali since 2023.',
                'icon' => 'Years Established',
                'is_visible' => true,
                'sort_order' => 2
            ],
            [
                'page' => 'homepage',
                'number' => '90%+',
                'label' => 'Our clients’ satisfaction is our top priority.',
                'icon' => 'Customer Satisfaction',
                'is_visible' => true,
                'sort_order' => 3
            ],
        ];

        foreach ($statistics as $stat) {
            Statistic::create($stat);
        }

        // 9. CMS Contents Default
        CmsContent::create([
            'page' => 'homepage',
            'section_key' => 'hero',
            'content' => [
                'heading' => 'Welcome to North Bali Real Estate Agency',
                'subheading' => 'If your dream is to live in beautiful North Bali, we can help that dream come true.',
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
                'description' => 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.',
                'vision' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
                'mission' => [
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
