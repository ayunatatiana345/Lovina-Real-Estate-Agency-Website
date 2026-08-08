<?php

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
}
