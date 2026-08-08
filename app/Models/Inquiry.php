<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'property_id',
        'subject',
        'message',
        'status',
        'admin_notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
