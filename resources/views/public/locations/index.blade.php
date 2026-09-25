{{-- Tatiana handles the locations directory here. --}}
@extends('layouts.public')

@section('title', 'Locations in North Bali | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', 'Explore 23 verified property locations across North Bali including Lovina, Kalibukbuk, Kaliasem, Anturan, Celuk Buluh, Kayu Putih, Temukus, Banjar, and Gerokgak. Discover villas, land plots, and residential opportunities.')
@section('canonical', route('locations.index'))

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ route('home') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Locations",
      "item": "{{ route('locations.index') }}"
    }
  ]
}
</script>
@endsection

@section('head_extra')
<style>
    .locations-card-btn {
        background-color: var(--primary-navy) !important;
        color: var(--white) !important;
        border: 2px solid var(--primary-navy) !important;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out !important;
    }
    .locations-card-btn:hover {
        background-color: var(--medium-blue) !important;
        border-color: var(--medium-blue) !important;
        color: var(--white) !important;
    }
</style>
@endsection

@section('content')
<!-- Hero Section (Sized exactly identical to About Us & Contact Us) -->
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px; font-family: 'Poppins', sans-serif;">
    <div class="container">
        <h1 style="margin-bottom: 12px; font-size: 48px; font-weight: 700; line-height: 1.2; color: var(--primary-navy);">Locations</h1>
        <p class="body-text" style="color: var(--text-secondary); margin-bottom: 24px; font-size: 18px; line-height: 1.6;">
            Discover the best areas in North Bali. Find the perfect location for your dream property.
        </p>
        
        <!-- Horizontal Statistics Bar inside Hero -->
        <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="map-pin" class="lucide-icon" style="color: var(--primary-navy);"></i>
                <div style="font-size: 15px; color: var(--text-secondary);">
                    <strong style="color: var(--primary-navy); font-weight: 700;">{{ $totalLocations }}+</strong> Prime Locations
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="home" class="lucide-icon" style="color: var(--primary-navy);"></i>
                <div style="font-size: 15px; color: var(--text-secondary);">
                    <strong style="color: var(--primary-navy); font-weight: 700;">{{ $totalProperties }}+</strong> Properties Available
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="users" class="lucide-icon" style="color: var(--primary-navy);"></i>
                <div style="font-size: 15px; color: var(--text-secondary);">
                    <strong style="color: var(--primary-navy); font-weight: 700;">Happy</strong> Satisfied Clients
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search & Filter Bar (White Overlapping Card) -->
<div class="container" style="position: relative; z-index: 20; margin-top: -30px; margin-bottom: 56px;">
    <div style="background-color: var(--white); border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-md); border: 1px solid var(--border);">
        <form action="{{ route('locations.index') }}" method="GET" id="search-locations-form">
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px; align-items: flex-end;" class="filter-layout-grid">
                
                <!-- Keyword input -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="keyword" style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: block;">Keyword</label>
                    <div style="position: relative;">
                        <input type="text" name="keyword" id="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="Search by location name..." style="height: 44px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0 12px 0 36px; width: 100%; font-family: 'Poppins', sans-serif; font-size: 14px; box-sizing: border-box;">
                        <i data-lucide="search" class="lucide-icon lucide-icon-sm" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    </div>
                </div>
                
                <!-- Property Type Dropdown -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="type" style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: block;">Property Type</label>
                    <select name="type" id="type" class="form-select" style="height: 44px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0 12px; width: 100%; background-color: var(--white); font-family: 'Poppins', sans-serif; font-size: 14px; box-sizing: border-box; cursor: pointer;">
                        <option value="">All Types</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('type') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Price Range Dropdown -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price_range" style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: block;">Price Range</label>
                    <select name="price_range" id="price_range" class="form-select" style="height: 44px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0 12px; width: 100%; background-color: var(--white); font-family: 'Poppins', sans-serif; font-size: 14px; box-sizing: border-box; cursor: pointer;">
                        @foreach($priceRangeOptions as $val => $label)
                            <option value="{{ $val }}" {{ request('price_range') == (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; height: 44px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0; font-size: 14px; font-weight: 600; border-radius: var(--radius-sm); border: none; background-color: var(--primary-navy); color: var(--white); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 44px; font-family: 'Poppins', sans-serif;">
                        Search
                    </button>
                    <a href="{{ route('locations.index') }}" class="btn btn-outline" style="padding: 0 20px; font-size: 14px; font-weight: 600; border-radius: var(--radius-sm); border: 2px solid var(--primary-navy); color: var(--primary-navy); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; background-color: transparent; height: 44px; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                        Reset
                    </a>
                </div>
                
            </div>
        </form>
    </div>
</div>

<!-- Popular Locations Grid Section -->
<section class="section-spacing bg-white" style="padding-top: 20px; padding-bottom: 60px;">
    <div class="container">
        
        <!-- Section title with blue indicator underline -->
        <div style="text-align: center; margin-bottom: 48px;">
            <h2 style="font-size: 36px; font-weight: 700; color: var(--primary-navy); position: relative; display: inline-block; padding-bottom: 12px; margin-bottom: 0;">
                Locations in North Bali
                <div style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background-color: var(--medium-blue); border-radius: 2px;"></div>
            </h2>
        </div>
        
        <!-- Grid layout (3 columns on desktop) -->
        <div class="locations-grid-wrapper">
            @forelse($locations as $loc)
                <div class="location-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; height: 100%; position: relative;">
                    
                    <!-- Genuine Image or Neutral Gray Placeholder Box -->
                    <div style="height: 200px; background-color: #E5E7EB; position: relative; flex-shrink: 0; overflow: hidden;">
                        @if($loc->has_image)
                            <img src="{{ $loc->image_url }}" 
                                 alt="{{ $loc->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'">
                        @endif

                        <!-- Properties Count Badge in top-right corner -->
                        <span style="position: absolute; top: 12px; right: 12px; background-color: var(--primary-navy); color: var(--white); font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: var(--radius-sm); z-index: 2;">
                            {{ $loc->properties_count }} {{ Str::plural('Property', $loc->properties_count) }}
                        </span>
                        
                        <!-- Gold Star Badge in top-left corner for popular locations -->
                        @if($loc->is_popular)
                            <div style="position: absolute; top: 12px; left: 12px; background-color: var(--white); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); z-index: 2;">
                                <i data-lucide="star" class="lucide-icon lucide-icon-sm" style="color: var(--secondary-gold); fill: var(--secondary-gold);"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Card Body with Full Description and Equal Height Flow -->
                    <div style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1; text-align: left;">
                        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--primary-navy); text-align: left;">
                            <a href="{{ route('locations.show', $loc->slug) }}" style="color: inherit; text-decoration: none;">{{ $loc->name }}</a>
                        </h3>
                        <p style="font-size: 15px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6; text-align: justify; text-justify: inter-word; flex-grow: 1;">
                            {{ $loc->description }}
                        </p>
                        <div style="margin-top: auto; padding-top: 8px;">
                            <!-- View Properties button (Solid Blue) -->
                            <a href="{{ route('locations.show', $loc->slug) }}" class="btn btn-primary locations-card-btn" style="width: 100%; height: 44px; padding: 0 16px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); text-decoration: none; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                                View Properties
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 64px 32px; border: 1px dashed var(--border); border-radius: var(--radius-md); background-color: var(--light-gray);">
                    <div style="color: var(--text-secondary); margin-bottom: 12px;">
                        <i data-lucide="search" class="lucide-icon lucide-icon-xl" style="width: 48px; height: 48px; color: var(--text-secondary);"></i>
                    </div>
                    <p style="font-size: 16px; color: var(--text-secondary); font-weight: 500;">No locations found matching your filter criteria.</p>
                    <a href="{{ route('locations.index') }}" style="margin-top: 12px; display: inline-block; color: var(--primary-navy); font-weight: 600; text-decoration: underline;">Clear Filters</a>
                </div>
            @endforelse
        </div>
        
    </div>
</section>

<!-- Explore North Bali Interactive Map Section -->
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px; border-top: 1px solid var(--border);">
    <div class="container">
        <div class="map-layout-grid">
            
            <!-- Left Grid Column: Text Descriptions -->
            <div>
                <h2 style="font-size: 32px; font-weight: 700; color: var(--primary-navy); margin-bottom: 16px; margin-top: 0; line-height: 1.3;">Explore North Bali</h2>
                <p style="font-size: 16px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 32px;">
                    North Bali offers a unique blend of natural beauty, rich culture, and excellent investment opportunities. Find your place in paradise.
                </p>
                
                <!-- 4 Points list of advantages (Clean text without decorative icons) -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 700; color: var(--primary-navy); margin-bottom: 4px; margin-top: 0; font-family: 'Poppins', sans-serif;">Beautiful Beaches</h4>
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.5; font-family: 'Poppins', sans-serif;">Stunning black sand beaches and crystal clear waters.</p>
                    </div>
                    
                    <div>
                        <h4 style="font-size: 18px; font-weight: 700; color: var(--primary-navy); margin-bottom: 4px; margin-top: 0; font-family: 'Poppins', sans-serif;">Natural Beauty</h4>
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.5; font-family: 'Poppins', sans-serif;">Mountains, waterfalls, and scenic tropical landscapes.</p>
                    </div>
                    
                    <div>
                        <h4 style="font-size: 18px; font-weight: 700; color: var(--primary-navy); margin-bottom: 4px; margin-top: 0; font-family: 'Poppins', sans-serif;">Rich Culture</h4>
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.5; font-family: 'Poppins', sans-serif;">Traditional Balinese culture, temples, and heritage.</p>
                    </div>
                    
                    <div>
                        <h4 style="font-size: 18px; font-weight: 700; color: var(--primary-navy); margin-bottom: 4px; margin-top: 0; font-family: 'Poppins', sans-serif;">Investment Potential</h4>
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.5; font-family: 'Poppins', sans-serif;">Growing property market with strong capital appreciation.</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Grid Column: Styled Map Graphic and Pins -->
            <div>
                <div class="interactive-map-wrapper" style="position: relative; width: 100%; height: 420px; background-color: #BAE6FD; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                    
                    <!-- Coastline Graphic SVG background -->
                    <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <path d="M 0 52 Q 25 45, 55 42 T 100 28 L 100 100 L 0 100 Z" fill="#F0FDF4" stroke="#4ADE80" stroke-width="1" />
                    </svg>
                    
                    <!-- Water Label -->
                    <div style="position: absolute; top: 15%; left: 45%; color: #0284C7; font-size: 12px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; user-select: none;">Bali Sea</div>
                    <!-- Land Label -->
                    <div style="position: absolute; bottom: 15%; left: 45%; color: #15803D; font-size: 12px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; user-select: none;">Buleleng Regency</div>
                    
                    <!-- Map Pins with label tooltips -->
                    @php
                        $mapLocations = $allLocations ?? $locations;
                        $coords = [
                            'gerokgak'     => ['left' => '7%',  'top' => '62%'],
                            'sanggalangit' => ['left' => '14%', 'top' => '58%'],
                            'tangguwisia'  => ['left' => '20%', 'top' => '56%'],
                            'sulanyah'     => ['left' => '26%', 'top' => '63%'],
                            'banjar'       => ['left' => '32%', 'top' => '69%'],
                            'dencarik'     => ['left' => '34%', 'top' => '54%'],
                            'temukus'      => ['left' => '39%', 'top' => '51%'],
                            'sing-sing'    => ['left' => '44%', 'top' => '64%'],
                            'cempaga'      => ['left' => '39%', 'top' => '79%'],
                            'kayu-putih'   => ['left' => '47%', 'top' => '72%'],
                            'kaliasem'     => ['left' => '49%', 'top' => '54%'],
                            'lovina'       => ['left' => '53%', 'top' => '47%'],
                            'kalibukbuk'   => ['left' => '57%', 'top' => '47%'],
                            'banyualit'    => ['left' => '61%', 'top' => '53%'],
                            'celuk-buluh'  => ['left' => '65%', 'top' => '49%'],
                            'anturan'      => ['left' => '69%', 'top' => '48%'],
                            'tukad-mungga' => ['left' => '74%', 'top' => '46%'],
                            'sambangan'    => ['left' => '75%', 'top' => '68%'],
                            'selat'        => ['left' => '70%', 'top' => '77%'],
                            'tegal-linggah'=> ['left' => '65%', 'top' => '84%'],
                            'gambuh'       => ['left' => '80%', 'top' => '82%'],
                            'kubutambahan' => ['left' => '85%', 'top' => '40%'],
                            'bondalem'     => ['left' => '93%', 'top' => '33%'],
                        ];
                    @endphp
                    @foreach($mapLocations as $loc)
                        @php
                            $slug = Str::slug($loc->name);
                            $pos = $coords[$slug] ?? null;
                        @endphp
                        @if($pos)
                            <a href="{{ route('locations.show', $loc->slug) }}" class="map-pin-marker" style="position: absolute; left: {{ $pos['left'] }}; top: {{ $pos['top'] }}; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; text-decoration: none;" title="{{ $loc->name }} ({{ $loc->properties_count }} properties) - View location details">
                                <div class="pin-dot" style="width: 10px; height: 10px; background-color: #EF4444; border: 2px solid #FFFFFF; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); margin: 0 auto; transition: transform 0.2s;"></div>
                                <div class="pin-label-box" style="background-color: #FFFFFF; color: #1F2937; font-size: 9px; font-weight: 700; padding: 2px 5px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #CBD5E1; white-space: nowrap; margin-top: 2px; text-align: center; line-height: 1.2;">
                                    <div style="color: #1E3A8A; font-weight: 700;">{{ $loc->name }}</div>
                                    <div style="font-size: 8px; color: #64748B; font-weight: 600;">{{ $loc->properties_count }} {{ Str::plural('Listing', $loc->properties_count) }}</div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                    
                </div>
                <!-- Explanatory Orientation Note -->
                <div style="margin-top: 10px; font-size: 12px; color: var(--text-muted); font-style: italic; text-align: center; line-height: 1.5;">
                    * Visual map orientation of North Bali service areas. Browse the complete, real-time database-driven directory below for all active listings and future areas.
                </div>
            </div>
            
        </div>
        
        <!-- Proper Contact Us CTA Card -->
        <div style="margin-top: 56px;">
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 48px 32px; text-align: center; box-shadow: var(--shadow-sm); max-width: 820px; margin: 0 auto;">
                <h3 style="font-size: 26px; font-weight: 700; color: var(--primary-navy); margin-top: 0; margin-bottom: 12px; font-family: 'Poppins', sans-serif;">
                    Can't Find the Right Location?
                </h3>
                <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6; margin: 0 auto 28px auto; max-width: 620px; font-family: 'Poppins', sans-serif;">
                    Looking for a property in a specific area of North Bali? Our team can help you explore available properties and find an option that matches your needs.
                </p>
                <a href="{{ route('contact') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 36px; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Contact Us
                </a>
            </div>
        </div>
        
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('btn-toggle-locations');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const extraCards = document.querySelectorAll('.extra-location-card');
            const isExpanded = toggleBtn.getAttribute('data-expanded') === 'true';
            
            if (isExpanded) {
                // Collapse back to 6 cards instantly
                extraCards.forEach(card => card.style.display = 'none');
                toggleBtn.textContent = 'View All Locations';
                toggleBtn.setAttribute('data-expanded', 'false');
            } else {
                // Show all cards instantly
                extraCards.forEach(card => card.style.display = 'flex');
                toggleBtn.textContent = 'Show Less';
                toggleBtn.setAttribute('data-expanded', 'true');
            }
        });
    }
});
</script>

<style>
/* Layout Styles for Search and Grid */
.locations-grid-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
    align-items: stretch;
}

@media (min-width: 641px) {
    .locations-grid-wrapper {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1025px) {
    .locations-grid-wrapper {
        grid-template-columns: repeat(3, 1fr);
    }
}

.map-layout-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 40px;
}

@media (min-width: 769px) {
    .map-layout-grid {
        grid-template-columns: 1fr 1.2fr;
        align-items: center;
    }
}

.filter-layout-grid {
    grid-template-columns: 1fr !important;
}

@media (min-width: 768px) {
    .filter-layout-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

/* Card Equal Height & Hover effects */
.location-card {
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
}

.location-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md) !important;
}

.map-pin-marker:hover .pin-dot {
    transform: scale(1.3);
    background-color: #DC2626 !important;
}

.map-pin-marker:hover .pin-label-box {
    background-color: #F8FAFC !important;
    border-color: #94A3B8 !important;
}
</style>
@endsection
