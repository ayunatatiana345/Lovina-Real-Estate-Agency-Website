@extends('layouts.admin')

@section('title', 'Company Settings')
@section('page_title', 'Company Settings')

@section('content')
<div style="max-width: 900px;">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- General Branding -->
        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px;">General & Branding Information</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name *</label>
                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $settings->company_name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="site_title">Website Title *</label>
                    <input type="text" name="site_title" id="site_title" class="form-control" value="{{ old('site_title', $settings->site_title) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="tagline">Tagline</label>
                <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $settings->tagline) }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="site_description">Site Description</label>
                <textarea name="site_description" id="site_description" class="form-control">{{ old('site_description', $settings->site_description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="logo_primary">Primary Logo (Upload)</label>
                    <input type="file" name="logo_primary" id="logo_primary" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" for="office_photo">Office Photo (Upload)</label>
                    <input type="file" name="office_photo" id="office_photo" class="form-control">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px;">Contact Information</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number *</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $settings->phone) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="whatsapp">WhatsApp Number *</label>
                    <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp', $settings->whatsapp) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $settings->email) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Office Address *</label>
                <textarea name="address" id="address" class="form-control" required>{{ old('address', $settings->address) }}</textarea>
            </div>
        </div>

        <!-- Social Media & Google Maps -->
        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px;">Social Media & Google Maps Embed</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="instagram_url">Instagram URL</label>
                    <input type="url" name="instagram_url" id="instagram_url" class="form-control" value="{{ old('instagram_url', $settings->instagram_url) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="facebook_url">Facebook URL</label>
                    <input type="url" name="facebook_url" id="facebook_url" class="form-control" value="{{ old('facebook_url', $settings->facebook_url) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="youtube_url">YouTube URL</label>
                    <input type="url" name="youtube_url" id="youtube_url" class="form-control" value="{{ old('youtube_url', $settings->youtube_url) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="google_maps_embed_url">Google Maps Embed URL / Iframe HTML</label>
                <textarea name="google_maps_embed_url" id="google_maps_embed_url" class="form-control">{{ old('google_maps_embed_url', $settings->google_maps_embed_url) }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 40px;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 36px;" id="btn-save-settings">
                💾 Save Company Settings
            </button>
        </div>
    </form>
</div>
@endsection
