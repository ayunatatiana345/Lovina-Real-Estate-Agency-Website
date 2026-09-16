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

        // 3. Locations (Official Company Locations list - 23 Verified Published Locations)
        $locationsData = [
            [
                'name' => 'Lovina',
                'description' => 'Lovina is one of North Bali’s best-known coastal tourism areas, with a relaxed atmosphere and convenient connections to nearby residential, hospitality, and local communities.',
                'image' => 'locations/lovina.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Kalibukbuk',
                'description' => 'Within the wider Lovina area, Kalibukbuk forms an established hub featuring residential properties, holiday accommodation, restaurants, and everyday services along the coastal strip.',
                'image' => 'locations/kalibukbuk.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Kaliasem',
                'description' => 'Positioned alongside the central Lovina corridor, Kaliasem provides a balanced mix of residential properties, private villas, and land parcels connected to nearby beachside neighborhoods.',
                'image' => 'locations/kaliasem.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Anturan',
                'description' => 'Along the coastal route east of Lovina, Anturan combines traditional seaside neighborhoods with residential properties and convenient road access toward Singaraja and neighboring communities.',
                'image' => 'locations/anturan.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Celuk Buluh',
                'description' => 'Associated closely with Kalibukbuk, Celuk Buluh features villas, residential dwellings, and land opportunities situated within the calm surroundings of the greater Lovina area.',
                'image' => 'locations/celuk-buluh.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Kayu Putih',
                'description' => 'Further inland on elevated ground, Kayu Putih offers a greener, tranquil setting with property opportunities ranging from open land to private residential villas.',
                'image' => 'locations/kayuputih.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Tukad Mungga',
                'description' => 'Tukad Mungga is a residential coastal area located along the main thoroughfare that connects established beachside communities west of Singaraja.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sambangan',
                'description' => 'Surrounded by natural hillsides and greenery, Sambangan appeals to buyers seeking an inland retreat distinct from North Bali’s primary seaside property corridor.',
                'image' => 'locations/sambangan.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sanggalangit',
                'description' => 'Near the peaceful western reaches of Buleleng, Sanggalangit provides a quiet rural setting with published property listings reflecting residential opportunities among local communities.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Dencarik',
                'description' => 'Dencarik stretches across coastal and agricultural terrain in North Bali, represented in published listings by versatile land and villa opportunities near the beach.',
                'image' => 'locations/dencarik.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Cempaga',
                'description' => 'For buyers considering larger land parcels, Cempaga provides an inland hillside environment distinct from the busier coastal tourism strip of Lovina.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Temukus',
                'description' => 'Stretching along the North Bali coastline near Lovina, Temukus features published listings that include seaside parcels and coastal villa properties.',
                'image' => 'locations/temukus.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Banjar',
                'description' => 'Banjar covers an extensive district with both coastal and elevated inland neighborhoods, giving property buyers diverse settings beyond the central Lovina corridor.',
                'image' => 'locations/banjar.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Banyualit',
                'description' => 'Situated along the Singaraja–Lovina coastal corridor, Banyualit is an established residential community with listings covering private houses and building land.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Gambuh',
                'description' => 'Set back from the busier coastal strip, Gambuh offers a peaceful inland setting suited to buyers exploring land and residential property options.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Selat',
                'description' => 'Characterized by agricultural scenery and rural hamlets, Selat features published listings with larger land parcels surrounded by North Bali’s inland landscape.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Tegal Linggah',
                'description' => 'Tegal Linggah represents an inland residential alternative in North Bali, offering quieter surroundings away from the busier coastal resort destinations.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Kubutambahan',
                'description' => 'Lying along the eastern North Bali coastline beyond Lovina, Kubutambahan provides property opportunities that include expansive land parcels near the sea.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sing-Sing',
                'description' => 'Bordering the scenic Sing-Sing valley, this local area features published villa properties that offer buyers a tranquil alternative to central Lovina.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Tangguwisia',
                'description' => 'The area offers a quieter coastal North Bali setting, with the published listings showing land opportunities within the wider Seririt-side property market.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Sulanyah',
                'description' => 'Sulanyah is a coastal North Bali location represented by published land property, giving buyers an option beyond the better-known Lovina and Singaraja areas.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Bondalem',
                'description' => 'On the eastern coastline of North Bali, Bondalem extends property searches into a calm seaside environment away from the central tourism center.',
                'image' => null,
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Gerokgak',
                'description' => 'Covering a broader region of western Buleleng, Gerokgak extends the property directory with coastal and agricultural acreage beyond the central Lovina corridor.',
                'image' => 'locations/gerokgak.jpg',
                'is_popular' => false,
                'status' => 'active',
            ],
            // Inactive historical reference records for backwards-compatible test property foreign keys
            [
                'name' => 'Singaraja',
                'description' => 'The main urban center and administrative capital of Buleleng Regency.',
                'image' => 'locations/singaraja.jpg',
                'is_popular' => false,
                'status' => 'inactive',
            ],
            [
                'name' => 'Seririt',
                'description' => 'A major commercial and agricultural town in western Buleleng, serving as a regional trading hub with local markets, commercial shops, and key transport connections along the north coast.',
                'image' => 'locations/sererit.jpg',
                'is_popular' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Ume Anyar',
                'description' => 'A rural coastal village in western Buleleng.',
                'image' => 'locations/ume-anyar.jpg',
                'is_popular' => false,
                'status' => 'inactive',
            ],
        ];

        $locations = [];
        foreach ($locationsData as $loc) {
            $locations[$loc['name']] = Location::updateOrCreate(
                ['slug' => Str::slug($loc['name'])],
                [
                    'name' => $loc['name'],
                    'description' => $loc['description'],
                    'image' => $loc['image'],
                    'is_popular' => $loc['is_popular'],
                    'status' => $loc['status'],
                ]
            );
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

        // 5. Inquiries: Real customer inquiries are submitted exclusively via Public Website.
        // No fake/dummy inquiries are seeded.

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

        // 12. Article Seeder
        $this->call(ArticleSeeder::class);
    }
}
