@extends('layouts.admin')

@section('title', 'Website CMS')

@section('content')
<!-- Header Area (Matching Reference Images A & B) -->
<div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Website CMS</h2>
        <p style="font-size: 14px; color: #64748B;">Manage all website content from one place.</p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center;">
        <a href="{{ $tab === 'about' ? route('about') : route('home') }}" target="_blank" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;" id="btn-preview-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            Preview {{ $tab === 'about' ? 'About Us' : 'Homepage' }}
        </a>
        <button type="submit" form="cms-main-form" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; background-color: #1E3A8A; border-color: #1E3A8A;" id="btn-save-all-header">
            Save All Changes
        </button>
    </div>
</div>

<!-- Tabs Navigation (Matching Reference Images A & B) -->
<div class="cms-tab-bar" id="cms-tab-bar">
    <a href="{{ route('admin.cms.index', ['tab' => 'homepage']) }}" class="cms-tab-item {{ $tab === 'homepage' ? 'active' : '' }}" id="tab-link-homepage">
        Homepage
    </a>
    <a href="{{ route('admin.cms.index', ['tab' => 'about']) }}" class="cms-tab-item {{ $tab === 'about' ? 'active' : '' }}" id="tab-link-about">
        About Us
    </a>
</div>

@if($tab === 'homepage')
<!-- =========================================================
     TAB 1: HOMEPAGE (9 SECTIONS) - 3 COLUMN LAYOUT
     ========================================================= -->
<form action="{{ route('admin.cms.homepage.update') }}" method="POST" enctype="multipart/form-data" id="cms-main-form">
    @csrf

    <div class="cms-3col-layout">
        <!-- COLUMN 1: Section Navigation List (Left) -->
        <div>
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Homepage Sections</div>
            
            <div id="section-nav-list">
                <!-- 1. Hero Section Card -->
                <div class="cms-nav-card active" data-section="sec-hero" onclick="switchSection('sec-hero')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">1. Hero Section</div>
                            <div style="font-size: 11px; color: #64748B;">Manage top hero content.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #2563EB;">▲</span>
                </div>

                <!-- 2. Search Section Card -->
                <div class="cms-nav-card" data-section="sec-search" onclick="switchSection('sec-search')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">2. Search Section</div>
                            <div style="font-size: 11px; color: #64748B;">Search form visibility & filters.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 3. Featured Properties Section Card -->
                <div class="cms-nav-card" data-section="sec-featured" onclick="switchSection('sec-featured')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">3. Featured Properties</div>
                            <div style="font-size: 11px; color: #64748B;">Select top featured properties.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 4. Latest Properties Section Card -->
                <div class="cms-nav-card" data-section="sec-latest" onclick="switchSection('sec-latest')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">4. Latest Properties</div>
                            <div style="font-size: 11px; color: #64748B;">Latest listings display count.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 5. Property Categories Section Card -->
                <div class="cms-nav-card" data-section="sec-categories" onclick="switchSection('sec-categories')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">5. Property Categories</div>
                            <div style="font-size: 11px; color: #64748B;">Category grid title & text.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 6. Popular Locations Section Card -->
                <div class="cms-nav-card" data-section="sec-locations" onclick="switchSection('sec-locations')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">6. Popular Locations</div>
                            <div style="font-size: 11px; color: #64748B;">Select popular North Bali regions.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 7. Why Choose Us Section Card -->
                <div class="cms-nav-card" data-section="sec-why" onclick="switchSection('sec-why')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">7. Why Choose Us</div>
                            <div style="font-size: 11px; color: #64748B;">Manage agency benefit items.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 8. Company Statistics Section Card -->
                <div class="cms-nav-card" data-section="sec-stats" onclick="switchSection('sec-stats')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">8. Company Statistics</div>
                            <div style="font-size: 11px; color: #64748B;">Edit key agency metrics.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- 9. Contact CTA Section Card -->
                <div class="cms-nav-card" data-section="sec-cta" onclick="switchSection('sec-cta')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">9. Call To Action</div>
                            <div style="font-size: 11px; color: #64748B;">Bottom CTA title & button.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>
            </div>

            <!-- Cara Kerja Helper Box -->
            <div style="background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px; padding: 16px; margin-top: 20px;">
                <div style="font-size: 12px; font-weight: 700; color: #1E3A8A; margin-bottom: 8px; text-transform: uppercase;">CARA KERJA</div>
                <ol style="margin-left: 16px; font-size: 12px; color: #475569; line-height: 1.6;">
                    <li>Pilih section di sebelah kiri.</li>
                    <li>Section akan terbuka dan menampilkan form input.</li>
                    <li>Ubah konten sesuai kebutuhan.</li>
                    <li>Klik <strong>Save All Changes</strong> untuk menyimpan.</li>
                    <li>Lihat live preview di sebelah kanan.</li>
                </ol>
            </div>
        </div>

        <!-- COLUMN 2: Content Editor Active Form (Center) -->
        <div>
            <!-- SECTION 1: HERO SECTION -->
            <div class="admin-card cms-section-editor" id="sec-hero" style="display: block;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">1. Hero Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage the hero content that appears at the top of your homepage.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="hero_enabled" value="1" {{ ($hero['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">Hero Background Image</label>
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 140px; height: 90px; border-radius: 8px; overflow: hidden; background-color: #E2E8F0; border: 1px solid #CBD5E1;">
                            <img src="{{ asset('storage/' . ($hero['background_image'] ?? 'cms/hero-bg.jpg')) }}" id="prev-hero-img-thumb" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80'">
                        </div>
                        <div>
                            <input type="file" name="hero_bg" id="hero_bg" class="form-control" accept="image/*" style="margin-bottom: 8px;">
                            <div style="font-size: 12px; color: #64748B;">Recommended size: 1920 x 800px (JPG, PNG or WebP max 2MB).</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_small_title">Small Title (Optional)</label>
                    <input type="text" name="hero_small_title" id="hero_small_title" class="form-control" value="{{ $hero['small_title'] ?? 'Find Your Dream' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_heading">Main Heading *</label>
                    <input type="text" name="hero_heading" id="hero_heading" class="form-control" value="{{ $hero['heading'] ?? '' }}" required oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_subheading">Description *</label>
                    <textarea name="hero_subheading" id="hero_subheading" class="form-control" style="min-height: 100px; resize: vertical;" required oninput="updatePreview()">{{ $hero['subheading'] ?? '' }}</textarea>
                </div>

                <!-- Hero Buttons Manager -->
                <div style="border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; margin-bottom: 20px; background-color: #F8FAFC;">
                    <div style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">Hero Buttons</div>
                    
                    <div id="hero-buttons-container">
                        @php $buttons = $hero['buttons'] ?? [['text' => 'Browse Properties', 'link' => '/properties', 'style' => 'primary']]; @endphp
                        @foreach($buttons as $bIdx => $btn)
                            <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr auto; gap: 10px; margin-bottom: 10px; align-items: center;" class="hero-btn-row">
                                <input type="text" name="buttons_text[]" class="form-control btn-txt-input" value="{{ $btn['text'] }}" placeholder="Button Label" oninput="updatePreview()">
                                <input type="text" name="buttons_link[]" class="form-control" value="{{ $btn['link'] }}" placeholder="/properties">
                                <select name="buttons_style[]" class="form-select">
                                    <option value="primary" {{ $btn['style'] === 'primary' ? 'selected' : '' }}>Primary</option>
                                    <option value="outline" {{ $btn['style'] === 'outline' ? 'selected' : '' }}>Outline</option>
                                </select>
                                <button type="button" class="btn btn-outline" style="padding: 8px 12px; color: #DC2626; border-color: #FCA5A5;" onclick="this.parentElement.remove(); updatePreview();">Remove</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline" style="font-size: 13px; padding: 6px 14px; margin-top: 6px;" onclick="addHeroButtonRow()">+ Add Button</button>
                </div>

                <!-- Display Settings -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Overlay</label>
                        <select name="hero_overlay" class="form-select">
                            <option value="dark" {{ ($hero['overlay'] ?? 'dark') === 'dark' ? 'selected' : '' }}>Dark</option>
                            <option value="light" {{ ($hero['overlay'] ?? '') === 'light' ? 'selected' : '' }}>Light</option>
                            <option value="none" {{ ($hero['overlay'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Overlay Opacity</label>
                        <select name="hero_overlay_opacity" class="form-select">
                            <option value="40">40%</option>
                            <option value="60" selected>60%</option>
                            <option value="80">80%</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Text Alignment</label>
                        <select name="hero_text_alignment" class="form-select">
                            <option value="left" selected>Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 2: SEARCH SECTION -->
            <div class="admin-card cms-section-editor" id="sec-search" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">2. Search Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage search form visibility, placeholder text, and filter settings.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="search_enabled" value="1" {{ ($searchSection['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="search_placeholder">Search Placeholder Text</label>
                    <input type="text" name="search_placeholder" id="search_placeholder" class="form-control" value="{{ $searchSection['placeholder'] ?? 'Search Location / Property Name...' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label">Displayed Filter Options</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <input type="checkbox" name="search_filter_type" value="1" {{ ($searchSection['filter_type'] ?? true) ? 'checked' : '' }}> Property Type Dropdown
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <input type="checkbox" name="search_filter_location" value="1" {{ ($searchSection['filter_location'] ?? true) ? 'checked' : '' }}> Location Dropdown
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <input type="checkbox" name="search_filter_price" value="1" {{ ($searchSection['filter_price'] ?? true) ? 'checked' : '' }}> Price Range Dropdown
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 3: FEATURED PROPERTIES SECTION -->
            <div class="admin-card cms-section-editor" id="sec-featured" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">3. Featured Properties Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage the title and select featured properties (maximum 3).</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="featured_title">Section Title *</label>
                    <input type="text" name="featured_title" id="featured_title" class="form-control" value="{{ $featuredSection['section_title'] ?? 'Featured North Bali Properties' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label">Select Featured Properties (Check up to 3)</label>
                    <div style="max-height: 240px; overflow-y: auto; border: 1px solid #CBD5E1; border-radius: 8px; padding: 12px; background-color: #F8FAFC;">
                        @foreach($allProperties as $p)
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 8px; border-bottom: 1px solid #E2E8F0; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="featured_ids[]" value="{{ $p->id }}" {{ in_array($p->id, $featuredSection['selected_ids'] ?? []) ? 'checked' : '' }}>
                                    <span style="font-weight: 600; font-size: 14px;">{{ $p->name }}</span>
                                </div>
                                <span style="font-size: 13px; color: #16A34A; font-weight: 600;">${{ number_format($p->price) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 4: LATEST PROPERTIES SECTION -->
            <div class="admin-card cms-section-editor" id="sec-latest" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">4. Latest Properties Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage the title and number of latest properties displayed.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="latest_enabled" value="1" {{ ($latestSection['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="latest_title">Section Title *</label>
                    <input type="text" name="latest_title" id="latest_title" class="form-control" value="{{ $latestSection['section_title'] ?? 'Latest Added Properties' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="latest_count">Display Count (Number of properties)</label>
                    <input type="number" name="latest_count" id="latest_count" class="form-control" value="{{ $latestSection['display_count'] ?? 6 }}" min="3" max="12">
                </div>

                <div style="background-color: #EFF6FF; padding: 12px; border-radius: 8px; font-size: 13px; color: #1E40AF; margin-bottom: 20px;">
                    ℹ️ Note: Latest properties are automatically fetched from database based on newest creation date.
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 5: PROPERTY CATEGORIES SECTION -->
            <div class="admin-card cms-section-editor" id="sec-categories" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">5. Property Categories Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage category section heading and description.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="categories_enabled" value="1" {{ ($categoriesSection['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="categories_heading">Section Heading *</label>
                    <input type="text" name="categories_heading" id="categories_heading" class="form-control" value="{{ $categoriesSection['heading'] ?? 'Explore Property Categories' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="categories_description">Section Description</label>
                    <textarea name="categories_description" id="categories_description" class="form-control" style="min-height: 80px;" oninput="updatePreview()">{{ $categoriesSection['description'] ?? 'Find your perfect real estate match by category in North Bali.' }}</textarea>
                </div>

                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px; font-size: 13px; color: #64748B; margin-bottom: 20px;">
                    ℹ️ Data category items are populated directly from <strong>Properties &gt; Categories</strong>.
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 6: POPULAR LOCATIONS SECTION -->
            <div class="admin-card cms-section-editor" id="sec-locations" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">6. Popular Locations Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Select which locations are highlighted on the homepage.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="locations_enabled" value="1" {{ ($locationsSection['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="locations_heading">Section Heading *</label>
                    <input type="text" name="locations_heading" id="locations_heading" class="form-control" value="{{ $locationsSection['heading'] ?? 'Popular Locations in North Bali' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="locations_description">Section Description</label>
                    <textarea name="locations_description" id="locations_description" class="form-control" style="min-height: 80px;" oninput="updatePreview()">{{ $locationsSection['description'] ?? 'Prime coastal & mountain regions in Buleleng Regency.' }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Popular Locations Checklist</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        @foreach($allLocations as $loc)
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                                <input type="checkbox" name="popular_location_ids[]" value="{{ $loc->id }}" {{ in_array($loc->id, $locationsSection['selected_ids'] ?? []) ? 'checked' : '' }}>
                                <span style="font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="map-pin" style="width: 14px; height: 14px; stroke-width: 2.5px; color: #475569;"></i> {{ $loc->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 7: WHY CHOOSE US SECTION -->
            <div class="admin-card cms-section-editor" id="sec-why" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">7. Why Choose Us Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage agency trust badges and benefit items.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="why_label">Section Label</label>
                    <input type="text" name="why_label" id="why_label" class="form-control" value="{{ $whyChooseSection['section_label'] ?? 'WHY CHOOSE US' }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="why_heading">Section Heading *</label>
                    <input type="text" name="why_heading" id="why_heading" class="form-control" value="{{ $whyChooseSection['heading'] ?? 'Why Choose PT Lovina North Bali' }}" oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="why_description">Section Description</label>
                    <textarea name="why_description" id="why_description" class="form-control" style="min-height: 80px;" oninput="updatePreview()">{{ $whyChooseSection['description'] ?? 'Your trusted local partner for smooth real estate acquisitions.' }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 8: COMPANY STATISTICS SECTION -->
            <div class="admin-card cms-section-editor" id="sec-stats" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">8. Company Statistics Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Edit key agency metrics displayed across website.</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    @php $statItems = $statsSection['items'] ?? [
                        ['number' => '120+', 'label' => 'Properties Listed', 'icon' => 'home', 'enabled' => true],
                        ['number' => '15+', 'label' => 'Years Experience', 'icon' => 'award', 'enabled' => true],
                        ['number' => '450+', 'label' => 'Happy Clients', 'icon' => 'users', 'enabled' => true],
                        ['number' => '99%', 'label' => 'Customer Satisfaction', 'icon' => 'star', 'enabled' => true],
                    ]; @endphp

                    @foreach($statItems as $sIdx => $st)
                        <div style="display: grid; grid-template-columns: 1.5fr 2fr 1fr auto; gap: 10px; align-items: center; background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px;">
                            <input type="text" name="stat_numbers[]" class="form-control" value="{{ $st['number'] }}" placeholder="e.g. 120+">
                            <input type="text" name="stat_labels[]" class="form-control" value="{{ $st['label'] }}" placeholder="Label">
                            <input type="text" name="stat_icons[]" class="form-control" value="{{ $st['icon'] }}" placeholder="icon">
                            <label style="display: flex; align-items: center; gap: 4px; font-size: 12px;">
                                <input type="checkbox" name="stat_enabled[{{ $sIdx }}]" value="1" {{ ($st['enabled'] ?? true) ? 'checked' : '' }}> Enable
                            </label>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- SECTION 9: CALL TO ACTION SECTION -->
            <div class="admin-card cms-section-editor" id="sec-cta" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">9. Call To Action Section</h3>
                        <p style="font-size: 13px; color: #64748B;">Manage the bottom CTA heading, description, and button.</p>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background-color: #DCFCE7; color: #15803D; padding: 6px 12px; border-radius: 20px;">
                        <input type="checkbox" name="cta_enabled" value="1" {{ ($cta['enabled'] ?? true) ? 'checked' : '' }} onchange="updatePreview()"> Enabled
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cta_heading">CTA Heading *</label>
                    <input type="text" name="cta_heading" id="cta_heading" class="form-control" value="{{ $cta['heading'] ?? 'Ready to Find Your Dream Property in North Bali?' }}" required oninput="updatePreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="cta_description">CTA Description *</label>
                    <textarea name="cta_description" id="cta_description" class="form-control" style="min-height: 100px; resize: vertical;" required oninput="updatePreview()">{{ $cta['description'] ?? 'Speak directly with our experienced property advisors today.' }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="cta_button_text">Button Text *</label>
                        <input type="text" name="cta_button_text" id="cta_button_text" class="form-control" value="{{ $cta['button_text'] ?? 'Contact Us Today' }}" required oninput="updatePreview()">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cta_button_link">Button Destination Link</label>
                        <input type="text" name="cta_button_link" id="cta_button_link" class="form-control" value="{{ $cta['button_link'] ?? '/contact' }}">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>
        </div>

        <!-- COLUMN 3: Live Preview Panel (Right) -->
        <div class="cms-preview-column">
            <div class="cms-preview-box">
                <div class="cms-preview-header">
                    Homepage Preview
                </div>
                <div class="cms-preview-body">
                    <!-- Preview 1: Hero -->
                    <div id="prev-hero-box" style="background-color: #1E3A8A; color: #FFFFFF; padding: 24px 16px; border-radius: 8px; margin-bottom: 16px; position: relative; overflow: hidden;">
                        <div style="font-size: 11px; color: #93C5FD; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;" id="prev-hero-small-title">{{ $hero['small_title'] ?? 'Find Your Dream' }}</div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;" id="prev-hero-heading">{{ $hero['heading'] ?? '' }}</h3>
                        <p style="font-size: 12px; color: #DBEAFE; line-height: 1.5; margin-bottom: 16px;" id="prev-hero-subheading">{{ $hero['subheading'] ?? '' }}</p>
                        
                        <div style="display: flex; gap: 8px;" id="prev-hero-buttons">
                            @foreach($buttons as $b)
                                <button type="button" class="btn {{ $b['style'] === 'primary' ? 'btn-primary' : 'btn-outline' }}" style="padding: 6px 12px; font-size: 11px;">{{ $b['text'] }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview 2: Search -->
                    <div id="prev-search-box" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 12px;">
                        <div style="font-weight: 700; color: #1E3A8A; margin-bottom: 6px;" id="prev-search-ph">{{ $searchSection['placeholder'] ?? 'Search...' }}</div>
                        <div style="display: flex; gap: 6px;">
                            <span style="background-color: #F1F5F9; padding: 4px 8px; border-radius: 4px;">Type ▼</span>
                            <span style="background-color: #F1F5F9; padding: 4px 8px; border-radius: 4px;">Location ▼</span>
                            <span style="background-color: #1E3A8A; color: white; padding: 4px 8px; border-radius: 4px;">Search</span>
                        </div>
                    </div>

                    <!-- Preview 3: Featured -->
                    <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; padding: 14px; border-radius: 8px; margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: 700; color: #1E3A8A; margin-bottom: 10px;" id="prev-featured-title">{{ $featuredSection['section_title'] ?? 'Featured Properties' }}</div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                            @foreach($featuredProperties->take(3) as $fp)
                                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 6px; font-size: 10px;">
                                    <div style="font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $fp->name }}</div>
                                    <div style="color: #16A34A; font-weight: 600;">${{ number_format($fp->price) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview 5: Categories -->
                    <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; padding: 14px; border-radius: 8px; margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: 700; color: #1E3A8A; margin-bottom: 4px;" id="prev-cat-heading">{{ $categoriesSection['heading'] ?? 'Categories' }}</div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px;">
                            @foreach($categories->take(6) as $cat)
                                <div style="border: 1px solid #E2E8F0; padding: 6px; text-align: center; border-radius: 4px; font-size: 10px; font-weight: 600;">
                                    {{ $cat->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview 9: Contact CTA -->
                    <div id="prev-cta-box" style="background-color: #D6E6F7; text-align: center; padding: 16px; border-radius: 8px;">
                        <h4 style="font-size: 14px; color: #1E3A8A; margin-bottom: 4px;" id="prev-cta-heading">{{ $cta['heading'] ?? '' }}</h4>
                        <p style="font-size: 11px; color: #334155; margin-bottom: 8px;" id="prev-cta-desc">{{ $cta['description'] ?? '' }}</p>
                        <button type="button" class="btn btn-primary" style="padding: 4px 12px; font-size: 11px;" id="prev-cta-btn">{{ $cta['button_text'] ?? 'Contact' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@else
<!-- =========================================================
     TAB 2: ABOUT US (7 SECTIONS) - 3 COLUMN LAYOUT
     ========================================================= -->
<form action="{{ route('admin.cms.about.update') }}" method="POST" enctype="multipart/form-data" id="cms-main-form">
    @csrf

    <div class="cms-3col-layout">
        <!-- COLUMN 1: Section Navigation List (Left) -->
        <div>
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">About Us Sections</div>

            <div id="section-nav-list-about">
                <!-- A. Page Banner -->
                <div class="cms-nav-card active" data-section="sec-ab-banner" onclick="switchSectionAbout('sec-ab-banner')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">A. Page Banner</div>
                            <div style="font-size: 11px; color: #64748B;">Top header banner & breadcrumbs.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #2563EB;">▲</span>
                </div>

                <!-- B. Company Story -->
                <div class="cms-nav-card" data-section="sec-ab-story" onclick="switchSectionAbout('sec-ab-story')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">B. Company Story</div>
                            <div style="font-size: 11px; color: #64748B;">Agency background text & photo.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- C. Vision -->
                <div class="cms-nav-card" data-section="sec-ab-vision" onclick="switchSectionAbout('sec-ab-vision')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">C. Vision</div>
                            <div style="font-size: 11px; color: #64748B;">Agency long-term vision.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- D. Mission -->
                <div class="cms-nav-card" data-section="sec-ab-mission" onclick="switchSectionAbout('sec-ab-mission')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">D. Mission</div>
                            <div style="font-size: 11px; color: #64748B;">Mission statement & key points.</div>
                        </div>
                    </div>
                    <span class="chevron-icon" style="font-size: 12px; color: #64748B;">▼</span>
                </div>

                <!-- E. Why Choose Us (With Status Badge) -->
                <div class="cms-nav-card" data-section="sec-ab-why" onclick="switchSectionAbout('sec-ab-why')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">E. Why Choose Us</div>
                            <div style="font-size: 11px; color: #64748B;">Consistency with homepage.</div>
                        </div>
                    </div>
                    <span style="font-size: 10px; font-weight: 600; background-color: #DCFCE7; color: #166534; padding: 2px 8px; border-radius: 12px;">Using Homepage Content</span>
                </div>

                <!-- F. Company Statistics (With Status Badge) -->
                <div class="cms-nav-card" data-section="sec-ab-stats" onclick="switchSectionAbout('sec-ab-stats')">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="cms-nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg></div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0F172A;">F. Company Statistics</div>
                            <div style="font-size: 11px; color: #64748B;">Reuse homepage stats metrics.</div>
                        </div>
                    </div>
                    <span style="font-size: 10px; font-weight: 600; background-color: #DCFCE7; color: #166534; padding: 2px 8px; border-radius: 12px;">Show Homepage Statistics</span>
                </div>
            </div>
        </div>

        <!-- COLUMN 2: Content Editor (Center) -->
        <div>
            <!-- A. Page Banner -->
            <div class="admin-card cms-ab-section" id="sec-ab-banner" style="display: block;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">A. Page Banner</h3>
                
                <div class="form-group">
                    <label class="form-label" for="banner_title">Page Title *</label>
                    <input type="text" name="banner_title" id="banner_title" class="form-control" value="{{ $aboutBanner['title'] ?? 'About Us' }}" required oninput="updateAboutPreview()">
                </div>

                <div class="form-group">
                    <label class="form-label">Banner Image</label>
                    <input type="file" name="banner_image" id="banner_image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label" for="banner_breadcrumb">Breadcrumb Text (Optional)</label>
                    <input type="text" name="banner_breadcrumb" id="banner_breadcrumb" class="form-control" value="{{ $aboutBanner['breadcrumb'] ?? 'Home / About Us' }}" oninput="updateAboutPreview()">
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- B. Company Story -->
            <div class="admin-card cms-ab-section" id="sec-ab-story" style="display: none;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">B. Company Story</h3>

                <div class="form-group">
                    <label class="form-label" for="story_label">Section Label</label>
                    <input type="text" name="story_label" id="story_label" class="form-control" value="{{ $aboutStory['label'] ?? 'OUR STORY' }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="story_heading">Heading *</label>
                    <input type="text" name="story_heading" id="story_heading" class="form-control" value="{{ $aboutStory['heading'] ?? 'Our Story & Heritage' }}" required oninput="updateAboutPreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="story_description">Company Description *</label>
                    <textarea name="story_description" id="story_description" class="form-control" style="min-height: 140px;" required oninput="updateAboutPreview()">{{ $aboutStory['description'] ?? '' }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- C. Vision -->
            <div class="admin-card cms-ab-section" id="sec-ab-vision" style="display: none;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">C. Vision</h3>

                <div class="form-group">
                    <label class="form-label" for="vision_title">Vision Title *</label>
                    <input type="text" name="vision_title" id="vision_title" class="form-control" value="{{ $aboutVision['title'] ?? 'Our Vision' }}" required oninput="updateAboutPreview()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="vision_description">Vision Description *</label>
                    <textarea name="vision_description" id="vision_description" class="form-control" style="min-height: 100px;" required oninput="updateAboutPreview()">{{ $aboutVision['description'] ?? '' }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- D. Mission -->
            <div class="admin-card cms-ab-section" id="sec-ab-mission" style="display: none;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">D. Mission</h3>

                <div class="form-group">
                    <label class="form-label" for="mission_title">Mission Title *</label>
                    <input type="text" name="mission_title" id="mission_title" class="form-control" value="{{ $aboutMission['title'] ?? 'Our Mission' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mission Points</label>
                    <div id="mission-points-list">
                        @foreach($aboutMission['points'] ?? ['Deliver uncompromised legal integrity.', 'Provide personalized consultation.'] as $pIdx => $point)
                            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" name="mission_points[]" class="form-control" value="{{ $point }}" placeholder="Mission point statement">
                                <button type="button" class="btn btn-outline" style="color: #DC2626; border-color: #FCA5A5;" onclick="this.parentElement.remove()">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline" style="font-size: 13px; margin-top: 8px;" onclick="addMissionPointRow()">+ Add Mission Point</button>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- E. Why Choose Us -->
            <div class="admin-card cms-ab-section" id="sec-ab-why" style="display: none;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">E. Why Choose Us</h3>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; cursor: pointer;">
                        <input type="radio" name="about_why_mode" value="use_homepage" {{ ($aboutWhyChoose['mode'] ?? 'use_homepage') === 'use_homepage' ? 'checked' : '' }}>
                        <span style="font-weight: 600; font-size: 14px;">Use Homepage Content (Recommended for consistency across pages)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="radio" name="about_why_mode" value="custom" {{ ($aboutWhyChoose['mode'] ?? '') === 'custom' ? 'checked' : '' }}>
                        <span style="font-weight: 600; font-size: 14px;">Custom Content for About Us</span>
                    </label>
                </div>

                <div style="background-color: #EFF6FF; border: 1px solid #BFDBFE; padding: 14px; border-radius: 8px; font-size: 13px; color: #1E40AF; margin-bottom: 20px;">
                    ✓ Currently reusing benefit cards from <strong>Homepage CMS &gt; Why Choose Us</strong>.
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>

            <!-- F. Company Statistics -->
            <div class="admin-card cms-ab-section" id="sec-ab-stats" style="display: none;">
                <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 14px;">F. Company Statistics</h3>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 8px;">
                        <input type="checkbox" name="about_show_stats" value="1" {{ ($aboutStats['show_homepage_stats'] ?? true) ? 'checked' : '' }}>
                        <span>Show Homepage Statistics bar on About Us page</span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save Section</button>
                </div>
            </div>
        </div>

        <!-- COLUMN 3: Live Preview & Shared Homepage Card (Right - Matching Reference Image B) -->
        <div class="cms-preview-column">
            <div class="cms-preview-box" style="margin-bottom: 20px;">
                <div class="cms-preview-header">
                    About Us Preview
                </div>
                <div class="cms-preview-body">
                    <!-- Preview Banner -->
                    <div style="background-color: #1E3A8A; color: white; padding: 24px 16px; border-radius: 8px; text-align: center; margin-bottom: 16px;">
                        <h2 style="font-size: 22px; color: white; margin-bottom: 4px;" id="prev-ab-title">{{ $aboutBanner['title'] ?? 'ABOUT US' }}</h2>
                        <div style="font-size: 11px; color: #93C5FD;" id="prev-ab-bc">{{ $aboutBanner['breadcrumb'] ?? 'Home / About Us' }}</div>
                    </div>

                    <!-- Preview Story -->
                    <div style="background-color: white; border: 1px solid #E2E8F0; padding: 14px; border-radius: 8px; margin-bottom: 16px;">
                        <h4 style="font-size: 14px; color: #1E3A8A; margin-bottom: 6px;" id="prev-ab-heading">{{ $aboutStory['heading'] ?? 'Our Story' }}</h4>
                        <p style="font-size: 11px; color: #475569; line-height: 1.5;" id="prev-ab-desc">{{ Str::limit($aboutStory['description'] ?? '', 160) }}</p>
                    </div>

                    <!-- Preview Vision -->
                    <div style="background-color: #F4F1FA; border-left: 4px solid #1E3A8A; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                        <div style="font-weight: 700; font-size: 12px; color: #1E3A8A;" id="prev-ab-vision-title">{{ $aboutVision['title'] ?? 'Our Vision' }}</div>
                        <div style="font-size: 11px; color: #475569;" id="prev-ab-vision-desc">{{ $aboutVision['description'] ?? '' }}</div>
                    </div>
                </div>
            </div>

            <!-- Shared Homepage Content Card (Matching Reference Image B) -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 28px; height: 28px; border-radius: 6px; background-color: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 14px;">ℹ️</div>
                    <div style="font-weight: 700; font-size: 14px; color: #0F172A;">Shared Homepage Content</div>
                </div>
                <p style="font-size: 12px; color: #64748B; margin-bottom: 14px; line-height: 1.5;">
                    The sections below use content from Homepage to keep your website consistent. You can manage the content in <a href="{{ route('admin.cms.index', ['tab' => 'homepage']) }}" style="color: #2563EB; font-weight: 600;">Homepage CMS</a>.
                </p>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('admin.cms.index', ['tab' => 'homepage']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; text-decoration: none; font-size: 13px; color: #334155;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: #16A34A; font-weight: 700;">✓</span>
                            <span style="font-weight: 600;">Why Choose Us</span>
                        </div>
                        <span style="font-size: 11px; color: #64748B;">Using homepage content &rsaquo;</span>
                    </a>

                    <a href="{{ route('admin.cms.index', ['tab' => 'homepage']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; text-decoration: none; font-size: 13px; color: #334155;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: #16A34A; font-weight: 700;">✓</span>
                            <span style="font-weight: 600;">Company Statistics</span>
                        </div>
                        <span style="font-size: 11px; color: #64748B;">Showing homepage statistics &rsaquo;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
@endsection

@section('scripts')
<script>
// Accordion Switcher for Homepage Sections (Column 1 -> Column 2)
function switchSection(sectionId) {
    document.querySelectorAll('.cms-section-editor').forEach(el => el.style.display = 'none');
    const target = document.getElementById(sectionId);
    if (target) target.style.display = 'block';

    document.querySelectorAll('#section-nav-list .cms-nav-card').forEach(card => {
        card.classList.remove('active');
        const chevron = card.querySelector('.chevron-icon');
        if (chevron) {
            chevron.textContent = '▼';
            chevron.style.color = '#64748B';
        }
    });

    const activeCard = document.querySelector(`#section-nav-list [data-section="${sectionId}"]`);
    if (activeCard) {
        activeCard.classList.add('active');
        const chevron = activeCard.querySelector('.chevron-icon');
        if (chevron) {
            chevron.textContent = '▲';
            chevron.style.color = '#2563EB';
        }
    }
}

// Accordion Switcher for About Us Sections
function switchSectionAbout(sectionId) {
    document.querySelectorAll('.cms-ab-section').forEach(el => el.style.display = 'none');
    const target = document.getElementById(sectionId);
    if (target) target.style.display = 'block';

    document.querySelectorAll('#section-nav-list-about .cms-nav-card').forEach(card => {
        card.classList.remove('active');
        const chevron = card.querySelector('.chevron-icon');
        if (chevron) {
            chevron.textContent = '▼';
            chevron.style.color = '#64748B';
        }
    });

    const activeCard = document.querySelector(`#section-nav-list-about [data-section="${sectionId}"]`);
    if (activeCard) {
        activeCard.classList.add('active');
        const chevron = activeCard.querySelector('.chevron-icon');
        if (chevron) {
            chevron.textContent = '▲';
            chevron.style.color = '#2563EB';
        }
    }
}

// Dynamic Hero Button Adder
function addHeroButtonRow() {
    const container = document.getElementById('hero-buttons-container');
    if (!container) return;
    const div = document.createElement('div');
    div.style.display = 'grid';
    div.style.gridTemplateColumns = '2fr 2fr 1.5fr auto';
    div.style.gap = '10px';
    div.style.marginBottom = '10px';
    div.style.alignItems = 'center';
    div.className = 'hero-btn-row';
    div.innerHTML = `
        <input type="text" name="buttons_text[]" class="form-control btn-txt-input" placeholder="Button Label" oninput="updatePreview()">
        <input type="text" name="buttons_link[]" class="form-control" placeholder="/contact">
        <select name="buttons_style[]" class="form-select">
            <option value="primary">Primary</option>
            <option value="outline">Outline</option>
        </select>
        <button type="button" class="btn btn-outline" style="padding: 8px 12px; color: #DC2626; border-color: #FCA5A5;" onclick="this.parentElement.remove(); updatePreview();">Remove</button>
    `;
    container.appendChild(div);
}

// Dynamic Mission Point Adder
function addMissionPointRow() {
    const container = document.getElementById('mission-points-list');
    if (!container) return;
    const div = document.createElement('div');
    div.style.display = 'flex';
    div.style.gap = '8px';
    div.style.marginBottom = '8px';
    div.innerHTML = `
        <input type="text" name="mission_points[]" class="form-control" placeholder="Mission point statement">
        <button type="button" class="btn btn-outline" style="color: #DC2626; border-color: #FCA5A5;" onclick="this.parentElement.remove()">Remove</button>
    `;
    container.appendChild(div);
}

// Real-Time Live Preview Update for Homepage
function updatePreview() {
    // 1. Hero
    const smallTitle = document.getElementById('hero_small_title');
    const heading = document.getElementById('hero_heading');
    const subheading = document.getElementById('hero_subheading');

    if (smallTitle && document.getElementById('prev-hero-small-title')) {
        document.getElementById('prev-hero-small-title').textContent = smallTitle.value;
    }
    if (heading && document.getElementById('prev-hero-heading')) {
        document.getElementById('prev-hero-heading').textContent = heading.value;
    }
    if (subheading && document.getElementById('prev-hero-subheading')) {
        document.getElementById('prev-hero-subheading').textContent = subheading.value;
    }

    // 2. Search
    const searchPh = document.getElementById('search_placeholder');
    if (searchPh && document.getElementById('prev-search-ph')) {
        document.getElementById('prev-search-ph').textContent = searchPh.value;
    }

    // 3. Featured Title
    const featTitle = document.getElementById('featured_title');
    if (featTitle && document.getElementById('prev-featured-title')) {
        document.getElementById('prev-featured-title').textContent = featTitle.value;
    }

    // 5. Categories
    const catHeading = document.getElementById('categories_heading');
    if (catHeading && document.getElementById('prev-cat-heading')) {
        document.getElementById('prev-cat-heading').textContent = catHeading.value;
    }

    // 9. CTA
    const ctaHead = document.getElementById('cta_heading');
    const ctaDesc = document.getElementById('cta_description');
    const ctaBtn = document.getElementById('cta_button_text');

    if (ctaHead && document.getElementById('prev-cta-heading')) {
        document.getElementById('prev-cta-heading').textContent = ctaHead.value;
    }
    if (ctaDesc && document.getElementById('prev-cta-desc')) {
        document.getElementById('prev-cta-desc').textContent = ctaDesc.value;
    }
    if (ctaBtn && document.getElementById('prev-cta-btn')) {
        document.getElementById('prev-cta-btn').textContent = ctaBtn.value;
    }
}

// Real-Time Live Preview Update for About Us
function updateAboutPreview() {
    const bannerTitle = document.getElementById('banner_title');
    const bannerBc = document.getElementById('banner_breadcrumb');
    const storyHead = document.getElementById('story_heading');
    const storyDesc = document.getElementById('story_description');
    const visionTitle = document.getElementById('vision_title');
    const visionDesc = document.getElementById('vision_description');

    if (bannerTitle && document.getElementById('prev-ab-title')) {
        document.getElementById('prev-ab-title').textContent = bannerTitle.value;
    }
    if (bannerBc && document.getElementById('prev-ab-bc')) {
        document.getElementById('prev-ab-bc').textContent = bannerBc.value;
    }
    if (storyHead && document.getElementById('prev-ab-heading')) {
        document.getElementById('prev-ab-heading').textContent = storyHead.value;
    }
    if (storyDesc && document.getElementById('prev-ab-desc')) {
        document.getElementById('prev-ab-desc').textContent = storyDesc.value;
    }
    if (visionTitle && document.getElementById('prev-ab-vision-title')) {
        document.getElementById('prev-ab-vision-title').textContent = visionTitle.value;
    }
    if (visionDesc && document.getElementById('prev-ab-vision-desc')) {
        document.getElementById('prev-ab-vision-desc').textContent = visionDesc.value;
    }
}
</script>
@endsection
