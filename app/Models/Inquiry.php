<?php

// Tatiana handles inquiry data here.

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

    /**
     * Normalize customer phone number for WhatsApp wa.me links.
     */
    public function getWhatsappNumberAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', (string)$this->phone);

        if (empty($digits)) {
            return null;
        }

        // Handle double-zero international prefix (e.g. 0062... or 001...)
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        // If local Indonesian format (starts with 08...), convert to 628...
        elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // Valid international WhatsApp number requires at least 7 digits
        if (strlen($digits) < 7) {
            return null;
        }

        return $digits;
    }

    /**
     * Generate dynamic contextual WhatsApp message.
     */
    public function getWhatsappMessageAttribute(): string
    {
        $name = trim($this->customer_name ?: 'there');
        $firstName = explode(' ', $name)[0];

        if ($this->property) {
            return "Hello {$firstName}, thank you for your inquiry regarding {$this->property->name} at PT Lovina North Bali Real Estate Agency. We would be delighted to assist you with further details and arrange a viewing. Please let us know how we can help.";
        }

        return "Hello {$firstName}, thank you for reaching out to PT Lovina North Bali Real Estate Agency. We would be happy to assist you with your property inquiry. Please let us know how we can help.";
    }

    /**
     * Generate complete WhatsApp URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $number = $this->whatsapp_number;
        if (!$number) {
            return null;
        }

        $text = rawurlencode($this->whatsapp_message);
        return "https://wa.me/{$number}?text={$text}";
    }

    /**
     * Generate default email reply subject.
     */
    public function getDefaultReplySubjectAttribute(): string
    {
        if ($this->property) {
            return "Re: Your Inquiry Regarding " . $this->property->name;
        }

        if (!empty($this->subject)) {
            return str_starts_with(strtolower($this->subject), 're:') ? $this->subject : "Re: " . $this->subject;
        }

        return "Re: Your Property Inquiry - PT Lovina North Bali";
    }

    /**
     * Generate default starter email body for admin reply form.
     */
    public function getDefaultReplyMessageAttribute(): string
    {
        $name = trim($this->customer_name ?: 'Valued Client');
        $firstName = explode(' ', $name)[0];

        $propertyContext = $this->property 
            ? "regarding {$this->property->name}" 
            : "regarding our real estate services in Lovina, Bali";

        return "Dear {$firstName},\n\nThank you for reaching out to PT Lovina North Bali Real Estate Agency {$propertyContext}.\n\nWe have received your message:\n\"" . trim($this->message) . "\"\n\nWe are very pleased to assist you with your property requirements. Please let us know if you would like additional specifications, brochures, or to schedule a private consultation / site viewing.\n\nWarm regards,\nPT Lovina North Bali Real Estate Agency\nJl. Raya Kalibukbuk-Anturan, Lovina, Bali\nhttps://lovinanorthbali.com";
    }

    /**
     * Generate mailto link for desktop email client.
     */
    public function getMailtoUrlAttribute(): ?string
    {
        if (empty($this->email)) {
            return null;
        }

        $subject = rawurlencode($this->default_reply_subject);
        $body = rawurlencode($this->default_reply_message);

        return "mailto:{$this->email}?subject={$subject}&body={$body}";
    }
}

