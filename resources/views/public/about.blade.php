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
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .info-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    
    @media (max-width: 1024px) {
        .benefits-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .info-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .about-grid-container {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .benefits-grid {
            grid-template-columns: 1fr;
        }
        .info-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">About PT Lovina North Bali</h1>
        <p class="body-text" style="color: var(--text-secondary);">Your trusted real estate partner in North Bali. Established in 2023.</p>
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

        <!-- Real Estate -->
        <div class="about-story-container">
            <h2>Real Estate</h2>
            <p class="body-text" style="margin-bottom: 16px;">
                We are constantly busy with selecting existing villas, houses, hotels, and restaurants, so we can offer you the best of the best of what is available here in beautiful North Bali. We have for each his own, from wonderful big villas on the beach, houses with nice views in the mountains, till small houses in the villages for the real Bali feeling.
            </p>
            <p class="body-text" style="margin-bottom: 16px;">
                On request we can also specifically search for you. Come in and visit us in our office, tell us what you are looking for and what your wishes are, and we will find your dreamhouse specially for you.
            </p>
            <p class="body-text">
                In the rare circumstances that we can’t find anything that meets all your wishes, then we have our other specialty.
            </p>
        </div>

        <!-- And further -->
        <div class="about-story-container">
            <h2>And further</h2>
            <p class="body-text">
                Maybe you have a villa, but you are not always in Bali, or you rent it out, then we can offer you a tailored maintenance package. We can also make sure that your villa and/or garden will always look the best that it can be, and if you receive guests, then someone of our team is there to welcome them. For the perfect first impression. Tell us your specific wishes and we will figure it out together.
            </p>
        </div>

        <!-- Our Vision & Our Mission -->
        <div class="about-grid-container">
            <!-- Our Vision -->
            <div class="about-card">
                <h3 style="margin-bottom: 20px;">Our Vision</h3>
                <p class="body-text" style="line-height: 1.8; color: var(--text-secondary); text-align: justify;">
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
                                <i data-lucide="check" class="lucide-icon lucide-icon-sm" style="color: var(--secondary-gold); margin-top: 4px; flex-shrink: 0;"></i>
                                <span style="text-align: justify;">{{ $m }}</span>
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
            <div class="benefits-grid">
                @foreach($benefits as $b)
                    <div style="background-color: var(--light-gray); border-radius: var(--radius-md); padding: 28px 20px; text-align: center;">
                        <div style="color: var(--primary-navy); margin-bottom: 12px;">
                            <i data-lucide="{{ $b->icon ?? 'shield' }}" class="lucide-icon lucide-icon-xl" style="color: var(--primary-navy);"></i>
                        </div>
                        <h3 style="font-size: 20px; margin-bottom: 8px;">{{ $b->title }}</h3>
                        <p style="font-size: 15px; color: var(--text-secondary);">{{ $b->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Stats -->
        <div class="info-stats-grid" style="background-color: var(--primary-navy); padding: 48px; border-radius: var(--radius-lg);">
            @foreach($statistics as $stat)
                <div class="stats-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 40px 24px;">
                    <div style="font-size: 48px; font-weight: 700; color: var(--primary-navy); margin-bottom: 12px; line-height: 1.1;">
                        {{ $stat->number }}
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: var(--primary-navy); margin-bottom: 12px; line-height: 1.3;">
                        {{ $stat->icon }}
                    </h3>
                    <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6; margin: 0; max-width: 280px;">
                        {{ $stat->label }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
