<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\PropertyImage;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::all();
        $locations = Location::all();

        $query = Property::with(['category', 'location', 'images']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured == '1');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $properties = $query->latest()->paginate(10)->withQueryString();

        return view('admin.properties.index', compact('properties', 'categories', 'locations', 'settings'));
    }

    public function create()
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        return view('admin.properties.create', compact('categories', 'locations', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:property_categories,id',
            'location_id' => 'required|exists:locations,id',
            'price' => 'required|numeric|min:0',
            'ownership_type' => 'required|string',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'land_size' => 'required|integer|min:0',
            'building_size' => 'required|integer|min:0',
            'garage' => 'required|integer|min:0',
            'swimming_pool' => 'nullable|boolean',
            'electricity' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['swimming_pool'] = $request->has('swimming_pool');

        $property = Property::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('properties', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_cover' => ($index === 0),
                    'sort_order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.properties.index')->with('success', 'Property created successfully.');
    }

    public function edit($id)
    {
        $settings = CompanySetting::getSettings();
        $property = Property::with(['category', 'location', 'images'])->findOrFail($id);
        $categories = PropertyCategory::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        return view('admin.properties.edit', compact('property', 'categories', 'locations', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:property_categories,id',
            'location_id' => 'required|exists:locations,id',
            'price' => 'required|numeric|min:0',
            'ownership_type' => 'required|string',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'land_size' => 'required|integer|min:0',
            'building_size' => 'required|integer|min:0',
            'garage' => 'required|integer|min:0',
            'swimming_pool' => 'nullable|boolean',
            'electricity' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['swimming_pool'] = $request->has('swimming_pool');

        $property->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');
                $maxOrder = PropertyImage::where('property_id', $property->id)->max('sort_order') ?? 0;
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_cover' => false,
                    'sort_order' => $maxOrder + 1,
                ]);
            }
        }

        return redirect()->route('admin.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        foreach ($property->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function toggleFeatured($id)
    {
        $property = Property::findOrFail($id);
        $property->is_featured = !$property->is_featured;
        $property->save();

        return redirect()->back()->with('success', 'Featured status updated.');
    }

    public function deleteImage($imageId)
    {
        $img = PropertyImage::findOrFail($imageId);
        Storage::disk('public')->delete($img->image_path);
        $img->delete();

        return response()->json(['success' => true]);
    }
}
