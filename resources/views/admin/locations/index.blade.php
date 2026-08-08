@extends('layouts.admin')

@section('title', 'Locations Management')
@section('page_title', 'Locations')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0F172A;">Locations</h2>
        <p style="font-size: 14px; color: #64748B;">Manage locations used across the website for filtering, popular locations, and property data.</p>
    </div>
    <div>
        <button class="btn btn-primary" id="openAddLocationBtn" style="padding: 10px 20px;">
            ➕ Add Location
        </button>
    </div>
</div>

<!-- Search & Reset -->
<div class="admin-card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form action="{{ route('admin.locations.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center;">
        <input type="text" name="search" class="form-control" placeholder="Search location..." value="{{ request('search') }}" style="max-width: 360px;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Search</button>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline" style="padding: 10px 16px;">🔄 Reset</a>
    </form>
</div>

<!-- Locations Data Table (Matching Reference Image 3) -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Location Name</th>
                <th>Description</th>
                <th>Property Count</th>
                <th>Popular</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $loc)
                <tr>
                    <td>
                        <img src="{{ $loc->image ? asset('storage/' . $loc->image) : asset('images/location-placeholder.jpg') }}" alt="{{ $loc->name }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 6px;">
                    </td>
                    <td style="font-weight: 700; color: #0F172A;">{{ $loc->name }}</td>
                    <td style="max-width: 300px; font-size: 13px; color: #475569; line-height: 1.5;">
                        {{ Str::limit($loc->description, 100) }}
                    </td>
                    <td style="font-weight: 700; color: #1E3A8A; text-align: center;">
                        {{ $loc->properties_count }}
                    </td>
                    <td style="text-align: center;">
                        <form action="{{ route('admin.locations.toggle-popular', $loc->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 20px; color: {{ $loc->is_popular ? '#F59E0B' : '#CBD5E1' }};">
                                {{ $loc->is_popular ? '⭐' : '☆' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <span class="status-badge badge-{{ $loc->status }}">{{ ucfirst($loc->status) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete location {{ $loc->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #DC2626; font-size: 16px;" title="Delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top: 24px;">
    {{ $locations->links() }}
</div>

<!-- Slide-in Panel "Add New Location" (Matching Reference Image 3) -->
<div class="slide-panel-container" id="locationSlidePanel">
    <div class="slide-panel-header">
        <div>
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">Add New Location</h3>
            <div style="font-size: 12px; color: #64748B;">Create a new location for website listings.</div>
        </div>
        <button id="closeLocationPanelBtn" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748B;">&times;</button>
    </div>

    <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%;">
        @csrf
        
        <div class="slide-panel-body">
            <!-- 1. Basic Information -->
            <div style="font-size: 14px; font-weight: 700; color: #2563EB; margin-bottom: 16px;">1. Basic Information</div>

            <div class="form-group">
                <label class="form-label" for="loc_name">Location Name *</label>
                <input type="text" name="name" id="loc_name" class="form-control" placeholder="Enter location name" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="locationDescInput">Description *</label>
                <textarea name="description" id="locationDescInput" class="form-control" style="min-height: 120px;" placeholder="Write location description..." maxlength="500" required></textarea>
                <div style="text-align: right; font-size: 12px; color: #64748B; margin-top: 4px;" id="charCounter">Characters: 0 / 500</div>
            </div>

            <!-- 2. Location Image -->
            <div style="font-size: 14px; font-weight: 700; color: #2563EB; margin-top: 24px; margin-bottom: 16px;">2. Location Image</div>

            <div class="form-group">
                <div style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 24px; text-align: center; background-color: #F8FAFC;">
                    <div style="font-size: 32px; color: #2563EB; margin-bottom: 8px;">☁️</div>
                    <div style="font-weight: 600; font-size: 14px; color: #0F172A; margin-bottom: 4px;">Click to upload image</div>
                    <div style="font-size: 12px; color: #64748B; margin-bottom: 12px;">Recommended size: 1200 x 800px (JPG, PNG or WebP max 2MB)</div>
                    <input type="file" name="image" id="loc_image" class="form-control" accept="image/*">
                </div>
            </div>

            <!-- 3. Settings -->
            <div style="font-size: 14px; font-weight: 700; color: #2563EB; margin-top: 24px; margin-bottom: 16px;">3. Settings</div>

            <div class="form-group">
                <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; background-color: #F8FAFC; padding: 12px; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #0F172A;">Popular Location</div>
                        <div style="font-size: 12px; color: #64748B;">Show this location in Popular Locations section</div>
                    </div>
                    <input type="checkbox" name="is_popular" value="1" style="width: 20px; height: 20px;">
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="loc_status">Status *</label>
                <select name="status" id="loc_status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="slide-panel-footer">
            <button type="button" class="btn btn-outline" id="cancelLocationBtn">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Location</button>
        </div>
    </form>
</div>
@endsection
