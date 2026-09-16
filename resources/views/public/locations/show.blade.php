@extends('layouts.public')

@section('title', 'Properties in ' . $location->name . ', North Bali | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', 'Explore properties for sale in ' . $location->name . ', North Bali. ' . Str::limit(strip_tags($location->description), 110) . ' Discover villas, land plots, and prime real estate.')
@section('canonical', route('locations.show', $location->slug))

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
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ $location->name }}",
      "item": "{{ route('locations.show', $location->slug) }}"
    }
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ItemPage",
  "name": "Properties in {{ $location->name }}, North Bali",
  "description": "{{ Str::limit(strip_tags($location->description), 160) }}",
  "url": "{{ route('locations.show', $location->slug) }}",
  "breadcrumb": {
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
      },
      {
        "@@type": "ListItem",
        "position": 3,
        "name": "{{ $location->name }}",
        "item": "{{ route('locations.show', $location->slug) }}"
      }
    ]
  }
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="section-spacing bg-light-blue" style="padding-top: 50px; padding-bottom: 50px; font-family: 'Poppins', sans-serif;">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="Breadcrumb" style="font-size: 14px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <i data-lucide="home" style="width: 16px; height: 16px;"></i>
            <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Home</a>
            <span>&gt;</span>
            <a href="{{ route('locations.index') }}" style="color: var(--text-muted); text-decoration: none;">Locations</a>
            <span>&gt;</span>
            <span style="color: var(--primary-navy); font-weight: 600;">{{ $location->name }}</span>
        </nav>

        <h1 style="margin-bottom: 12px; font-size: 40px; font-weight: 700; line-height: 1.2; color: var(--primary-navy);">
            Properties in {{ $location->name }}, North Bali
        </h1>
        <p class="body-text" style="color: var(--text-secondary); margin-bottom: 20px; font-size: 17px; line-height: 1.7; max-width: 860px;">
            {{ $location->description }}
        </p>
        
        <div style="display: flex; gap: 24px; flex-wrap: wrap; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 15px; color: var(--text-secondary);">
                <i data-lucide="home" class="lucide-icon" style="color: var(--primary-navy);"></i>
                <strong style="color: var(--primary-navy); font-weight: 700;">{{ $properties->total() }}</strong>
                {{ Str::plural('Property', $properties->total()) }} Available
            </div>
            @if($location->is_popular)
                <div style="display: flex; align-items: center; gap: 6px; font-size: 14px; background-color: #FEF3C7; color: #92400E; padding: 4px 12px; border-radius: var(--radius-sm); font-weight: 600;">
                    <i data-lucide="star" style="width: 14px; height: 14px; fill: #D97706; color: #D97706;"></i>
                    Popular Investment Area
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Property Listings in Location -->
<section class="section-spacing bg-white" style="padding-top: 48px; padding-bottom: 64px;">
    <div class="container">
        @if(isset($categories) && $categories->count() > 0 && ($properties->total() > 0 || request('type')))
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; align-items: center;">
                <span style="font-size: 14px; font-weight: 600; color: var(--text-muted); margin-right: 6px;">Filter by Type:</span>
                <a href="{{ route('locations.show', $location->slug) }}" 
                   class="btn" 
                   style="padding: 6px 16px; font-size: 13px; border-radius: 9999px; {{ !request('type') ? 'background: var(--primary-navy); color: #fff;' : 'background: #f1f5f9; color: var(--text-secondary); border: 1px solid #e2e8f0;' }}">
                    All Types
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('locations.show', [$location->slug, 'type' => $cat->slug]) }}" 
                       class="btn" 
                       style="padding: 6px 16px; font-size: 13px; border-radius: 9999px; {{ request('type') == $cat->slug ? 'background: var(--primary-navy); color: #fff;' : 'background: #f1f5f9; color: var(--text-secondary); border: 1px solid #e2e8f0;' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($properties->count() > 0)
            <div class="property-grid">
                @foreach($properties as $prop)
                    <div class="property-card">
                        <div class="property-card-image-wrap">
                            @if($prop->real_cover_image_url)
                                <img src="{{ $prop->real_cover_image_url }}" 
                                     alt="{{ $prop->name }} - {{ $prop->category->name ?? 'Property' }} in {{ $location->name }}, North Bali" 
                                     class="property-card-image" 
                                     loading="lazy" 
                                     onerror="this.onerror=null;this.style.display='none';">
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
                            <div class="property-category-tag">{{ $prop->category->name ?? 'Villa' }}</div>
                            <h3 class="property-card-title">{{ $prop->name }}</h3>
                            <div class="property-location-tag">
                                <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $location->name }}
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
                        Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of {{ $properties->total() }} results in {{ $location->name }}
                    </div>
                @endif
                {{ $properties->links('vendor.pagination.custom') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state-box">
                <div class="empty-state-icon" style="color: var(--text-muted); margin-bottom: 16px;">
                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-xl" style="color: var(--text-muted); width: 48px; height: 48px;"></i>
                </div>
                <h2 class="empty-state-title">No Properties Listed in {{ $location->name }} Yet</h2>
                <p class="empty-state-text">
                    We are continuously curating premium properties across {{ $location->name }} and North Bali. Inquire with our team or explore other nearby locations.
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('locations.index') }}" class="btn btn-outline">Explore All Locations</a>
                    <a href="{{ route('contact') }}" class="btn btn-primary">Contact Our Team</a>
                </div>
            </div>
        @endif

        <!-- Internal Links: Other Locations in North Bali -->
        @if(isset($otherLocations) && $otherLocations->count() > 0)
            <div style="margin-top: 64px; padding-top: 40px; border-top: 1px solid var(--border);">
                <h2 style="font-size: 24px; font-weight: 700; color: var(--primary-navy); margin-bottom: 16px;">
                    Explore Other Areas in North Bali
                </h2>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    @foreach($otherLocations as $other)
                        <a href="{{ route('locations.show', $other->slug) }}" 
                           class="btn btn-outline" 
                           style="border-color: var(--border); color: var(--primary-navy); padding: 8px 18px; font-size: 14px;">
                            {{ $other->name }}
                        </a>
                    @endforeach
                    <a href="{{ route('locations.index') }}" 
                       class="btn btn-outline" 
                       style="border-color: var(--primary-navy); color: var(--primary-navy); padding: 8px 18px; font-size: 14px; font-weight: 600;">
                        View All Locations &rarr;
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
