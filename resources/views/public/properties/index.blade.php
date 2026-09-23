{{-- Aragon handles property grid layout. --}}
{{-- Tatiana handles the filter section here. --}}
@extends('layouts.public')

@php
    $pageTitle = 'Properties for Sale in North Bali | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency');
    $pageDesc = 'Explore premier properties for sale in North Bali including luxury beachfront villas, hillside ocean view land plots, and houses in Lovina, Temukus, and Singaraja.';
    if (request()->filled('type')) {
        $selectedCat = $categories->firstWhere('slug', request('type')) ?? $categories->firstWhere('id', request('type'));
        if ($selectedCat) {
            $pageTitle = $selectedCat->name . ' for Sale in North Bali | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency');
            $pageDesc = 'Discover luxury ' . strtolower($selectedCat->name) . ' for sale across North Bali. View photos, pricing, and property details.';
        }
    } elseif (request()->filled('location')) {
        $selectedLoc = $locations->firstWhere('slug', request('location')) ?? $locations->firstWhere('id', request('location'));
        if ($selectedLoc) {
            $pageTitle = 'Properties for Sale in ' . $selectedLoc->name . ', Bali | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency');
            $pageDesc = 'Browse property for sale in ' . $selectedLoc->name . ', North Bali. Explore villas, land plots, and residential properties with trusted legal guidance.';
        }
    }
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDesc)
@section('canonical', route('properties.index'))

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
      "name": "Properties",
      "item": "{{ route('properties.index') }}"
    }
  ]
}
</script>
@endsection

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">North Bali Property Listings</h1>
        <p class="body-text" style="color: var(--text-secondary);">Browse luxury villas, residential homes, beachfront land plots, and investment opportunities.</p>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <!-- Search & Filter Bar -->
        <div class="search-bar-box" style="margin-bottom: 40px;">
            <form action="{{ route('properties.index') }}" method="GET">
                <div class="search-bar-grid">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="keyword">Search Keyword / Title</label>
                        <input type="text" name="keyword" id="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="e.g. Ocean View, Villa, Lovina...">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="type">Property Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('type') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="location">Location</label>
                        <select name="location" id="location" class="form-select">
                            <option value="">All Locations</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->slug }}" {{ request('location') == $loc->slug ? 'selected' : '' }}>{{ $loc->name }}</option>
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

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-primary" id="btn-apply-filter">Apply Filter</button>
                        <a href="{{ route('properties.index') }}" class="btn btn-outline" id="btn-reset-filter">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Property Grid / Empty State -->
        @if($properties->count() > 0)
            <div class="property-grid">
                @foreach($properties as $prop)
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

            <!-- Pagination -->
            <div style="margin-top: 48px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                @if ($properties->total() > 0)
                    <div class="pagination-info" style="margin-bottom: 16px; font-size: 14px; color: var(--text-muted);">
                        Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of {{ $properties->total() }} results
                    </div>
                @endif
                {{ $properties->links('vendor.pagination.custom') }}
            </div>
        @else
            <!-- Mandatory Empty State -->
            <div class="empty-state-box">
                <div class="empty-state-icon" style="color: var(--text-muted); margin-bottom: 16px;">
                    <i data-lucide="search" class="lucide-icon lucide-icon-xl" style="color: var(--text-muted); width: 48px; height: 48px;"></i>
                </div>
                <h2 class="empty-state-title">No Properties Found</h2>
                <p class="empty-state-text">
                    We could not find any properties matching your current filter criteria. Try adjusting your search keywords, location, or price filters.
                </p>
                <div>
                    <a href="{{ route('properties.index') }}" class="btn btn-primary" id="btn-empty-reset">Reset All Filters</a>
                </div>

                <div style="margin-top: 32px;">
                    <div class="caption" style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Quick Shortcuts:</div>
                    <div class="chip-container">
                        <a href="{{ route('properties.index') }}" class="chip">All Locations</a>
                        <a href="{{ route('properties.index', ['type' => 'villa']) }}" class="chip">All Villas</a>
                        <a href="{{ route('properties.index', ['type' => 'land']) }}" class="chip">All Land Plots</a>
                        <a href="{{ route('properties.index', ['date_uploaded' => 'last_30_days']) }}" class="chip">Last 30 Days</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
