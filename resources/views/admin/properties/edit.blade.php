@extends('layouts.admin')

@section('title', 'Edit Property')
@section('page_title', 'Edit Property: ' . $property->name)

@section('content')
<form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" id="property-form">
    @csrf
    @method('PUT')

    <!-- Header Area -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Edit Property</h2>
            <p style="font-size: 14px; color: #64748B;">Manage property details, specifications, gallery photos, and live preview.</p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('properties.show', $property->slug) }}" target="_blank" class="btn btn-outline" style="padding: 10px 16px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                View on Public Site
            </a>
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; background-color: #1E3A8A; border-color: #1E3A8A; font-weight: 600;">
                Update Property
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
            Gallery (<span id="gallery-count-badge">{{ $property->images->count() }}</span>)
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
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $property->name) }}" style="width: 100%;" required oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="slug">URL Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $property->slug) }}" style="width: 100%;" placeholder="leave blank to auto-generate">
                    <small style="color: #64748B; font-size: 12px;">Used in public web address.</small>
                </div>
            </div>

            <div class="form-grid-2" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="category_id">Primary Category *</label>
                    <select name="category_id" id="category_id" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ $property->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <div style="margin-top: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Assigned Categories (Multi-Category Support):</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach($categories as $c)
                                @php
                                    $assigned = $property->categories->contains('id', $c->id) || $property->category_id == $c->id;
                                    if (is_array(old('category_ids'))) {
                                        $assigned = in_array($c->id, old('category_ids'));
                                    }
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
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" data-name="{{ $l->name }}" {{ $property->location_id == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid-3" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price">Price (IDR)</label>
                    <input type="number" step="1" name="price" id="price" class="form-control" value="{{ old('price', $property->price !== null ? (int)$property->price : '') }}" style="width: 100%;" placeholder="Leave blank for Price on Request" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 12px;">Base IDR currency. Leave blank for "Price on Request".</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="ownership_type">Ownership Title *</label>
                    <select name="ownership_type" id="ownership_type" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="Freehold" {{ $property->ownership_type == 'Freehold' ? 'selected' : '' }}>Freehold (SHM)</option>
                        <option value="Leasehold" {{ $property->ownership_type == 'Leasehold' ? 'selected' : '' }}>Leasehold (Hak Sewa)</option>
                        <option value="Hak Pakai" {{ $property->ownership_type == 'Hak Pakai' ? 'selected' : '' }}>Hak Pakai</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Publication Status *</label>
                    <select name="status" id="status" class="form-select" style="width: 100%;" required onchange="updateLivePreview()">
                        <option value="published" {{ $property->status == 'published' ? 'selected' : '' }}>Published (Visible to Public)</option>
                        <option value="draft" {{ $property->status == 'draft' ? 'selected' : '' }}>Draft (Hidden from Public)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ $property->is_featured ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="star" style="width: 16px; height: 16px; color: #C7A86D; fill: #C7A86D;"></i> Mark as Featured Property (Featured Homepage Section)</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="short_description">Short Description / Summary (Optional)</label>
                <textarea name="short_description" id="short_description" class="form-control" style="min-height: 80px; width: 100%;" placeholder="Concise property summary for search cards and meta descriptions...">{{ old('short_description', $property->short_description) }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="description">About This Property (Detailed Description)</label>
                <div class="editor-toolbar">
                    <button type="button" class="editor-btn" onclick="formatDoc('bold')">B</button>
                    <button type="button" class="editor-btn" style="font-style: italic;" onclick="formatDoc('italic')">I</button>
                    <button type="button" class="editor-btn" style="text-decoration: underline;" onclick="formatDoc('underline')">U</button>
                    <button type="button" class="editor-btn" onclick="formatDoc('insertUnorderedList')">• List</button>
                </div>
                <textarea name="description" id="description" class="form-control editor-textarea" style="min-height: 180px; width: 100%; resize: vertical; border-top: none;" placeholder="Full overview of the property..." oninput="updateLivePreview()">{{ old('description', $property->description) }}</textarea>
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
                    <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms) }}" placeholder="e.g. 3" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty if not applicable (e.g. Land).</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="bathrooms">Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', $property->bathrooms) }}" placeholder="e.g. 2" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty if not applicable.</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="land_size">Land Size (m²)</label>
                    <input type="number" name="land_size" id="land_size" class="form-control" value="{{ old('land_size', $property->land_size) }}" placeholder="e.g. 500" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="building_size">Building Size (m²)</label>
                    <input type="number" name="building_size" id="building_size" class="form-control" value="{{ old('building_size', $property->building_size) }}" placeholder="e.g. 250" style="width: 100%;" oninput="updateLivePreview()">
                    <small style="color: #64748B; font-size: 11px;">Leave empty for land plots.</small>
                </div>
            </div>

            <div class="form-grid-3" style="margin-bottom: 24px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="garage">Garage (Cars)</label>
                    <input type="number" name="garage" id="garage" class="form-control" value="{{ old('garage', $property->garage) }}" placeholder="e.g. 2" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="electricity">Electricity</label>
                    <input type="text" name="electricity" id="electricity" class="form-control" value="{{ old('electricity', $property->electricity) }}" placeholder="e.g. 5500 VA / 7700 VA" style="width: 100%;" oninput="updateLivePreview()">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="water_supply">Water Supply</label>
                    <input type="text" name="water_supply" id="water_supply" class="form-control" value="{{ old('water_supply', $property->water_supply) }}" placeholder="e.g. PDAM / Deep Well (Sumur Bor)" style="width: 100%;" oninput="updateLivePreview()">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="furnishing">Furnishing Status</label>
                    <select name="furnishing" id="furnishing" class="form-select" style="width: 100%;" onchange="updateLivePreview()">
                        <option value="Fully Furnished" {{ old('furnishing', $property->furnishing ?? 'Fully Furnished') == 'Fully Furnished' ? 'selected' : '' }}>Fully Furnished</option>
                        <option value="Semi-Furnished" {{ old('furnishing', $property->furnishing) == 'Semi-Furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                        <option value="Unfurnished" {{ old('furnishing', $property->furnishing) == 'Unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="air_conditioning">Air Conditioning</label>
                    <input type="text" name="air_conditioning" id="air_conditioning" class="form-control" value="{{ old('air_conditioning', $property->air_conditioning ?? 'Yes') }}" placeholder="e.g. Yes / 4 Inverter Units" style="width: 100%;" oninput="updateLivePreview()">
                </div>
            </div>
        </div>

        <!-- Property Features (Checkboxes) -->
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 12px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">3. Property Features (Displayed on Public Page)</h3>
            <p style="font-size: 13px; color: #64748B; margin-bottom: 20px;">Only selected features will be displayed on the Public Property detail page.</p>

            <div class="form-grid-3" style="gap: 16px;">
                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="swimming_pool" {{ $property->hasFeature('swimming_pool') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🏊 Swimming Pool</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="garden" {{ $property->hasFeature('garden') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🌿 Tropical Garden</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="furnishing" {{ $property->hasFeature('furnishing') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🛋️ Furnished</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="air_conditioning" {{ $property->hasFeature('air_conditioning') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">❄️ Air Conditioning</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="water_supply" {{ $property->hasFeature('water_supply') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">💧 Water Supply</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="garage" {{ $property->hasFeature('garage') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">🚗 Garage / Parking</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="internet" {{ $property->hasFeature('internet') ? 'checked' : '' }} onchange="updateLivePreview()">
                    <span style="font-size: 14px; font-weight: 500; color: #1E293B;">📶 High-Speed Internet</span>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; cursor: pointer;">
                    <input type="checkbox" name="features[]" value="security" {{ $property->hasFeature('security') ? 'checked' : '' }} onchange="updateLivePreview()">
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
                <span style="font-size: 13px; color: #64748B;">Supports 20+ photos in any order. Click "Set as Cover" to designate the primary highlight photo.</span>
            </div>

            <!-- Upload Area with Multi-Batch Queuing -->
            @php
                $covers = $property->images->where('is_cover', true);
                $activeCoverId = $covers->sortByDesc('sort_order')->first()?->id;
            @endphp
            <input type="hidden" name="existing_cover_id" id="existing_cover_id" value="{{ $activeCoverId ?: '' }}">
            <input type="hidden" name="new_cover_index" id="new_cover_index" value="">
            <label class="gallery-upload-zone" for="images" style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 36px 20px; text-align: center; display: block; cursor: pointer; background-color: #F8FAFC; margin-bottom: 28px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="margin-bottom: 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <div style="font-size: 15px; font-weight: 600; color: #0F172A; margin-bottom: 4px;">Click to select photos or drag and drop</div>
                <div style="font-size: 12px; color: #64748B;">Select multiple files at once or in batches (JPG, PNG, WebP up to 10MB each).</div>
                <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;" onchange="handleEditGallerySelect(this)">
            </label>

            <!-- Pending New Uploads -->
            <div id="pending-uploads-container" style="display: none; margin-bottom: 32px; background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px; padding: 20px;">
                <h4 style="font-size: 14px; font-weight: 700; color: #1E3A8A; margin-bottom: 12px;">New Photos Selected (Ready to Save with "Update Property"):</h4>
                <div class="gallery-grid" id="new-pending-grid"></div>
            </div>

            <!-- Existing Saved Gallery Images -->
            <h4 style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 14px;">Existing Property Photos ({{ $property->images->count() }}):</h4>
            <div class="gallery-grid" id="gallery-preview-grid">
                @forelse($property->images as $img)
                    @php
                        $isThisCover = ($activeCoverId !== null && $img->id === $activeCoverId);
                    @endphp
                    <div class="gallery-card" id="gallery-card-{{ $img->id }}" style="position: relative; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; background-color: #FFFFFF;">
                        <div class="gallery-card-img-wrap" style="height: 140px; overflow: hidden; position: relative;">
                            <img src="{{ $img->image_url }}" alt="{{ $img->image_alt ?: 'Property Image' }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.style.display='none';">
                        </div>
                        
                        <span class="cover-badge" id="cover-badge-{{ $img->id }}" style="display: {{ $isThisCover ? 'inline-block' : 'none' }}; position: absolute; top: 8px; left: 8px; background-color: #C7A86D; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">★ Cover</span>

                        <button type="button" onclick="deletePropertyImage({{ $img->id }})" style="position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.9); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; line-height: 1;" title="Delete image">&times;</button>

                        <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; background-color: #F8FAFC; border-top: 1px solid #E2E8F0;">
                            <div class="cover-action-wrap" id="cover-action-wrap-{{ $img->id }}">
                                @if($isThisCover)
                                    <span class="main-cover-label" style="font-size: 11px; color: #166534; font-weight: 700;">✓ Main Cover</span>
                                    <button type="button" onclick="removeCover({{ $img->id }})" class="btn btn-outline unset-cover-btn" style="padding: 2px 6px; font-size: 10px; color: #DC2626; border: 1px solid #FECACA; margin-left: 6px;" title="Remove cover designation">Unset</button>
                                @else
                                    <button type="button" onclick="setAsCover({{ $img->id }})" class="btn btn-outline set-cover-btn" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as Cover</button>
                                @endif
                            </div>

                            <div style="display: flex; gap: 4px;">
                                <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageLeft('gallery-card-{{ $img->id }}')">&lsaquo;</button>
                                <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageRight('gallery-card-{{ $img->id }}')">&rsaquo;</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 32px; text-align: center; color: #64748B; background-color: #F8FAFC; border-radius: 6px;">
                        No photos uploaded yet. Use the upload zone above to add property photos.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 4: Live Preview -->
    <div class="property-tab-content" id="property-tab-preview" style="display: none;">
        <div class="admin-card" style="padding: 28px; background-color: #F8FAFC; border: 1px solid #E2E8F0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #CBD5E1; padding-bottom: 12px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin: 0;">Public Page Preview</h3>
                    <p style="font-size: 13px; color: #64748B; margin: 2px 0 0 0;">High-fidelity representation of how this property renders to visitors on the Public Property Detail page.</p>
                </div>
                <span style="font-size: 12px; background-color: #1E3A8A; color: #FFFFFF; padding: 4px 10px; border-radius: 4px; font-weight: 600;">Real-time Preview</span>
            </div>

            <!-- Preview Card Container -->
            <div style="background-color: #FFFFFF; border-radius: 8px; border: 1px solid #E2E8F0; padding: 32px; max-width: 1000px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                
                <!-- Breadcrumb Preview -->
                <div style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                    Home &gt; Properties &gt; <span id="prev-category-crumb">{{ $property->category->name ?? 'Villa' }}</span> &gt; <span id="prev-title-crumb" style="font-weight: 600; color: #0F172A;">{{ $property->name }}</span>
                </div>

                <!-- Title & Meta Bar -->
                <div style="margin-bottom: 24px;">
                    <h1 id="prev-title" style="font-size: 28px; font-weight: 700; color: #0F172A; margin-bottom: 8px;">{{ $property->name }}</h1>
                    <div style="display: flex; gap: 16px; align-items: center; font-size: 14px; color: #64748B;">
                        <span>📍 <span id="prev-location">{{ $property->location->name ?? 'Lovina' }}, North Bali</span></span>
                        <span>🏠 <span id="prev-category">{{ $property->category->name ?? 'Villa' }}</span></span>
                        <span id="prev-featured-badge" style="display: {{ $property->is_featured ? 'inline-block' : 'none' }}; background-color: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">★ Featured</span>
                    </div>
                </div>

                <!-- Image Gallery Hero Preview -->
                <div style="height: 360px; border-radius: 8px; overflow: hidden; background-color: #F1F5F9; margin-bottom: 24px; position: relative;">
                    @if($property->real_cover_image_url)
                        <img id="prev-cover-img" src="{{ $property->real_cover_image_url }}" alt="Property Cover" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <img id="prev-cover-img" src="" alt="Property Cover" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    @endif
                    <div style="position: absolute; bottom: 12px; right: 12px; background-color: rgba(0,0,0,0.75); color: #FFFFFF; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                        📷 <span id="prev-photo-count">{{ $property->images->count() }}</span> Photos
                    </div>
                </div>

                <!-- Price & Details Grid -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 32px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">About This Property</h3>
                        <div id="prev-description" style="font-size: 15px; line-height: 1.7; color: #334155; white-space: pre-line; margin-bottom: 24px;">
                            {{ $property->description }}
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
                                {{ $property->formatted_price_admin }}
                            </div>
                            <div id="prev-price-usd" style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                                ≈ {{ $property->formatted_price }}
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button type="button" class="btn btn-primary" style="width: 100%; pointer-events: none;">Contact Us</button>
                                <button type="button" class="btn btn-outline" style="width: 100%; pointer-events: none; color: #16A34A; border-color: #86EFAC;">WhatsApp Us</button>
                            </div>
                        </div>

                        <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">Key Information</h4>
                            <div style="font-size: 13px; display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between;"><span>Bedrooms:</span><strong id="prev-beds">{{ $property->bedrooms ?: '-' }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Bathrooms:</span><strong id="prev-baths">{{ $property->bathrooms ?: '-' }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Land Size:</span><strong id="prev-land">{{ $property->land_size ? $property->land_size . ' m²' : '-' }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Building Size:</span><strong id="prev-building">{{ $property->building_size ? $property->building_size . ' m²' : '-' }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Garage:</span><strong id="prev-garage">{{ $property->garage ? $property->garage . ' Cars' : '-' }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Ownership:</span><strong id="prev-ownership">{{ $property->ownership_type }}</strong></div>
                                <div style="display: flex; justify-content: space-between;"><span>Status:</span><strong id="prev-status">{{ ucfirst($property->status) }}</strong></div>
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
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; background-color: #1E3A8A; border-color: #1E3A8A; font-weight: 600;">Update Property</button>
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

// Multi-batch DataTransfer for New Photos in Edit Mode
const editGalleryDT = new DataTransfer();

function handleEditGallerySelect(input) {
    if (!input.files || input.files.length === 0) return;

    for (let i = 0; i < input.files.length; i++) {
        editGalleryDT.items.add(input.files[i]);
    }
    input.files = editGalleryDT.files;
    renderEditPendingUploads();
}

function removeEditPendingFile(index) {
    const newDT = new DataTransfer();
    for (let i = 0; i < editGalleryDT.files.length; i++) {
        if (i !== index) {
            newDT.items.add(editGalleryDT.files[i]);
        }
    }
    editGalleryDT.items.clear();
    for (let i = 0; i < newDT.files.length; i++) {
        editGalleryDT.items.add(newDT.files[i]);
    }
    const input = document.getElementById('images');
    if (input) input.files = editGalleryDT.files;
    renderEditPendingUploads();
}

function setNewCoverIndex(index) {
    const coverInput = document.getElementById('new_cover_index');
    if (coverInput) coverInput.value = index;

    // Clear existing images cover in UI and hidden input
    const existingCoverInput = document.getElementById('existing_cover_id');
    if (existingCoverInput) existingCoverInput.value = '';

    const cards = document.querySelectorAll('#gallery-preview-grid .gallery-card');
    cards.forEach(card => {
        const id = card.id.replace('gallery-card-', '');
        const badge = card.querySelector('.cover-badge');
        const actionWrap = card.querySelector('.cover-action-wrap') || card.querySelector('div[id^="cover-action-wrap-"]');
        if (badge) badge.style.display = 'none';
        if (actionWrap) {
            actionWrap.innerHTML = `<button type="button" onclick="setAsCover(${id})" class="btn btn-outline set-cover-btn" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as Cover</button>`;
        }
    });

    renderEditPendingUploads();
}

function renderEditPendingUploads() {
    const container = document.getElementById('pending-uploads-container');
    const grid = document.getElementById('new-pending-grid');
    if (!container || !grid) return;

    if (editGalleryDT.files.length === 0) {
        container.style.display = 'none';
        grid.innerHTML = '';
        return;
    }

    container.style.display = 'block';
    grid.innerHTML = '';
    const currentCover = document.getElementById('new_cover_index')?.value;

    for (let i = 0; i < editGalleryDT.files.length; i++) {
        const file = editGalleryDT.files[i];
        const isCover = (currentCover !== '' && parseInt(currentCover) === i);
        const cardId = 'edit-pending-card-' + i;
        const card = document.createElement('div');
        card.className = 'gallery-card';
        card.id = cardId;
        card.style.position = 'relative';
        card.style.border = '1px solid #93C5FD';
        card.style.borderRadius = '8px';
        card.style.overflow = 'hidden';
        card.style.backgroundColor = '#FFFFFF';

        const reader = new FileReader();
        reader.onload = function(e) {
            card.innerHTML = `
                <div class="gallery-card-img-wrap" style="height: 140px; overflow: hidden; position: relative;">
                    <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                ${isCover ? '<span style="position: absolute; top: 8px; left: 8px; background-color: #C7A86D; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">★ New Cover</span>' : '<span style="position: absolute; top: 8px; left: 8px; background-color: #2563EB; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">New</span>'}
                <button type="button" onclick="removeEditPendingFile(${i})" style="position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.9); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; line-height: 1;" title="Remove">&times;</button>
                <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; background-color: #F8FAFC; border-top: 1px solid #E2E8F0;">
                    ${isCover ? '<span style="font-size: 11px; color: #166534; font-weight: 700;">✓ Designated Cover</span>' : `<button type="button" onclick="setNewCoverIndex(${i})" class="btn btn-outline" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as New Cover</button>`}
                    <span style="font-size: 10px; color: #64748B;">${(file.size / 1024).toFixed(0)} KB</span>
                </div>
            `;
            if (isCover) {
                const prevCoverImg = document.getElementById('prev-cover-img');
                if (prevCoverImg) {
                    prevCoverImg.src = e.target.result;
                    prevCoverImg.style.display = 'block';
                }
            }
        };
        reader.readAsDataURL(file);
        grid.appendChild(card);
    }
}

// Delete Server Image
function deletePropertyImage(imageId) {
    if (!confirm('Are you sure you want to permanently delete this photo?')) return;

    fetch(`/admin/properties/image/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById(`gallery-card-${imageId}`);
            if (card) card.remove();
            const badge = document.getElementById('gallery-count-badge');
            if (badge) badge.innerText = Math.max(0, parseInt(badge.innerText || 1) - 1);

            if (data.new_cover_id) {
                const nextCoverCard = document.getElementById(`gallery-card-${data.new_cover_id}`);
                if (nextCoverCard) {
                    const coverBadge = nextCoverCard.querySelector('.cover-badge');
                    if (coverBadge) coverBadge.style.display = 'inline-block';
                    const actionWrap = nextCoverCard.querySelector('.cover-action-wrap') || nextCoverCard.querySelector('div[id^="cover-action-wrap-"]');
                    if (actionWrap) {
                        actionWrap.innerHTML = '<span class="main-cover-label" style="font-size: 11px; color: #166534; font-weight: 700;">✓ Main Cover</span>';
                    }
                    const existingCoverInput = document.getElementById('existing_cover_id');
                    if (existingCoverInput) existingCoverInput.value = data.new_cover_id;
                    const nextImg = nextCoverCard.querySelector('img');
                    if (nextImg) {
                        const prevCoverImg = document.getElementById('prev-cover-img');
                        if (prevCoverImg) {
                            prevCoverImg.src = nextImg.src;
                            prevCoverImg.style.display = 'block';
                        }
                    }
                }
            }
        }
    })
    .catch(err => console.error('Error deleting image:', err));
}

// Set as Main Cover Image
function setAsCover(imageId) {
    // 1. Instantly update UI on existing cards
    const cards = document.querySelectorAll('#gallery-preview-grid .gallery-card');
    let selectedImgUrl = null;

    cards.forEach(card => {
        const id = card.id.replace('gallery-card-', '');
        const badge = card.querySelector('.cover-badge');
        const actionWrap = card.querySelector('.cover-action-wrap') || card.querySelector('div[id^="cover-action-wrap-"]');
        
        if (id === String(imageId)) {
            if (badge) badge.style.display = 'inline-block';
            if (actionWrap) {
                actionWrap.innerHTML = `
                    <span class="main-cover-label" style="font-size: 11px; color: #166534; font-weight: 700;">✓ Main Cover</span>
                    <button type="button" onclick="removeCover(${imageId})" class="btn btn-outline unset-cover-btn" style="padding: 2px 6px; font-size: 10px; color: #DC2626; border: 1px solid #FECACA; margin-left: 6px;" title="Remove cover designation">Unset</button>
                `;
            }
            const imgEl = card.querySelector('img');
            if (imgEl) selectedImgUrl = imgEl.src;
        } else {
            if (badge) badge.style.display = 'none';
            if (actionWrap) {
                actionWrap.innerHTML = `<button type="button" onclick="setAsCover(${id})" class="btn btn-outline set-cover-btn" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as Cover</button>`;
            }
        }
    });

    // 2. Clear any pending new upload cover selection
    const newCoverInput = document.getElementById('new_cover_index');
    if (newCoverInput && newCoverInput.value !== '') {
        newCoverInput.value = '';
        renderEditPendingUploads();
    }

    // 3. Set hidden input for form submission
    const existingCoverInput = document.getElementById('existing_cover_id');
    if (existingCoverInput) existingCoverInput.value = imageId;

    // 4. Update Live Preview image
    if (selectedImgUrl) {
        const prevCoverImg = document.getElementById('prev-cover-img');
        if (prevCoverImg) {
            prevCoverImg.src = selectedImgUrl;
            prevCoverImg.style.display = 'block';
        }
    }

    // 5. Send background AJAX to persist in database immediately
    fetch(`/admin/properties/image/${imageId}/set-cover`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .catch(err => console.error('Error setting cover image:', err));
}

// Remove/Unset Main Cover Image
function removeCover(imageId) {
    const cards = document.querySelectorAll('#gallery-preview-grid .gallery-card');

    cards.forEach(card => {
        const id = card.id.replace('gallery-card-', '');
        const badge = card.querySelector('.cover-badge');
        const actionWrap = card.querySelector('.cover-action-wrap') || card.querySelector('div[id^="cover-action-wrap-"]');
        
        if (badge) badge.style.display = 'none';
        if (actionWrap) {
            actionWrap.innerHTML = `<button type="button" onclick="setAsCover(${id})" class="btn btn-outline set-cover-btn" style="padding: 4px 8px; font-size: 11px; font-weight: 600; color: #2563EB; border: 1px solid #BFDBFE;">Set as Cover</button>`;
        }
    });

    const newCoverInput = document.getElementById('new_cover_index');
    if (newCoverInput) newCoverInput.value = '';

    const existingCoverInput = document.getElementById('existing_cover_id');
    if (existingCoverInput) existingCoverInput.value = '';

    fetch(`/admin/properties/image/${imageId}/unset-cover`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .catch(err => console.error('Error unsetting cover image:', err));
}

// Reordering images left/right
function moveImageLeft(cardId) {
    const card = document.getElementById(cardId);
    if (card && card.previousElementSibling) {
        card.parentNode.insertBefore(card, card.previousElementSibling);
        saveGalleryOrder();
    }
}

function moveImageRight(cardId) {
    const card = document.getElementById(cardId);
    if (card && card.nextElementSibling) {
        card.parentNode.insertBefore(card.nextElementSibling, card);
        saveGalleryOrder();
    }
}

function saveGalleryOrder() {
    const cards = document.querySelectorAll('#gallery-preview-grid .gallery-card');
    const order = [];
    cards.forEach(c => {
        const id = c.id.replace('gallery-card-', '');
        if (id && !isNaN(id)) order.push(parseInt(id));
    });
    if (order.length > 0) {
        fetch(`/admin/properties/{{ $property->id }}/reorder-images`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order: order })
        }).catch(err => console.error(err));
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

    // Populate DOM preview fields
    const prevTitle = document.getElementById('prev-title');
    if (prevTitle) prevTitle.innerText = name;
    const prevTitleCrumb = document.getElementById('prev-title-crumb');
    if (prevTitleCrumb) prevTitleCrumb.innerText = name;
    const prevCatCrumb = document.getElementById('prev-category-crumb');
    if (prevCatCrumb) prevCatCrumb.innerText = catName;
    const prevCat = document.getElementById('prev-category');
    if (prevCat) prevCat.innerText = catName;
    const prevLoc = document.getElementById('prev-location');
    if (prevLoc) prevLoc.innerText = locName + ', North Bali';
    const prevDesc = document.getElementById('prev-description');
    if (prevDesc) prevDesc.innerText = desc;
    const prevFeatured = document.getElementById('prev-featured-badge');
    if (prevFeatured) prevFeatured.style.display = isFeatured ? 'inline-block' : 'none';

    // Formatted price preview
    const prevPriceIdr = document.getElementById('prev-price-idr');
    if (prevPriceIdr) {
        const numPrice = parseFloat(price) || 0;
        prevPriceIdr.innerText = 'Rp ' + numPrice.toLocaleString('id-ID');
    }

    // Specs
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

    // Feature chips preview
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

// Initial preview populate
document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview();
});
</script>
@endsection
