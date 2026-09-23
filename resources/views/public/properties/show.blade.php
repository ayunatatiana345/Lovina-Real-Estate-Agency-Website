{{-- Aragon handles property detail here. --}}
@extends('layouts.public')

@php
    $propCategory = $property->category->name ?? 'Property';
    $propLocation = $property->location->name ?? 'North Bali';
    $metaDesc = $propCategory . ' for sale in ' . $propLocation . ', Bali. ' . 
        ($property->bedrooms ? $property->bedrooms . ' beds, ' : '') . 
        ($property->bathrooms ? $property->bathrooms . ' baths, ' : '') . 
        ($property->land_size ? $property->land_size . ' sqm land. ' : '') . 
        (!empty($property->description) ? Str::limit(strip_tags($property->description), 110) : 'Explore listing details, verified location, and contact information with PT Lovina North Bali Real Estate Agency.');
@endphp

@section('title', $property->name . ' | Property for Sale in ' . $propLocation . ', Bali')
@section('meta_description', $metaDesc)
@section('canonical', route('properties.show', $property->slug))
@if($property->primary_image_url)
@section('og_image', $property->primary_image_url)
@endif

@section('structured_data')
<!-- JSON-LD BreadcrumbList Schema -->
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
    }@if($property->category),
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ $property->category->name }}",
      "item": "{{ route('properties.index', ['type' => $property->category->slug ?? $property->category->id]) }}"
    }@endif,
    {
      "@@type": "ListItem",
      "position": {{ $property->category ? 4 : 3 }},
      "name": "{{ $property->name }}",
      "item": "{{ route('properties.show', $property->slug) }}"
    }
  ]
}
</script>

<!-- JSON-LD RealEstateListing / SingleFamilyResidence Schema -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "SingleFamilyResidence",
  "name": "{{ $property->name }}",
  "description": "{{ Str::limit(strip_tags($property->description), 200) }}",
  "url": "{{ route('properties.show', $property->slug) }}",
  "datePosted": "{{ $property->created_at->toIso8601String() }}",
  @if($property->images->count() > 0)
  "image": [
    @foreach($property->images->take(5) as $img)
      "{{ $img->image_url }}"{{ !$loop->last ? ',' : '' }}
    @endforeach
  ],
  @endif
  @if($property->bedrooms)
  "numberOfBedrooms": {{ $property->bedrooms }},
  @endif
  @if($property->bathrooms)
  "numberOfBathroomsTotal": {{ $property->bathrooms }},
  @endif
  @if($property->building_size)
  "floorSize": {
    "@@type": "QuantitativeValue",
    "value": {{ $property->building_size }},
    "unitCode": "MTK"
  },
  @endif
  "address": {
    "@@type": "PostalAddress",
    "addressLocality": "{{ $property->location->name ?? 'Lovina' }}",
    "addressRegion": "Buleleng, Bali",
    "addressCountry": "ID"
  },
  @if($property->price !== null)
  "offers": {
    "@@type": "Offer",
    "price": "{{ $property->price }}",
    "priceCurrency": "IDR",
    "availability": "https://schema.org/InStock",
    "url": "{{ route('properties.show', $property->slug) }}"
  }
  @endif
}
</script>
@endsection

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $userCurr = $currencyService->getUserCurrency();
    
    // Collect all valid images for this property
    $allImages = $property->images->values();
    $coverImg = $allImages->firstWhere('is_cover', true) ?? $allImages->first();
    $mainImageUrl = $coverImg ? $coverImg->image_url : null;
    $totalImages = $allImages->count();

    // Land size in Are conversion
    $landSize = $property->land_size;
    $landAre = $landSize ? round($landSize / 100, 1) : null;
@endphp

<div class="property-detail-page">
    <div class="container">

        <!-- 1. Breadcrumb -->
        <nav class="pdp-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="pdp-breadcrumb-sep">&gt;</span>
            <a href="{{ route('properties.index') }}">Properties</a>
            <span class="pdp-breadcrumb-sep">&gt;</span>
            @if($property->category)
                <a href="{{ route('properties.index', ['type' => $property->category->slug ?? $property->category->id]) }}">{{ $property->category->name }}</a>
                <span class="pdp-breadcrumb-sep">&gt;</span>
            @endif
            <span class="pdp-breadcrumb-current">{{ $property->name }}</span>
        </nav>

        <!-- 2. Property Header -->
        <div class="pdp-header">
            <h1 class="pdp-title">{{ $property->name }}</h1>
            <div class="pdp-meta-bar">
                <span class="pdp-meta-item">
                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--primary-navy);"></i>
                    <span>{{ $property->location->name ?? 'Lovina' }}, Buleleng</span>
                </span>
                @if($property->category)
                    <span class="pdp-meta-item">
                        <i data-lucide="home" class="lucide-icon lucide-icon-sm" style="color: var(--primary-navy);"></i>
                        <span>{{ $property->category_badge }}</span>
                    </span>
                @endif
                @if($property->is_featured)
                    <span class="pdp-badge-featured">
                        <i data-lucide="sparkles" class="lucide-icon lucide-icon-sm"></i>
                        <span>Featured</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- 3. Main Grid (Left Content + Right Sticky Sidebar) -->
        <div class="pdp-main-grid">

            <!-- LEFT COLUMN -->
            <div class="pdp-main-col">

                <!-- Main Highlight Photo Hero -->
                <div class="pdp-hero-container" id="pdpHeroBox" @if($mainImageUrl) style="cursor: pointer;" title="Click to view full screen photo viewer" @endif>
                    @if($mainImageUrl)
                        @php
                            $coverImg = $allImages->firstWhere('is_cover', true) ?? $allImages->first();
                            $heroAlt = $coverImg ? ($coverImg->image_alt ?: $coverImg->alt_text) : ($property->name . ' in ' . ($property->location->name ?? 'North Bali') . ', North Bali');
                        @endphp
                        <img src="{{ $mainImageUrl }}" 
                             alt="{{ $heroAlt }}" 
                             id="pdpHeroImg" 
                             class="pdp-hero-img"
                             onerror="this.onerror=null;this.style.display='none';">
                        
                        <span class="pdp-photo-badge" id="pdpPhotoCounter">
                            <i data-lucide="camera" class="lucide-icon lucide-icon-sm"></i>
                            <span id="pdpCurrentIndex">1</span> / {{ $totalImages }}
                        </span>
                    @else
                        <div class="pdp-hero-placeholder" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 380px; background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); color: var(--text-muted); border-radius: var(--radius-md); text-align: center; padding: 32px;">
                            <i data-lucide="camera" class="lucide-icon" style="width: 56px; height: 56px; stroke-width: 1.5; margin-bottom: 16px; opacity: 0.45;"></i>
                            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Photos Coming Soon</h3>
                            <p style="font-size: 14px; max-width: 380px; color: var(--text-muted); margin-bottom: 0;">Genuine photos for this listing are being updated. Contact our team to request current photos or arrange an on-site viewing.</p>
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Carousel Slider (Only if images exist) -->
                @if($allImages->count() > 0)
                    <div class="pdp-thumb-bar">
                        <button type="button" class="pdp-thumb-arrow" id="pdpThumbPrev" aria-label="Previous photo">
                            <i data-lucide="chevron-left" class="lucide-icon"></i>
                        </button>
                        <div class="pdp-thumb-strip" id="pdpThumbStrip">
                            @foreach($allImages as $index => $img)
                                <button type="button" 
                                         class="pdp-thumb-item {{ $index === 0 ? 'active' : '' }}" 
                                         data-src="{{ $img->image_url }}" 
                                         data-index="{{ $index + 1 }}"
                                         aria-label="View photo {{ $index + 1 }}">
                                    <img src="{{ $img->image_url }}" 
                                         alt="{{ $img->image_alt ?: $img->alt_text }}"
                                         onerror="this.onerror=null;this.style.display='none';">
                                </button>
                            @endforeach
                        </div>
                        <button type="button" class="pdp-thumb-arrow" id="pdpThumbNext" aria-label="Next photo">
                            <i data-lucide="chevron-right" class="lucide-icon"></i>
                        </button>
                    </div>
                @else
                    <div style="margin-bottom: 32px;"></div>
                @endif

                @if(!empty(trim($property->description ?? '')))
                <!-- About This Property -->
                <section class="pdp-section" id="about-property">
                    <h2 class="pdp-section-title">About This Property</h2>
                    <div class="pdp-description-text">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </section>
                @endif

                <!-- Detailed Photo Gallery (Vertical room-by-room explore) -->
                @if($allImages->count() > 0)
                    <section class="pdp-section" id="property-gallery">
                        <h2 class="pdp-section-title">Property Gallery</h2>
                        <p class="pdp-section-subtitle">Explore more photos of this property (Click any image to view in full screen)</p>

                        <div class="pdp-gallery-list">
                            @foreach($allImages as $idx => $galleryImg)
                                @php
                                    $photoNumber = $idx + 1;
                                    $photoTitle = 'Photo ' . $photoNumber;
                                    $photoDesc = 'Photo ' . $photoNumber . ' of ' . $totalImages . ' — ' . $property->name;
                                @endphp
                                <div class="pdp-gallery-card pdp-gallery-clickable" 
                                     data-index="{{ $idx }}" 
                                     style="cursor: pointer;" 
                                     title="Click to view {{ $photoTitle }} in full screen">
                                    <div class="pdp-gallery-img-wrap">
                                        <img src="{{ $galleryImg->image_url }}" 
                                             alt="{{ $galleryImg->image_alt ?: $galleryImg->alt_text }}" 
                                             loading="lazy"
                                             onerror="this.onerror=null;this.style.display='none';">
                                    </div>
                                    <div class="pdp-gallery-info">
                                        <h3 class="pdp-gallery-room-title">{{ $photoTitle }}</h3>
                                        <p class="pdp-gallery-room-desc">{{ $photoDesc }}</p>
                                        <div style="margin-top: 8px;">
                                            <span style="font-size: 13px; font-weight: 600; color: var(--primary-navy); display: inline-flex; align-items: center; gap: 4px;">
                                                <i data-lucide="maximize-2" class="lucide-icon lucide-icon-sm"></i> Click to enlarge
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Property Features / Facilities -->
                @php
                    $hasAnyFeatures = $property->hasFeature('swimming_pool') ||
                                      $property->hasFeature('garden') ||
                                      $property->hasFeature('furnishing') ||
                                      $property->hasFeature('air_conditioning') ||
                                      $property->hasFeature('water_supply') ||
                                      !empty($property->water_supply) ||
                                      !empty($property->electricity) ||
                                      $property->hasFeature('garage') ||
                                      $property->hasFeature('internet') ||
                                      $property->hasFeature('security');
                @endphp
                @if($hasAnyFeatures)
                    <section class="pdp-section" id="property-features">
                        <h2 class="pdp-section-title">Property Features</h2>
                        <p class="pdp-section-subtitle">This property comes with the following features</p>

                        <div class="pdp-features-grid">
                            @if($property->hasFeature('swimming_pool'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="waves" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Swimming Pool</span>
                                </div>
                            @endif

                            @if($property->hasFeature('garden'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="leaf" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Garden</span>
                                </div>
                            @endif

                            @if($property->hasFeature('furnishing'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="armchair" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>{{ $property->furnishing }}</span>
                                </div>
                            @endif

                            @if($property->hasFeature('air_conditioning'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="snowflake" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Air Conditioning{{ $property->air_conditioning && $property->air_conditioning !== 'Yes' ? ' (' . $property->air_conditioning . ')' : '' }}</span>
                                </div>
                            @endif

                            @if($property->hasFeature('water_supply') || !empty($property->water_supply))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="droplet" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Water Supply{{ !empty($property->water_supply) ? ' (' . $property->water_supply . ')' : '' }}</span>
                                </div>
                            @endif

                            @if(!empty($property->electricity))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="zap" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Electricity ({{ $property->electricity }})</span>
                                </div>
                            @endif

                            @if($property->hasFeature('garage') || ($property->garage && $property->garage > 0))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="car" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Garage{{ $property->garage ? ' (' . $property->garage . ' ' . Str::plural('Car', $property->garage) . ')' : '' }}</span>
                                </div>
                            @endif

                            @if($property->hasFeature('internet'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="wifi" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>High-Speed Internet</span>
                                </div>
                            @endif

                            @if($property->hasFeature('security'))
                                <div class="pdp-feature-chip">
                                    <i data-lucide="shield-check" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                    <span>Secure Environment</span>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

            </div>

            <!-- RIGHT SIDEBAR (STICKY) -->
            <aside class="pdp-sidebar">

                <!-- Price & Primary CTA Card -->
                <div class="pdp-card pdp-price-card">
                    <div class="pdp-price-label">Price</div>
                    <div class="pdp-price-value" id="pdpPriceDisplay">
                        {{ $property->formatted_price }}
                    </div>

                    <!-- Dual Currency Approximate Notice -->
                    @if($property->price !== null)
                        <div class="pdp-price-sub" id="pdpDualCurrencyNote">
                            @if($userCurr === 'USD')
                                ≈ IDR {{ number_format($property->price, 0, ',', '.') }}
                                <span title="Converted using latest published reference exchange rate from Frankfurter (European Central Bank)" style="cursor: help; color: var(--medium-blue);">ⓘ</span>
                            @else
                                @php
                                    $usdVal = $currencyService->convert((float)$property->price, 'IDR', 'USD');
                                @endphp
                                ≈ USD {{ number_format($usdVal, 0) }}
                                <span title="Converted using latest published reference exchange rate from Frankfurter (European Central Bank)" style="cursor: help; color: var(--medium-blue);">ⓘ</span>
                            @endif
                        </div>
                    @endif

                    <!-- Quick Currency Dropdown & Toggle -->
                    <div class="pdp-currency-row">
                        <select class="form-select pdp-currency-select" id="pdpCurrencySelector" aria-label="Select Currency" onchange="window.location.href=this.value">
                            <option value="{{ route('currency.switch', 'USD') }}" {{ $userCurr === 'USD' ? 'selected' : '' }}>USD – US Dollar</option>
                            <option value="{{ route('currency.switch', 'IDR') }}" {{ $userCurr === 'IDR' ? 'selected' : '' }}>IDR – Indonesian Rupiah</option>
                        </select>
                        <a href="{{ route('currency.switch', $userCurr === 'USD' ? 'IDR' : 'USD') }}" 
                           class="btn btn-outline pdp-currency-swap-btn" 
                           title="Switch to {{ $userCurr === 'USD' ? 'IDR' : 'USD' }}"
                           aria-label="Switch Currency">
                            <i data-lucide="arrow-left-right" class="lucide-icon lucide-icon-sm"></i>
                        </a>
                    </div>

                    <!-- Contact & WhatsApp Buttons -->
                    <div class="pdp-actions-stack">
                        <button type="button" class="btn btn-primary pdp-btn-action" id="pdpOpenInquiryBtn">
                            Contact Us
                        </button>

                        @php
                            $waMessage = "Hello Lovina Real Estate Agency, I am interested in " . $property->name . " (Price: " . $property->formatted_price . "). Please arrange more information and viewing schedule.";
                            $waCleanPhone = preg_replace('/[^0-9]/', '', $settings->whatsapp ?? '6281234567890');
                        @endphp
                        <a href="https://wa.me/{{ $waCleanPhone }}?text={{ urlencode($waMessage) }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn btn-outline pdp-btn-wa"
                           style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #16A34A; flex-shrink: 0;" aria-hidden="true">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            WhatsApp Us
                        </a>
                    </div>
                </div>

                <!-- Key Information Card (Only show fields with real data) -->
                @php
                    $hasKeyInfo = ($property->bedrooms && $property->bedrooms > 0) ||
                                  ($property->bathrooms && $property->bathrooms > 0) ||
                                  ($property->building_size && $property->building_size > 0) ||
                                  ($property->land_size && $property->land_size > 0) ||
                                  ($property->garage && $property->garage > 0) ||
                                  !empty($property->electricity);
                @endphp

                @if($hasKeyInfo)
                    <div class="pdp-card pdp-info-card">
                        <h3 class="pdp-card-title">Key Information</h3>
                        <div class="pdp-info-list">
                            @if($property->bedrooms && $property->bedrooms > 0)
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="bed" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Bedrooms</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->bedrooms }}</div>
                                </div>
                            @endif

                            @if($property->bathrooms && $property->bathrooms > 0)
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="bath" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Bathrooms</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->bathrooms }}</div>
                                </div>
                            @endif

                            @if($property->building_size && $property->building_size > 0)
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="building" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Building Size</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->building_size }} m²</div>
                                </div>
                            @endif

                            @if($property->land_size && $property->land_size > 0)
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="maximize" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Land Size</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->land_size }} m² @if($landAre)({{ $landAre }} are)@endif</div>
                                </div>
                            @endif

                            @if($property->garage && $property->garage > 0)
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="car" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Garage</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->garage }} {{ Str::plural('car', $property->garage) }}</div>
                                </div>
                            @endif

                            @if(!empty($property->electricity))
                                <div class="pdp-info-item">
                                    <div class="pdp-info-left">
                                        <i data-lucide="zap" class="lucide-icon" style="color: var(--primary-navy);"></i>
                                        <span>Electricity</span>
                                    </div>
                                    <div class="pdp-info-val">{{ $property->electricity }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Additional Details Card -->
                <div class="pdp-card pdp-details-card">
                    <h3 class="pdp-card-title">Additional Details</h3>
                    <table class="pdp-details-table">
                        <tbody>
                            @if($property->category)
                                <tr>
                                    <td class="label">Property Type</td>
                                    <td class="value">{{ $property->category_badge }}</td>
                                </tr>
                            @endif

                            @if(!empty($property->ownership_type))
                                <tr>
                                    <td class="label">Ownership</td>
                                    <td class="value">{{ $property->ownership_type }}</td>
                                </tr>
                            @endif

                            @if(!empty($property->furnishing))
                                <tr>
                                    <td class="label">Furnishing</td>
                                    <td class="value">{{ $property->furnishing }}</td>
                                </tr>
                            @endif

                            @if(!empty($property->water_supply))
                                <tr>
                                    <td class="label">Water Supply</td>
                                    <td class="value">{{ $property->water_supply }}</td>
                                </tr>
                            @endif

                            @if($property->swimming_pool !== null && !in_array(strtolower($property->category->name ?? ''), ['land', 'lands']))
                                <tr>
                                    <td class="label">Swimming Pool</td>
                                    <td class="value">{{ $property->swimming_pool ? 'Yes (Private)' : 'No' }}</td>
                                </tr>
                            @endif

                            @if(!empty($property->air_conditioning) && !in_array(strtolower($property->category->name ?? ''), ['land', 'lands']))
                                <tr>
                                    <td class="label">Air Conditioning</td>
                                    <td class="value">{{ $property->air_conditioning }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td class="label">Status</td>
                                <td class="value">{{ ucfirst($property->status ?? 'Available') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </aside>

        </div>

        <!-- 4. Interested in This Property? CTA Banner -->
        <div class="pdp-cta-banner">
            <div>
                <h3 class="pdp-cta-title">Interested in This Property?</h3>
                <p class="pdp-cta-text">Contact our team for more information, arrange a viewing, or discuss your property requirements.</p>
            </div>
            <div class="pdp-cta-btns">
                <button type="button" class="btn btn-primary" id="pdpBannerInquiryBtn">
                    Contact Us
                </button>
                <a href="https://wa.me/{{ $waCleanPhone }}?text={{ urlencode($waMessage) }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="btn btn-outline" 
                   style="border-color: #16A34A; color: #16A34A; background-color: var(--white); display: inline-flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #16A34A; flex-shrink: 0;" aria-hidden="true">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    WhatsApp Us
                </a>
            </div>
        </div>

        <!-- 5. You May Also Like (Related Properties) -->
        @if(isset($similarProperties) && $similarProperties->count() > 0)
            <section class="pdp-related-section">
                <h2 class="pdp-section-title">You May Also Like</h2>
                <p class="pdp-section-subtitle">Discover other properties in North Bali</p>

                <div class="pdp-related-grid">
                    @foreach($similarProperties as $similar)
                        <div class="pdp-related-card">
                            <div class="pdp-related-img-wrap">
                                @if($similar->real_cover_image_url)
                                    <img src="{{ $similar->real_cover_image_url }}" 
                                         alt="{{ $similar->name }}" 
                                         class="pdp-related-img" 
                                         loading="lazy" 
                                         onerror="this.onerror=null;this.style.display='none';">
                                @else
                                    <div class="property-card-no-image" style="height: 100%;">
                                        <i data-lucide="camera" class="lucide-icon" style="width: 28px; height: 28px; stroke-width: 1.5; opacity: 0.45; margin-bottom: 4px;"></i>
                                        <span style="font-size: 12px; font-weight: 500; opacity: 0.65;">Photos coming soon</span>
                                    </div>
                                @endif
                                @if($similar->category)
                                    <span class="pdp-related-cat-badge">{{ $similar->category_badge }}</span>
                                @endif
                            </div>
                            <div class="pdp-related-body">
                                <h3 class="pdp-related-title">{{ $similar->name }}</h3>
                                <div class="pdp-related-loc">
                                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm"></i>
                                    <span>{{ $similar->location->name ?? 'Lovina' }}, Buleleng</span>
                                </div>
                                <div class="pdp-related-footer">
                                    <div class="pdp-related-price">{{ $similar->formatted_price }}</div>
                                    <a href="{{ route('properties.show', $similar->slug) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 13px; font-weight: 600;">
                                        View Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    <!-- 6. Full-Screen Lightbox Image Viewer -->
    <div class="pdp-lightbox" id="pdpLightboxModal" role="dialog" aria-modal="true" aria-label="Property Image Lightbox">
        <!-- Lightbox Header -->
        <div class="pdp-lightbox-header">
            <div class="pdp-lightbox-title">
                <span>{{ $property->name }}</span>
                <span class="pdp-lightbox-counter" id="lbCounter">1 / {{ $totalImages }}</span>
            </div>

            <div class="pdp-lightbox-tools">
                <button type="button" class="pdp-lightbox-tool-btn" id="lbZoomIn" title="Zoom In (+)">
                    <i data-lucide="zoom-in" class="lucide-icon-sm"></i>
                    <span>Zoom In</span>
                </button>
                <button type="button" class="pdp-lightbox-tool-btn" id="lbZoomOut" title="Zoom Out (-)">
                    <i data-lucide="zoom-out" class="lucide-icon-sm"></i>
                    <span>Zoom Out</span>
                </button>
                <button type="button" class="pdp-lightbox-tool-btn" id="lbZoomReset" title="Reset Zoom">
                    <i data-lucide="rotate-ccw" class="lucide-icon-sm"></i>
                    <span>Reset</span>
                </button>
                <button type="button" class="pdp-lightbox-close-btn" id="lbCloseBtn" title="Close Viewer (Esc)">
                    <i data-lucide="x" class="lucide-icon-sm"></i>
                    <span>Close</span>
                </button>
            </div>
        </div>

        <!-- Lightbox Body -->
        <div class="pdp-lightbox-body" id="lbBody">
            <button type="button" class="pdp-lightbox-nav prev" id="lbPrevBtn" aria-label="Previous image">
                <i data-lucide="chevron-left" class="lucide-icon"></i>
                <span>Previous</span>
            </button>

            <div class="pdp-lightbox-img-wrap" id="lbImgWrap">
                <img src="{{ $mainImageUrl }}" alt="{{ $property->name }}" id="lbImg" class="pdp-lightbox-img" onerror="this.onerror=null;this.style.display='none';">
            </div>

            <button type="button" class="pdp-lightbox-nav next" id="lbNextBtn" aria-label="Next image">
                <span>Next</span>
                <i data-lucide="chevron-right" class="lucide-icon"></i>
            </button>
        </div>

        <!-- Lightbox Footer -->
        <div class="pdp-lightbox-footer">
            <p class="pdp-lightbox-caption" id="lbCaption">Photo 1 of {{ $totalImages }} — {{ $property->name }}</p>
        </div>
    </div>

    <!-- 7. Inquiry Modal Dialog for This Property -->
    <div class="pdp-modal-overlay" id="pdpInquiryModal">
        <div class="pdp-modal-box">
            <button type="button" class="modal-close-btn" id="pdpCloseModalBtn" aria-label="Close inquiry popup">&times;</button>
            
            <h3 style="font-size: 24px; color: var(--primary-navy); margin-bottom: 6px;">Inquire About This Property</h3>
            <p class="caption" style="margin-bottom: 20px;">
                Interested in <strong>{{ $property->name }}</strong>? Fill out your details below and our agent will connect with you promptly.
            </p>

            <form action="{{ route('inquiry.store') }}" method="POST" id="pdpInquiryForm">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="source" value="Property Detail Page">
                <input type="hidden" name="subject" value="Inquiry for {{ $property->name }}">

                <div class="form-group">
                    <label class="form-label" for="pdp_customer_name">Full Name *</label>
                    <input type="text" name="customer_name" id="pdp_customer_name" class="form-control" placeholder="Your full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pdp_email">Email Address *</label>
                    <input type="email" name="email" id="pdp_email" class="form-control" placeholder="name@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pdp_phone">Phone / WhatsApp *</label>
                    <input type="text" name="phone" id="pdp_phone" class="form-control" placeholder="+62 812 3456 7890" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pdp_message">Message *</label>
                    <textarea name="message" id="pdp_message" class="form-control" required style="min-height: 100px;">Hello, I am interested in {{ $property->name }}. Please send me further details and available viewing times.</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 8px;">
                    Send Inquiry &rarr;
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gallery Data Collection
    @php
        $galleryData = [];
        if ($allImages->count() > 0) {
            foreach ($allImages as $idx => $img) {
                $photoNum = $idx + 1;
                $galleryData[] = [
                    'url' => $img->image_url,
                    'title' => 'Photo ' . $photoNum,
                    'desc' => 'Photo ' . $photoNum . ' of ' . $totalImages . ' — ' . $property->name
                ];
            }
        }
    @endphp
    const galleryItems = @json($galleryData);
    let activeHeroIdx = 0;
    let lbActiveIdx = 0;
    let lbZoomLevel = 1.0;

    const heroImg = document.getElementById('pdpHeroImg');
    const heroBox = document.getElementById('pdpHeroBox');
    const currentIndexEl = document.getElementById('pdpCurrentIndex');
    const thumbItems = document.querySelectorAll('.pdp-thumb-item');
    const thumbStrip = document.getElementById('pdpThumbStrip');
    const prevBtn = document.getElementById('pdpThumbPrev');
    const nextBtn = document.getElementById('pdpThumbNext');

    function setActiveHeroImage(index) {
        if (index < 0) index = thumbItems.length - 1;
        if (index >= thumbItems.length) index = 0;
        activeHeroIdx = index;

        thumbItems.forEach((btn, i) => {
            if (i === activeHeroIdx) {
                btn.classList.add('active');
                const src = btn.getAttribute('data-src');
                if (heroImg && src) {
                    heroImg.src = src;
                }
                if (currentIndexEl) {
                    currentIndexEl.textContent = (activeHeroIdx + 1);
                }
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                btn.classList.remove('active');
            }
        });
    }

    if (thumbItems.length > 0) {
        thumbItems.forEach((btn, i) => {
            btn.addEventListener('click', function() {
                setActiveHeroImage(i);
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                setActiveHeroImage(activeHeroIdx - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                setActiveHeroImage(activeHeroIdx + 1);
            });
        }
    }

    // 2. Lightbox Full-Screen Viewer
    const lbModal = document.getElementById('pdpLightboxModal');
    const lbImg = document.getElementById('lbImg');
    const lbCounter = document.getElementById('lbCounter');
    const lbCaption = document.getElementById('lbCaption');
    const lbPrevBtn = document.getElementById('lbPrevBtn');
    const lbNextBtn = document.getElementById('lbNextBtn');
    const lbCloseBtn = document.getElementById('lbCloseBtn');
    const lbZoomIn = document.getElementById('lbZoomIn');
    const lbZoomOut = document.getElementById('lbZoomOut');
    const lbZoomReset = document.getElementById('lbZoomReset');

    function updateLightboxDisplay() {
        if (!galleryItems || galleryItems.length === 0) return;
        if (lbActiveIdx < 0) lbActiveIdx = galleryItems.length - 1;
        if (lbActiveIdx >= galleryItems.length) lbActiveIdx = 0;

        const current = galleryItems[lbActiveIdx];
        if (lbImg && current) {
            lbImg.src = current.url;
        }
        if (lbCounter) {
            lbCounter.textContent = `${lbActiveIdx + 1} / ${galleryItems.length}`;
        }
        if (lbCaption && current) {
            lbCaption.textContent = current.desc;
        }

        // Reset zoom
        lbZoomLevel = 1.0;
        applyZoom();

        // Single image navigation state
        if (galleryItems.length <= 1) {
            if (lbPrevBtn) lbPrevBtn.style.display = 'none';
            if (lbNextBtn) lbNextBtn.style.display = 'none';
        } else {
            if (lbPrevBtn) lbPrevBtn.style.display = 'inline-flex';
            if (lbNextBtn) lbNextBtn.style.display = 'inline-flex';
        }
    }

    function applyZoom() {
        if (lbImg) {
            lbImg.style.transform = `scale(${lbZoomLevel})`;
        }
    }

    function openLightbox(index = 0) {
        lbActiveIdx = parseInt(index) || 0;
        updateLightboxDisplay();
        if (lbModal) {
            lbModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightbox() {
        if (lbModal) {
            lbModal.style.display = 'none';
            document.body.style.overflow = '';
        }
        lbZoomLevel = 1.0;
        applyZoom();
    }

    function nextLbImage() {
        lbActiveIdx = (lbActiveIdx + 1) % galleryItems.length;
        updateLightboxDisplay();
    }

    function prevLbImage() {
        lbActiveIdx = (lbActiveIdx - 1 + galleryItems.length) % galleryItems.length;
        updateLightboxDisplay();
    }

    // Trigger Lightbox from Hero Box
    if (heroBox && galleryItems && galleryItems.length > 0) {
        heroBox.addEventListener('click', function() {
            openLightbox(activeHeroIdx);
        });
    }

    // Trigger Lightbox from Vertical Gallery Cards
    document.querySelectorAll('.pdp-gallery-clickable').forEach(card => {
        card.addEventListener('click', function() {
            const idx = this.getAttribute('data-index');
            openLightbox(idx);
        });
    });

    // Lightbox Controls Events
    if (lbCloseBtn) lbCloseBtn.addEventListener('click', closeLightbox);
    if (lbNextBtn) lbNextBtn.addEventListener('click', nextLbImage);
    if (lbPrevBtn) lbPrevBtn.addEventListener('click', prevLbImage);

    if (lbZoomIn) {
        lbZoomIn.addEventListener('click', function() {
            if (lbZoomLevel < 2.5) {
                lbZoomLevel += 0.25;
                applyZoom();
            }
        });
    }

    if (lbZoomOut) {
        lbZoomOut.addEventListener('click', function() {
            if (lbZoomLevel > 0.75) {
                lbZoomLevel -= 0.25;
                applyZoom();
            }
        });
    }

    if (lbZoomReset) {
        lbZoomReset.addEventListener('click', function() {
            lbZoomLevel = 1.0;
            applyZoom();
        });
    }

    // Keyboard controls for Lightbox
    document.addEventListener('keydown', function(e) {
        if (!lbModal || lbModal.style.display !== 'flex') return;

        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowRight') {
            nextLbImage();
        } else if (e.key === 'ArrowLeft') {
            prevLbImage();
        } else if (e.key === '+' || e.key === '=') {
            if (lbZoomLevel < 2.5) {
                lbZoomLevel += 0.25;
                applyZoom();
            }
        } else if (e.key === '-') {
            if (lbZoomLevel > 0.75) {
                lbZoomLevel -= 0.25;
                applyZoom();
            }
        }
    });

    // 3. Inquiry Modal Handling
    const modal = document.getElementById('pdpInquiryModal');
    const openBtn1 = document.getElementById('pdpOpenInquiryBtn');
    const openBtn2 = document.getElementById('pdpBannerInquiryBtn');
    const closeBtn = document.getElementById('pdpCloseModalBtn');

    function openModal() {
        if (modal) modal.style.display = 'flex';
    }

    function closeModal() {
        if (modal) modal.style.display = 'none';
    }

    if (openBtn1) openBtn1.addEventListener('click', openModal);
    if (openBtn2) openBtn2.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Initialize Lucide icons
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endsection
