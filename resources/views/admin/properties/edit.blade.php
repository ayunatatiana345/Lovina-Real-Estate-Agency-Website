@extends('layouts.admin')

@section('title', 'Edit Property')
@section('page_title', 'Edit Property: ' . $property->name)

@section('content')
<form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" id="property-form">
    @csrf
    @method('PUT')

    <!-- Header Area (Matching Reference Design) -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Edit Property</h2>
            <p style="font-size: 14px; color: #64748B;">Update property information, specifications, and gallery.</p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
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
            Gallery
        </a>
    </div>

    <!-- TAB 1: General Information -->
    <div class="property-tab-content" id="property-tab-general" style="display: block;">
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">1. General Information</h3>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="name">Property Name *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $property->name) }}" style="width: 100%;" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-select" style="width: 100%;" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $property->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="location_id">Location *</label>
                    <select name="location_id" id="location_id" class="form-select" style="width: 100%;" required>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ $property->location_id == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="price">Price *</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $property->price) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="ownership_type">Ownership Title *</label>
                    <select name="ownership_type" id="ownership_type" class="form-select" style="width: 100%;" required>
                        <option value="Freehold" {{ $property->ownership_type == 'Freehold' ? 'selected' : '' }}>Freehold</option>
                        <option value="Leasehold" {{ $property->ownership_type == 'Leasehold' ? 'selected' : '' }}>Leasehold</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Publication Status *</label>
                    <select name="status" id="status" class="form-select" style="width: 100%;" required>
                        <option value="published" {{ $property->status == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $property->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="is_featured" value="1" {{ $property->is_featured ? 'checked' : '' }}>
                    <span style="display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="star" style="width: 16px; height: 16px; color: #C7A86D; fill: #C7A86D;"></i> Mark as Featured Property</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="description">Detailed Description</label>
                <!-- Simple Simulated Rich Text Editor (Matching reference image) -->
                <div class="editor-toolbar">
                    <button type="button" class="editor-btn" onclick="formatDoc('bold')">B</button>
                    <button type="button" class="editor-btn" style="font-style: italic;" onclick="formatDoc('italic')">I</button>
                    <button type="button" class="editor-btn" style="text-decoration: underline;" onclick="formatDoc('underline')">U</button>
                    <button type="button" class="editor-btn" onclick="formatDoc('insertUnorderedList')">• List</button>
                </div>
                <textarea name="description" id="description" class="form-control editor-textarea" style="min-height: 180px; width: 100%; resize: vertical; border-top: none;" placeholder="Full overview of the property...">{{ old('description', $property->description) }}</textarea>
            </div>
        </div>
    </div>

    <!-- TAB 2: Specifications -->
    <div class="property-tab-content" id="property-tab-specs" style="display: none;">
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">2. Specifications</h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="bedrooms">Bedrooms</label>
                    <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms ?? 0) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="bathrooms">Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', $property->bathrooms ?? 0) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="land_size">Land Size (m²)</label>
                    <input type="number" name="land_size" id="land_size" class="form-control" value="{{ old('land_size', $property->land_size ?? 0) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="building_size">Building Size (m²)</label>
                    <input type="number" name="building_size" id="building_size" class="form-control" value="{{ old('building_size', $property->building_size ?? 0) }}" style="width: 100%;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="garage">Garage (Cars)</label>
                    <input type="number" name="garage" id="garage" class="form-control" value="{{ old('garage', $property->garage ?? 0) }}" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="electricity">Electricity (VA)</label>
                    <input type="text" name="electricity" id="electricity" class="form-control" value="{{ old('electricity', $property->electricity) }}" style="width: 100%;">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="water_supply">Water Supply</label>
                    <input type="text" name="water_supply" id="water_supply" class="form-control" value="{{ old('water_supply', $property->water_supply) }}" style="width: 100%;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="swimming_pool" value="1" {{ $property->swimming_pool ? 'checked' : '' }}>
                    <span>🏊‍♂️ Includes Private Swimming Pool</span>
                </label>
            </div>
        </div>
    </div>

    <!-- TAB 3: Gallery -->
    <div class="property-tab-content" id="property-tab-gallery" style="display: none;">
        <div class="admin-card" style="padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">3. Image Gallery</h3>

            <!-- Dashed Border Upload Area -->
            <label class="gallery-upload-zone" for="images">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="margin-bottom: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <span style="font-size: 14px; font-weight: 600; color: #0F172A;">Click to upload or drag and drop</span>
                <span style="font-size: 11px; color: #64748B;">Recommended: 1200 x 800px / JPG, PNG or WebP (Max. 2MB)</span>
                <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;" onchange="handleGallerySelect(this)">
            </label>

            <!-- Gallery Images Grid -->
            <div class="gallery-grid" id="gallery-preview-grid">
                @foreach($property->images as $img)
                    <div class="gallery-card" id="gallery-card-{{ $img->id }}">
                        <div class="gallery-card-img-wrap">
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Property Image">
                        </div>
                        
                        <!-- Badges and Small Close Trigger -->
                        @if($img->is_cover)
                            <span style="position: absolute; top: 8px; left: 8px; background-color: #C7A86D; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">Cover</span>
                        @endif

                        <button type="button" onclick="deletePropertyImage({{ $img->id }})" style="position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.85); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; line-height: 1;">&times;</button>

                        <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between;">
                            @if(!$img->is_cover)
                                <a href="#" style="font-size: 11px; font-weight: 600; color: #2563EB; text-decoration: none;">Set as Cover</a>
                            @else
                                <span style="font-size: 11px; color: #64748B; font-weight: 600;">Main Cover</span>
                            @endif

                            <!-- Arrow Reorder Buttons -->
                            <div style="display: flex; gap: 4px;">
                                <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageLeft('gallery-card-{{ $img->id }}')">&lsaquo;</button>
                                <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageRight('gallery-card-{{ $img->id }}')">&rsaquo;</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Actions footer -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 12px 28px;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; background-color: #1E3A8A; border-color: #1E3A8A;">Update Property</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
// Tab Switching functionality
function switchPropertyTab(e, tabName) {
    if (e) e.preventDefault();
    
    // Switch Active Link state
    document.querySelectorAll('.cms-tab-bar .cms-tab-item').forEach(item => {
        item.classList.remove('active');
    });
    const link = document.getElementById(`tab-link-${tabName}`);
    if (link) link.classList.add('active');

    // Switch Active Tab Content
    document.querySelectorAll('.property-tab-content').forEach(content => {
        content.style.display = 'none';
    });
    const activeContent = document.getElementById(`property-tab-${tabName}`);
    if (activeContent) activeContent.style.display = 'block';
}

// Simple Text Formatter for Simulated Rich Text
function formatDoc(cmd, val = null) {
    const textarea = document.getElementById('description');
    if (!textarea) return;
    
    let text = textarea.value;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = text.substring(start, end);
    
    let replacement = '';
    if (cmd === 'bold') {
        replacement = `**${selectedText}**`;
    } else if (cmd === 'italic') {
        replacement = `*${selectedText}*`;
    } else if (cmd === 'underline') {
        replacement = `_${selectedText}_`;
    } else if (cmd === 'insertUnorderedList') {
        replacement = `\n- ${selectedText}`;
    }

    textarea.value = text.substring(0, start) + replacement + text.substring(end);
    textarea.focus();
}

// Dynamic Thumbnail Preview for newly chosen local gallery files
function handleGallerySelect(input) {
    const grid = document.getElementById('gallery-preview-grid');
    if (!grid || !input.files) return;

    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const reader = new FileReader();
        reader.onload = function(e) {
            const cardId = 'new-gallery-card-' + Date.now() + '-' + i;
            const card = document.createElement('div');
            card.className = 'gallery-card';
            card.id = cardId;
            card.innerHTML = `
                <div class="gallery-card-img-wrap">
                    <img src="${e.target.result}" alt="Preview Image">
                </div>
                <button type="button" onclick="document.getElementById('${cardId}').remove()" style="position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.85); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; line-height: 1;">&times;</button>
                <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 11px; color: #64748B;">Ready to upload</span>
                    <div style="display: flex; gap: 4px;">
                        <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageLeft('${cardId}')">&lsaquo;</button>
                        <button type="button" class="btn btn-outline" style="padding: 2px 6px; font-size: 10px;" onclick="moveImageRight('${cardId}')">&rsaquo;</button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        };
        reader.readAsDataURL(file);
    }
}

// AJAX Delete for Existing Server Gallery Images
function deletePropertyImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) return;

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
        }
    })
    .catch(err => console.error('Error deleting image:', err));
}

// Reordering images left/right
function moveImageLeft(cardId) {
    const card = document.getElementById(cardId);
    if (card && card.previousElementSibling) {
        card.parentNode.insertBefore(card, card.previousElementSibling);
    }
}

function moveImageRight(cardId) {
    const card = document.getElementById(cardId);
    if (card && card.nextElementSibling) {
        card.parentNode.insertBefore(card.nextElementSibling, card);
    }
}
</script>
@endsection
