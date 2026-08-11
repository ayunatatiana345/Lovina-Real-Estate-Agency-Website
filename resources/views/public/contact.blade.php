@extends('layouts.public')

@section('title', 'Contact Us - ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))

@section('head_extra')
<style>
.office-map-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 32px;
    margin-bottom: 64px;
}
@media (min-width: 768px) {
    .office-map-grid {
        grid-template-columns: 1fr 1fr;
    }
}
.social-circle-btn:hover {
    background-color: var(--light-blue) !important;
    transform: translateY(-2px);
}
</style>
@endsection

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
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Office Address</h3>
                <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6;">
                    {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Anturan, Kec. Buleleng, Kabupaten Buleleng, Bali 81119 Indonesia' }}
                </p>
            </div>

            <!-- Card 2: Phone Number -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <i data-lucide="phone" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Phone Number</h3>
                <p style="font-size: 16px; font-weight: 600; color: var(--primary-navy);">
                    {{ $settings->phone ?? '+62 812 3456 7890' }}
                </p>
            </div>

            <!-- Card 3: Email Address -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <i data-lucide="mail" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Email Address</h3>
                <p style="font-size: 16px; font-weight: 600; color: var(--primary-navy);">
                    {{ $settings->email ?? 'info@lovinanorthbali.com' }}
                </p>
            </div>

            <!-- Card 4: Business Hours -->
            <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 32px 24px; text-align: center; box-shadow: var(--shadow-sm);">
                <div style="width: 56px; height: 56px; background-color: var(--light-blue); color: var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <i data-lucide="clock" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px;">Business Hours</h3>
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                    Monday - Friday: 09:00 - 17:00<br>
                    Saturday: 09:00 - 14:00<br>
                    Sunday: Closed
                </div>
            </div>
        </div>

        <!-- Our Office Photo & Find Us Google Maps -->
        <div class="office-map-grid">
            <div>
                <h3 style="margin-bottom: 16px;">Our Office</h3>
                <div style="height: 320px; border-radius: var(--radius-md); border: 1px solid #E5E7EB; background-color: #F3F4F6; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; box-sizing: border-box;">
                    <i data-lucide="building" style="width: 48px; height: 48px; color: #9CA3AF; stroke-width: 1.5px;"></i>
                    <span style="color: #9CA3AF; font-size: 15px; font-weight: 500; font-family: 'Poppins', sans-serif;">Office Photo</span>
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 16px;">Find Us</h3>
                <div style="height: 320px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border); background-color: #E2E8F0; position: relative; margin-bottom: 16px;">
                    @if($settings->google_maps_embed_url && (str_contains($settings->google_maps_embed_url, 'google.com/maps/embed') || str_contains($settings->google_maps_embed_url, 'iframe')))
                        <a href="{{ $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic' }}" target="_blank" rel="noopener noreferrer" style="display: block; width: 100%; height: 100%; position: relative;">
                            @if(str_contains($settings->google_maps_embed_url, '<iframe'))
                                {!! $settings->google_maps_embed_url !!}
                            @else
                                <iframe src="{{ $settings->google_maps_embed_url }}" width="100%" height="100%" style="border:0; pointer-events: none;" allowfullscreen="" loading="lazy"></iframe>
                            @endif
                            <!-- Overlay to make embed clickable -->
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: transparent; z-index: 10;"></div>
                        </a>
                    @else
                        <!-- Fallback Static Map Image -->
                        <a href="{{ $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic' }}" target="_blank" rel="noopener noreferrer" style="display: block; width: 100%; height: 100%; transition: opacity 0.2s;">
                            <img src="{{ asset('images/office_map_screenshot.png') }}" alt="Office Location Map" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </a>
                    @endif
                </div>
                <!-- Button below the box -->
                <a href="{{ $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic' }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; position: relative; border: 1.5px solid var(--primary-navy); color: var(--primary-navy); background-color: var(--white); height: 44px; font-weight: 600; font-size: 14px; text-decoration: none; border-radius: var(--radius-sm); font-family: 'Poppins', sans-serif; box-sizing: border-box; transition: background-color 0.2s;">
                    <i data-lucide="map-pin" style="width: 16px; height: 16px; margin-right: 8px;"></i>
                    Open in Google Maps
                    <i data-lucide="external-link" style="width: 16px; height: 16px; position: absolute; right: 16px;"></i>
                </a>
            </div>
        </div>

        <!-- Social Media Links -->
        <div style="text-align: center; margin-bottom: 64px;">
            <h3 style="margin-bottom: 32px; font-size: 28px; font-weight: 700; color: var(--primary-navy); position: relative; display: inline-block; padding-bottom: 10px;">
                Connect With Us
                <div style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background-color: var(--medium-blue); border-radius: 2px;"></div>
            </h3>
            
            <div style="display: flex; justify-content: center; gap: 48px; align-items: center; flex-wrap: wrap;">
                <!-- WhatsApp -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a href="{{ $settings->whatsapp_url ?? 'https://wa.me/6281234567890' }}" target="_blank" rel="noopener noreferrer" style="width: 64px; height: 64px; border: 1.5px solid var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" class="social-circle-btn">
                        <i data-lucide="message-circle" style="width: 32px; height: 32px; color: var(--primary-navy); stroke-width: 1.5px;"></i>
                    </a>
                    <span style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Poppins', sans-serif;">WhatsApp</span>
                </div>
                
                <!-- Facebook -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a href="{{ $settings->facebook_url ?? 'https://facebook.com' }}" target="_blank" rel="noopener noreferrer" style="width: 64px; height: 64px; border: 1.5px solid var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" class="social-circle-btn">
                        <i data-lucide="facebook" style="width: 32px; height: 32px; color: var(--primary-navy); stroke-width: 1.5px;"></i>
                    </a>
                    <span style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Poppins', sans-serif;">Facebook</span>
                </div>
                
                <!-- YouTube -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a href="{{ $settings->youtube_url ?? 'https://youtube.com' }}" target="_blank" rel="noopener noreferrer" style="width: 64px; height: 64px; border: 1.5px solid var(--primary-navy); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" class="social-circle-btn">
                        <i data-lucide="youtube" style="width: 32px; height: 32px; color: var(--primary-navy); stroke-width: 1.5px;"></i>
                    </a>
                    <span style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Poppins', sans-serif;">YouTube</span>
                </div>
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
                <input type="hidden" name="source" value="Contact Us Form">
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

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 18px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;" id="btn-submit-inquiry">
                    Send Message <i data-lucide="send" class="lucide-icon lucide-icon-sm" style="color: var(--white);"></i>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
