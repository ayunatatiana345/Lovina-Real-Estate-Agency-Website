<?php

// Tatiana handles property categories here.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'status'];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'category_property', 'category_id', 'property_id')->withTimestamps();
    }

    public function primaryProperties()
    {
        return $this->hasMany(Property::class, 'category_id');
    }
}
