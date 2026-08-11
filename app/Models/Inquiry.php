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
        'source',
        'status',
        'admin_notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(InquiryStatusLog::class, 'inquiry_id')->orderBy('changed_at', 'asc');
    }
}
