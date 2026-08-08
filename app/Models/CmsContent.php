<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    use HasFactory;

    protected $fillable = ['page', 'section_key', 'content'];

    protected $casts = [
        'content' => 'array',
    ];

    public static function getContent($page, $sectionKey, $default = [])
    {
        $item = self::where('page', $page)->where('section_key', $sectionKey)->first();
        return $item ? $item->content : $default;
    }
}
