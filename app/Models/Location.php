<?php

// Tatiana handles locations here.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image', 'is_popular', 'status'];

    public function properties()
    {
        return $this->hasMany(Property::class, 'location_id');
    }

    public function getPropertyCountAttribute()
    {
        return $this->properties()->where('status', 'published')->count();
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }
            if (\Illuminate\Support\Str::startsWith($this->image, 'images/')) {
                return asset($this->image);
            }
            if (file_exists(public_path('storage/' . $this->image))) {
                return asset('storage/' . $this->image);
            }
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
            return asset('storage/' . $this->image);
        }
        return asset('images/sample-house-1.jpg');
    }

    public function getHasImageAttribute(): bool
    {
        if (empty($this->image)) {
            return false;
        }
        if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
            return true;
        }
        return file_exists(public_path('storage/' . $this->image)) || file_exists(public_path($this->image));
    }
}
