<?php

// Aragon handles property images here.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'image_path', 'is_cover', 'sort_order'];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            if (file_exists(public_path('storage/' . $this->image_path))) {
                return asset('storage/' . $this->image_path);
            }
            if (file_exists(public_path('images/' . basename($this->image_path)))) {
                return asset('images/' . basename($this->image_path));
            }
            if (file_exists(public_path($this->image_path))) {
                return asset($this->image_path);
            }
        }
        return null;
    }
}
