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
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary" id="btn-add-property">
            ➕ Add Property
        </a>
    </div>
</div>

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

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline" style="padding: 10px 16px;">Reset</a>
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
                <th>Price (USD)</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Created Date</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($properties as $prop)
                <tr>
                    <td>
                        <img src="{{ $prop->primary_image_url }}" alt="{{ $prop->name }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td style="font-weight: 600;">
                        <a href="{{ route('properties.show', $prop->slug) }}" target="_blank" style="color: #1E3A8A; text-decoration: none;">
                            {{ $prop->name }}
                        </a>
                    </td>
                    <td>{{ $prop->category->name ?? 'N/A' }}</td>
                    <td>{{ $prop->location->name ?? 'N/A' }}</td>
                    <td style="font-weight: 600; color: #15803D;">${{ number_format($prop->price) }}</td>
                    <td>
                        <span class="status-badge badge-{{ $prop->status }}">{{ ucfirst($prop->status) }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.properties.toggle-featured', $prop->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 18px;">
                                {{ $prop->is_featured ? '⭐' : '☆' }}
                            </button>
                        </form>
                    </td>
                    <td style="font-size: 13px; color: #64748B;">{{ $prop->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.properties.edit', $prop->id) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                        <form action="{{ route('admin.properties.destroy', $prop->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this property permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; border-color: #EF4444; color: #DC2626;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 32px; color: #64748B;">No properties found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 24px;">
    {{ $properties->links() }}
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
                        <th>Icon</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr>
                            <td style="font-weight: 600;">{{ $cat->name }}</td>
                            <td>{{ $cat->icon ?? 'home' }}</td>
                            <td><span class="status-badge badge-{{ $cat->status }}">{{ ucfirst($cat->status) }}</span></td>
                            <td style="text-align: right;">
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #DC2626; border: none; background: none; cursor: pointer; font-weight: 600;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
