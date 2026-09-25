<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'featured_image',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
        'author_name',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $minutes = max(1, (int) ceil($wordCount / 200));
        return $minutes . ' min read';
    }

    public function getSeoTitleAttribute()
    {
        return !empty($this->meta_title) ? $this->meta_title : $this->title;
    }

    public function getSeoDescriptionAttribute()
    {
        return !empty($this->meta_description) ? $this->meta_description : Str::limit(strip_tags($this->excerpt ?? $this->content), 160);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return asset('images/sample-article.jpg');
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://'])) {
            return $this->featured_image;
        }

        if (Str::startsWith($this->featured_image, 'images/')) {
            if (file_exists(public_path($this->featured_image))) {
                return asset($this->featured_image);
            }
            return asset('images/sample-article.jpg');
        }

        if (file_exists(public_path('storage/' . $this->featured_image))) {
            return asset('storage/' . $this->featured_image);
        }

        return asset('images/sample-article.jpg');
    }

    public function getViewsCountAttribute()
    {
        if (array_key_exists('views_count', $this->attributes)) {
            return (int) $this->attributes['views_count'];
        }
        return $this->views()->count();
    }
}
