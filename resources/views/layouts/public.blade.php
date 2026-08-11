<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings->site_title ?? 'PT Lovina North Bali Real Estate Agency')</title>
    <meta name="description" content="@yield('meta_description', $settings->site_description ?? 'Your trusted luxury real estate partner in Lovina, Temukus, Singaraja, and North Bali.')">
    
    <!-- OpenGraph SEO -->
    <meta property="og:title" content="@yield('title', $settings->site_title ?? 'PT Lovina North Bali Real Estate Agency')">
    <meta property="og:description" content="@yield('meta_description', $settings->site_description ?? 'Your trusted luxury real estate partner in North Bali.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/lucide@0.428.0/dist/umd/lucide.min.js"></script>
    @yield('head_extra')
</head>
<body>
    <!-- Sticky Navbar -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar" id="main-navbar">
                <a href="{{ route('home') }}" class="brand-logo" id="nav-brand-logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>PT LOVINA NORTH BALI</span>
                </a>

                <ul class="nav-menu" id="nav-menu">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" id="nav-link-home">Home</a></li>
                    <li><a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" id="nav-link-properties">Properties</a></li>
                    <li><a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" id="nav-link-locations">Locations</a></li>
                    <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" id="nav-link-about">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" id="nav-link-contact">Contact Us</a></li>
                </ul>

                <div>
                    <a href="{{ route('contact') }}" class="btn btn-secondary" id="btn-list-property">List Your Property</a>
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
                    <h3 class="footer-heading" style="color: var(--white); font-size: 24px;">PT LOVINA NORTH BALI</h3>
                    <p style="color: var(--light-blue); margin-bottom: 20px; font-size: 16px;">
                        The premier luxury real estate agency in North Bali. Specializing in oceanfront villas, beachfront land plots, and prime property investments.
                    </p>
                    <p style="color: var(--light-blue); font-size: 15px; display: flex; align-items: flex-start; gap: 8px;">
                        <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue); margin-top: 3px; flex-shrink: 0;"></i> <span>{{ $settings->address ?? 'Jl. Raya Kalibukbuk-Anturan, Lovina, Buleleng, Bali' }}</span>
                    </p>
                </div>

                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('properties.index') }}">All Properties</a></li>
                        <li><a href="{{ route('locations.index') }}">Popular Locations</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
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
                        <i data-lucide="phone" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue);"></i> Phone: {{ $settings->phone ?? '+62 812 3456 7890' }}
                    </p>
                    <p style="color: var(--light-blue); margin-bottom: 8px; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="message-circle" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue);"></i> WhatsApp: {{ $settings->whatsapp ?? '+62 812 3456 7890' }}
                    </p>
                    <p style="color: var(--light-blue); margin-bottom: 16px; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="mail" class="lucide-icon lucide-icon-sm" style="color: var(--light-blue);"></i> Email: {{ $settings->email ?? 'info@lovinanorthbali.com' }}
                    </p>
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

    <!-- Inquiry Success Modal (Matching Reference Image 4) -->
    <div class="modal-overlay" id="successModal" style="display: {{ session('success_modal') ? 'flex' : 'none' }};">
        <div class="modal-box">
            <button class="modal-close-btn" id="closeSuccessModalBtn">&times;</button>
            <div class="modal-icon-success" style="display: flex; align-items: center; justify-content: center;">
                <i data-lucide="check" style="width: 32px; height: 32px; stroke-width: 3px; color: #16A34A;"></i>
            </div>
            <h2 style="font-size: 32px; color: var(--primary-navy); margin-bottom: 12px;">Message Sent Successfully!</h2>
            <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 24px;">
                Thank you for reaching out to Lovina North Bali Real Estate Agency.<br>
                Our team has received your message and will get back to you as soon as possible.
            </p>

            <div style="background-color: #EFF6FF; border-radius: 10px; padding: 20px; text-align: left; margin-bottom: 24px;">
                <div style="font-weight: 700; color: #1E40AF; margin-bottom: 4px;">What happens next?</div>
                <div style="font-size: 15px; color: #1E3A8A;">
                    We will review your inquiry and contact you via WhatsApp or email shortly. Please make sure your contact details are correct.
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp ?? '6281234567890') }}" target="_blank" class="btn btn-outline" style="border-color: #16A34A; color: #16A34A;">
                    <i data-lucide="message-circle" class="lucide-icon lucide-icon-sm" style="margin-right: 6px; color: #16A34A;"></i> Chat on WhatsApp
                </a>
                <button class="btn btn-primary" id="backHomeBtn">Back to Homepage</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
