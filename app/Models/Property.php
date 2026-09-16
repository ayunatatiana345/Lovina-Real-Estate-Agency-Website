<?php

// Aragon handles the property model here.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'location_id',
        'price',
        'ownership_type',
        'status',
        'is_featured',
        'description',
        'short_description',
        'bedrooms',
        'bathrooms',
        'land_size',
        'building_size',
        'garage',
        'swimming_pool',
        'features',
        'electricity',
        'water_supply',
        'furnishing',
        'air_conditioning',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'swimming_pool' => 'boolean',
        'features' => 'array',
    ];

    public function hasFeature(string $featureKey): bool
    {
        if (is_array($this->features)) {
            return in_array($featureKey, $this->features);
        }

        if ($featureKey === 'swimming_pool') return (bool)$this->swimming_pool;
        if ($featureKey === 'garage') return (int)($this->garage ?? 0) > 0;
        if ($featureKey === 'furnishing') return !empty($this->furnishing) && $this->furnishing !== 'Unfurnished';
        if ($featureKey === 'air_conditioning') return !empty($this->air_conditioning) && strtolower($this->air_conditioning) !== 'no';
        if ($featureKey === 'water_supply') return !empty($this->water_supply);

        return false;
    }

    public function category()
    {
        return $this->belongsTo(PropertyCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id')->orderBy('sort_order', 'asc');
    }

    public function coverImage()
    {
        return $this->hasOne(PropertyImage::class, 'property_id')->where('is_cover', true);
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover) {
            return $cover->image_url;
        }
        return null;
    }

    public function getHasRealImageAttribute(): bool
    {
        return $this->images && $this->images->count() > 0;
    }

    public function getRealCoverImageUrlAttribute(): ?string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover && $cover->image_path) {
            if (file_exists(public_path('storage/' . $cover->image_path))) {
                return asset('storage/' . $cover->image_path);
            }
            if (file_exists(public_path('images/' . basename($cover->image_path)))) {
                return asset('images/' . basename($cover->image_path));
            }
            if (file_exists(public_path($cover->image_path))) {
                return asset($cover->image_path);
            }
        }
        return null;
    }

    public function getFormattedPriceAttribute(): string
    {
        $formatted = app(\App\Services\CurrencyService::class)->formatPropertyPrice($this->price);
        if ($this->category && in_array(strtolower($this->category->slug ?? $this->category->name ?? ''), ['rent', 'rental', 'rentals'])) {
            return $formatted . ' / month';
        }
        return $formatted;
    }

    public function getDisplayPriceAttribute(): string
    {
        return $this->getFormattedPriceAttribute();
    }

    public function getConvertedPriceAttribute(): ?float
    {
        if ($this->price === null) {
            return null;
        }
        $currency = app(\App\Services\CurrencyService::class)->getUserCurrency();
        return app(\App\Services\CurrencyService::class)->convert((float) $this->price, 'IDR', $currency);
    }

    public function getFormattedPriceAdminAttribute(): string
    {
        if ($this->price === null) {
            return 'Price on Request';
        }
        $formatted = 'Rp ' . number_format($this->price, 0, ',', '.');
        if ($this->category && in_array(strtolower($this->category->slug ?? $this->category->name ?? ''), ['rent', 'rental', 'rentals'])) {
            return $formatted . ' / month';
        }
        return $formatted;
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'property_id');
    }
}
