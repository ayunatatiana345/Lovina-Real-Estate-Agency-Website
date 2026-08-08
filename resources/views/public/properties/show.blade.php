@extends('layouts.public')

@section('title', $property->name . ' - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', Str::limit(strip_tags($property->description), 160))

@section('head_extra')
<!-- JSON-LD Schema RealEstateListing -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "RealEstateListing",
  "name": "{{ $property->name }}",
  "description": "{{ Str::limit(strip_tags($property->description), 200) }}",
  "url": "{{ url()->current() }}",
  "datePosted": "{{ $property->created_at->toIso8601String() }}",
  "price": "{{ $property->price }}",
  "priceCurrency": "USD",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "{{ $property->location->name ?? 'Lovina' }}",
    "addressRegion": "Buleleng, Bali",
    "addressCountry": "ID"
  }
}
</script>
@endsection

@section('content')
<section class="section-spacing bg-white" style="padding-top: 40px;">
    <div class="container">
        <!-- Breadcrumb -->
        <div style="margin-bottom: 24px; font-size: 15px; color: var(--text-muted);">
            <a href="{{ route('home') }}">Home</a> &gt; 
            <a href="{{ route('properties.index') }}">Properties</a> &gt; 
            <a href="{{ route('properties.index', ['location' => $property->location->slug ?? '']) }}">{{ $property->location->name ?? 'Location' }}</a> &gt;
            <span style="color: var(--text-primary); font-weight: 600;">{{ $property->name }}</span>
        </div>

        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 32px;">
            <div>
                <span class="property-category-tag" style="font-size: 16px;">{{ $property->category->name }} &bull; {{ $property->ownership_type }}</span>
                <h1 style="margin-bottom: 8px;">{{ $property->name }}</h1>
                <div style="font-size: 18px; color: var(--text-secondary);">
                    📍 {{ $property->location->name }}, Buleleng Regency, North Bali
                </div>
            </div>

            <div style="text-align: right;">
                <div class="caption">Asking Price</div>
                <div style="font-size: 38px; font-weight: 700; color: var(--primary-navy);">
                    USD ${{ number_format($property->price) }}
                </div>
            </div>
        </div>

        <!-- Image Gallery Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 48px;">
            <div style="height: 480px; border-radius: var(--radius-md); overflow: hidden; background-color: var(--light-gray);">
                <img src="{{ $property->primary_image_url }}" alt="{{ $property->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($property->images->take(2) as $img)
                    <div style="height: 232px; border-radius: var(--radius-md); overflow: hidden; background-color: var(--light-gray);">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $property->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Details Grid Layout -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 48px;">
            <!-- Left Side Details -->
            <div>
                <div style="background-color: var(--light-gray); border-radius: var(--radius-md); padding: 32px; margin-bottom: 40px;">
                    <h3 style="margin-bottom: 24px;">Property Specifications</h3>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div>
                            <div class="caption">Bedrooms</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">🛏 {{ $property->bedrooms }}</div>
                        </div>
                        <div>
                            <div class="caption">Bathrooms</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">🚿 {{ $property->bathrooms }}</div>
                        </div>
                        <div>
                            <div class="caption">Land Size</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">📐 {{ $property->land_size }} m²</div>
                        </div>
                        <div>
                            <div class="caption">Building Size</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">🏛 {{ $property->building_size }} m²</div>
                        </div>
                        <div>
                            <div class="caption">Garage</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">🚗 {{ $property->garage }} Cars</div>
                        </div>
                        <div>
                            <div class="caption">Swimming Pool</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--primary-navy);">🏊‍♂️ {{ $property->swimming_pool ? 'Yes (Private)' : 'No' }}</div>
                        </div>
                        <div>
                            <div class="caption">Electricity</div>
                            <div style="font-size: 18px; font-weight: 600; color: var(--text-primary);">⚡ {{ $property->electricity ?? 'Standard' }}</div>
                        </div>
                        <div>
                            <div class="caption">Water Supply</div>
                            <div style="font-size: 18px; font-weight: 600; color: var(--text-primary);">💧 {{ $property->water_supply ?? 'PDAM / Well' }}</div>
                        </div>
                        <div>
                            <div class="caption">Ownership Title</div>
                            <div style="font-size: 18px; font-weight: 600; color: var(--text-primary);">📜 {{ $property->ownership_type }}</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 40px;">
                    <h3 style="margin-bottom: 16px;">Property Description</h3>
                    <div class="body-text" style="line-height: 1.8; color: var(--text-primary); white-space: pre-line;">
                        {{ $property->description }}
                    </div>
                </div>

                <!-- Location Map Embed Card -->
                <div style="margin-bottom: 40px;">
                    <h3 style="margin-bottom: 16px;">Location & Area</h3>
                    <div style="background-color: var(--light-blue); border-radius: var(--radius-md); padding: 24px;">
                        <h4 style="color: var(--primary-navy); margin-bottom: 8px;">📍 {{ $property->location->name }} Area Overview</h4>
                        <p style="font-size: 16px; color: var(--text-primary); margin-bottom: 16px;">
                            {{ $property->location->description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side Inquiry Card -->
            <div>
                <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-md); position: sticky; top: 100px;">
                    <h3 style="font-size: 24px; margin-bottom: 8px;">Inquire About This Property</h3>
                    <p class="caption" style="margin-bottom: 24px;">Fill out the form below to request a private viewing or video tour.</p>

                    <form action="{{ route('inquiry.store') }}" method="POST" id="inquiryForm">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                        <div class="form-group">
                            <label class="form-label" for="customer_name">Full Name *</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="John Doe" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Phone / WhatsApp *</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="+62 812 3456 7890" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Message *</label>
                            <textarea name="message" id="message" class="form-control" required>Hello, I am interested in {{ $property->name }}. Please send me more information and viewing schedule.</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">Send Inquiry &rarr;</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
