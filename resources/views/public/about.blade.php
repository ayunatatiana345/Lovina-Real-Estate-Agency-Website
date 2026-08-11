@extends('layouts.public')

@section('title', 'About Us - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('head_extra')
<style>
    .about-story-container {
        max-width: 800px;
        margin: 0 auto 64px auto;
    }
    .about-story-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .about-story-container p.body-text {
        text-align: justify;
        line-height: 1.8;
        color: var(--text-primary);
    }
    .about-grid-container {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 32px;
        align-items: stretch;
        margin-bottom: 64px;
    }
    .about-card {
        background-color: var(--light-gray);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 40px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    @media (max-width: 768px) {
        .about-grid-container {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }
</style>
@endsection

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">About PT Lovina North Bali</h1>
        <p class="body-text" style="color: var(--text-secondary);">Your trusted real estate partner with over 15 years of North Bali experience.</p>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <!-- Our Story -->
        <div class="about-story-container">
            <h2>{{ $story['title'] ?? 'Our Story' }}</h2>
            <p class="body-text">
                {{ $story['description'] ?? 'Founded in Lovina, PT Lovina North Bali Real Estate Agency has established itself as the leading property agency dedicated to North Bali real estate.' }}
            </p>
        </div>

        <!-- Our Vision & Our Mission -->
        <div class="about-grid-container">
            <!-- Our Vision -->
            <div class="about-card">
                <h3 style="margin-bottom: 20px;">Our Vision</h3>
                <p class="body-text" style="line-height: 1.8; color: var(--text-secondary);">
                    {{ $story['vision'] ?? 'To be the most trusted and transparent real estate agency in North Bali.' }}
                </p>
            </div>

            <!-- Our Mission -->
            <div class="about-card">
                <h3 style="margin-bottom: 20px;">Our Mission</h3>
                <ul style="list-style: none; padding-left: 0; margin-bottom: 0;">
                    @if(isset($story['mission']) && is_array($story['mission']))
                        @foreach($story['mission'] as $m)
                            <li style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px; font-size: 16px;">
                                <i data-lucide="check" class="lucide-icon lucide-icon-sm" style="color: var(--secondary-gold); margin-top: 4px;"></i>
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
                        <div style="color: var(--primary-navy); margin-bottom: 12px;">
                            <i data-lucide="shield" class="lucide-icon lucide-icon-xl" style="color: var(--primary-navy);"></i>
                        </div>
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
