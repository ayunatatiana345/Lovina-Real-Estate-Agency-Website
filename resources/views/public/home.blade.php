@extends('layouts.public')

@section('title', 'Home - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('content')
<!-- 1. Hero Section -->
<section class="section-spacing bg-light-blue" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div style="max-width: 800px; margin-bottom: 40px;">
            <h1 style="margin-bottom: 20px;">{{ $hero['heading'] ?? 'Discover Premier Luxury Real Estate in Beautiful North Bali' }}</h1>
            <p class="body-text" style="font-size: 20px; color: var(--text-secondary);">
                {{ $hero['subheading'] ?? 'Explore beachfront luxury villas, ocean view land plots, and prime investments in Lovina, Temukus, and Singaraja.' }}
            </p>
        </div>

        <!-- Search Bar -->
        <div class="search-bar-box" id="home-search-bar">
            <form action="{{ route('properties.index') }}" method="GET" class="search-bar-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="keyword">Search Location / Property Name</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="e.g. Lovina Villa, Beachfront Land...">
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
                        <option value="">Any Price</option>
                        <option value="under_250k">Under $250,000</option>
                        <option value="250k_500k">$250,000 - $500,000</option>
                        <option value="above_500k">Above $500,000</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" id="btn-search-home" style="width: 100%; height: 50px;">
                        <i data-lucide="search" class="lucide-icon lucide-icon-sm" style="margin-right: 6px;"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- 2. Featured Properties Section (Max 3) -->
@if($featuredProperties->count() > 0)
<section class="section-spacing bg-white">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px;">
            <div>
                <span class="property-category-tag">Featured Selection</span>
                <h2>Featured North Bali Properties</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-outline">View All Properties &rarr;</a>
        </div>

        <div class="property-grid">
            @foreach($featuredProperties as $prop)
                <div class="property-card">
                    <div class="property-card-image-wrap">
                        @php
                            $cover = $prop->images->firstWhere('is_cover', true) ?? $prop->images->first();
                        @endphp
                        @if($cover && file_exists(public_path('storage/' . $cover->image_path)))
                            <img src="{{ asset('storage/' . $cover->image_path) }}" alt="{{ $prop->name }}" class="property-card-image">
                        @else
                            <div style="width: 100%; height: 100%; background-color: #F3F4F6; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="home" style="width: 64px; height: 64px; color: #9CA3AF; stroke-width: 1.25px;"></i>
                            </div>
                        @endif
                        <span class="property-badge-featured">
                            <i data-lucide="star" class="lucide-icon lucide-icon-sm" style="fill: var(--white); stroke: var(--white); margin-right: 4px;"></i>Featured
                        </span>
                    </div>
                    <div class="property-card-body">
                        <div class="property-category-tag">{{ $prop->category->name ?? 'Villa' }}</div>
                        <h3 class="property-card-title">{{ $prop->name }}</h3>
                        <div class="property-location-tag">
                            <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $prop->location->name ?? 'Lovina, North Bali' }}
                        </div>
                        <div class="property-price">{{ $prop->formatted_price }}</div>
                        <div class="property-specs-bar">
                            <div class="spec-item"><i data-lucide="bed" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bedrooms }} Beds</div>
                            <div class="spec-item"><i data-lucide="bath" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bathrooms }} Baths</div>
                            <div class="spec-item"><i data-lucide="maximize" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->land_size }} m²</div>
                        </div>
                        <div style="margin-top: 16px;">
                            <a href="{{ route('properties.show', $prop->slug) }}" class="btn btn-outline" style="width: 100%;">View Details</a>
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
            <h2>Explore Property Categories</h2>
            <p class="body-text" style="color: var(--text-secondary);">
                Find your perfect real estate match by category in North Bali.
            </p>
        </div>

        <div class="category-grid">
            @foreach($categories as $cat)
                <a href="{{ route('properties.index', ['type' => $cat->slug]) }}" class="category-card">
                    <div class="category-card-icon" style="color: var(--primary-navy); margin-bottom: 12px;">
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
            <h2>Latest Added Properties</h2>
            <p class="body-text" style="color: var(--text-secondary);">
                Explore our newest luxury real estate arrivals in North Bali.
            </p>
        </div>

        <div class="property-grid">
            @foreach($latestProperties as $prop)
                <div class="property-card">
                    <div class="property-card-image-wrap">
                        @php
                            $cover = $prop->images->firstWhere('is_cover', true) ?? $prop->images->first();
                        @endphp
                        @if($cover && file_exists(public_path('storage/' . $cover->image_path)))
                            <img src="{{ asset('storage/' . $cover->image_path) }}" alt="{{ $prop->name }}" class="property-card-image">
                        @else
                            <div style="width: 100%; height: 100%; background-color: #F3F4F6; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="home" style="width: 64px; height: 64px; color: #9CA3AF; stroke-width: 1.25px;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="property-card-body">
                        <div class="property-category-tag">{{ $prop->category->name ?? 'Villa' }}</div>
                        <h3 class="property-card-title">{{ $prop->name }}</h3>
                        <div class="property-location-tag">
                            <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $prop->location->name ?? 'North Bali' }}
                        </div>
                        <div class="property-price">{{ $prop->formatted_price }}</div>
                        <div class="property-specs-bar">
                            <div class="spec-item"><i data-lucide="bed" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bedrooms }} Beds</div>
                            <div class="spec-item"><i data-lucide="bath" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->bathrooms }} Baths</div>
                            <div class="spec-item"><i data-lucide="maximize" class="lucide-icon lucide-icon-sm" style="margin-right: 4px;"></i> {{ $prop->land_size }} m²</div>
                        </div>
                        <div style="margin-top: 16px;">
                            <a href="{{ route('properties.show', $prop->slug) }}" class="btn btn-outline" style="width: 100%;">View Details</a>
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
                <h2>Popular Locations in North Bali</h2>
                <p class="body-text" style="color: var(--text-secondary);">Prime coastal & mountain regions in Buleleng Regency.</p>
            </div>
            <a href="{{ route('locations.index') }}" class="btn btn-outline">Explore All Locations &rarr;</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            @foreach($popularLocations as $loc)
                <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 24px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; color: var(--primary-navy);">
                        <i data-lucide="map-pin" class="lucide-icon" style="color: var(--primary-navy);"></i> {{ $loc->name }}
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 16px;">{{ $loc->description }}</p>
                    <a href="{{ route('properties.index', ['location' => $loc->slug]) }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 14px;">View Properties &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 6. Why Choose Us -->
<section class="section-spacing bg-white">
    <div class="container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 48px auto;">
            <h2>Why Choose PT Lovina North Bali</h2>
            <p class="body-text" style="color: var(--text-secondary);">Your trusted local partner for smooth real estate acquisitions.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
            @foreach($benefits as $b)
                <div style="background-color: var(--light-gray); border-radius: var(--radius-md); padding: 28px 20px; text-align: center;">
                    <div style="color: var(--primary-navy); margin-bottom: 12px;">
                        <i data-lucide="shield" class="lucide-icon lucide-icon-xl" style="color: var(--primary-navy);"></i>
                    </div>
                    <h3 style="font-size: 20px; margin-bottom: 8px;">{{ $b->title }}</h3>
                    <p style="font-size: 15px; color: var(--text-secondary);">{{ $b->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 7. Company Statistics Card -->
<section class="section-spacing bg-navy">
    <div class="container">
        <div class="stats-grid">
            @foreach($statistics as $stat)
                <div class="stats-card">
                    <div class="stats-number">{{ $stat->number }}</div>
                    <div class="stats-label">{{ $stat->label }}</div>
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
