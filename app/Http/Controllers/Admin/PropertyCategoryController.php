<?php

// Tatiana handles property categories here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_categories,name',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        PropertyCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = PropertyCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_categories,name,' . $id,
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function toggleStatus($id)
    {
        $category = PropertyCategory::findOrFail($id);
        $category->status = ($category->status === 'active') ? 'inactive' : 'active';
        $category->save();

        return redirect()->back()->with('success', 'Category "' . $category->name . '" status changed to ' . ucfirst($category->status) . '.');
    }

    public function destroy($id)
    {
        $category = PropertyCategory::findOrFail($id);

        $assignedCount = $category->properties()->count();
        if ($assignedCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete category "' . $category->name . '" because it has ' . $assignedCount . ' ' . Str::plural('property', $assignedCount) . ' assigned to it. Please deactivate the category or reassign its properties first.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
