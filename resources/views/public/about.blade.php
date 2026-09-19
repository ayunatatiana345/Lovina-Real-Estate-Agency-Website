{{-- Tara handles the about us page here. --}}
@extends('layouts.public')

@section('title', 'About Us | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', 'Learn about PT Lovina North Bali Real Estate Agency. Established in 2023, we provide trusted local expertise, personalized property consultation, and transparent guidance across North Bali.')
@section('canonical', route('about'))

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
      "name": "About Us",
      "item": "{{ route('about') }}"
    }
  ]
}
</script>
@endsection

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
@php
    $hasBannerImage = !empty($banner['image']) && (
        \Illuminate\Support\Facades\Storage::disk('public')->exists($banner['image']) ||
        file_exists(public_path('storage/' . $banner['image'])) ||
        file_exists(public_path($banner['image']))
    );
    $bannerImageUrl = $hasBannerImage ? (
        file_exists(public_path('storage/' . $banner['image'])) || \Illuminate\Support\Facades\Storage::disk('public')->exists($banner['image'])
            ? asset('storage/' . $banner['image'])
            : asset($banner['image'])
    ) : null;
@endphp

<section class="section-spacing bg-light-blue" style="{{ $hasBannerImage ? "background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('" . $bannerImageUrl . "') center/cover no-repeat; color: #FFFFFF;" : '' }} padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px; {{ $hasBannerImage ? 'color: #FFFFFF;' : '' }}">{{ $banner['title'] ?? 'About PT Lovina North Bali' }}</h1>
        <p class="body-text" style="color: {{ $hasBannerImage ? '#DBEAFE' : 'var(--text-secondary)' }}; margin-bottom: 0;">{{ $banner['subtitle'] ?? 'Your trusted real estate partner in North Bali. Established in 2023.' }}</p>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <!-- Our Story -->
        <div class="about-story-container">
            <h2>{{ $story['heading'] ?? $story['title'] ?? 'Our Story' }}</h2>
            @php
                $hasStoryImage = !empty($story['image']) && (
                    \Illuminate\Support\Facades\Storage::disk('public')->exists($story['image']) ||
                    file_exists(public_path('storage/' . $story['image'])) ||
                    file_exists(public_path($story['image']))
                );
                $storyImageUrl = $hasStoryImage ? (
                    file_exists(public_path('storage/' . $story['image'])) || \Illuminate\Support\Facades\Storage::disk('public')->exists($story['image'])
                        ? asset('storage/' . $story['image'])
                        : asset($story['image'])
                ) : null;
            @endphp
            @if($hasStoryImage)
                <div style="margin-bottom: 24px; text-align: center;">
                    <img src="{{ $storyImageUrl }}" alt="{{ $story['heading'] ?? 'Our Story' }}" style="max-width: 100%; height: auto; max-height: 400px; border-radius: var(--radius-md); object-fit: cover; box-shadow: var(--shadow-sm);">
                </div>
            @endif
            <p class="body-text">
                {{ $story['description'] ?? 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.' }}
            </p>
        </div>

        <!-- Real Estate -->
        <div class="about-story-container">
            <h2>{{ $realEstate['title'] ?? 'Real Estate' }}</h2>
            @if(!empty($realEstate['paragraph_1']))
                <p class="body-text" style="margin-bottom: 16px;">{{ $realEstate['paragraph_1'] }}</p>
            @endif
            @if(!empty($realEstate['paragraph_2']))
                <p class="body-text" style="margin-bottom: 16px;">{{ $realEstate['paragraph_2'] }}</p>
            @endif
            @if(!empty($realEstate['paragraph_3']))
                <p class="body-text">{{ $realEstate['paragraph_3'] }}</p>
            @endif
        </div>

        <!-- And further -->
        <div class="about-story-container">
            <h2>{{ $andFurther['title'] ?? 'And further' }}</h2>
            <p class="body-text">
                {{ $andFurther['description'] ?? 'Maybe you have a villa, but you are not always in Bali, or you rent it out, then we can offer you a tailored maintenance package. We can also make sure that your villa and/or garden will always look the best that it can be, and if you receive guests, then someone of our team is there to welcome them. For the perfect first impression. Tell us your specific wishes and we will figure it out together.' }}
            </p>
        </div>

        <!-- Our Vision & Our Mission -->
        <div class="about-grid-container">
            <!-- Our Vision -->
            <div class="about-card">
                <h3 style="margin-bottom: 20px;">{{ $vision['title'] ?? 'Our Vision' }}</h3>
                <p class="body-text" style="line-height: 1.8; color: var(--text-secondary); text-align: justify;">
                    {{ $vision['description'] ?? 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.' }}
                </p>
            </div>

            <!-- Our Mission -->
            <div class="about-card">
                <h3 style="margin-bottom: 20px;">{{ $mission['title'] ?? 'Our Mission' }}</h3>
                @if(!empty($mission['description']))
                    <p class="body-text" style="margin-bottom: 16px; color: var(--text-secondary);">{{ $mission['description'] }}</p>
                @endif
                <ul style="list-style: none; padding-left: 0; margin-bottom: 0;">
                    @if(isset($mission['points']) && is_array($mission['points']))
                        @foreach($mission['points'] as $m)
                            @if(!empty($m))
                                <li style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px; font-size: 16px;">
                                    <i data-lucide="check" class="lucide-icon lucide-icon-sm" style="color: var(--secondary-gold); margin-top: 4px; flex-shrink: 0;"></i>
                                    <span style="text-align: justify;">{{ $m }}</span>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div style="margin-bottom: 64px;">
            <div style="text-align: center; max-width: 600px; margin: 0 auto 40px auto;">
                <h2>{{ $whyChoose['heading'] ?? 'Why International Buyers Trust Us' }}</h2>
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

        @if($aboutStats['show_homepage_stats'] ?? true)
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
        @endif
    </div>
</section>
@endsection
