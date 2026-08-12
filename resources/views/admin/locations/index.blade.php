@extends('layouts.admin')

@section('title', 'Locations Management')
@section('page_title', 'Locations')

@section('content')
<!-- Header Area (Matching Reference Design) -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Locations</h2>
        <p style="font-size: 14px; color: #64748B;">Manage locations used across the website for filtering, popular locations, and property data.</p>
    </div>

    <div>
        <button class="btn btn-primary" onclick="openLocationDrawer('add')" style="padding: 10px 24px; font-size: 14px; display: flex; align-items: center; gap: 6px; font-weight: 600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Location
        </button>
    </div>
</div>

<!-- Success Notification Banner -->
<div class="settings-success-alert" id="success-toast-banner" style="display: none; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span id="success-toast-text">Location deleted successfully.</span>
    </div>
    <button type="button" onclick="document.getElementById('success-toast-banner').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>

@if(session('success'))
<div class="settings-success-alert" id="success-session-banner" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('success-session-banner').remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>
@endif

<!-- Search & Reset (Matching Reference Design) -->
<div class="admin-card" style="margin-bottom: 24px; padding: 20px 24px;">
    <form action="{{ route('admin.locations.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" placeholder="Search location..." value="{{ request('search') }}" style="max-width: 360px; width: 100%;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">Search</button>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px; text-decoration: none;">
            Reset
        </a>
    </form>
</div>

<!-- Locations Data Table -->
<div class="admin-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="admin-table-container" style="border: none; border-radius: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Image</th>
                    <th>Location Name</th>
                    <th>Description</th>
                    <th style="text-align: center; width: 140px;">Property Count</th>
                    <th style="text-align: center; width: 100px;">Popular</th>
                    <th style="width: 120px;">Status</th>
                    <th style="text-align: right; width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $loc)
                    <tr>
                        <td>
                            <div style="width: 64px; height: 48px; border-radius: 6px; overflow: hidden; border: 1px solid #E2E8F0; background-color: #F1F5F9;">
                                @if($loc->image)
                                    <img src="{{ asset('storage/' . $loc->image) }}" 
                                         alt="{{ $loc->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;"
                                         onerror="this.style.display='none'">
                                @endif
                            </div>
                        </td>
                        <td style="font-weight: 700; color: #0F172A;">{{ $loc->name }}</td>
                        <td style="max-width: 320px; font-size: 13px; color: #475569; line-height: 1.5; white-space: normal;">
                            {{ Str::limit($loc->description, 100) }}
                        </td>
                        <td style="font-weight: 700; color: #1E3A8A; text-align: center;">
                            {{ $loc->properties_count }}
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.locations.toggle-popular', $loc->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 20px; color: {{ $loc->is_popular ? '#F59E0B' : '#CBD5E1' }}; padding: 4px;" title="Toggle Popular">
                                    {{ $loc->is_popular ? '★' : '☆' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <span class="status-badge {{ $loc->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $loc->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button type="button" class="btn btn-outline" style="padding: 6px 10px; font-size: 12px;" onclick="viewLocationDetails(this)" data-location="{{ json_encode($loc) }}">View</button>
                                <button type="button" class="btn btn-outline" style="padding: 6px 10px; font-size: 12px; color: #2563EB; border-color: #BFDBFE;" onclick="editLocationDrawer(this)" data-location="{{ json_encode($loc) }}">Edit</button>
                                <button type="button" class="btn btn-outline" style="padding: 6px 6px; font-size: 12px; color: #DC2626; border-color: #FCA5A5; display: inline-flex; align-items: center; justify-content: center;" onclick="triggerDeleteLocation(this)" data-location="{{ json_encode($loc) }}" aria-label="Delete location" title="Delete location">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748B; padding: 24px;">No locations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Backdrop Overlay for Drawer Panels -->
<div class="modal-overlay" id="drawerBackdrop" style="display: none; z-index: 1000;" onclick="closeAllDrawers()"></div>

<!-- Slide-in Panel Drawer (Add/Edit) -->
<div class="slide-panel-container" id="locationSlidePanel" style="display: none; z-index: 1050; width: 440px; box-shadow: -4px 0 24px rgba(0,0,0,0.15);">
    <div class="slide-panel-header" style="position: sticky; top: 0; background-color: #FFFFFF; z-index: 10; border-bottom: 1px solid var(--admin-border);">
        <div>
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;" id="drawer-title">Add New Location</h3>
            <div style="font-size: 12px; color: #64748B;" id="drawer-subtitle">Create a new location for website listings.</div>
        </div>
        <button onclick="closeLocationDrawer('form')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748B; padding: 4px; line-height: 1;">&times;</button>
    </div>

    <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data" id="location-drawer-form" style="display: flex; flex-direction: column; height: calc(100% - 65px);">
        @csrf
        <div id="method-field-wrapper"></div>
        
        <div class="slide-panel-body" style="padding: 24px; overflow-y: auto; flex-grow: 1;">
            <!-- 1. Basic Information -->
            <div style="font-size: 13px; font-weight: 700; color: #1E3A8A; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">1. Basic Information</div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="loc_name">Location Name *</label>
                <input type="text" name="name" id="loc_name" class="form-control" placeholder="Enter location name" style="width: 100%;" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="locationDescInput">Description *</label>
                <textarea name="description" id="locationDescInput" class="form-control" style="min-height: 120px; width: 100%; resize: vertical;" placeholder="Write location description..." maxlength="500" oninput="updateDrawerCounter(this)" required></textarea>
                <div style="text-align: right; font-size: 11px; color: #64748B; margin-top: 4px;" id="charCounter">Characters: 0 / 500</div>
            </div>

            <!-- 2. Location Image -->
            <div style="font-size: 13px; font-weight: 700; color: #1E3A8A; margin-top: 24px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">2. Location Image</div>

            <div class="form-group" style="margin-bottom: 20px;">
                <div style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 20px; text-align: center; background-color: #F8FAFC;">
                    <div style="font-size: 28px; color: #2563EB; margin-bottom: 8px;">☁️</div>
                    <div style="font-weight: 600; font-size: 13px; color: #0F172A; margin-bottom: 4px;">Click to upload image</div>
                    <div style="font-size: 11px; color: #64748B; margin-bottom: 12px;">Recommended size: 1200 x 800px (JPG, PNG or WebP max 2MB)</div>
                    <input type="file" name="image" id="loc_image" class="form-control" accept="image/*" onchange="previewDrawerImage(this)">
                </div>
                
                <div id="drawer-img-preview-wrap" style="display: none; margin-top: 12px; position: relative; width: 100%; height: 160px; border-radius: 6px; overflow: hidden; border: 1px solid #E2E8F0;">
                    <img src="" id="drawer-img-preview-tag" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" onclick="removeDrawerImage()" style="position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; border: none; background-color: rgba(220,38,38,0.9); color: white; cursor: pointer;">&times;</button>
                </div>
            </div>

            <!-- 3. Settings -->
            <div style="font-size: 13px; font-weight: 700; color: #1E3A8A; margin-top: 24px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">3. Settings</div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; background-color: #F8FAFC; padding: 14px; border-radius: 8px; border: 1px solid #E2E8F0; user-select: none;">
                    <div>
                        <div style="font-weight: 600; font-size: 13px; color: #0F172A;">Popular Location</div>
                        <div style="font-size: 11px; color: #64748B;">Show this location in Popular Locations section</div>
                    </div>
                    <input type="checkbox" name="is_popular" id="loc_is_popular" value="1" style="width: 18px; height: 18px;">
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="loc_status">Status *</label>
                <select name="status" id="loc_status" class="form-select" style="width: 100%;" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="slide-panel-footer" style="position: sticky; bottom: 0; background-color: #FFFFFF; border-top: 1px solid var(--admin-border); padding: 16px 24px; z-index: 10;">
            <button type="button" class="btn btn-outline" onclick="closeLocationDrawer('form')" style="padding: 10px 20px;">Cancel</button>
            <button type="submit" class="btn btn-primary" id="drawer-submit-btn" style="padding: 10px 24px;">Save Location</button>
        </div>
    </form>
</div>

<!-- Slide-in Panel Drawer (Read-Only Detail View - Matching Drawer Theme) -->
<div class="slide-panel-container" id="locationViewDrawer" style="display: none; z-index: 1050; width: 440px; box-shadow: -4px 0 24px rgba(0,0,0,0.15);">
    <div class="slide-panel-header" style="position: sticky; top: 0; background-color: #FFFFFF; z-index: 10; border-bottom: 1px solid var(--admin-border);">
        <div>
            <h3 style="font-size: 18px; font-weight: 700; color: #0F172A;">Location Details</h3>
            <div style="font-size: 12px; color: #64748B;">View regional configuration and analytics.</div>
        </div>
        <button onclick="closeLocationDrawer('view')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748B; padding: 4px; line-height: 1;">&times;</button>
    </div>

    <div class="slide-panel-body" style="padding: 24px; overflow-y: auto; flex-grow: 1;">
        <!-- Large Image Box -->
        <div style="width: 100%; height: 200px; border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0; background-color: #F1F5F9; margin-bottom: 24px;">
            <img src="" id="view-drawer-img" style="width: 100%; height: 100%; object-fit: cover; display: none;" onerror="this.style.display='none'">
        </div>

        <div style="margin-bottom: 20px;">
            <div style="font-size: 11px; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Location Name</div>
            <div style="font-weight: 700; font-size: 18px; color: #0F172A;" id="view-drawer-name">-</div>
        </div>

        <div style="margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <div style="font-size: 11px; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Property Count</div>
                <div style="font-weight: 700; color: #1E3A8A; font-size: 15px;" id="view-drawer-count">0 properties</div>
            </div>
            <div>
                <div style="font-size: 11px; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Status & Options</div>
                <div style="display: flex; gap: 6px; align-items: center; margin-top: 4px;">
                    <span class="status-badge" id="view-drawer-status">Active</span>
                    <span class="status-badge" style="background-color: #FEF3C7; color: #D97706;" id="view-drawer-popular">★ Popular</span>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 0;">
            <div style="font-size: 11px; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 6px;">Description</div>
            <p style="font-size: 13px; color: #475569; line-height: 1.6; white-space: pre-line;" id="view-drawer-desc">-</p>
        </div>
    </div>

    <div class="slide-panel-footer" style="position: sticky; bottom: 0; background-color: #FFFFFF; border-top: 1px solid var(--admin-border); padding: 16px 24px; z-index: 10; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" class="btn btn-outline" onclick="closeLocationDrawer('view')" style="padding: 10px 20px;">Close</button>
        <button type="button" class="btn btn-primary" id="view-drawer-edit-btn" style="padding: 10px 24px;">Edit Location</button>
    </div>
</div>

<!-- Delete Confirmation Modal (Detailed Pixel-Accurate Replication) -->
<div class="danger-modal-overlay" id="deleteLocationModal" style="display: none;" onclick="closeDeleteModal(event)">
    <div class="danger-modal-box" onclick="event.stopPropagation()">
        
        <!-- Close Button × -->
        <button type="button" class="danger-modal-close" onclick="closeDeleteModal(event)">&times;</button>
        
        <!-- 1. Icon Warning (Outline warning triangle inside light-red background circle) -->
        <div style="width: 56px; height: 56px; border-radius: 50%; background-color: #FEE2E2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        
        <!-- 2. Judul -->
        <h3 style="font-size: 22px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">Delete Location</h3>
        
        <!-- 3. Deskripsi peringatan -->
        <div style="font-size: 14px; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
            <div>Are you sure you want to delete this location?</div>
            <div>This action cannot be undone.</div>
        </div>
        
        <!-- 4. Garis pembatas (divider) -->
        <div style="height: 1px; background-color: #E5E7EB; margin-bottom: 20px; width: 100%;"></div>
        
        <!-- 5. Card Preview Lokasi -->
        <div class="danger-preview-card">
            <!-- Left Thumbnail -->
            <div class="danger-preview-img-wrap">
                <img id="delete-preview-img-tag" src="" alt="Location image" class="danger-preview-img" style="display: none;" onerror="this.style.display='none'">
            </div>
            <!-- Right Info Stack -->
            <div class="danger-preview-info">
                <!-- Location name row -->
                <div class="danger-preview-row-center">
                    <span class="danger-preview-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1E3A8A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </span>
                    <span id="delete-preview-name-val" class="danger-preview-name">-</span>
                </div>
                <!-- Location description row -->
                <div class="danger-preview-row">
                    <span class="danger-preview-icon" style="margin-top: 2px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </span>
                    <span id="delete-preview-desc-val" class="danger-preview-desc">-</span>
                </div>
                <!-- Total properties row -->
                <div class="danger-preview-row-center">
                    <span class="danger-preview-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <line x1="9" y1="22" x2="9" y2="16"></line>
                            <line x1="15" y1="22" x2="15" y2="16"></line>
                            <line x1="9" y1="16" x2="15" y2="16"></line>
                            <path d="M8 6h2v2H8V6zm4 0h2v2h-2V6zm-4 4h2v2H8v-2zm4 0h2v2h-2v-2zm-4 4h2v2H8v-2zm4 0h2v2h-2v-2z"></path>
                        </svg>
                    </span>
                    <span style="font-size: 13px; color: #4B5563;">
                        <span class="danger-preview-label">Total Properties:</span> <span id="delete-preview-count-val" class="danger-preview-value">0</span>
                    </span>
                </div>
                <!-- Popular location row -->
                <div class="danger-preview-row-center">
                    <span class="danger-preview-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="#C7A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #C7A86D;">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </span>
                    <span style="font-size: 13px; color: #4B5563;">
                        <span class="danger-preview-label">Popular Location:</span> <span id="delete-preview-popular-val" class="danger-preview-value">No</span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- 6. Alert Banner Permanen -->
        <div class="danger-alert-banner">
            <div class="danger-alert-icon-wrap">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <div class="danger-alert-text">
                <span class="danger-alert-title">This location will be permanently removed.</span>
                <span class="danger-alert-sub">All associated data will be affected.</span>
            </div>
        </div>
        
        <!-- 7. Tombol Aksi -->
        <div class="danger-modal-buttons">
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal(event)">
                Cancel
            </button>
            <button type="button" class="btn btn-delete" onclick="confirmDeleteLocationLocal(event)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
                Delete Location
            </button>
        </div>
        
    </div>
</div>

@endsection

@section('scripts')
<script>
// Open Drawer Slide Panel (Add / Edit mode)
function openLocationDrawer(mode, data = null) {
    const drawer = document.getElementById('locationSlidePanel');
    const backdrop = document.getElementById('drawerBackdrop');
    const form = document.getElementById('location-drawer-form');
    const title = document.getElementById('drawer-title');
    const subtitle = document.getElementById('drawer-subtitle');
    const submitBtn = document.getElementById('drawer-submit-btn');
    const methodField = document.getElementById('method-field-wrapper');

    if (!drawer || !backdrop) return;

    // Reset Form
    form.reset();
    removeDrawerImage();

    if (mode === 'add') {
        title.textContent = 'Add New Location';
        subtitle.textContent = 'Create a new location for website listings.';
        submitBtn.textContent = 'Save Location';
        form.action = "{{ route('admin.locations.store') }}";
        methodField.innerHTML = '';
    } else if (mode === 'edit' && data) {
        title.textContent = 'Edit Location';
        subtitle.textContent = 'Update regional records and images.';
        submitBtn.textContent = 'Save Changes';
        form.action = `/admin/locations/${data.id}`;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Fill Form
        document.getElementById('loc_name').value = data.name;
        document.getElementById('locationDescInput').value = data.description;
        document.getElementById('loc_status').value = data.status;
        document.getElementById('loc_is_popular').checked = !!data.is_popular;

        // Image Preview
        if (data.image) {
            const previewWrap = document.getElementById('drawer-img-preview-wrap');
            const previewTag = document.getElementById('drawer-img-preview-tag');
            previewWrap.style.display = 'block';
            previewTag.src = `/storage/${data.image}`;
        }
        
        updateDrawerCounter(document.getElementById('locationDescInput'));
    }

    // Display
    backdrop.style.display = 'block';
    drawer.style.display = 'flex';
}

// Wrapper for trigger Edit from button
function editLocationDrawer(btn) {
    const data = JSON.parse(btn.getAttribute('data-location'));
    openLocationDrawer('edit', data);
}

// Close Drawers
function closeLocationDrawer(type) {
    if (type === 'form') {
        document.getElementById('locationSlidePanel').style.display = 'none';
    } else if (type === 'view') {
        document.getElementById('locationViewDrawer').style.display = 'none';
    }
    
    // Hide backdrop only if both drawers are closed
    if (document.getElementById('locationSlidePanel').style.display === 'none' && 
        document.getElementById('locationViewDrawer').style.display === 'none') {
        document.getElementById('drawerBackdrop').style.display = 'none';
    }
}

function closeAllDrawers() {
    document.getElementById('locationSlidePanel').style.display = 'none';
    document.getElementById('locationViewDrawer').style.display = 'none';
    document.getElementById('drawerBackdrop').style.display = 'none';
}

// Character counter inside Drawer
function updateDrawerCounter(el) {
    const counter = document.getElementById('charCounter');
    if (counter) {
        counter.textContent = `Characters: ${el.value.length} / 500`;
    }
}

// Preview file input inside drawer
function previewDrawerImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewWrap = document.getElementById('drawer-img-preview-wrap');
            const previewTag = document.getElementById('drawer-img-preview-tag');
            previewWrap.style.display = 'block';
            previewTag.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove preview image
function removeDrawerImage() {
    const fileInput = document.getElementById('loc_image');
    if (fileInput) fileInput.value = '';
    document.getElementById('drawer-img-preview-wrap').style.display = 'none';
}

// View details slide-in drawer (matching style)
function viewLocationDetails(btn) {
    const data = JSON.parse(btn.getAttribute('data-location'));
    
    document.getElementById('view-drawer-name').textContent = data.name;
    document.getElementById('view-drawer-desc').textContent = data.description;
    document.getElementById('view-drawer-count').textContent = `${data.properties_count || 0} properties`;
    
    // Status Badge
    const statusTag = document.getElementById('view-drawer-status');
    statusTag.textContent = data.status === 'active' ? 'Active' : 'Inactive';
    statusTag.className = data.status === 'active' ? 'status-badge badge-active' : 'status-badge badge-inactive';

    // Popular Badge
    const popularTag = document.getElementById('view-drawer-popular');
    popularTag.style.display = data.is_popular ? 'inline-block' : 'none';

    // Image
    const imgTag = document.getElementById('view-drawer-img');
    if (data.image) {
        imgTag.src = `/storage/${data.image}`;
        imgTag.style.display = 'block';
    } else {
        imgTag.src = '';
        imgTag.style.display = 'none';
    }

    // Setup Edit button link action inside View drawer
    const editBtn = document.getElementById('view-drawer-edit-btn');
    editBtn.onclick = function() {
        closeLocationDrawer('view');
        openLocationDrawer('edit', data);
    };

    // Open Drawer
    document.getElementById('drawerBackdrop').style.display = 'block';
    document.getElementById('locationViewDrawer').style.display = 'flex';
}

// Local delete tracking variables
let activeDeleteRowEl = null;

// Trigger Delete Modal
function triggerDeleteLocation(btn) {
    const data = JSON.parse(btn.getAttribute('data-location'));
    activeDeleteRowEl = btn.closest('tr');

    // Fill preview card dynamically
    document.getElementById('delete-preview-name-val').textContent = data.name;
    document.getElementById('delete-preview-desc-val').textContent = data.description || '';
    document.getElementById('delete-preview-count-val').textContent = data.properties_count !== undefined ? data.properties_count : 0;
    document.getElementById('delete-preview-popular-val').textContent = data.is_popular ? 'Yes' : 'No';
    
    // Image fallback and routing
    const imgTag = document.getElementById('delete-preview-img-tag');
    if (imgTag) {
        if (data.image) {
            imgTag.src = `/storage/${data.image}`;
            imgTag.style.display = 'block';
        } else {
            imgTag.src = '';
            imgTag.style.display = 'none';
        }
    }

    // Lock background scroll
    document.body.style.overflow = 'hidden';
    
    document.getElementById('deleteLocationModal').style.display = 'flex';
}

// Close Delete Modal
function closeDeleteModal(e) {
    if (e) e.preventDefault();
    document.getElementById('deleteLocationModal').style.display = 'none';
    
    // Unlock background scroll
    document.body.style.overflow = '';
}

// Local Delete action (UI State update only as required)
function confirmDeleteLocationLocal(e) {
    if (e) e.preventDefault();
    
    if (activeDeleteRowEl) {
        activeDeleteRowEl.remove();
    }
    
    // Close modal & unlock scroll
    closeDeleteModal(e);
    
    // Show success notification banner
    const banner = document.getElementById('success-toast-banner');
    if (banner) {
        banner.style.display = 'flex';
        document.getElementById('success-toast-text').textContent = "Location deleted successfully.";
        
        // Auto scroll to top to see notification clearly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

</script>
@endsection
