@extends('layouts.public')

@section('title', 'Properties - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

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
                            <option value="">Any Price</option>
                            <option value="under_250k" {{ request('price_range') == 'under_250k' ? 'selected' : '' }}>Under $250,000</option>
                            <option value="250k_500k" {{ request('price_range') == '250k_500k' ? 'selected' : '' }}>$250,000 - $500,000</option>
                            <option value="above_500k" {{ request('price_range') == 'above_500k' ? 'selected' : '' }}>Above $500,000</option>
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
                            <img src="{{ $prop->primary_image_url }}" alt="{{ $prop->name }}" class="property-card-image">
                            @if($prop->is_featured)
                                <span class="property-badge-featured">★ Featured</span>
                            @endif
                        </div>
                        <div class="property-card-body">
                            <div class="property-category-tag">{{ $prop->category->name ?? 'Villa' }}</div>
                            <h3 class="property-card-title">{{ $prop->name }}</h3>
                            <div class="property-location-tag">
                                📍 {{ $prop->location->name ?? 'North Bali' }}
                            </div>
                            <div class="property-price">USD ${{ number_format($prop->price) }}</div>
                            <div class="property-specs-bar">
                                <div class="spec-item">🛏 {{ $prop->bedrooms }} Beds</div>
                                <div class="spec-item">🚿 {{ $prop->bathrooms }} Baths</div>
                                <div class="spec-item">📐 {{ $prop->land_size }} m²</div>
                            </div>
                            <div style="margin-top: 16px;">
                                <a href="{{ route('properties.show', $prop->slug) }}" class="btn btn-outline" style="width: 100%;">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 48px; display: flex; justify-content: center;">
                {{ $properties->links() }}
            </div>
        @else
            <!-- Mandatory Empty State -->
            <div class="empty-state-box">
                <div class="empty-state-icon">🔍</div>
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
