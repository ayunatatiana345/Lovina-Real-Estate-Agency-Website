@extends('layouts.public')

@section('title', 'About Us - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">About PT Lovina North Bali</h1>
        <p class="body-text" style="color: var(--text-secondary);">Your trusted real estate partner with over 15 years of North Bali experience.</p>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; margin-bottom: 64px;">
            <div>
                <h2>{{ $story['title'] ?? 'Our Story' }}</h2>
                <p class="body-text" style="line-height: 1.8; color: var(--text-primary); margin-bottom: 24px;">
                    {{ $story['description'] ?? 'Founded in Lovina, PT Lovina North Bali Real Estate Agency has established itself as the leading property agency dedicated to North Bali real estate.' }}
                </p>
                <div style="background-color: var(--accent-lavender); border-left: 4px solid var(--primary-navy); padding: 20px; border-radius: 4px;">
                    <div style="font-weight: 700; color: var(--primary-navy); margin-bottom: 4px;">Our Vision</div>
                    <p style="font-size: 16px; color: var(--text-secondary);">
                        {{ $story['vision'] ?? 'To be the most trusted and transparent real estate agency in North Bali.' }}
                    </p>
                </div>
            </div>

            <div style="background-color: var(--light-gray); border-radius: var(--radius-lg); padding: 40px; border: 1px solid var(--border);">
                <h3 style="margin-bottom: 20px;">Our Mission</h3>
                <ul style="list-style: none; padding-left: 0;">
                    @if(isset($story['mission']) && is_array($story['mission']))
                        @foreach($story['mission'] as $m)
                            <li style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px; font-size: 16px;">
                                <span style="color: var(--secondary-gold); font-size: 20px;">✓</span>
                                <span>{{ $m }}</span>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div style="margin-bottom: 64px;">
            <div style="text-align: center; max-width: 600px; margin: 0 auto 40px auto;">
                <h2>Why International Buyers Trust Us</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                @foreach($benefits as $b)
                    <div style="background-color: var(--light-gray); border-radius: var(--radius-md); padding: 28px 20px; text-align: center;">
                        <div style="font-size: 36px; margin-bottom: 12px;">🛡️</div>
                        <h3 style="font-size: 20px; margin-bottom: 8px;">{{ $b->title }}</h3>
                        <p style="font-size: 15px; color: var(--text-secondary);">{{ $b->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid" style="background-color: var(--primary-navy); padding: 48px; border-radius: var(--radius-lg);">
            @foreach($statistics as $stat)
                <div class="stats-card">
                    <div class="stats-number">{{ $stat->number }}</div>
                    <div class="stats-label">{{ $stat->label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
