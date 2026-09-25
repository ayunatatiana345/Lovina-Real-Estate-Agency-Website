<?php

// Tara handles company settings here.

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
            'logo_primary' => 'images/black logo lovina.png',
            'logo_alt' => 'images/white logo lovina.png',
            'favicon' => 'favicon.ico',
            'phone' => '0859 3666 6384',
            'whatsapp' => '0859 3666 6384',
            'email' => 'lovinanorthbaliagency2023@gmail.com',
            'address' => 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali',
            'facebook_url' => 'https://www.facebook.com/people/Lovina-North-Bali-Real-Estate-Agency/61552694420689/',
            'youtube_url' => 'https://www.youtube.com/@LOVINANORTHBALIREALESTATEAGENC',
            'google_maps_embed_url' => 'https://maps.google.com/maps?q=Lovina+North+Bali+Real+Estate+Agency,+Jl.+Desa+Kalibukbuk-Anturan,+Kalibukbuk,+Kec.+Buleleng,+Kabupaten+Buleleng,+Bali+81119&t=&z=16&ie=UTF8&iwloc=&output=embed',
            'google_maps_direction_url' => 'https://maps.app.goo.gl/scYXTttd854dwuWc9?g_st=ic',
            'business_hours' => json_encode([
                ['day' => 'Monday – Friday', 'hours' => '09.00 – 12.00, 13.00 – 17.00'],
            ]),
        ]);
    }

    public function getLogoPrimaryUrlAttribute(): string
    {
        if (!empty($this->logo_primary)) {
            if (\Illuminate\Support\Str::startsWith($this->logo_primary, ['http://', 'https://'])) {
                return $this->logo_primary;
            }
            if (\Illuminate\Support\Str::startsWith($this->logo_primary, 'images/')) {
                return asset($this->logo_primary);
            }
            if (file_exists(public_path('storage/' . $this->logo_primary))) {
                return asset('storage/' . $this->logo_primary);
            }
            return asset($this->logo_primary);
        }
        return asset('images/black logo lovina.png');
    }

    public function getLogoAltUrlAttribute(): string
    {
        if (!empty($this->logo_alt)) {
            if (\Illuminate\Support\Str::startsWith($this->logo_alt, ['http://', 'https://'])) {
                return $this->logo_alt;
            }
            if (\Illuminate\Support\Str::startsWith($this->logo_alt, 'images/')) {
                return asset($this->logo_alt);
            }
            if (file_exists(public_path('storage/' . $this->logo_alt))) {
                return asset('storage/' . $this->logo_alt);
            }
            return asset($this->logo_alt);
        }
        return asset('images/white logo lovina.png');
    }

    public function getFaviconUrlAttribute(): string
    {
        if (!empty($this->favicon)) {
            if (\Illuminate\Support\Str::startsWith($this->favicon, ['http://', 'https://'])) {
                return $this->favicon;
            }
            if (\Illuminate\Support\Str::startsWith($this->favicon, 'images/')) {
                return asset($this->favicon);
            }
            if (file_exists(public_path('storage/' . $this->favicon))) {
                return asset('storage/' . $this->favicon);
            }
            return asset($this->favicon);
        }
        return asset('favicon.ico');
    }

    public function getCleanWhatsappAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp ?? '085936666384');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone ?: '6285936666384';
    }

    public function getCleanPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $this->phone ?? '085936666384');
        return $phone ?: '085936666384';
    }

    public function getWhatsappUrlAttribute(): string
    {
        $phone = $this->clean_whatsapp;
        $defaultMsg = rawurlencode("Hello, I would like to get more information about properties in North Bali.");
        return "https://wa.me/{$phone}?text={$defaultMsg}";
    }

    public function getGoogleMapsEmbedSrcAttribute()
    {
        if (!empty($this->google_maps_embed_url)) {
            if (preg_match('/src="([^"]+)"/', $this->google_maps_embed_url, $match)) {
                return $match[1];
            }
            return $this->google_maps_embed_url;
        }

        $query = urlencode(($this->company_name ?? 'Lovina North Bali Real Estate Agency') . ', ' . ($this->address ?? 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali'));
        return "https://maps.google.com/maps?q={$query}&t=&z=16&ie=UTF8&iwloc=&output=embed";
    }

    public function getFormattedBusinessHoursAttribute(): array
    {
        $raw = $this->business_hours;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $raw = $decoded;
            }
        }

        if (is_array($raw)) {
            $formatted = [];
            // Sequential array with 'day' and 'hours'
            if (isset($raw[0]) && is_array($raw[0])) {
                foreach ($raw as $item) {
                    if (!empty($item['day']) && !empty($item['hours'])) {
                        $formatted[] = [
                            'day' => $item['day'],
                            'hours' => $item['hours'],
                        ];
                    }
                }
                if (!empty($formatted)) {
                    return $formatted;
                }
            }

            // Associative key-value pair
            $dayLabels = [
                'monday_friday' => 'Monday – Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];
            foreach ($raw as $key => $val) {
                if (is_string($val) && !empty($val)) {
                    $day = $dayLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                    $formatted[] = [
                        'day' => $day,
                        'hours' => $val,
                    ];
                }
            }
            if (!empty($formatted)) {
                return $formatted;
            }
        }

        return [
            ['day' => 'Monday – Friday', 'hours' => '09.00 – 12.00, 13.00 – 17.00'],
        ];
    }

    public function getBusinessHoursSummaryAttribute(): string
    {
        $hours = $this->formatted_business_hours;
        $parts = [];
        foreach ($hours as $h) {
            $parts[] = "{$h['day']}: {$h['hours']}";
        }
        return implode(' | ', $parts);
    }
}
