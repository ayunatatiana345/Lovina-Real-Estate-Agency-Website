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
                            <option value="under_2b" {{ request('price_range') == 'under_2b' ? 'selected' : '' }}>Under IDR 2 Billion</option>
                            <option value="2b_to_5b" {{ request('price_range') == '2b_to_5b' ? 'selected' : '' }}>IDR 2 Billion – IDR 5 Billion</option>
                            <option value="above_5b" {{ request('price_range') == 'above_5b' ? 'selected' : '' }}>Above IDR 5 Billion</option>
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
                            @if($prop->is_featured)
                                <span class="property-badge-featured">
                                    <i data-lucide="star" class="lucide-icon lucide-icon-sm" style="fill: var(--white); stroke: var(--white); margin-right: 4px;"></i>Featured
                                </span>
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
