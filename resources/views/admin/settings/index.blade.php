@extends('layouts.admin')

@section('title', 'Company Settings')
@section('page_title', 'Company Settings')

@section('content')
<!-- Header Area (Matching Reference Image) -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Company Settings</h2>
        <p style="font-size: 14px; color: #64748B;">Manage your company identity and global information used across the website.</p>
    </div>

    <div style="display: flex; align-items: center; gap: 16px;">
        <button type="submit" form="company-settings-form" class="btn btn-primary" style="padding: 12px 28px; font-size: 14px; background-color: #1E3A8A; border-color: #1E3A8A; display: flex; align-items: center; gap: 8px; font-weight: 600;" id="btn-save-settings-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Changes
        </button>
    </div>
</div>

<!-- Success Notification Banner -->
@if(session('success'))
<div class="settings-success-alert" id="settings-success-alert" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('settings-success-alert').remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>
@endif

<!-- Settings Form Wrapper -->
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="company-settings-form">
    @csrf

    <!-- ROW 1 (2 Columns: A & B) -->
    <div class="settings-row-2col">
        <!-- CARD A: Site Information -->
        <div class="admin-card" id="sec-site-info" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">A. Site Information</h3>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="company_name">Company Name *</label>
                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $settings->company_name) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="site_title">Site Title *</label>
                    <input type="text" name="site_title" id="site_title" class="form-control" value="{{ old('site_title', $settings->site_title) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="tagline">Tagline</label>
                    <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $settings->tagline) }}" style="width: 100%;" placeholder="Your Trusted Property Partner in North Bali">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="site_description">Site Description</label>
                    <textarea name="site_description" id="site_description" class="form-control" style="min-height: 120px; width: 100%; resize: vertical;" maxlength="160" oninput="updateCharCounter(this, 'site-desc-counter')">{{ old('site_description', $settings->site_description) }}</textarea>
                    <div style="text-align: right; font-size: 11px; color: #64748B; margin-top: 4px;" id="site-desc-counter">Character: {{ strlen($settings->site_description ?? '') }} / 160</div>
                </div>
            </div>
        </div>

        <!-- CARD B: Branding -->
        <div class="admin-card" id="sec-branding" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">B. Branding</h3>

            <!-- Primary Logo -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Primary Logo</label>
                <div class="branding-thumb-box" style="margin-bottom: 0;">
                    <div class="branding-thumb-preview" id="prev-primary-logo-wrap">
                        <img src="{{ $settings->logo_primary ? asset('storage/' . $settings->logo_primary) : asset('images/logo-placeholder.png') }}" id="prev-primary-logo-img" alt="Primary Logo" onerror="this.src='https://via.placeholder.com/120x40?text=LOVINA+NAVY'">
                    </div>
                    <div>
                        <div style="display: flex; gap: 8px; margin-bottom: 6px;">
                            <label class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; cursor: pointer; color: #2563EB; border-color: #2563EB;">
                                Change
                                <input type="file" name="logo_primary" accept="image/*" style="display: none;" onchange="previewBrandingImage(this, 'prev-primary-logo-img')">
                            </label>
                            <button type="button" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; color: #DC2626; border-color: #FCA5A5;" onclick="clearBrandingImage('prev-primary-logo-img')">Remove</button>
                        </div>
                        <div style="font-size: 11px; color: #64748B;">Recommended: 300 x 100px PNG or SVG</div>
                    </div>
                </div>
            </div>

            <!-- Alternative Logo -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Alternative Logo</label>
                <div class="branding-thumb-box" style="margin-bottom: 0;">
                    <div class="branding-thumb-preview" id="prev-alt-logo-wrap">
                        <img src="{{ $settings->logo_alt ? asset('storage/' . $settings->logo_alt) : asset('images/logo-alt-placeholder.png') }}" id="prev-alt-logo-img" alt="Alternative Logo" onerror="this.src='https://via.placeholder.com/120x40?text=LOVINA+ALT'">
                    </div>
                    <div>
                        <div style="display: flex; gap: 8px; margin-bottom: 6px;">
                            <label class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; cursor: pointer; color: #2563EB; border-color: #2563EB;">
                                Change
                                <input type="file" name="logo_alt" accept="image/*" style="display: none;" onchange="previewBrandingImage(this, 'prev-alt-logo-img')">
                            </label>
                            <button type="button" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; color: #DC2626; border-color: #FCA5A5;" onclick="clearBrandingImage('prev-alt-logo-img')">Remove</button>
                        </div>
                        <div style="font-size: 11px; color: #64748B;">Recommended: 300 x 80px PNG or SVG</div>
                    </div>
                </div>
            </div>

            <!-- Site Icon / Favicon -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Site Icon / Favicon</label>
                <div class="branding-thumb-box" style="margin-bottom: 0; padding: 12px 16px;">
                    <div class="branding-thumb-preview" style="width: 50px; height: 50px;">
                        <img src="{{ $settings->favicon ? asset('storage/' . $settings->favicon) : asset('images/favicon-placeholder.png') }}" id="prev-favicon-img" alt="Favicon" onerror="this.src='https://via.placeholder.com/40x40?text=BALI'">
                    </div>
                    <div>
                        <div style="display: flex; gap: 8px; margin-bottom: 6px;">
                            <label class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; cursor: pointer; color: #2563EB; border-color: #2563EB;">
                                Change
                                <input type="file" name="favicon" accept="image/*" style="display: none;" onchange="previewBrandingImage(this, 'prev-favicon-img')">
                            </label>
                            <button type="button" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; color: #DC2626; border-color: #FCA5A5;" onclick="clearBrandingImage('prev-favicon-img')">Remove</button>
                        </div>
                        <div style="font-size: 11px; color: #64748B;">Recommended: 512 x 512px PNG</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2 (3 Columns: C, D & E) -->
    <div class="settings-row-3col">
        <!-- CARD C: Contact Information -->
        <div class="admin-card" id="sec-contact-info" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">C. Contact Information</h3>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="phone">Phone Number *</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $settings->phone ?? '+62 362 1234567') }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="whatsapp">WhatsApp Number *</label>
                    <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp', $settings->whatsapp ?? '+62 812 3456 7890') }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $settings->email ?? 'info@lovinabali.com') }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="address">Office Address *</label>
                    <textarea name="address" id="address" class="form-control" style="min-height: 100px; width: 100%; resize: vertical;" required>{{ old('address', $settings->address ?? 'Jl. Raya Lovina No. 88, Kalibukbuk, Buleleng, Bali 81152, Indonesia') }}</textarea>
                </div>
            </div>

            <div class="helper-text-box" style="margin-top: 20px;">
                ℹ️ This information will automatically appear in the Footer and Contact Us page.
            </div>
        </div>

        <!-- CARD D: Social Media -->
        <div class="admin-card" id="sec-social-media" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">D. Social Media</h3>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="instagram_url">Instagram URL</label>
                    <input type="text" name="instagram_url" id="instagram_url" class="form-control" value="{{ old('instagram_url', $settings->instagram_url ?? 'https://instagram.com/lovinabali') }}" style="width: 100%;" placeholder="https://instagram.com/lovinabali">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="facebook_url">Facebook URL</label>
                    <input type="text" name="facebook_url" id="facebook_url" class="form-control" value="{{ old('facebook_url', $settings->facebook_url ?? 'https://facebook.com/lovinabali') }}" style="width: 100%;" placeholder="https://facebook.com/lovinabali">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="whatsapp_url">WhatsApp URL</label>
                    <input type="text" name="whatsapp_url" id="whatsapp_url" class="form-control" value="{{ old('whatsapp_url', $settings->whatsapp_url ?? 'https://wa.me/6281234567890') }}" style="width: 100%;" placeholder="https://wa.me/6281234567890">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="youtube_url">YouTube URL (Optional)</label>
                    <input type="text" name="youtube_url" id="youtube_url" class="form-control" value="{{ old('youtube_url', $settings->youtube_url ?? 'https://youtube.com/@lovinabali') }}" style="width: 100%;" placeholder="https://youtube.com/@lovinabali">
                </div>
            </div>
        </div>

        <!-- CARD E: Business Hours -->
        <div class="admin-card" id="sec-business-hours" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">E. Business Hours</h3>

                <!-- Monday - Friday -->
                <div class="business-hour-row">
                    <div style="font-size: 13px; font-weight: 600; color: #334155; width: 110px;">Monday - Friday</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="text" id="mf-start" class="form-control" value="09:00" style="width: 60px; padding: 6px 8px; text-align: center; font-size: 13px;">
                        <span style="color: #64748B;">-</span>
                        <input type="text" id="mf-end" class="form-control" value="17:00" style="width: 60px; padding: 6px 8px; text-align: center; font-size: 13px;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" id="mf-open" checked onchange="toggleDayStatus('mf-start', 'mf-end', this)">
                        <span style="font-weight: 600; color: #0F172A;">Open</span>
                    </label>
                </div>

                <!-- Saturday -->
                <div class="business-hour-row">
                    <div style="font-size: 13px; font-weight: 600; color: #334155; width: 110px;">Saturday</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="text" id="sat-start" class="form-control" value="09:00" style="width: 60px; padding: 6px 8px; text-align: center; font-size: 13px;">
                        <span style="color: #64748B;">-</span>
                        <input type="text" id="sat-end" class="form-control" value="14:00" style="width: 60px; padding: 6px 8px; text-align: center; font-size: 13px;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" id="sat-open" checked onchange="toggleDayStatus('sat-start', 'sat-end', this)">
                        <span style="font-weight: 600; color: #0F172A;">Open</span>
                    </label>
                </div>

                <!-- Sunday -->
                <div class="business-hour-row">
                    <div style="font-size: 13px; font-weight: 600; color: #334155; width: 110px;">Sunday</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="text" id="sun-start" class="form-control" value="Closed" disabled style="width: 132px; padding: 6px 8px; text-align: center; font-size: 13px; background-color: #F1F5F9; color: #94A3B8;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" id="sun-open" onchange="toggleSundayStatus('sun-start', this)">
                        <span style="font-weight: 600; color: #0F172A;">Open</span>
                    </label>
                </div>

                <div class="form-group" style="margin-top: 16px; margin-bottom: 0;">
                    <label class="form-label" for="public_holiday">Public Holiday</label>
                    <textarea id="public_holiday" class="form-control" style="min-height: 60px; font-size: 13px; width: 100%;" placeholder="Please check our Google Maps or contact us for more information.">Please check our Google Maps or contact us for more information.</textarea>
                </div>
            </div>

            <div class="helper-text-box" style="margin-top: 20px;">
                ℹ️ These hours will be displayed on the Contact Us page.
            </div>
        </div>
    </div>

    <!-- ROW 3 (2 Columns: F & G) -->
    <div class="settings-row-2col">
        <!-- CARD F: Google Maps -->
        <div class="admin-card" id="sec-google-maps" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">F. Google Maps</h3>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="google_maps_embed_url">Google Maps Embed URL</label>
                    <input type="text" name="google_maps_embed_url" id="google_maps_embed_url" class="form-control" value="{{ old('google_maps_embed_url', $settings->google_maps_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15796...') }}" style="width: 100%;" placeholder="https://www.google.com/maps/embed?pb=...">
                    <div style="font-size: 11px; color: #64748B; margin-top: 4px;">Paste the embed code from Google Maps (Share &gt; Embed a map).</div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="google_maps_direction_url">Google Maps Direction URL</label>
                    <input type="text" name="google_maps_direction_url" id="google_maps_direction_url" class="form-control" value="{{ old('google_maps_direction_url', $settings->google_maps_direction_url ?? 'https://maps.app.goo.gl/abc123example') }}" style="width: 100%;" placeholder="https://maps.app.goo.gl/abc123example">
                    <div style="font-size: 11px; color: #64748B; margin-top: 4px;">Paste the direction link from Google Maps (Share &gt; Copy link).</div>
                </div>
            </div>

            <!-- Small Map Preview -->
            <div style="height: 140px; border-radius: 8px; overflow: hidden; border: 1px solid #CBD5E1; position: relative; background-color: #E2E8F0;">
                <svg width="100%" height="100%" viewBox="0 0 400 140" preserveAspectRatio="none">
                    <rect width="400" height="140" fill="#E0F2FE"/>
                    <path d="M0 60 Q 100 40, 200 70 T 400 50" stroke="#93C5FD" stroke-width="20" fill="none"/>
                    <path d="M0 110 Q 150 90, 300 120 T 400 100" stroke="#FDE68A" stroke-width="12" fill="none"/>
                    <!-- Pin Marker -->
                    <g transform="translate(220, 45)">
                        <circle cx="0" cy="0" r="14" fill="#EF4444"/>
                        <circle cx="0" cy="0" r="5" fill="#FFFFFF"/>
                        <path d="M -14 0 L 0 20 L 14 0 Z" fill="#EF4444"/>
                    </g>
                    <text x="240" y="45" font-family="'Poppins', sans-serif" font-size="11" font-weight="bold" fill="#1E3A8A">Lovina North Bali Real Estate Agency</text>
                    <text x="240" y="60" font-family="'Poppins', sans-serif" font-size="9" fill="#475569">Kalibukbuk, Lovina</text>
                </svg>
            </div>
        </div>

        <!-- CARD G: SEO Defaults (Optional) -->
        <div class="admin-card" id="sec-seo-defaults" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">G. SEO Defaults <span style="font-size: 13px; font-weight: 400; color: #64748B;">(Optional)</span></h3>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="seo_meta_title">Default Meta Title</label>
                    <input type="text" name="seo_meta_title" id="seo_meta_title" class="form-control" value="{{ old('seo_meta_title', $settings->seo_meta_title ?? 'Lovina North Bali Real Estate') }}" style="width: 100%;" placeholder="Lovina North Bali Real Estate">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="seo_meta_description">Default Meta Description</label>
                    <textarea name="seo_meta_description" id="seo_meta_description" class="form-control" style="min-height: 100px; width: 100%; resize: vertical;" placeholder="Find your dream property in North Bali. Villas, Houses, Land, and Commercial Properties.">{{ old('seo_meta_description', $settings->seo_meta_description ?? 'Find your dream property in North Bali. Villas, Houses, Land, and Commercial Properties.') }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Default Social Sharing Image</label>
                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div style="width: 120px; height: 75px; border-radius: 6px; overflow: hidden; border: 1px solid #CBD5E1; background-color: #F8FAFC; flex-shrink: 0;">
                            <img src="{{ $settings->seo_social_image ? asset('storage/' . $settings->seo_social_image) : asset('images/property-placeholder.jpg') }}" id="prev-seo-img" alt="SEO Social Sharing" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <div style="display: flex; gap: 8px; margin-bottom: 6px;">
                                <label class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; cursor: pointer; color: #2563EB; border-color: #2563EB;">
                                    Change
                                    <input type="file" name="seo_social_image" accept="image/*" style="display: none;" onchange="previewBrandingImage(this, 'prev-seo-img')">
                                </label>
                                <button type="button" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; color: #DC2626; border-color: #FCA5A5;" onclick="clearBrandingImage('prev-seo-img')">Remove</button>
                            </div>
                            <div style="font-size: 11px; color: #64748B;">Recommended: 1200 x 630px JPG or PNG</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="helper-text-box" style="margin-top: 20px;">
                ℹ️ These settings will be used as default SEO values across the website.
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
// Character Counter
function updateCharCounter(el, counterId) {
    const counter = document.getElementById(counterId);
    if (counter) {
        counter.textContent = `Character: ${el.value.length} / ${el.getAttribute('maxlength') || 160}`;
    }
}

// Branding Image Thumbnail Previewer
function previewBrandingImage(input, targetImgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(targetImgId);
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Branding Image Clear / Remove
function clearBrandingImage(targetImgId) {
    const img = document.getElementById(targetImgId);
    if (img) {
        img.src = 'https://via.placeholder.com/120x40?text=REMOVED';
    }
}

// Business Hours Day Status Toggler
function toggleDayStatus(startId, endId, checkbox) {
    const startInput = document.getElementById(startId);
    const endInput = document.getElementById(endId);

    if (checkbox.checked) {
        startInput.disabled = false;
        endInput.disabled = false;
        startInput.style.backgroundColor = '#FFFFFF';
        endInput.style.backgroundColor = '#FFFFFF';
    } else {
        startInput.disabled = true;
        endInput.disabled = true;
        startInput.style.backgroundColor = '#F1F5F9';
        endInput.style.backgroundColor = '#F1F5F9';
    }
}

function toggleSundayStatus(startId, checkbox) {
    const startInput = document.getElementById(startId);
    if (checkbox.checked) {
        startInput.disabled = false;
        startInput.value = '09:00 - 14:00';
        startInput.style.backgroundColor = '#FFFFFF';
    } else {
        startInput.disabled = true;
        startInput.value = 'Closed';
        startInput.style.backgroundColor = '#F1F5F9';
    }
}
</script>
@endsection
