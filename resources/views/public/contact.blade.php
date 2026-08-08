@extends('layouts.public')

@section('title', 'Contact Us - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('content')
<section class="section-spacing bg-light-blue" style="padding-top: 60px; padding-bottom: 60px;">
    <div class="container">
        <h1 style="margin-bottom: 12px;">Contact Us</h1>
        <p class="body-text" style="color: var(--text-secondary);">
            We are here to answer your questions and help you find the perfect property in beautiful North Bali.
        </p>
    </div>
</section>

<section class="section-spacing bg-white">
    <div class="container">
        <!-- 4 Get In Touch Cards -->
        <div style="text-align: center; margin-bottom: 32px;">
            <h2>Get In Touch</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 64px;">
            <!-- Card 1: Office Address -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px auto;">📍</div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Office Address</h3>
                <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6;">
                    {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Anturan, Kec. Buleleng, Kabupaten Buleleng, Bali 81119 Indonesia' }}
                </p>
            </div>

            <!-- Card 2: Phone Number -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px auto;">📞</div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Phone Number</h3>
                <p style="font-size: 16px; font-weight: 600; color: var(--primary-navy);">
                    {{ $settings->phone ?? '+62 812 3456 7890' }}
                </p>
            </div>

            <!-- Card 3: Email Address -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px auto;">✉️</div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Email Address</h3>
                <p style="font-size: 16px; font-weight: 600; color: var(--primary-navy);">
                    {{ $settings->email ?? 'info@lovinanorthbali.com' }}
                </p>
            </div>

            <!-- Card 4: Business Hours -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px auto;">🕒</div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Business Hours</h3>
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                    Monday - Friday: 09:00 - 17:00<br>
                    Saturday: 09:00 - 14:00<br>
                    Sunday: Closed
                </div>
            </div>
        </div>

        <!-- Our Office Photo & Find Us Google Maps -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 64px;">
            <div>
                <h3 style="margin-bottom: 16px;">Our Office</h3>
                <div style="height: 320px; border-radius: var(--radius-md); overflow: hidden; background-color: var(--light-gray); border: 1px solid var(--border);">
                    <img src="{{ asset('images/office-building.jpg') }}" alt="Our Office" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=800&q=80'">
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 16px;">Find Us</h3>
                <div style="height: 320px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border); background-color: #E2E8F0;">
                    @if($settings->google_maps_embed_url)
                        <iframe src="{{ $settings->google_maps_embed_url }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); font-size: 16px;">
                            📍 Interactive Google Maps Embed (Lovina, Bali)
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div style="text-align: center; margin-bottom: 64px;">
            <h3 style="margin-bottom: 20px;">Connect With Us</h3>
            <div style="display: flex; justify-content: center; gap: 24px;">
                <a href="{{ $settings->whatsapp_url ?? 'https://wa.me/6281234567890' }}" target="_blank" class="btn btn-outline" style="border-color: #22C55E; color: #15803D;">
                    💬 WhatsApp
                </a>
                <a href="{{ $settings->facebook_url ?? 'https://facebook.com' }}" target="_blank" class="btn btn-outline" style="border-color: #2563EB; color: #1D4ED8;">
                    👍 Facebook
                </a>
                <a href="{{ $settings->youtube_url ?? 'https://youtube.com' }}" target="_blank" class="btn btn-outline" style="border-color: #EF4444; color: #B91C1C;">
                    ▶️ YouTube
                </a>
            </div>
        </div>

        <!-- Inquiry Form Section -->
        <div style="background-color: var(--light-gray); border-radius: var(--radius-lg); padding: 48px; max-width: 800px; margin: 0 auto; border: 1px solid var(--border);">
            <div style="text-align: center; margin-bottom: 32px;">
                <h2>Tell Us About the Property You're Interested In</h2>
                <p class="body-text" style="color: var(--text-secondary);">
                    Please fill out the form below and our team will get back to you as soon as possible.
                </p>
            </div>

            <form action="{{ route('inquiry.store') }}" method="POST" id="inquiryForm">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="customer_name">Full Name *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="property_id">Property You're Interested In</label>
                    <select name="property_id" id="property_id" class="form-select">
                        <option value="">Select a property (optional)...</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->location->name ?? 'North Bali' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Message *</label>
                    <textarea name="message" id="message" class="form-control" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 18px;" id="btn-submit-inquiry">
                    Send Message 🚀
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
