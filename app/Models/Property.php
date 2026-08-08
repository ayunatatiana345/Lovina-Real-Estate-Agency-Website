<?php

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
        'bedrooms',
        'bathrooms',
        'land_size',
        'building_size',
        'garage',
        'swimming_pool',
        'electricity',
        'water_supply',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'swimming_pool' => 'boolean',
    ];

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

    public function getPrimaryImageUrlAttribute()
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover) {
            return asset('storage/' . $cover->image_path);
        }
        return asset('images/property-placeholder.jpg');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'property_id');
    }
}
