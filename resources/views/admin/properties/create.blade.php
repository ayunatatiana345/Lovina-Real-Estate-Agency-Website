@extends('layouts.admin')

@section('title', 'Add New Property')
@section('page_title', 'Create Property Listing')

@section('content')
<form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data" id="property-form">
    @csrf

    <!-- Header Area -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Add New Property</h2>
            <p style="font-size: 14px; color: #64748B;">Create a new property listing with specifications, multi-photo gallery, and live preview.</p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; background-color: #1E3A8A; border-color: #1E3A8A; font-weight: 600;">
                Save & Publish Property
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="cms-tab-bar" style="margin-bottom: 24px;">
        <a href="#" class="cms-tab-item active" id="tab-link-general" onclick="switchPropertyTab(event, 'general')">
            General Information
        </a>
        <a href="#" class="cms-tab-item" id="tab-link-specs" onclick="switchPropertyTab(event, 'specs')">
            Specifications
        </a>
        <a href="#" class="cms-tab-item" id="tab-link-gallery" onclick="switchPropertyTab(event, 'gallery')">
            Gallery (<span id="gallery-count-badge">0</span>)
        </a>
        <a href="#" class="cms-tab-item" id="tab-link-preview" onclick="switchPropertyTab(event, 'preview')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            Live Preview
        </a>
    </div>

    <!-- TAB 1: General Information -->
    <div class="property-tab-content" id="property-tab-general" style="display: block;">
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">1. General Information</h3>

            <div class="form-grid-2-1" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="name">Property Name *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Azure Vista Oceanfront Residence" value="{{ old('name') }}" style="width: 100%;" required oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="slug">URL Slug (Optional)</label>
                    <input type="text" name="slug" id="slug" class="form-control" placeholder="leave blank to auto-generate" value="{{ old('slug') }}" style="width: 100%;">
                    <small style="color: #64748B; font-size: 12px;">Auto-generated from property name if left empty.</small>
                </div>
            </div>

            <div class="form-grid-2" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="category_id">Primary Category *</label>
                    <select name="category_id" id="category_id" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="">Select Category...</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <div style="margin-top: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Assigned Categories (Multi-Category Support):</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach($categories as $c)
                                @php
                                    $assigned = (is_array(old('category_ids')) && in_array($c->id, old('category_ids'))) || (old('category_id') == $c->id);
                                @endphp
                                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; cursor: pointer; background: #F8FAFC; padding: 4px 10px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                    <input type="checkbox" name="category_ids[]" value="{{ $c->id }}" {{ $assigned ? 'checked' : '' }}>
                                    <span>{{ $c->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="location_id">Location in North Bali *</label>
                    <select name="location_id" id="location_id" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="">Select Location...</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" data-name="{{ $l->name }}" {{ old('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid-3" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price">Price (IDR)</label>
                    <input type="number" step="1" name="price" id="price" class="form-control" placeholder="Leave blank for Price on Request" value="{{ old('price') }}" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 12px;">Base IDR currency. Leave blank for "Price on Request".</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="ownership_type">Ownership Title *</label>
                    <select name="ownership_type" id="ownership_type" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="Freehold" {{ old('ownership_type') == 'Freehold' ? 'selected' : '' }}>Freehold (SHM)</option>
                        <option value="Leasehold" {{ old('ownership_type') == 'Leasehold' ? 'selected' : '' }}>Leasehold (Hak Sewa)</option>
                        <option value="Hak Pakai" {{ old('ownership_type') == 'Hak Pakai' ? 'selected' : '' }}>Hak Pakai</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Publication Status *</label>
                    <select name="status" id="status" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Visible to Public)</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Hidden from Public)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="star" style="width: 16px; height: 16px; color: #C7A86D; fill: #C7A86D;"></i> Mark as Featured Property (Featured Homepage Section)</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="short_description">Short Description / Summary (Optional)</label>
                <textarea name="short_description" id="short_description" class="form-control" style="min-height: 80px; width: 100%;" placeholder="Concise property summary for search cards and meta descriptions...">{{ old('short_description') }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="description">About This Property (Detailed Description)</label>
                <div class="editor-toolbar">
                    <button type="button" class="editor-btn" onclick="formatDoc('bold')">B</button>
                    <button type="button" class="editor-btn" style="font-style: italic;" onclick="formatDoc('italic')">I</button>
                    <button type="button" class="editor-btn" style="text-decoration: underline;" onclick="formatDoc('underline')">U</button>
                    <button type="button" class="editor-btn" onclick="formatDoc('insertUnorderedList')">• List</button>
                </div>
                <textarea name="description" id="description" class="form-control editor-textarea" style="min-height: 180px; width: 100%; resize: vertical; border-top: none;" placeholder="Full overview of the property..." oninput="updateLivePreview()">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <!-- TAB 2: Specifications -->
    <div class="property-tab-content" id="property-tab-specs" style="display: none;">
        <div class="admin-card" style="padding: 28px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">2. Key Information</h3>

            <div class="form-grid-4" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="bedrooms">Bedrooms</label>
                    <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', 3) }}" placeholder="e.g. 3" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty if not applicable (e.g. Land).</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="bathrooms">Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', 2) }}" placeholder="e.g. 2" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty if not applicable.</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="land_size">Land Size (m²)</label>
                    <input type="number" name="land_size" id="land_size" class="form-control" value="{{ old('land_size', 400) }}" placeholder="e.g. 400" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="building_size">Building Size (m²)</label>
                    <input type="number" name="building_size" id="building_size" class="form-control" value="{{ old('building_size', 250) }}" placeholder="e.g. 250" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty for land plots.</small>
                </div>
            </div>

            <div class="form-grid-3" style="margin-bottom: 24px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="garage">Garage (Cars)</label>
                    <input type="number" name="garage" id="garage" class="form-control" value="{{ old('garage', 2) }}" placeholder="e.g. 2" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="electricity">Electricity</label>
                    <input type="text" name="electricity" id="electricity" class="form-control" value="{{ old('electricity', '5500 VA') }}" placeholder="e.g. 5500 VA / 7700 VA" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="water_supply">Water Supply</label>
                    <input type="text" name="water_supply" id="water_supply" class="form-control" value="{{ old('water_supply', 'PDAM') }}" placeholder="e.g. PDAM / Deep Well" style="width: 100%;" oninput="updateLivePreview()">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="furnishing">Furnishing Status</label>
                    <select name="furnishing" id="furnishing" class="form-select" style="width: 100%;" onchange="updateLivePreview()">
                        <option value="Fully Furnished" {{ old('furnishing', 'Fully Furnished') == 'Fully Furnished' ? 'selected' : '' }}>Fully Furnished</option>
                        <option value="Semi-Furnished" {{ old('furnishing') == 'Semi-Furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                        <option value="Unfurnished" {{ old('furnishing') == 'Unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="air_conditioning">Air Conditioning</label>
                    <input type="text" name="air_conditioning" id="air_conditioning" class="form-control" value="{{ old('air_conditioning', 'Yes') }}" placeholder="e.g. Yes / 4 Inverter Units" style="width: 100%;" oninput="updateLivePreview()">
                </div>
            </div>
        </div>

        <!-- Property Features (Checkboxes) -->
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 12px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">3. Property Features (Displayed on Public Page)</h3>
            <p style="font-size: 13px; color: #64748B; margin-bottom: 20px;">Only selected features will be displayed on the Public Property detail page.</p>

            <div class="form-grid-3" style="gap: 16px;">
                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="swimming_pool" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🏊 Swimming Pool</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="garden" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🌿 Tropical Garden</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="furnishing" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🛋️ Furnished</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="air_conditioning" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">❄️ Air Conditioning</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="water_supply" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">💧 Water Supply</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="garage" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🚗 Garage / Parking</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="internet" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">📶 High-Speed Internet</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="security" checked onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🛡️ Secure Environment</span>
                </label>
            </div>
        </div>
    </div>

    <!-- TAB 3: Gallery -->
    <div class="property-tab-content" id="property-tab-gallery" style="display: none;">
        <div class="admin-card" style="padding: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">4. Image Gallery Management</h3>
                <span style="font-size: 13px; color: #64748B;">Supports 20+ photos in any order. Click "Set as Cover" on any photo to designate as the primary cover photo.</span>
            </div>

            <!-- Upload Area with Multi-Batch Queuing -->
            <input type="hidden" name="cover_index" id="cover_index" value="">
            <label class="gallery-upload-zone" for="images" style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 36px 20px; text-align: center; display: block; cursor: pointer; background-color: #F8FAFC; margin-bottom: 28px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="margin-bottom: 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <div style="font-size: 15px; font-weight: 600; color: #0F172A; margin-bottom: 4px;">Click to select photos or drag and drop</div>
                <div style="font-size: 12px; color: #64748B;">Select multiple files at once or add more files in batches (JPG, PNG, WebP up to 10MB each).</div>
                <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;" onchange="handleGallerySelect(this)">
            </label>

            <!-- Gallery Images Grid -->
            <div class="gallery-grid" id="gallery-preview-grid">
                <!-- Dynamically populated via local JS DataTransfer queue -->
            </div>
        </div>
    </div>

    <!-- TAB 4: Live Preview -->
    <div class="property-tab-content" id="property-tab-preview" style="display: none;">
        <div class="admin-card" style="padding: 28px; background-color: #F8FAFC; border: 1px solid #E2E8F0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #CBD5E1; padding-bottom: 12px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin: 0;">Public Page Preview</h3>
                    <p style="font-size: 13px; color: #64748B; margin: 2px 0 0 0;">High-fidelity representation of how this new property will render to visitors on the Public Property Detail page.</p>
                </div>
                <span style="font-size: 12px; background-color: #1E3A8A; color: #FFFFFF; padding: 4px 10px; border-radius: 4px; font-weight: 600;">Real-time Preview</span>
            </div>

            <!-- Preview Card Container -->
            <div style="background-color: #FFFFFF; border-radius: 8px; border: 1px solid #E2E8F0; padding: 32px; max-width: 1000px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                
                <!-- Breadcrumb Preview -->
                <div style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                    Home &gt; Properties &gt; <span id="prev-category-crumb">Villa</span> &gt; <span id="prev-title-crumb" style="font-weight: 600; color: #0F172A;">Property Name</span>
                </div>

                <!-- Title & Meta Bar -->
                <div style="margin-bottom: 24px;">
                    <h1 id="prev-title" style="font-size: 28px; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Property Title</h1>
                    <div style="display: flex; gap: 16px; align-items: center; font-size: 14px; color: #64748B;">
                        <span>📍 <span id="prev-location">Lovina, North Bali</span></span>
                        <span>🏠 <span id="prev-category">Villa</span></span>
                        <span id="prev-featured-badge" style="display: none; background-color: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">★ Featured</span>
                    </div>
                </div>

                <!-- Image Gallery Hero Preview -->
                <div style="height: 360px; border-radius: 8px; overflow: hidden; background-color: #F1F5F9; margin-bottom: 24px; position: relative;">
                    <img id="prev-cover-img" src="" alt="Property Cover" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div style="position: absolute; bottom: 12px; right: 12px; background-color: rgba(0,0,0,0.75); color: #FFFFFF; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                        📷 <span id="prev-photo-count">0</span> Photos
                    </div>
                </div>

                <!-- Price & Details Grid -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 32px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">About This Property</h3>
                        <div id="prev-description" style="font-size: 15px; line-height: 1.7; color: #334155; white-space: pre-line; margin-bottom: 24px;">
                            Detailed property description will appear here...
                        </div>

                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">Property Features</h3>
                        <div id="prev-features-grid" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px;">
                            <!-- Dynamically updated by JS -->
                        </div>
                    </div>

                    <!-- Right Sticky Preview Card -->
                    <div>
                        <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                            <div style="font-size: 12px; color: #64748B; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Price</div>
                            <div id="prev-price-idr" style="font-size: 24px; font-weight: 700; color: #15803D; margin-bottom: 4px;">
                                Rp 0
                            </div>
                            <div id="prev-price-usd" style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                                ≈ USD $0
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button type="button" class="btn btn-primary" style="width: 100%; pointer-events: none;">Contact Us</button>
                                <button type="button" class="btn btn-outline" style="width: 100%; pointer-events: none; color: #16A34A; border-color: #86EFAC;">WhatsApp Us</button>
                            </div>
                        </div>

                        <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">Key Information</h4>
                            <div style="font-size: 13px; display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between;"><span>Bedrooms:</span><strong id="prev-beds">3</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Bathrooms:</span><strong id="prev-baths">2</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Land Size:</span><strong id="prev-land">400 m²</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Building Size:</span><strong id="prev-building">250 m²</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Garage:</span><strong id="prev-garage">2 Cars</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Ownership:</span><strong id="prev-ownership">Freehold</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Status:</span><strong id="prev-status">Published</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bottom Actions footer -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 12px 28px;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; background-color: #1E3A8A; border-color: #1E3A8A; font-weight: 600;">Save & Publish Property</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
// Tab Switching
function switchPropertyTab(e, tabName) {
    if (e) e.preventDefault();
    
    document.querySelectorAll('.cms-tab-bar .cms-tab-item').forEach(item => item.classList.remove('active'));
    const link = document.getElementById(`tab-link-${tabName}`);
    if (link) link.classList.add('active');

    document.querySelectorAll('.property-tab-content').forEach(content => content.style.display = 'none');
    const activeContent = document.getElementById(`property-tab-${tabName}`);
    if (activeContent) activeContent.style.display = 'block';

    if (tabName === 'preview') {
        updateLivePreview();
    }
}

// Text Formatter
function formatDoc(cmd, val = null) {
    const textarea = document.getElementById('description');
    if (!textarea) return;
    
    let text = textarea.value;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = text.substring(start, end);
    
    let replacement = '';
    if (cmd === 'bold') replacement = `**${selectedText}**`;
    else if (cmd === 'italic') replacement = `*${selectedText}*`;
    else if (cmd === 'underline') replacement = `_${selectedText}_`;
    else if (cmd === 'insertUnorderedList') replacement = `\n- ${selectedText}`;

    textarea.value = text.substring(0, start) + replacement + text.substring(end);
    textarea.focus();
    updateLivePreview();
}

// DataTransfer Queue for multi-batch photo uploading (supports 20+ photos in any order)
const galleryDT = new DataTransfer();

function handleGallerySelect(input) {
    const grid = document.getElementById('gallery-preview-grid');
    if (!grid || !input.files) return;

    for (let i = 0; i < input.files.length; i++) {
        galleryDT.items.add(input.files[i]);
    }
    input.files = galleryDT.files;
    renderGalleryPreview();
}

function removeGalleryFile(index) {
    const newDT = new DataTransfer();
    for (let i = 0; i < galleryDT.files.length; i++) {
        if (i !== index) {
            newDT.items.add(galleryDT.files[i]);
        }
    }
    galleryDT.items.clear();
    for (let i = 0; i < newDT.files.length; i++) {
        galleryDT.items.add(newDT.files[i]);
    }
    const input = document.getElementById('images');
    if (input) input.files = galleryDT.files;
    renderGalleryPreview();
}

function setCoverIndex(index) {
    const coverInput = document.getElementById('cover_index');
    if (coverInput) coverInput.value = index;
    renderGalleryPreview();
}

function renderGalleryPreview() {
    const grid = document.getElementById('gallery-preview-grid');
    if (!grid) return;
    grid.innerHTML = '';
    const coverVal = document.getElementById('cover_index')?.value;
    const hasExplicitCover = (coverVal !== '' && coverVal !== undefined && coverVal !== null && !isNaN(parseInt(coverVal)));
    const currentCover = hasExplicitCover ? parseInt(coverVal) : null;

    const countBadge = document.getElementById('gallery-count-badge');
    if (countBadge) countBadge.innerText = galleryDT.files.length;
    const prevCount = document.getElementById('prev-photo-count');
    if (prevCount) prevCount.innerText = galleryDT.files.length;

    for (let i = 0; i < galleryDT.files.length; i++) {
        const file = galleryDT.files[i];
        const isCover = (currentCover !== null && i === currentCover);
        const cardId = 'new-gallery-card-' + i;
        const card = document.createElement('div');
        card.className = 'gallery-card';
        card.id = cardId;
        card.style.position = 'relative';
        card.style.border = '1px solid #E2E8F0';
        card.style.borderRadius = '8px';
        card.style.overflow = 'hidden';
        card.style.backgroundColor = '#FFFFFF';

        const reader = new FileReader();
        reader.onload = function(e) {
            card.innerHTML = `
                <div class="gallery-card-img-wrap" style="height: 140px; overflow: hidden; position: relative;">
                    <img src="${e.target.result}" alt="Preview Image" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                ${isCover ? '<span style="position: absolute; top: 8px; left: 8px; background-color: #C7A86D; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">★ Cover</span>' : ''}
                <button type="button" onclick="removeGalleryFile(${i})" style="position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.9); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; line-height: 1;" title="Remove image">&times;</button>
                <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; background-color: #F8FAFC; border-top: 1px solid #E2E8F0;">
                    ${isCover ? '<span style="font-size: 11px; color: #166534; font-weight: 700;">✓ Main Cover</span>' : `<button type="button" onclick="setCoverIndex(${i})" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as Cover</button>`}
                    <span style="font-size: 10px; color: #64748B;">${(file.size / 1024).toFixed(0)} KB</span>
                </div>
            `;

            if (isCover) {
                const prevCoverImg = document.getElementById('prev-cover-img');
                if (prevCoverImg) prevCoverImg.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
        grid.appendChild(card);
    }
}

// Live Preview Updater
function updateLivePreview() {
    const name = document.getElementById('name')?.value || 'Property Title';
    const desc = document.getElementById('description')?.value || 'Property description overview...';
    const price = document.getElementById('price')?.value || 0;
    const catSelect = document.getElementById('category_id');
    const catName = catSelect ? catSelect.options[catSelect.selectedIndex]?.getAttribute('data-name') || catSelect.options[catSelect.selectedIndex]?.text : 'Category';
    const locSelect = document.getElementById('location_id');
    const locName = locSelect ? locSelect.options[locSelect.selectedIndex]?.getAttribute('data-name') || locSelect.options[locSelect.selectedIndex]?.text : 'Location';
    const isFeatured = document.getElementById('is_featured')?.checked;
    const beds = document.getElementById('bedrooms')?.value || '-';
    const baths = document.getElementById('bathrooms')?.value || '-';
    const land = document.getElementById('land_size')?.value;
    const building = document.getElementById('building_size')?.value;
    const garage = document.getElementById('garage')?.value;
    const ownership = document.getElementById('ownership_type')?.value || 'Freehold';
    const status = document.getElementById('status')?.value || 'published';

    const prevTitle = document.getElementById('prev-title');
    if (prevTitle) prevTitle.innerText = name;
    const prevTitleCrumb = document.getElementById('prev-title-crumb');
    if (prevTitleCrumb) prevTitleCrumb.innerText = name;
    const prevCatCrumb = document.getElementById('prev-category-crumb');
    if (prevCatCrumb) prevCatCrumb.innerText = catName;
    const prevCat = document.getElementById('prev-category');
    if (prevCat) prevCat.innerText = catName;
    const prevLoc = document.getElementById('prev-location');
    if (prevLoc) prevLoc.innerText = (locName !== 'Select Location...' ? locName : 'Lovina') + ', North Bali';
    const prevDesc = document.getElementById('prev-description');
    if (prevDesc) prevDesc.innerText = desc;
    const prevFeatured = document.getElementById('prev-featured-badge');
    if (prevFeatured) prevFeatured.style.display = isFeatured ? 'inline-block' : 'none';

    const prevPriceIdr = document.getElementById('prev-price-idr');
    if (prevPriceIdr) {
        const numPrice = parseFloat(price) || 0;
        prevPriceIdr.innerText = 'Rp ' + numPrice.toLocaleString('id-ID');
    }

    const prevBeds = document.getElementById('prev-beds');
    if (prevBeds) prevBeds.innerText = beds;
    const prevBaths = document.getElementById('prev-baths');
    if (prevBaths) prevBaths.innerText = baths;
    const prevLand = document.getElementById('prev-land');
    if (prevLand) prevLand.innerText = land ? land + ' m²' : '-';
    const prevBuilding = document.getElementById('prev-building');
    if (prevBuilding) prevBuilding.innerText = building ? building + ' m²' : '-';
    const prevGarage = document.getElementById('prev-garage');
    if (prevGarage) prevGarage.innerText = garage ? garage + ' Cars' : '-';
    const prevOwnership = document.getElementById('prev-ownership');
    if (prevOwnership) prevOwnership.innerText = ownership;
    const prevStatus = document.getElementById('prev-status');
    if (prevStatus) prevStatus.innerText = status.charAt(0).toUpperCase() + status.slice(1);

    const featContainer = document.getElementById('prev-features-grid');
    if (featContainer) {
        featContainer.innerHTML = '';
        const checkboxes = document.querySelectorAll('input[name="features[]"]:checked');
        const featureLabels = {
            'swimming_pool': '🏊 Swimming Pool',
            'garden': '🌿 Garden',
            'furnishing': '🛋️ Fully Furnished',
            'air_conditioning': '❄️ Air Conditioning',
            'water_supply': '💧 Water Supply',
            'garage': '🚗 Garage',
            'internet': '📶 High-Speed Internet',
            'security': '🛡️ Secure Environment'
        };
        checkboxes.forEach(cb => {
            const label = featureLabels[cb.value] || cb.value;
            const chip = document.createElement('span');
            chip.style.cssText = 'background-color: #F1F5F9; color: #1E293B; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid #CBD5E1;';
            chip.innerText = label;
            featContainer.appendChild(chip);
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview();
});
</script>
@endsection
