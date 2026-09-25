<?php

// Tara handles company settings here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function index(\App\Services\CurrencyService $currencyService)
    {
        $settings = CompanySetting::getSettings();
        $currencyMeta = $currencyService->getRateMetadata();
        return view('admin.settings.index', compact('settings', 'currencyMeta'));
    }

    public function update(Request $request, \App\Services\BrandingImageService $brandingService)
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
            'instagram_url' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'whatsapp_url' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'google_maps_embed_url' => 'nullable|string',
            'google_maps_direction_url' => 'nullable|string',
            'seo_meta_title' => 'nullable|string|max:255',
            'seo_meta_description' => 'nullable|string',
        ]);

        if ($request->has('b_hours_mf')) {
            $hours = [];
            $mf = $request->input('b_hours_mf', '09.00 – 12.00, 13.00 – 17.00');
            if (!empty($mf)) {
                $hours[] = ['day' => 'Monday – Friday', 'hours' => $mf];
            }
            $sat = $request->input('b_hours_sat');
            if (!empty($sat) && strtolower(trim($sat)) !== 'closed') {
                $hours[] = ['day' => 'Saturday', 'hours' => $sat];
            }
            $sun = $request->input('b_hours_sun');
            if (!empty($sun) && strtolower(trim($sun)) !== 'closed') {
                $hours[] = ['day' => 'Sunday', 'hours' => $sun];
            }
            $data['business_hours'] = json_encode($hours);
        }

        if ($request->hasFile('logo_primary')) {
            $processedPath = $brandingService->processAndStorePrimaryLogo($request->file('logo_primary'));
            if ($processedPath) {
                $brandingService->deleteOldBrandingImage($settings->logo_primary);
                $data['logo_primary'] = $processedPath;
            }
        }

        if ($request->hasFile('logo_alt')) {
            $processedPath = $brandingService->processAndStoreAltLogo($request->file('logo_alt'));
            if ($processedPath) {
                $brandingService->deleteOldBrandingImage($settings->logo_alt);
                $data['logo_alt'] = $processedPath;
            }
        }

        if ($request->hasFile('favicon')) {
            $processedPath = $brandingService->processAndStoreFavicon($request->file('favicon'));
            if ($processedPath) {
                $brandingService->deleteOldBrandingImage($settings->favicon);
                $data['favicon'] = $processedPath;
            }
        }

        if ($request->hasFile('seo_social_image')) {
            $processedPath = $brandingService->processAndStoreSocialImage($request->file('seo_social_image'));
            if ($processedPath) {
                $brandingService->deleteOldBrandingImage($settings->seo_social_image);
                $data['seo_social_image'] = $processedPath;
            }
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'Company settings updated successfully.');
    }
}
