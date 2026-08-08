<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\CompanySetting;
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

        $locations = $query->latest()->paginate(10)->withQueryString();

        return view('admin.locations.index', compact('locations', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_popular'] = $request->has('is_popular');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        Location::create($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Location created successfully.');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id,
            'description' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_popular'] = $request->has('is_popular');

        if ($request->hasFile('image')) {
            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        $location->update($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        if ($location->image) {
            Storage::disk('public')->delete($location->image);
        }
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted successfully.');
    }

    public function togglePopular($id)
    {
        $location = Location::findOrFail($id);
        $location->is_popular = !$location->is_popular;
        $location->save();

        return redirect()->back()->with('success', 'Popular status toggled.');
    }
}
