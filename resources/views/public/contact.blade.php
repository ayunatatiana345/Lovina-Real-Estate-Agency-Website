{{-- Contact page UI is handled by Tatiana. --}}
{{-- Inquiry data is connected to the inquiry module. --}}
@extends('layouts.public')

@section('title', 'Contact Us | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', 'Contact PT Lovina North Bali Real Estate Agency. Inquire about buying, renting, or investing in luxury villas, beachfront land plots, or commercial real estate in North Bali.')
@section('canonical', route('contact'))

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
      "name": "Contact Us",
      "item": "{{ route('contact') }}"
    }
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ContactPage",
  "name": "Contact PT Lovina North Bali Real Estate Agency",
  "url": "{{ route('contact') }}",
  "mainEntity": {
    "@@type": "RealEstateAgent",
    "name": "{{ $settings->company_name ?? 'PT Lovina North Bali Real Estate Agency' }}",
    "telephone": "{{ $settings->phone ?? '+62 812 3456 7890' }}",
    "email": "{{ $settings->email ?? 'info@lovinanorthbali.com' }}",
    "address": {
      "@@type": "PostalAddress",
      "streetAddress": "{{ $settings->address ?? 'Jl. Raya Kalibukbuk-Anturan, Lovina' }}",
      "addressLocality": "Lovina",
      "addressRegion": "Buleleng, Bali",
      "postalCode": "81119",
      "addressCountry": "ID"
    }
  }
}
</script>
@endsection

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

/* Contact Information Cards Grid & Hover Interaction */
.contact-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 64px;
}
@media (max-width: 1024px) {
    .contact-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    .contact-cards-grid {
        grid-template-columns: 1fr;
    }
}

.contact-info-card {
    background-color: var(--white, #FFFFFF);
    border: 1px solid var(--border, #E2E8F0);
    border-radius: var(--radius-md, 12px);
    padding: 32px 24px;
    text-align: center;
    box-shadow: var(--shadow-sm, 0 1px 2px rgba(0,0,0,0.05));
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}

/* Hover Interaction: Smooth transition to subtle soft light blue background */
.contact-info-card:hover {
    background-color: #F0F7FF;
    border-color: #BFDBFE;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.08);
}

.contact-info-card:focus-visible {
    outline: 2px solid var(--primary-navy, #1E3A8A);
    outline-offset: 2px;
    background-color: #F0F7FF;
}

.contact-info-card-icon {
    width: 56px;
    height: 56px;
    background-color: var(--light-blue, #EFF6FF);
    color: var(--primary-navy, #1E3A8A);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px auto;
    flex-shrink: 0;
    transition: background-color 0.2s ease;
}

.contact-info-card:hover .contact-info-card-icon {
    background-color: #DBEAFE;
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

        <div class="contact-cards-grid">
            <!-- Card 1: Office Address (Opens real Google Maps location) -->
            <a href="{{ $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic' }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="contact-info-card"
               title="Open office location in Google Maps"
               aria-label="Office Address: {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali' }} (Opens in Google Maps)">
                <div class="contact-info-card-icon">
                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px; color: var(--primary-navy);">Office Address</h3>
                <p style="font-size: 15px; font-weight: 400; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                    {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali' }}
                </p>
            </a>

            <!-- Card 2: Phone Number (Directly callable via tel:) -->
            <a href="tel:{{ $settings->clean_phone }}" 
               class="contact-info-card"
               title="Call {{ $settings->phone ?? '0859 3666 6384' }}"
               aria-label="Phone Number: {{ $settings->phone ?? '0859 3666 6384' }} (Tap to call)">
                <div class="contact-info-card-icon">
                    <i data-lucide="phone" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px; color: var(--primary-navy);">Phone Number</h3>
                <p style="font-size: 15px; font-weight: 400; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                    {{ $settings->phone ?? '0859 3666 6384' }}
                </p>
            </a>

            <!-- Card 3: Email Address (Directly opens default email client via mailto:) -->
            <a href="mailto:{{ $settings->email ?? 'lovinanorthbaliagency2023@gmail.com' }}" 
               class="contact-info-card"
               title="Send email to {{ $settings->email ?? 'lovinanorthbaliagency2023@gmail.com' }}"
               aria-label="Email Address: {{ $settings->email ?? 'lovinanorthbaliagency2023@gmail.com' }} (Tap to send email)">
                <div class="contact-info-card-icon">
                    <i data-lucide="mail" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px; color: var(--primary-navy);">Email Address</h3>
                <p style="font-size: 15px; font-weight: 400; color: var(--text-secondary); line-height: 1.6; margin: 0; word-break: break-word;">
                    {{ $settings->email ?? 'lovinanorthbaliagency2023@gmail.com' }}
                </p>
            </a>

            <!-- Card 4: Business Hours (Informational only with consistent hover treatment) -->
            <div class="contact-info-card" style="cursor: default;" aria-label="Business Hours">
                <div class="contact-info-card-icon">
                    <i data-lucide="clock" class="lucide-icon lucide-icon-lg" style="color: var(--primary-navy);"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 8px; color: var(--primary-navy);">Business Hours</h3>
                @php
                    $bHours = $settings->formatted_business_hours;
                    $hourLines = [];
                    foreach ($bHours as $bh) {
                        $day = $bh['day'] ?? 'Monday – Friday';
                        $hoursStr = $bh['hours'] ?? '09.00 – 12.00, 13.00 – 17.00';
                        
                        $hourLines[] = $day;
                        
                        if (str_contains($hoursStr, ',')) {
                            $shifts = array_map('trim', explode(',', $hoursStr));
                            if (count($shifts) >= 2) {
                                $hourLines[] = $shifts[0];
                                $hourLines[] = 'Break: 12.00 – 13.00';
                                $hourLines[] = $shifts[1];
                            } else {
                                $hourLines[] = $hoursStr;
                            }
                        } elseif (str_contains(strtolower($hoursStr), '09.00 – 17.00') || str_contains(strtolower($hoursStr), '09:00 - 17:00')) {
                            $hourLines[] = '09.00 – 12.00';
                            $hourLines[] = 'Break: 12.00 – 13.00';
                            $hourLines[] = '13.00 – 17.00';
                        } else {
                            $hourLines[] = $hoursStr;
                        }
                    }
                @endphp
                <div style="font-size: 15px; font-weight: 400; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                    @foreach($hourLines as $line)
                        {{ $line }}@if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Our Office Photo & Find Us Google Maps -->
        <div class="office-map-grid">
            <div>
                <h3 style="margin-bottom: 16px;">Our Office</h3>
                <div style="height: 320px; border-radius: var(--radius-md); border: 1px solid #E5E7EB; background-color: #F3F4F6; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; position: relative; box-sizing: border-box;">
                    @if($settings->has_office_photo)
                        <img src="{{ $settings->office_photo_url }}" 
                             alt="{{ $settings->company_name ?? 'Lovina North Bali Real Estate' }} Office" 
                             style="width: 100%; height: 100%; object-fit: cover; display: block;"
                             onerror="this.style.display='none'; var fb = document.getElementById('office-photo-fallback'); if(fb) fb.style.display='flex';">
                        <div id="office-photo-fallback" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                            <i data-lucide="building" style="width: 48px; height: 48px; color: #9CA3AF; stroke-width: 1.5px;"></i>
                            <span style="color: #9CA3AF; font-size: 15px; font-weight: 500; font-family: 'Poppins', sans-serif;">Office Photo</span>
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                            <i data-lucide="building" style="width: 48px; height: 48px; color: #9CA3AF; stroke-width: 1.5px;"></i>
                            <span style="color: #9CA3AF; font-size: 15px; font-weight: 500; font-family: 'Poppins', sans-serif;">Office Photo</span>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 16px;">Find Us</h3>
                <!-- Real Google Maps Embed -->
                <div style="height: 320px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border); background-color: #E2E8F0; position: relative; margin-bottom: 16px;">
                    <iframe 
                        src="{{ $settings->google_maps_embed_src }}" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Office Address Display -->
                <div style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px; background-color: var(--light-gray); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 14px 16px;">
                    <i data-lucide="map-pin" class="lucide-icon lucide-icon-sm" style="color: var(--primary-navy); margin-top: 2px; flex-shrink: 0;"></i>
                    <div style="font-size: 14px; line-height: 1.5; color: var(--text-secondary);">
                        <strong style="color: var(--primary-navy); display: block; margin-bottom: 2px;">Office Address:</strong>
                        {{ $settings->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali' }}
                    </div>
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
                    <a href="{{ $settings->whatsapp_url ?? 'https://wa.me/' . ($settings->clean_whatsapp ?? '6285936666384') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       style="width: 64px; height: 64px; border: 1.5px solid #25D366; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" 
                       class="social-circle-btn" 
                       aria-label="WhatsApp"
                       title="Chat on WhatsApp">
                        <!-- Official WhatsApp Brand Icon -->
                        <svg viewBox="0 0 24 24" style="width: 38px; height: 38px; fill: #25D366;" aria-hidden="true">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </a>
                    <span style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Poppins', sans-serif;">WhatsApp</span>
                </div>
                
                <!-- Facebook -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a @if(!empty($settings->facebook_url)) href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer" title="Visit Facebook Page" @else href="#" onclick="return false;" title="Official Facebook link (Pending configuration)" @endif
                       style="width: 64px; height: 64px; border: 1.5px solid #1877F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" 
                       class="social-circle-btn" 
                       aria-label="Facebook">
                        <!-- Official Facebook 'f' Logo -->
                        <svg viewBox="0 0 24 24" style="width: 34px; height: 34px; fill: #1877F2;" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <span style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Poppins', sans-serif;">Facebook</span>
                </div>
                
                <!-- YouTube -->
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a @if(!empty($settings->youtube_url)) href="{{ $settings->youtube_url }}" target="_blank" rel="noopener noreferrer" title="Visit YouTube Channel" @else href="#" onclick="return false;" title="Official YouTube link (Pending configuration)" @endif
                       style="width: 64px; height: 64px; border: 1.5px solid #FF0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; background-color: var(--white); transition: background-color 0.2s, transform 0.2s;" 
                       class="social-circle-btn" 
                       aria-label="YouTube">
                        <!-- Official YouTube Play Button Logo -->
                        <svg viewBox="0 0 24 24" style="width: 38px; height: 38px; fill: #FF0000;" aria-hidden="true">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
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

        <!-- Closing CTA Message -->
        <div style="text-align: center; max-width: 700px; margin: 48px auto 0 auto; padding-top: 24px;">
            <p class="body-text" style="font-size: 18px; font-weight: 500; color: var(--primary-navy); margin-bottom: 8px;">
                We hope we can do something for you, and we would love to hear from you.
            </p>
            <p class="body-text" style="font-size: 16px; font-weight: 600; color: var(--secondary-gold);">
                The Lovina North Bali Real Estate Agency Team.
            </p>
        </div>
    </div>
</section>
@endsection
