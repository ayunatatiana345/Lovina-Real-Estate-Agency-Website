{{-- Tara handles the homepage layout. --}}
{{-- Aragon provides the property data for this section. --}}
@extends('layouts.public')

@section('title', 'North Bali Property for Sale & Investment | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', $settings->site_description ?? 'Explore beachfront luxury villas, ocean view land plots, and prime property investments for sale across Lovina, Temukus, Singaraja, and North Bali.')
@section('canonical', route('home'))

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "{{ $settings->company_name ?? 'PT Lovina North Bali Real Estate Agency' }}",
  "url": "{{ route('home') }}",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": {
      "@@type": "EntryPoint",
      "urlTemplate": "{{ route('properties.index') }}?keyword={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('head_extra')
<style>
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .info-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .benefits-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .info-grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .benefits-grid {
            grid-template-columns: 1fr;
        }
        .info-grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@section('content')
@php
    $hasHeroBg = !empty($hero['background_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($hero['background_image']);
    $heroBgUrl = $hasHeroBg ? asset('storage/' . $hero['background_image']) : null;
    $overlayOpacity = intval($hero['overlay_opacity'] ?? 60) / 100;
@endphp

<!-- 1. Hero Section -->
@if($hasHeroBg)
<section class="section-spacing" style="position: relative; padding-top: 80px; padding-bottom: 80px; background-image: linear-gradient(rgba(15, 23, 42, {{ $overlayOpacity }}), rgba(15, 23, 42, {{ $overlayOpacity }})), url('{{ $heroBgUrl }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">
    <div class="container" style="position: relative; z-index: 2;">
        <div style="max-width: 800px; margin-bottom: 40px;">
            @if(!empty($hero['small_title']))
                <div style="font-size: 14px; font-weight: 700; color: #93C5FD; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">{{ $hero['small_title'] }}</div>
            @endif
            <h1 style="margin-bottom: 20px; color: #FFFFFF;">{{ $hero['heading'] ?? 'Discover Premier Luxury Real Estate in Beautiful North Bali' }}</h1>
            <p class="body-text" style="font-size: 20px; color: #F1F5F9;">
                {{ $hero['subheading'] ?? 'Explore beachfront luxury villas, ocean view land plots, and prime investments in Lovina, Temukus, and Singaraja.' }}
            </p>
        </div>

        @if($searchSection['enabled'] ?? true)
        <!-- Search Bar -->
        <div class="search-bar-box" id="home-search-bar">
            <form action="{{ route('properties.index') }}" method="GET" class="search-bar-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="keyword">Search Location / Property Name</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="{{ $searchSection['placeholder'] ?? 'e.g. Lovina Villa, Beachfront Land...' }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="type">Property Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price_range">Price Range</label>
                    <select name="price_range" id="price_range" class="form-select">
                        @foreach($priceRangeOptions as $val => $label)
                            <option value="{{ $val }}" {{ request('price_range') == (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" id="btn-search-home" style="width: 100%; height: 50px;">
                        <i data-lucide="search" class="lucide-icon lucide-icon-sm" style="margin-right: 6px;"></i> Search
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</section>
@else
<section class="section-spacing bg-light-blue" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div style="max-width: 800px; margin-bottom: 40px;">
            @if(!empty($hero['small_title']))
                <div style="font-size: 14px; font-weight: 700; color: var(--primary-navy); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">{{ $hero['small_title'] }}</div>
            @endif
            <h1 style="margin-bottom: 20px;">{{ $hero['heading'] ?? 'Discover Premier Luxury Real Estate in Beautiful North Bali' }}</h1>
            <p class="body-text" style="font-size: 20px; color: var(--text-secondary);">
                {{ $hero['subheading'] ?? 'Explore beachfront luxury villas, ocean view land plots, and prime investments in Lovina, Temukus, and Singaraja.' }}
            </p>
        </div>

        @if($searchSection['enabled'] ?? true)
        <!-- Search Bar -->
        <div class="search-bar-box" id="home-search-bar">
            <form action="{{ route('properties.index') }}" method="GET" class="search-bar-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="keyword">Search Location / Property Name</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="{{ $searchSection['placeholder'] ?? 'e.g. Lovina Villa, Beachfront Land...' }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="type">Property Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price_range">Price Range</label>
                    <select name="price_range" id="price_range" class="form-select">
                        @foreach($priceRangeOptions as $val => $label)
                            <option value="{{ $val }}" {{ request('price_range') == (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" id="btn-search-home" style="width: 100%; height: 50px;">
                        <i data-lucide="search" class="lucide-icon lucide-icon-sm" style="margin-right: 6px;"></i> Search
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</section>
@endif

<!-- 2. Featured Properties Section (Max 6) -->
@if($featuredProperties->count() > 0)
<section class="section-spacing bg-white">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px;">
            <div>
                <span class="property-category-tag">Featured Selection</span>
                <h2>{{ $featuredSection['section_title'] ?? 'Featured North Bali Properties' }}</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-outline">View All Properties &rarr;</a>
        </div>

        <div class="property-grid">
            @foreach($featuredProperties as $prop)
                <div class="property-card">
                    <div class="property-card-image-wrap">
                        @if($prop->real_cover_image_url)
                            <img src="{{ $prop->real_cover_image_url }}" alt="{{ $prop->name }} - {{ $prop->category->name ?? 'Property' }} in {{ $prop->location->name ?? 'North Bali' }}" class="property-card-image" loading="lazy" onerror="this.onerror=null;this.style.display='none';">
                        @else
                            <div class="property-card-no-image">
                                <i data-lucide="camera" class="lucide-icon" style="width: 32px; height: 32px; stroke-width: 1.5; opacity: 0.45; margin-bottom: 6px;"></i>
                                <span style="font-size: 13px; font-weight: 500; opacity: 0.65;">Photos coming soon</span>
                            </div>
                        @endif
                        @if($prop->is_featured)
                            <span class="property-badge-featured">
                                <i data-lucide="star" class="lucide-icon lucide-icon-sm" style="fill: var(--white); stroke: var(--white); margin-right: 4px;"></i>Featured
                            </span>
                        @endif
                    </div>
                    <div class="property-card-body">
                        <div class="property-category-tag">{{ $prop->category_badge }}</div>
                        <h3 class="property-card-title">{{ $prop->name }}</h3>
                        <div class="property-location-tag">
                            <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $prop->location->name ?? 'Lovina, North Bali' }}
                        </div>
                        <div class="property-price">{{ $prop->formatted_price }}</div>
                        @php
                            $hasSpecs = ($prop->bedrooms && $prop->bedrooms > 0) ||
                                        ($prop->bathrooms && $prop->bathrooms > 0) ||
                                        ($prop->land_size && $prop->land_size > 0);
                        @endphp
                        @if($hasSpecs)
                            <div class="property-specs-bar">
                                @if($prop->bedrooms && $prop->bedrooms > 0)
                                    <div class="spec-item"><i data-lucide="bed" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bedrooms }} Beds</div>
                                @endif
                                @if($prop->bathrooms && $prop->bathrooms > 0)
                                    <div class="spec-item"><i data-lucide="bath" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bathrooms }} Baths</div>
                                @endif
                                @if($prop->land_size && $prop->land_size > 0)
                                    <div class="spec-item"><i data-lucide="maximize" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->land_size }} m²</div>
                                @endif
                            </div>
                        @endif
                        <div style="margin-top: 16px;">
                            <a href="{{ route('properties.show', $prop->slug) }}" class="btn btn-outline" style="width: 100%;">View Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 3. Property Categories Section -->
<section class="section-spacing bg-light-gray">
    <div class="container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 48px auto;">
            <h2>{{ $categoriesSection['heading'] ?? 'Explore Property Categories' }}</h2>
            <p class="body-text" style="color: var(--text-secondary);">
                {{ $categoriesSection['description'] ?? 'Find your perfect real estate match by category in North Bali.' }}
            </p>
        </div>

        <div class="category-grid">
            @foreach($categories as $cat)
                <a href="{{ route('properties.index', ['type' => $cat->slug]) }}" class="category-card">
                    <div class="category-card-icon">
                        <i data-lucide="home" class="lucide-icon lucide-icon-lg"></i>
                    </div>
                    <div class="category-card-title">{{ $cat->name }}</div>
                    <div class="caption">{{ $cat->properties_count }} listings</div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- 4. Latest Properties Section -->
<section class="section-spacing bg-white">
    <div class="container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 48px auto;">
            <h2>{{ $latestSection['section_title'] ?? 'Latest Added Properties' }}</h2>
            <p class="body-text" style="color: var(--text-secondary);">
                Explore our newest luxury real estate arrivals in North Bali.
            </p>
        </div>

        <div class="property-grid">
            @foreach($latestProperties as $prop)
                <div class="property-card">
                    <div class="property-card-image-wrap">
                        @if($prop->real_cover_image_url)
                            <img src="{{ $prop->real_cover_image_url }}" alt="{{ $prop->name }} - {{ $prop->category->name ?? 'Property' }} in {{ $prop->location->name ?? 'North Bali' }}" class="property-card-image" loading="lazy" onerror="this.onerror=null;this.style.display='none';">
                        @else
                            <div class="property-card-no-image">
                                <i data-lucide="camera" class="lucide-icon" style="width: 32px; height: 32px; stroke-width: 1.5; opacity: 0.45; margin-bottom: 6px;"></i>
                                <span style="font-size: 13px; font-weight: 500; opacity: 0.65;">Photos coming soon</span>
                            </div>
                        @endif
                        @if($prop->is_featured)
                            <span class="property-badge-featured">
                                <i data-lucide="star" class="lucide-icon lucide-icon-sm" style="fill: var(--white); stroke: var(--white); margin-right: 4px;"></i>Featured
                            </span>
                        @endif
                    </div>
                    <div class="property-card-body">
                        <div class="property-category-tag">{{ $prop->category_badge }}</div>
                        <h3 class="property-card-title">{{ $prop->name }}</h3>
                        <div class="property-location-tag">
                            <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $prop->location->name ?? 'North Bali' }}
                        </div>
                        <div class="property-price">{{ $prop->formatted_price }}</div>
                        @php
                            $hasSpecs = ($prop->bedrooms && $prop->bedrooms > 0) ||
                                        ($prop->bathrooms && $prop->bathrooms > 0) ||
                                        ($prop->land_size && $prop->land_size > 0);
                        @endphp
                        @if($hasSpecs)
                            <div class="property-specs-bar">
                                @if($prop->bedrooms && $prop->bedrooms > 0)
                                    <div class="spec-item"><i data-lucide="bed" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bedrooms }} Beds</div>
                                @endif
                                @if($prop->bathrooms && $prop->bathrooms > 0)
                                    <div class="spec-item"><i data-lucide="bath" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bathrooms }} Baths</div>
                                @endif
                                @if($prop->land_size && $prop->land_size > 0)
                                    <div class="spec-item"><i data-lucide="maximize" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->land_size }} m²</div>
                                @endif
                            </div>
                        @endif
                        <div style="margin-top: 16px;">
                            <a href="{{ route('properties.show', $prop->slug) }}" class="btn btn-outline" style="width: 100%;">View Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 5. Popular Locations -->
<section class="section-spacing bg-accent-lavender">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px;">
            <div>
                <h2>{{ $locationsSection['heading'] ?? 'Popular Locations in North Bali' }}</h2>
                <p class="body-text" style="color: var(--text-secondary);">{{ $locationsSection['description'] ?? 'Prime coastal & mountain regions in Buleleng Regency.' }}</p>
            </div>
            <a href="{{ route('locations.index') }}" class="btn btn-outline">Explore All Locations</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            @foreach($popularLocations as $loc)
                <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
                    <h3 style="font-size: 24px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; color: var(--primary-navy); text-align: left;">
                        <i data-lucide="map-pin" class="lucide-icon" style="color: var(--primary-navy);"></i> {{ $loc->name }}
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 20px; text-align: justify; flex-grow: 1;">{{ $loc->description }}</p>
                    <div style="text-align: center; margin-top: auto;">
                        <a href="{{ route('locations.show', $loc->slug) }}" class="btn btn-primary" style="padding: 8px 24px; font-size: 14px; display: inline-block;">View Properties</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 6. Why Choose Us -->
<section class="section-spacing bg-white">
    <div class="container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 48px auto;">
            <h2>{{ $whyChooseSection['heading'] ?? 'Why Choose PT Lovina North Bali' }}</h2>
            <p class="body-text" style="color: var(--text-secondary);">{{ $whyChooseSection['description'] ?? 'Your trusted local partner for smooth real estate acquisitions.' }}</p>
        </div>

        <div class="benefits-grid">
            @foreach($benefits as $b)
                <div style="background-color: var(--light-gray); border-radius: var(--radius-md); padding: 28px 20px; text-align: center;">
                    <div style="color: var(--primary-navy); margin-bottom: 12px;">
                        <i data-lucide="{{ $b->icon ?? 'shield' }}" class="lucide-icon lucide-icon-xl" style="color: var(--primary-navy);"></i>
                    </div>
                    <h3 style="font-size: 20px; margin-bottom: 8px;">{{ $b->title }}</h3>
                    <p style="font-size: 15px; color: var(--text-secondary);">{{ $b->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 7. Key Facts & Services -->
<section class="section-spacing bg-navy">
    <div class="container">
        <div class="info-grid-3">
            @foreach($statistics as $stat)
                <div class="stats-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 40px 24px;">
                    <div style="font-size: 48px; font-weight: 700; color: var(--primary-navy); margin-bottom: 12px; line-height: 1.1;">
                        {{ $stat->number }}
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: var(--primary-navy); margin-bottom: 12px; line-height: 1.3;">
                        {{ $stat->icon }}
                    </h3>
                    <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6; margin: 0; max-width: 280px;">
                        {{ $stat->label }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 8. Contact CTA -->
<section class="section-spacing bg-light-blue" style="text-align: center;">
    <div class="container" style="max-width: 700px;">
        <h2>{{ $cta['heading'] ?? 'Ready to Find Your Dream Property in North Bali?' }}</h2>
        <p class="body-text" style="margin-bottom: 32px; color: var(--text-secondary);">
            {{ $cta['description'] ?? 'Speak directly with our experienced property advisors today and schedule a private villa inspection.' }}
        </p>
        <a href="{{ route('contact') }}" class="btn btn-primary" style="padding: 16px 36px; font-size: 18px;">
            {{ $cta['button_text'] ?? 'Contact Us Today' }} &rarr;
        </a>
    </div>
</section>
@endsection
