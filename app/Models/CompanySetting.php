<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'site_title',
        'tagline',
        'site_description',
        'logo_primary',
        'logo_alt',
        'favicon',
        'office_photo',
        'phone',
        'whatsapp',
        'email',
        'address',
        'instagram_url',
        'facebook_url',
        'youtube_url',
        'business_hours',
        'google_maps_embed_url',
        'google_maps_direction_url',
        'seo_meta_title',
        'seo_meta_description',
        'seo_social_image',
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'site_title' => 'PT Lovina North Bali Real Estate Agency',
            'tagline' => 'Your Trusted Property Partner in North Bali',
            'phone' => '+62 812 3456 7890',
            'whatsapp' => '+62 812 3456 7890',
            'email' => 'info@lovinanorthbali.com',
            'address' => 'Jl. Raya Kalibukbuk-Anturan, Lovina, Buleleng, Bali 81119, Indonesia',
            'google_maps_direction_url' => 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic',
            'business_hours' => json_encode([
                ['day' => 'Monday - Friday', 'hours' => '09:00 - 17:00'],
                ['day' => 'Saturday', 'hours' => '09:00 - 14:00'],
                ['day' => 'Sunday', 'hours' => 'Closed'],
            ]),
        ]);
    }
}
