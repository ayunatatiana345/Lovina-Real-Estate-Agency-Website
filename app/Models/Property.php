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

    public function categories()
    {
        return $this->belongsToMany(PropertyCategory::class, 'category_property', 'property_id', 'category_id')->withTimestamps();
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
            return asset('storage/' . $cover->image_path);
        }
        return null;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->name === 'House/Homestay in Kalibukbuk') {
            return 'IDR 4 billion + IDR 3 billion';
        }
        if ($this->name === '2 plots of land 50 meter from the beach Banjar') {
            return 'IDR 100 million per are separately';
        }
        if ($this->name === 'Beautiful Sumatra House') {
            return '80,000 Euro / 135,000 Euro';
        }
        if ($this->price === null) {
            return 'Price on Request';
        }

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
        if ($this->name === 'House/Homestay in Kalibukbuk') {
            return 'IDR 4 billion + IDR 3 billion';
        }
        if ($this->name === '2 plots of land 50 meter from the beach Banjar') {
            return 'IDR 100 million per are separately';
        }
        if ($this->name === 'Beautiful Sumatra House') {
            return '80,000 Euro / 135,000 Euro';
        }
        if ($this->price === null) {
            return 'Price on Request';
        }
        $formatted = 'Rp ' . number_format($this->price, 0, ',', '.');
        if ($this->category && in_array(strtolower($this->category->slug ?? $this->category->name ?? ''), ['rent', 'rental', 'rentals'])) {
            return $formatted . ' / month';
        }
        return $formatted;
    }

    public function getCategoryBadgeAttribute(): string
    {
        $cats = $this->relationLoaded('categories') ? $this->categories : $this->categories()->get();

        if ($cats->isNotEmpty()) {
            // Canonical sort order: Land (1), Restaurant (2), Bar (3), Hotel (4), House (5), Villa (6), Others (7)
            $sorted = $cats->sortBy(function ($c) {
                $slug = strtolower($c->slug ?? '');
                $name = strtolower($c->name ?? '');
                if ($slug === 'land' || str_contains($name, 'land')) return 1;
                if ($slug === 'restaurant' || str_contains($name, 'restaurant')) return 2;
                if ($slug === 'bar' || str_contains($name, 'bar')) return 3;
                if ($slug === 'hotel' || str_contains($name, 'hotel')) return 4;
                if ($slug === 'house' || str_contains($name, 'house')) return 5;
                if ($slug === 'villa' || str_contains($name, 'villa')) return 6;
                return 7;
            });

            $names = $sorted->map(function ($c) {
                return $c->name;
            })->unique()->values();

            if ($names->isNotEmpty()) {
                return $names->implode(' / ');
            }
        }

        if ($this->category) {
            return $this->category->name;
        }

        return 'Villa';
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'property_id');
    }
}
