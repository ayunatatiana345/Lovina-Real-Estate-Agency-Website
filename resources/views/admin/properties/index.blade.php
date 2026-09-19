@extends('layouts.admin')

@section('title', 'Properties Management')
@section('page_title', 'Property Management')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0F172A;">Properties Listing</h2>
        <p style="font-size: 14px; color: #64748B;">Manage property listings, specifications, and categories.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary" id="btn-add-property" style="padding: 10px 24px; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; text-decoration: none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Property
        </a>
    </div>
</div>

<!-- Success Notification Banner -->
@if(session('success'))
<div class="settings-success-alert" id="properties-success-toast" style="margin-bottom: 24px; background-color: #DCFCE7; border: 1px solid #86EFAC; color: #166534; padding: 14px 18px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span id="properties-success-toast-text">{{ session('success') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('properties-success-toast').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>
@endif

@if(session('error'))
<div class="settings-error-alert" id="properties-error-toast" style="margin-bottom: 24px; background-color: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 14px 18px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #DC2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">!</div>
        <span>{{ session('error') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('properties-error-toast').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #991B1B;">&times;</button>
</div>
@endif

<!-- Search & Filters -->
<div class="admin-card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form action="{{ route('admin.properties.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="width: 240px;" placeholder="Search name..." value="{{ request('search') }}">
        
        <select name="category_id" class="form-select" style="width: 180px;">
            <option value="">All Categories</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>

        <select name="location_id" class="form-select" style="width: 180px;">
            <option value="">All Locations</option>
            @foreach($locations as $l)
                <option value="{{ $l->id }}" {{ request('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
            @endforeach
        </select>

        <select name="status" class="form-select" style="width: 150px;">
            <option value="">All Status</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
        </select>

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px; font-weight: 600; background-color: #1E3A8A !important; border: 1px solid #1E3A8A !important; color: #FFFFFF !important; border-radius: 6px; height: 42px; font-family: 'Poppins', sans-serif;">Filter</button>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px; font-weight: 500; color: #1E3A8A; background-color: #FFFFFF; border: 1px solid #1E3A8A; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box; height: 42px; font-family: 'Poppins', sans-serif;">Reset</a>
    </form>
</div>

<!-- Properties Data Table -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Property Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Price (IDR)</th>
                <th>Status</th>
                <th>Upload Date & Time</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($properties as $prop)
                <tr>
                    <td>
                        @if($prop->real_cover_image_url)
                            <img src="{{ $prop->real_cover_image_url }}" alt="{{ $prop->name }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;" onerror="this.onerror=null;this.style.display='none';">
                        @else
                            <div style="width: 60px; height: 45px; background-color: #F1F5F9; border-radius: 4px; border: 1px dashed #CBD5E1;"></div>
                        @endif
                    </td>
                    <td style="font-weight: 600;">
                        <a href="{{ route('properties.show', $prop->slug) }}" target="_blank" style="color: #1E3A8A; text-decoration: none;">
                            {{ $prop->name }}
                        </a>
                    </td>
                    <td>{{ $prop->category->name ?? 'N/A' }}</td>
                    <td>{{ $prop->location->name ?? 'N/A' }}</td>
                    <td style="font-weight: 600; color: #15803D; white-space: nowrap;">{{ $prop->formatted_price_admin }}</td>
                    <td>
                        <span class="status-badge badge-{{ $prop->status }}">{{ ucfirst($prop->status) }}</span>
                    </td>
                    <td style="font-size: 13px; color: #64748B; white-space: nowrap;">{{ $prop->created_at->format('d M Y, h:i A \W\I\T\A') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                            <form action="{{ route('admin.properties.toggle-featured', $prop->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @if($prop->is_featured)
                                    <button type="submit" class="btn btn-outline" style="padding: 5px 8px; font-size: 11px; font-weight: 700; color: #B45309; background-color: #FEF3C7; border-color: #FCD34D; display: inline-flex; align-items: center; gap: 3px;" title="Currently Featured on Homepage (Click to unfeature)">★ Featured</button>
                                @else
                                    <button type="submit" class="btn btn-outline" style="padding: 5px 8px; font-size: 11px; font-weight: 600; color: #64748B; border-color: #CBD5E1; display: inline-flex; align-items: center; gap: 3px;" title="Feature on Homepage (Max 6)">☆ Feature</button>
                                @endif
                            </form>
                            <a href="{{ route('admin.properties.edit', $prop->id) }}" class="btn btn-outline" style="padding: 6px 10px; font-size: 12px; color: #1E3A8A; border-color: #BFDBFE; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-weight: 600;">Edit</a>
                            <form action="{{ route('admin.properties.destroy', $prop->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this property permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline" style="padding: 6px 6px; font-size: 12px; color: #DC2626; border-color: #FCA5A5; display: inline-flex; align-items: center; justify-content: center;" aria-label="Delete property" title="Delete property">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 32px; color: #64748B;">No properties found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; width: 100%;">
    @if ($properties->total() > 0)
        <div style="font-size: 13px; color: #64748B; text-align: center;">
            Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }} of {{ $properties->total() }} results
        </div>
    @endif
    <div style="width: 100%; display: flex; justify-content: center;">
        {{ $properties->links('vendor.pagination.admin') }}
    </div>
</div>

<!-- Category Management Section Inside Properties Page -->
<div class="admin-card" style="margin-top: 48px;">
    <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 16px;">Property Categories Management</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px;">
        <!-- Add Category Form -->
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="cat_name">Category Name *</label>
                <input type="text" name="name" id="cat_name" class="form-control" placeholder="e.g. Resort, Penthouse" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="cat_icon">Icon Tag</label>
                <input type="text" name="icon" id="cat_icon" class="form-control" placeholder="e.g. home, building">
            </div>
            <div class="form-group">
                <label class="form-label" for="cat_status">Status</label>
                <select name="status" id="cat_status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add Category</button>
        </form>

        <!-- Category Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Properties</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr>
                            <td style="font-weight: 600;">{{ $cat->name }}</td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #F1F5F9; color: #475569;">
                                    {{ $cat->properties()->count() }} listings
                                </span>
                            </td>
                            <td>{{ $cat->icon ?? 'home' }}</td>
                            <td><span class="status-badge badge-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    <button type="button" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; color: #1E3A8A; border-color: #BFDBFE;" onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->icon ?? '') }}', '{{ $cat->status }}')">Edit</button>
                                    <form action="{{ route('admin.categories.toggle-status', $cat->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @if($cat->status === 'active')
                                            <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 12px; color: #D97706; border-color: #FCD34D;" title="Deactivate Category">Deactivate</button>
                                        @else
                                            <button type="submit" class="btn btn-outline" style="padding: 4px 8px; font-size: 12px; color: #16A34A; border-color: #86EFAC;" title="Activate Category">Activate</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="danger-modal-overlay" id="editCategoryModal" style="display: none;" onclick="closeEditCategoryModal(event)">
    <div class="danger-modal-box" style="text-align: left; max-width: 460px;" onclick="event.stopPropagation()">
        <button type="button" class="danger-modal-close" onclick="closeEditCategoryModal(event)">&times;</button>
        <h3 style="font-size: 20px; font-weight: 700; color: #0F172A; margin-bottom: 20px;">Edit Category</h3>
        
        <form id="editCategoryForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="edit_cat_name">Category Name *</label>
                <input type="text" name="name" id="edit_cat_name" class="form-control" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="edit_cat_icon">Icon Tag</label>
                <input type="text" name="icon" id="edit_cat_icon" class="form-control" placeholder="e.g. home, building">
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" for="edit_cat_status">Status</label>
                <select name="status" id="edit_cat_status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeEditCategoryModal(event)" style="padding: 8px 18px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="padding: 8px 20px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successToast = document.getElementById('properties-success-toast');
    if (successToast) {
        setTimeout(() => {
            successToast.style.display = 'none';
        }, 6000);
    }
});

function openEditCategoryModal(id, name, icon, status) {
    const modal = document.getElementById('editCategoryModal');
    const form = document.getElementById('editCategoryForm');
    form.action = `/admin/properties/categories/${id}`;
    document.getElementById('edit_cat_name').value = name;
    document.getElementById('edit_cat_icon').value = icon;
    document.getElementById('edit_cat_status').value = status;
    modal.style.display = 'flex';
}

function closeEditCategoryModal(event) {
    if (event) event.stopPropagation();
    document.getElementById('editCategoryModal').style.display = 'none';
}
</script>
@endsection
@endsection
