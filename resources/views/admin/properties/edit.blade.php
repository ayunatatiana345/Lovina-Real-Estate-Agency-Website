@extends('layouts.admin')

@section('title', 'Edit Property')
@section('page_title', 'Edit Property: ' . $property->name)

@section('content')
<div style="max-width: 900px;">
    <form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1E3A8A; margin-bottom: 20px;">1. General Information</h3>

            <div class="form-group">
                <label class="form-label" for="name">Property Name *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $property->name) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $property->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="location_id">Location *</label>
                    <select name="location_id" id="location_id" class="form-select" required>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ $property->location_id == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="price">Asking Price (USD $) *</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $property->price) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="ownership_type">Ownership Title *</label>
                    <select name="ownership_type" id="ownership_type" class="form-select" required>
                        <option value="Freehold" {{ $property->ownership_type == 'Freehold' ? 'selected' : '' }}>Freehold (Hak Milik)</option>
                        <option value="Leasehold" {{ $property->ownership_type == 'Leasehold' ? 'selected' : '' }}>Leasehold (Hak Sewa)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Publication Status *</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="published" {{ $property->status == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $property->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" {{ $property->is_featured ? 'checked' : '' }}>
                    <span>⭐ Mark as Featured Property</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Detailed Description</label>
                <textarea name="description" id="description" class="form-control" style="min-height: 140px;">{{ old('description', $property->description) }}</textarea>
            </div>
        </div>

        <!-- Specifications Tab -->
        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1E3A8A; margin-bottom: 20px;">2. Specifications</h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="bedrooms">Bedrooms</label>
                    <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bathrooms">Bathrooms</label>
                    <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', $property->bathrooms) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="land_size">Land Size (m²)</label>
                    <input type="number" name="land_size" id="land_size" class="form-control" value="{{ old('land_size', $property->land_size) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="building_size">Building Size (m²)</label>
                    <input type="number" name="building_size" id="building_size" class="form-control" value="{{ old('building_size', $property->building_size) }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="garage">Garage (Cars)</label>
                    <input type="number" name="garage" id="garage" class="form-control" value="{{ old('garage', $property->garage) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="electricity">Electricity (VA)</label>
                    <input type="text" name="electricity" id="electricity" class="form-control" value="{{ old('electricity', $property->electricity) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="water_supply">Water Supply</label>
                    <input type="text" name="water_supply" id="water_supply" class="form-control" value="{{ old('water_supply', $property->water_supply) }}">
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="swimming_pool" value="1" {{ $property->swimming_pool ? 'checked' : '' }}>
                    <span>🏊‍♂️ Includes Private Swimming Pool</span>
                </label>
            </div>
        </div>

        <!-- Gallery Tab -->
        <div class="admin-card" style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1E3A8A; margin-bottom: 20px;">3. Image Gallery</h3>

            <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px;">
                @foreach($property->images as $img)
                    <div style="position: relative; width: 120px; height: 90px; border-radius: 6px; overflow: hidden; border: 1px solid #CBD5E1;">
                        <img src="{{ asset('storage/' . $img->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @if($img->is_cover)
                            <span style="position: absolute; top: 4px; left: 4px; background-color: #C7A86D; color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">Cover</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label" for="images">Add More Photos</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">Update Property</button>
        </div>
    </form>
</div>
@endsection
