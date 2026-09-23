<?php

// Aragon handles property data here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\PropertyImage;
use App\Models\CompanySetting;
use App\Models\CmsContent;
use App\Services\PropertyImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    protected PropertyImageService $imageService;

    public function __construct(PropertyImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::orderBy('name', 'asc')->get();
        $locations = Location::orderBy('name', 'asc')->get();

        $query = Property::with(['category', 'categories', 'location', 'images']);

        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->where(function ($q) use ($catId) {
                $q->where('category_id', $catId)
                  ->orWhereHas('categories', function ($cq) use ($catId) {
                      $cq->where('property_categories.id', $catId);
                  });
            });
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

        $properties = $query->latest()->paginate(30)->withQueryString();

        return view('admin.properties.index', compact('properties', 'categories', 'locations', 'settings'));
    }

    public function create()
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::where('status', 'active')->orderBy('name', 'asc')->get();
        $locations = Location::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('admin.properties.create', compact('categories', 'locations', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:properties,slug',
            'category_id' => 'required|exists:property_categories,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:property_categories,id',
            'location_id' => 'required|exists:locations,id',
            'price' => 'nullable|numeric|min:0',
            'ownership_type' => 'required|string',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'land_size' => 'nullable|integer|min:0',
            'building_size' => 'nullable|integer|min:0',
            'garage' => 'nullable|integer|min:0',
            'swimming_pool' => 'nullable|boolean',
            'features' => 'nullable|array',
            'electricity' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'furnishing' => 'nullable|string',
            'air_conditioning' => 'nullable|string',
            'cover_index' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $validated['slug'] = $baseSlug . '-' . Str::random(4);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $isFeatured = $request->has('is_featured');
        if ($isFeatured) {
            $featuredCount = Property::where('is_featured', true)->count();
            if ($featuredCount >= 6) {
                return redirect()->back()->withInput()->withErrors([
                    'is_featured' => 'Maximum of 6 featured properties reached. Please unfeature an existing property before selecting another.'
                ]);
            }
        }
        $validated['is_featured'] = $isFeatured;
        $features = $request->input('features', []);
        $validated['swimming_pool'] = $request->has('swimming_pool') || in_array('swimming_pool', $features);
        $validated['features'] = $features;

        DB::transaction(function () use ($validated, $request) {
            $property = Property::create($validated);
            $catIds = $request->input('category_ids', [$property->category_id]);
            if (!empty($catIds)) {
                $property->categories()->sync($catIds);
            }

            if ($request->hasFile('images')) {
                $chosenCoverIndex = (int)$request->input('cover_index', 0);
                $seq = 0;
                foreach ($request->file('images') as $index => $file) {
                    if ($file->isValid()) {
                        $seq++;
                        $processed = $this->imageService->processAndStore($file, $property, $seq);
                        if ($processed) {
                            PropertyImage::create([
                                'property_id' => $property->id,
                                'image_path' => $processed['path'],
                                'image_alt' => $processed['alt'],
                                'is_cover' => ($index === $chosenCoverIndex),
                                'sort_order' => $seq,
                            ]);
                        }
                    }
                }

                // If none was marked as cover, make the first image cover
                if (!PropertyImage::where('property_id', $property->id)->where('is_cover', true)->exists()) {
                    $first = PropertyImage::where('property_id', $property->id)->first();
                    if ($first) {
                        $first->update(['is_cover' => true]);
                    }
                }
            }
        });

        // Sync cms_contents selected_ids
        $currentFeaturedIds = Property::where('is_featured', true)->pluck('id')->toArray();
        $featuredContent = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
        ]);
        $featuredContent['selected_ids'] = $currentFeaturedIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], ['content' => $featuredContent]);

        return redirect()->route('admin.properties.index')->with('success', 'Property created successfully.');
    }

    public function edit($id)
    {
        $settings = CompanySetting::getSettings();
        $property = Property::with(['category', 'categories', 'location', 'images'])->findOrFail($id);
        $categories = PropertyCategory::where('status', 'active')
            ->orWhere('id', $property->category_id)
            ->orderBy('name', 'asc')
            ->get();
        $locations = Location::where('status', 'active')
            ->orWhere('id', $property->location_id)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.properties.edit', compact('property', 'categories', 'locations', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:properties,slug,' . $property->id,
            'category_id' => 'required|exists:property_categories,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:property_categories,id',
            'location_id' => 'required|exists:locations,id',
            'price' => 'nullable|numeric|min:0',
            'ownership_type' => 'required|string',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'land_size' => 'nullable|integer|min:0',
            'building_size' => 'nullable|integer|min:0',
            'garage' => 'nullable|integer|min:0',
            'swimming_pool' => 'nullable|boolean',
            'features' => 'nullable|array',
            'electricity' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'furnishing' => 'nullable|string',
            'air_conditioning' => 'nullable|string',
            'new_cover_index' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = $property->slug ?: (Str::slug($validated['name']) . '-' . Str::random(4));
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $isFeatured = $request->has('is_featured');
        if ($isFeatured && !$property->is_featured) {
            $featuredCount = Property::where('is_featured', true)->where('id', '!=', $property->id)->count();
            if ($featuredCount >= 6) {
                return redirect()->back()->withInput()->withErrors([
                    'is_featured' => 'Maximum of 6 featured properties reached. Please unfeature an existing property before selecting another.'
                ]);
            }
        }
        $validated['is_featured'] = $isFeatured;
        $features = $request->input('features', []);
        $validated['swimming_pool'] = $request->has('swimming_pool') || in_array('swimming_pool', $features);
        $validated['features'] = $features;

        DB::transaction(function () use ($property, $validated, $request) {
            $property->update($validated);
            $catIds = $request->input('category_ids', [$property->category_id]);
            if (!empty($catIds)) {
                $property->categories()->sync($catIds);
            }

            if ($request->hasFile('images')) {
                $hasCover = PropertyImage::where('property_id', $property->id)->where('is_cover', true)->exists();
                $newCoverIndex = ($request->filled('new_cover_index') && $request->new_cover_index !== '') ? (int)$request->new_cover_index : null;

                if ($newCoverIndex !== null) {
                    PropertyImage::where('property_id', $property->id)->update(['is_cover' => false]);
                    $hasCover = false;
                }

                $maxOrder = PropertyImage::where('property_id', $property->id)->max('sort_order') ?? 0;
                $existingCount = PropertyImage::where('property_id', $property->id)->count();

                foreach ($request->file('images') as $index => $file) {
                    if ($file->isValid()) {
                        $sequenceNumber = $existingCount + $index + 1;
                        $processed = $this->imageService->processAndStore($file, $property, $sequenceNumber);
                        if ($processed) {
                            $isCover = ($newCoverIndex !== null && $index === $newCoverIndex) || (!$hasCover && $index === 0);
                            PropertyImage::create([
                                'property_id' => $property->id,
                                'image_path' => $processed['path'],
                                'image_alt' => $processed['alt'],
                                'is_cover' => $isCover,
                                'sort_order' => ++$maxOrder,
                            ]);
                            if ($isCover) {
                                $hasCover = true;
                            }
                        }
                    }
                }

                // Ensure at least one image is cover if images exist
                if (!PropertyImage::where('property_id', $property->id)->where('is_cover', true)->exists()) {
                    $firstImg = PropertyImage::where('property_id', $property->id)->orderBy('sort_order', 'asc')->first();
                    if ($firstImg) {
                        $firstImg->update(['is_cover' => true]);
                    }
                }
            }
        });

        // Sync cms_contents selected_ids
        $currentFeaturedIds = Property::where('is_featured', true)->pluck('id')->toArray();
        $featuredContent = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
        ]);
        $featuredContent['selected_ids'] = $currentFeaturedIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], ['content' => $featuredContent]);

        return redirect()->route('admin.properties.edit', $property->id)->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $property = Property::findOrFail($id);
            foreach ($property->images as $img) {
                Storage::disk('public')->delete($img->image_path);
            }
            $property->delete();
        });

        // Sync cms_contents selected_ids
        $currentFeaturedIds = Property::where('is_featured', true)->pluck('id')->toArray();
        $featuredContent = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
        ]);
        $featuredContent['selected_ids'] = $currentFeaturedIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], ['content' => $featuredContent]);

        return redirect()->route('admin.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function toggleFeatured(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if (!$property->is_featured) {
            $featuredCount = Property::where('is_featured', true)->count();
            if ($featuredCount >= 6) {
                $errorMsg = 'Maximum of 6 featured properties reached. Please unfeature an existing property before selecting another.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return redirect()->back()->with('error', $errorMsg);
            }
            $property->is_featured = true;
            $msg = 'Featured property updated successfully.';
        } else {
            $property->is_featured = false;
            $msg = 'Featured property updated successfully.';
        }

        $property->save();

        // Synchronize cms_contents selected_ids
        $currentFeaturedIds = Property::where('is_featured', true)->pluck('id')->toArray();
        $featuredContent = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
        ]);
        $featuredContent['selected_ids'] = $currentFeaturedIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], ['content' => $featuredContent]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'is_featured' => (bool)$property->is_featured,
                'featured_count' => count($currentFeaturedIds)
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function deleteImage($imageId)
    {
        return DB::transaction(function () use ($imageId) {
            $img = PropertyImage::findOrFail($imageId);
            $propertyId = $img->property_id;
            $wasCover = $img->is_cover;
            Storage::disk('public')->delete($img->image_path);
            $img->delete();

            if ($wasCover) {
                $nextCover = PropertyImage::where('property_id', $propertyId)->orderBy('sort_order', 'asc')->first();
                if ($nextCover) {
                    $nextCover->update(['is_cover' => true]);
                }
            }

            return response()->json(['success' => true]);
        });
    }

    public function setCoverImage($imageId)
    {
        return DB::transaction(function () use ($imageId) {
            $img = PropertyImage::findOrFail($imageId);
            PropertyImage::where('property_id', $img->property_id)->update(['is_cover' => false]);
            $img->update(['is_cover' => true]);

            return response()->json(['success' => true]);
        });
    }

    public function reorderImages(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $order = $request->input('order', []);
            if (is_array($order)) {
                foreach ($order as $index => $imgId) {
                    PropertyImage::where('id', $imgId)->where('property_id', $id)->update(['sort_order' => $index + 1]);
                }
            }
            return response()->json(['success' => true]);
        });
    }
}
