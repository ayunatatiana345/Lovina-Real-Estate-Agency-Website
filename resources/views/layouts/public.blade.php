<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings->site_title ?? 'PT Lovina North Bali Real Estate Agency')</title>
    <meta name="description" content="@yield('meta_description', $settings->site_description ?? 'Your trusted luxury real estate partner in Lovina, Temukus, Singaraja, and North Bali.')">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- OpenGraph Social SEO -->
    <meta property="og:site_name" content="{{ $settings->company_name ?? 'PT Lovina North Bali Real Estate Agency' }}">
    <meta property="og:title" content="@yield('title', $settings->site_title ?? 'PT Lovina North Bali Real Estate Agency')">
    <meta property="og:description" content="@yield('meta_description', $settings->site_description ?? 'Your trusted luxury real estate partner in North Bali.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/black logo lovina.png'))">

    <!-- Twitter Card SEO -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $settings->site_title ?? 'PT Lovina North Bali Real Estate Agency')">
    <meta name="twitter:description" content="@yield('meta_description', $settings->site_description ?? 'Your trusted luxury real estate partner in North Bali.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/black logo lovina.png'))">

    <!-- Global Organization / RealEstateAgent Structured Data -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "RealEstateAgent",
      "name": "{{ $settings->company_name ?? 'PT Lovina North Bali Real Estate Agency' }}",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/black logo lovina.png') }}",
      "image": "{{ asset('images/black logo lovina.png') }}",
      "description": "{{ $settings->site_description ?? 'The premier luxury real estate agency in North Bali, specializing in beachfront villas, ocean-view land, and investment properties.' }}",
      "telephone": "{{ $settings->clean_phone ?? '085936666384' }}",
      "email": "{{ $settings->email ?? 'lovinanorthbaliagency2023@gmail.com' }}",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali' }}",
        "addressLocality": "Lovina",
        "addressRegion": "Buleleng, Bali",
        "postalCode": "81119",
        "addressCountry": "ID"
      },
      "priceRange": "$$$$"@if(!empty($settings->instagram_url) || !empty($settings->facebook_url) || !empty($settings->youtube_url)),
      "sameAs": [
        @php
            $socials = array_filter([
                $settings->instagram_url ?? null,
                $settings->facebook_url ?? null,
                $settings->youtube_url ?? null,
            ]);
        @endphp
        {!! implode(',', array_map(fn($u) => '"' . $u . '"', $socials)) !!}
      ]@endif
    }
    </script>
    
    @yield('structured_data')
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/lucide@0.428.0/dist/umd/lucide.min.js"></script>
    @yield('head_extra')
</head>
<body>
    <!-- Sticky Navbar -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar" id="main-navbar">
                <a href="{{ route('home') }}" class="brand-logo" id="nav-brand-logo" style="text-decoration: none;">
                    <img src="{{ asset('images/black logo lovina.png') }}" alt="PT LOVINA NORTH BALI" style="height: 54px; width: auto;">
                    <div style="display: flex; flex-direction: column; line-height: 1.15; text-align: left;">
                        <span style="font-size: 15px; font-weight: 800; color: var(--primary-navy, #1E3A8A); letter-spacing: 0.5px; font-family: sans-serif;">LOVINA NORTH BALI</span>
                        <span style="font-size: 10px; font-weight: 600; color: var(--primary-navy, #1E3A8A); opacity: 0.85; letter-spacing: 0.5px; font-family: sans-serif;">REAL ESTATE AGENCY</span>
                    </div>
                </a>

                <div style="display: flex; align-items: center; gap: 24px;">
                    <ul class="nav-menu" id="nav-menu">
                        <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" id="nav-link-home">Home</a></li>
                        <li><a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" id="nav-link-properties">Properties</a></li>
                        <li><a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" id="nav-link-locations">Locations</a></li>
                        <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" id="nav-link-about">About Us</a></li>
                        <li><a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.*') ? 'active' : '' }}" id="nav-link-articles">Articles</a></li>
                        <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" id="nav-link-contact">Contact Us</a></li>
                    </ul>

                    @php
                        $currencyService = app(\App\Services\CurrencyService::class);
                        $userCurr = $currencyService->getUserCurrency();
                        $currMeta = $currencyService->getRateMetadata();
                    @endphp
                    <div class="currency-selector-box" id="header-currency-selector">
                        <div class="currency-pills" role="group" aria-label="Currency Selector" title="{{ $currMeta['formatted_rate'] ? $currMeta['formatted_rate'] . ' (Using latest exchange rate from ' . $currMeta['provider'] . ')' : 'Currency' }}">
                            <a href="{{ route('currency.switch', 'IDR') }}" 
                               class="currency-pill-btn {{ $userCurr === 'IDR' ? 'active' : '' }}" 
                               id="btn-currency-idr" 
                               aria-label="Set currency to IDR">
                                IDR
                            </a>
                            <a href="{{ route('currency.switch', 'USD') }}" 
                               class="currency-pill-btn {{ $userCurr === 'USD' ? 'active' : '' }}" 
                               id="btn-currency-usd" 
                               aria-label="Set currency to USD">
                                USD
                            </a>
                        </div>
                    </div>
                </div>

            </nav>
        </div>
    </header>

    <!-- Main Page Content -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer" id="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <img src="{{ asset('images/white logo lovina.png') }}" alt="PT LOVINA NORTH BALI" style="height: 45px; margin-bottom: 20px;">
                    <p style="color: var(--light-blue); margin-bottom: 20px; font-size: 16px;">
                        The premier luxury real estate agency in North Bali. Specializing in oceanfront villas, beachfront land plots, and prime property investments.
                    </p>
                    <p style="color: var(--light-blue); font-size: 15px; display: flex; align-items: flex-start; gap: 8px; margin-bottom: 20px;">
                        <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue); margin-top: 3px; flex-shrink: 0;"></i>
                        <a href="{{ $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic' }}" target="_blank" rel="noopener noreferrer" style="color: var(--light-blue); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='var(--light-blue)'" aria-label="Visit office location on Google Maps">
                            {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali' }}
                        </a>
                    </p>
                </div>

                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('properties.index') }}">All Properties</a></li>
                        <li><a href="{{ route('locations.index') }}">Popular Locations</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('articles.index') }}">Articles</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Property Types</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('properties.index', ['type' => 'villa']) }}">Villas</a></li>
                        <li><a href="{{ route('properties.index', ['type' => 'land']) }}">Land Plots</a></li>
                        <li><a href="{{ route('properties.index', ['type' => 'house']) }}">Houses</a></li>
                        <li><a href="{{ route('properties.index', ['type' => 'commercial']) }}">Commercial</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Get in Touch</h4>
                    <p style="color: var(--light-blue); margin-bottom: 8px; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="phone" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue); flex-shrink: 0;"></i>
                        <span>Phone: <a href="tel:{{ $settings->clean_phone }}" style="color: var(--light-blue); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='var(--light-blue)'">{{ $settings->phone }}</a></span>
                    </p>
                    <p style="color: var(--light-blue); margin-bottom: 8px; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: var(--light-blue); flex-shrink: 0;" aria-hidden="true">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>WhatsApp: <a href="{{ $settings->whatsapp_url }}" target="_blank" rel="noopener noreferrer" style="color: var(--light-blue); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='var(--light-blue)'">{{ $settings->whatsapp }}</a></span>
                    </p>
                    <p style="color: var(--light-blue); margin-bottom: 12px; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="mail" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue); flex-shrink: 0;"></i>
                        <span>Email: <a href="mailto:{{ $settings->email }}" style="color: var(--light-blue); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='var(--light-blue)'">{{ $settings->email }}</a></span>
                    </p>
                    <div style="color: var(--light-blue); margin-bottom: 14px; font-size: 14px; display: flex; align-items: flex-start; gap: 8px;">
                        <i data-lucide="clock" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue); margin-top: 3px; flex-shrink: 0;"></i>
                        <div style="line-height: 1.4;">
                            <strong style="display: block; color: #FFFFFF; font-size: 14px; margin-bottom: 2px;">Opening Hours:</strong>
                            @foreach($settings->formatted_business_hours as $bh)
                                <span>{{ $bh['day'] }}: {{ $bh['hours'] }}</span>@if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    </div>
                    @if(!empty($settings->facebook_url) || !empty($settings->youtube_url))
                    <div style="display: flex; gap: 10px; align-items: center; margin-top: 12px;">
                        @if(!empty($settings->facebook_url))
                        <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); transition: background 0.2s, transform 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'; this.style.transform='none'" title="Facebook" aria-label="Official Facebook Page">
                            <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: #FFFFFF;" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        @endif
                        @if(!empty($settings->youtube_url))
                        <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); transition: background 0.2s, transform 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'; this.style.transform='none'" title="YouTube" aria-label="Official YouTube Channel">
                            <svg viewBox="0 0 24 24" style="width: 17px; height: 17px; fill: #FFFFFF;" aria-hidden="true">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="footer-bottom">
                <p style="color: #FFFFFF; margin: 0;">&copy; {{ date('Y') }} PT Lovina North Bali Real Estate Agency. All rights reserved.</p>
                <div style="display: flex; gap: 16px;">
                    <a href="{{ route('admin.login') }}" style="color: rgba(255, 255, 255, 0.45); font-size: 14px; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#FFFFFF'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Inquiry Success Modal (Premium Real Estate Design) -->
    <style>
        .premium-success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 16px;
            box-sizing: border-box;
            animation: modalFadeIn 0.25s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
        .premium-success-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            width: 100%;
            max-width: 780px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
            display: flex;
            overflow: hidden;
            position: relative;
            font-family: 'Poppins', sans-serif;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .success-modal-visual {
            width: 38%;
            min-width: 280px;
            position: relative;
            background-image: url('{{ asset('images/sample-villa-1.jpg') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 32px 24px;
            box-sizing: border-box;
        }
        .success-modal-visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.55) 0%, rgba(15, 23, 42, 0.2) 40%, rgba(15, 23, 42, 0.88) 100%);
            z-index: 1;
        }
        .success-modal-visual-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .success-modal-content {
            flex: 1;
            padding: 36px 36px 28px 36px;
            background-color: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            box-sizing: border-box;
            text-align: center;
        }
        .success-modal-close {
            position: absolute;
            top: 16px;
            right: 18px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background-color: #F1F5F9;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            transition: background-color 0.2s, color 0.2s;
            padding: 0;
            z-index: 10;
        }
        .success-modal-close:hover {
            background-color: #E2E8F0;
            color: #0F172A;
        }
        .whatsapp-cta-btn {
            background-color: #25D366;
            color: #FFFFFF !important;
            border-radius: 10px;
            height: 48px;
            padding: 0 20px;
            font-size: 14.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.28);
            transition: background-color 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-bottom: 12px;
            width: 100%;
            box-sizing: border-box;
            border: none;
        }
        .whatsapp-cta-btn:hover {
            background-color: #20BD5A;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 211, 102, 0.35);
        }
        .back-home-link {
            background: none;
            border: none;
            color: #2563EB;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            padding: 6px 12px;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: color 0.2s;
        }
        .back-home-link:hover {
            color: #1E3A8A;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .premium-success-card {
                flex-direction: column;
                max-width: 440px;
                border-radius: 16px;
            }
            .success-modal-visual {
                display: none;
            }
            .success-modal-content {
                padding: 32px 24px 24px 24px;
            }
        }
    </style>

    <div class="premium-success-modal-overlay" id="successModal" style="display: {{ session('success_modal') ? 'flex' : 'none' }};">
        <div class="premium-success-card">
            <!-- Left: Real Company Visual & Branding -->
            <div class="success-modal-visual">
                <div class="success-modal-visual-overlay"></div>
                <div class="success-modal-visual-content">
                    <!-- Top: Official White Logo -->
                    <div>
                        <img src="{{ asset('images/white logo lovina.png') }}" alt="PT LOVINA NORTH BALI" style="height: 38px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    </div>

                    <!-- Middle: Brand Tagline Quote -->
                    <div style="margin: 30px 0;">
                        <div style="width: 32px; height: 2px; background: rgba(255,255,255,0.7); margin-bottom: 12px; border-radius: 2px;"></div>
                        <p style="color: #FFFFFF; font-size: 18px; line-height: 1.45; font-weight: 500; text-shadow: 0 2px 8px rgba(0,0,0,0.5); margin: 0; font-family: 'Poppins', sans-serif;">
                            “Your next chapter in North Bali starts here.”
                        </p>
                    </div>

                    <!-- Bottom: Brand Anchor Text -->
                    <div>
                        <div style="width: 24px; height: 1.5px; background: rgba(255,255,255,0.5); margin-bottom: 6px;"></div>
                        <span style="font-size: 9.5px; font-weight: 600; letter-spacing: 1.5px; color: rgba(255,255,255,0.85); text-transform: uppercase;">
                            HOMES &bull; INVESTMENTS &bull; BALI
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Success Content & Actions -->
            <div class="success-modal-content">
                <!-- Close Button -->
                <button class="success-modal-close" id="closeSuccessModalBtn" aria-label="Close modal">&times;</button>

                <!-- Success Indicator Badge -->
                <div style="width: 58px; height: 58px; background-color: #ECFDF5; border: 1.5px solid #A7F3D0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px auto; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);">
                    <i data-lucide="check" style="width: 28px; height: 28px; stroke-width: 2.5px; color: #10B981;"></i>
                </div>

                <!-- Main Success Heading -->
                <h2 style="font-size: 23px; font-weight: 700; color: var(--primary-navy, #0F2942); margin: 0 0 8px 0; font-family: 'Poppins', sans-serif;">
                    Message Sent Successfully!
                </h2>

                <!-- Subtitle Description -->
                <p style="font-size: 13.5px; color: #64748B; line-height: 1.55; margin: 0 0 16px 0;">
                    Thank you for reaching out to Lovina North Bali Real Estate Agency. Our team has received your message and will get back to you as soon as possible.
                </p>

                <!-- What happens next? Box -->
                <div style="background-color: #F0F7FF; border: 1px solid #DBEAFE; border-radius: 12px; padding: 12px 14px; text-align: left; display: flex; gap: 12px; align-items: flex-start; margin-bottom: 18px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #DBEAFE; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <i data-lucide="mail" style="width: 16px; height: 16px; color: #2563EB;"></i>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E40AF; margin-bottom: 2px;">What happens next?</div>
                        <div style="font-size: 12.5px; color: #475569; line-height: 1.45;">
                            We'll review your inquiry and contact you via WhatsApp or email using the details you provided.
                        </div>
                    </div>
                </div>

                <!-- Divider: Need Help Sooner? -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 14px;">
                    <div style="flex: 1; height: 1px; background-color: #E2E8F0;"></div>
                    <span style="font-size: 10px; font-weight: 600; letter-spacing: 0.8px; color: #94A3B8; text-transform: uppercase;">Need help sooner?</span>
                    <div style="flex: 1; height: 1px; background-color: #E2E8F0;"></div>
                </div>

                <!-- Primary Action: Chat on WhatsApp -->
                <a href="{{ $settings->whatsapp_url ?? ('https://wa.me/' . ($settings->clean_whatsapp ?? '6285936666384')) }}" target="_blank" rel="noopener noreferrer" class="whatsapp-cta-btn">
                    <!-- WhatsApp Official Brand Icon -->
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #FFFFFF; flex-shrink: 0;" aria-hidden="true">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Chat on WhatsApp</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px; stroke-width: 2.2px;"></i>
                </a>

                <!-- Secondary Action: Back to Homepage -->
                <button type="button" class="back-home-link" id="backHomeBtn">
                    Back to Homepage
                </button>
            </div>
        </div>
    </div>

    <!-- Global Floating WhatsApp CTA Button -->
    @include('components.floating-whatsapp')

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
