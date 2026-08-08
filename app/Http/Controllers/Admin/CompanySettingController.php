<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = CompanySetting::getSettings();

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'site_title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'phone' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'instagram_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'google_maps_embed_url' => 'nullable|string',
            'google_maps_direction_url' => 'nullable|url',
            'seo_meta_title' => 'nullable|string|max:255',
            'seo_meta_description' => 'nullable|string',
        ]);

        if ($request->hasFile('logo_primary')) {
            if ($settings->logo_primary) {
                Storage::disk('public')->delete($settings->logo_primary);
            }
            $data['logo_primary'] = $request->file('logo_primary')->store('branding', 'public');
        }

        if ($request->hasFile('office_photo')) {
            if ($settings->office_photo) {
                Storage::disk('public')->delete($settings->office_photo);
            }
            $data['office_photo'] = $request->file('office_photo')->store('branding', 'public');
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'Company settings updated successfully.');
    }
}
