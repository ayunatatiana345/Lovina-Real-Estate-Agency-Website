@extends('layouts.admin')

@section('title', 'Website CMS')
@section('page_title', 'Website CMS Management')

@section('content')
<!-- Tabs -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #E2E8F0; padding-bottom: 12px;">
    <a href="{{ route('admin.cms.index', ['tab' => 'homepage']) }}" class="btn {{ $tab === 'homepage' ? 'btn-primary' : 'btn-outline' }}" style="padding: 8px 20px;">
        🏠 Homepage CMS
    </a>
    <a href="{{ route('admin.cms.index', ['tab' => 'about']) }}" class="btn {{ $tab === 'about' ? 'btn-primary' : 'btn-outline' }}" style="padding: 8px 20px;">
        ℹ️ About Us CMS
    </a>
</div>

@if($tab === 'homepage')
<!-- Homepage CMS Split Layout (Form Left, Live Preview Right - Matching Reference Image 2) -->
<div class="cms-split-container">
    <!-- Form Side (Left) -->
    <div class="admin-card">
        <h3 style="font-size: 18px; margin-bottom: 20px; color: #0F172A;">Edit Homepage Content</h3>

        <form action="{{ route('admin.cms.homepage.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Hero Section -->
            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                <h4 style="font-size: 16px; margin-bottom: 14px; color: #1E3A8A;">1. Hero Section</h4>

                <div class="form-group">
                    <label class="form-label" for="hero_heading">Hero Heading *</label>
                    <input type="text" name="hero_heading" id="hero_heading" class="form-control" value="{{ $hero['heading'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_subheading">Hero Subheading *</label>
                    <textarea name="hero_subheading" id="hero_subheading" class="form-control" required>{{ $hero['subheading'] ?? '' }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_bg">Hero Background Image</label>
                    <input type="file" name="hero_bg" id="hero_bg" class="form-control">
                </div>
            </div>

            <!-- Contact CTA Section -->
            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                <h4 style="font-size: 16px; margin-bottom: 14px; color: #1E3A8A;">2. Contact CTA Section</h4>

                <div class="form-group">
                    <label class="form-label" for="cta_heading">CTA Heading *</label>
                    <input type="text" name="cta_heading" id="cta_heading" class="form-control" value="{{ $cta['heading'] ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cta_description">CTA Description *</label>
                    <textarea name="cta_description" id="cta_description" class="form-control" required>{{ $cta['description'] ?? '' }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cta_button_text">Button Label *</label>
                    <input type="text" name="cta_button_text" id="cta_button_text" class="form-control" value="{{ $cta['button_text'] ?? '' }}" required>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">💾 Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Live Preview Side (Right - Matching Reference Image 2 Pattern) -->
    <div>
        <div style="font-size: 14px; font-weight: 700; color: #64748B; margin-bottom: 12px;">Live Preview – Homepage</div>
        <div class="cms-preview-panel">
            <div style="background-color: #D6E6F7; padding: 32px 20px; border-radius: 8px; margin-bottom: 20px;">
                <h2 style="font-size: 24px; color: #1E3A8A; margin-bottom: 8px;">{{ $hero['heading'] ?? '' }}</h2>
                <p style="font-size: 14px; color: #334155;">{{ $hero['subheading'] ?? '' }}</p>
            </div>

            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="font-size: 16px; font-weight: 700; color: #1E3A8A; margin-bottom: 12px;">Featured Properties</div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    @foreach($featuredProperties->take(3) as $fp)
                        <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px; font-size: 11px;">
                            <div style="font-weight: 700;">{{ $fp->name }}</div>
                            <div style="color: #16A34A; font-weight: 600;">${{ number_format($fp->price) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="background-color: #D6E6F7; text-align: center; padding: 24px; border-radius: 8px;">
                <h3 style="font-size: 18px; color: #1E3A8A; margin-bottom: 6px;">{{ $cta['heading'] ?? '' }}</h3>
                <p style="font-size: 13px; color: #334155; margin-bottom: 12px;">{{ $cta['description'] ?? '' }}</p>
                <button class="btn btn-primary" style="padding: 6px 16px; font-size: 12px;">{{ $cta['button_text'] ?? 'Contact' }}</button>
            </div>
        </div>
    </div>
</div>
@else
<!-- About Us CMS Split Layout -->
<div class="cms-split-container">
    <!-- Form Side (Left) -->
    <div class="admin-card">
        <h3 style="font-size: 18px; margin-bottom: 20px; color: #0F172A;">Edit About Us Content</h3>

        <form action="{{ route('admin.cms.about.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="story_title">Story Title *</label>
                <input type="text" name="story_title" id="story_title" class="form-control" value="{{ $story['title'] ?? '' }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="story_description">Story Description *</label>
                <textarea name="story_description" id="story_description" class="form-control" style="min-height: 140px;" required>{{ $story['description'] ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="vision">Vision Statement *</label>
                <textarea name="vision" id="vision" class="form-control" required>{{ $story['vision'] ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Mission Points</label>
                <input type="text" name="mission_1" class="form-control" value="{{ $story['mission'][0] ?? '' }}" style="margin-bottom: 8px;" placeholder="Mission Point 1">
                <input type="text" name="mission_2" class="form-control" value="{{ $story['mission'][1] ?? '' }}" style="margin-bottom: 8px;" placeholder="Mission Point 2">
                <input type="text" name="mission_3" class="form-control" value="{{ $story['mission'][2] ?? '' }}" placeholder="Mission Point 3">
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">💾 Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Live Preview Side (Right) -->
    <div>
        <div style="font-size: 14px; font-weight: 700; color: #64748B; margin-bottom: 12px;">Live Preview – About Us</div>
        <div class="cms-preview-panel">
            <h2 style="font-size: 22px; color: #1E3A8A; margin-bottom: 8px;">{{ $story['title'] ?? 'Our Story' }}</h2>
            <p style="font-size: 14px; color: #334155; line-height: 1.6; margin-bottom: 16px;">
                {{ $story['description'] ?? '' }}
            </p>

            <div style="background-color: #F4F1FA; border-left: 4px solid #1E3A8A; padding: 12px 16px; margin-bottom: 16px;">
                <div style="font-weight: 700; font-size: 13px; color: #1E3A8A;">Vision</div>
                <div style="font-size: 13px; color: #475569;">{{ $story['vision'] ?? '' }}</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
