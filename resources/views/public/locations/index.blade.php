@extends('layouts.public')

@section('title', 'Locations - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">Explore North Bali Locations</h1>
        <p class="body-text" style="color: var(--text-secondary);">
            Discover key coastal and mountain regions across Buleleng Regency.
        </p>

        <div style="display: flex; gap: 32px; margin-top: 32px;">
            <div style="background-color: var(--white); border-radius: var(--radius-md); padding: 20px 32px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 32px; font-weight: 700; color: var(--primary-navy);">{{ $totalLocations }}</div>
                <div class="caption">Active Locations</div>
            </div>
            <div style="background-color: var(--white); border-radius: var(--radius-md); padding: 20px 32px; box-shadow: var(--shadow-sm);">
                <div style="font-size: 32px; font-weight: 700; color: var(--primary-navy);">{{ $totalProperties }}</div>
                <div class="caption">Total Listed Properties</div>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <h2 style="margin-bottom: 32px;">Popular Real Estate Destinations</h2>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 64px;">
            @foreach($locations as $loc)
                <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
                    <div style="height: 200px; background-color: var(--light-gray); overflow: hidden; position: relative;">
                        <img src="{{ $loc->image ? asset('storage/' . $loc->image) : asset('images/location-placeholder.jpg') }}" alt="{{ $loc->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <span style="position: absolute; top: 12px; right: 12px; background-color: var(--primary-navy); color: var(--white); font-size: 13px; font-weight: 600; padding: 4px 10px; border-radius: var(--radius-sm);">
                            {{ $loc->properties_count }} Properties
                        </span>
                    </div>
                    <div style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-size: 24px; margin-bottom: 8px; color: var(--primary-navy);">📍 {{ $loc->name }}</h3>
                        <p style="font-size: 15px; color: var(--text-secondary); margin-bottom: 20px; line-height: 1.6;">
                            {{ $loc->description }}
                        </p>
                        <div style="margin-top: auto;">
                            <a href="{{ route('properties.index', ['location' => $loc->slug]) }}" class="btn btn-outline" style="width: 100%;">View Properties &rarr;</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Explore North Bali Interactive Map Container -->
        <div style="background-color: var(--accent-lavender); border-radius: var(--radius-lg); padding: 48px;">
            <div style="text-align: center; max-width: 600px; margin: 0 auto 32px auto;">
                <h2>Explore North Bali Map</h2>
                <p class="body-text" style="color: var(--text-secondary);">Interactive regional overview of North Bali property zones.</p>
            </div>

            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 24px; text-align: center;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    @foreach($locations as $loc)
                        <div style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; background-color: #F8FAFC;">
                            <div style="font-weight: 700; color: var(--primary-navy);">📍 {{ $loc->name }}</div>
                            <div style="font-size: 14px; color: var(--medium-blue); margin-top: 4px;">{{ $loc->properties_count }} active listings</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
