<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;
use App\Mail\InquiryReplyMail;
use Illuminate\Support\Facades\Mail;

$inquiry = Inquiry::first();
if (!$inquiry) {
    echo "No inquiry found.\n";
    exit(1);
}

echo "Testing Mail::to('{$inquiry->email}')->send() with Inquiry ID {$inquiry->id}...\n";

try {
    Mail::to($inquiry->email, $inquiry->customer_name)->send(
        new InquiryReplyMail($inquiry, "Test Subject: " . $inquiry->default_reply_subject, "This is a test reply content.")
    );
    echo "SUCCESS: Email sent without errors!\n";
} catch (\Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
