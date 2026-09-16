<?php

namespace App\Mail;

use App\Models\Inquiry;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class InquiryReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Inquiry $inquiry;
    public string $emailSubject;
    public string $emailContent;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct(Inquiry $inquiry, string $emailSubject, string $emailContent)
    {
        $this->inquiry = $inquiry;
        $this->emailSubject = $emailSubject;
        $this->emailContent = $emailContent;
        $this->settings = CompanySetting::getSettings();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'hello@lovinanorthbali.com');
        $fromName = $this->settings->site_name ?? 'PT Lovina North Bali Real Estate Agency';

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry-reply',
            with: [
                'inquiry' => $this->inquiry,
                'emailSubject' => $this->emailSubject,
                'emailContent' => $this->emailContent,
                'settings' => $this->settings,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
