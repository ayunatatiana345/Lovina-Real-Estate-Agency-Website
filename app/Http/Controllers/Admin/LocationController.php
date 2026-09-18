<?php

// Tatiana handles locations here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\CompanySetting;
use App\Models\CmsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        
        $query = Location::withCount(['properties']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $locations = $query->latest()->get();

        return view('admin.locations.index', compact('locations', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Location::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$counter);
        }
        $validated['slug'] = $slug;
        $validated['is_popular'] = $request->has('is_popular');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        Location::create($validated);

        // Synchronize cms_contents selected_ids
        $currentPopularIds = Location::where('is_popular', true)->pluck('id')->toArray();
        $locationsContent = CmsContent::getContent('homepage', 'locations', [
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
        ]);
        $locationsContent['selected_ids'] = $currentPopularIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], ['content' => $locationsContent]);

        return redirect()->route('admin.locations.index')->with('success', 'Location created successfully.');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id,
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Location::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . (++$counter);
        }
        $validated['slug'] = $slug;
        $validated['is_popular'] = $request->has('is_popular');

        if ($request->hasFile('image')) {
            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        $location->update($validated);

        // Synchronize cms_contents selected_ids
        $currentPopularIds = Location::where('is_popular', true)->pluck('id')->toArray();
        $locationsContent = CmsContent::getContent('homepage', 'locations', [
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
        ]);
        $locationsContent['selected_ids'] = $currentPopularIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], ['content' => $locationsContent]);

        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        $assignedPropsCount = $location->properties()->count();
        if ($assignedPropsCount > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location "' . $location->name . '" because it still has ' . $assignedPropsCount . ' ' . Str::plural('property', $assignedPropsCount) . ' assigned to it. Please reassign or remove the properties first to protect data integrity.');
        }

        if ($location->image) {
            Storage::disk('public')->delete($location->image);
        }
        $location->delete();

        // Synchronize cms_contents selected_ids
        $currentPopularIds = Location::where('is_popular', true)->pluck('id')->toArray();
        $locationsContent = CmsContent::getContent('homepage', 'locations', [
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
        ]);
        $locationsContent['selected_ids'] = $currentPopularIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], ['content' => $locationsContent]);

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted successfully.');
    }

    public function togglePopular(Request $request, $id)
    {
        $location = Location::findOrFail($id);
        $location->is_popular = !$location->is_popular;
        $location->save();

        // Synchronize cms_contents selected_ids
        $currentPopularIds = Location::where('is_popular', true)->pluck('id')->toArray();
        $locationsContent = CmsContent::getContent('homepage', 'locations', [
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
        ]);
        $locationsContent['selected_ids'] = $currentPopularIds;
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], ['content' => $locationsContent]);

        $msg = 'Popular location updated successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'is_popular' => (bool)$location->is_popular,
                'popular_count' => count($currentPopularIds)
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
