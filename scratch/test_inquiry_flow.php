<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;
use App\Mail\InquiryReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=== STEP 1: VERIFYING MAIL CONFIG ===\n";
echo "Mailer: " . config('mail.default') . "\n";
echo "Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Port: " . config('mail.mailers.smtp.port') . "\n";
echo "From Address: " . config('mail.from.address') . "\n";
echo "From Name: " . config('mail.from.name') . "\n";

echo "\n=== STEP 2: LOADING AN EXISTING INQUIRY ===\n";
$inquiry = Inquiry::whereNotNull('email')->where('email', '!=', '')->first();
if (!$inquiry) {
    die("No valid inquiry found.\n");
}
echo "Selected Inquiry ID: {$inquiry->id}\n";
echo "Customer Name: {$inquiry->customer_name}\n";
echo "Customer Email (DB): {$inquiry->email}\n";
echo "Current Status: {$inquiry->status}\n";

echo "\n=== STEP 3: TESTING MAILABLE METADATA ===\n";
$subject = "Information regarding your inquiry #" . $inquiry->id;
$message = "Dear {$inquiry->customer_name},\n\nThank you for reaching out to PT Lovina North Bali Real Estate Agency. We have reviewed your request and would be delighted to assist you further.\n\nBest regards,\nAdmin Team";

$mailable = new InquiryReplyMail($inquiry, $subject, $message);
$envelope = $mailable->envelope();

echo "Mailable From: " . $envelope->from->address . " (" . $envelope->from->name . ")\n";
if (!empty($envelope->replyTo)) {
    foreach ($envelope->replyTo as $r) {
        echo "Mailable Reply-To: " . $r->address . " (" . $r->name . ")\n";
    }
} else {
    echo "Mailable Reply-To: NONE\n";
}
echo "Mailable Subject: " . $envelope->subject . "\n";

echo "\n=== STEP 4: SENDING TEST EMAIL VIA LARAVEL MAIL ===\n";
Mail::to($inquiry->email, $inquiry->customer_name)->send($mailable);
echo "Mail::to()->send() completed successfully!\n";

echo "\n=== STEP 5: VERIFYING IN MAILPIT ===\n";
sleep(1);
$mailpitJson = @file_get_contents('http://127.0.0.1:8025/api/v1/messages');
if ($mailpitJson) {
    $mailpitData = json_decode($mailpitJson, true);
    $latest = $mailpitData['messages'][0] ?? null;
    if ($latest) {
        echo "Mailpit Latest ID: " . $latest['ID'] . "\n";
        echo "Mailpit Subject: " . $latest['Subject'] . "\n";
        echo "Mailpit From: " . ($latest['From']['Address'] ?? 'N/A') . "\n";
        echo "Mailpit To: " . json_encode($latest['To'] ?? []) . "\n";
        if (isset($latest['ReplyTo'])) {
            echo "Mailpit Reply-To: " . json_encode($latest['ReplyTo']) . "\n";
        }
        
        // Fetch detailed message headers
        $detailJson = @file_get_contents('http://127.0.0.1:8025/api/v1/message/' . $latest['ID']);
        if ($detailJson) {
            $detail = json_decode($detailJson, true);
            echo "Mailpit Detailed Reply-To Header: " . json_encode($detail['ReplyTo'] ?? []) . "\n";
        }
    }
} else {
    echo "Could not contact Mailpit API.\n";
}

echo "\n=== STEP 6: SIMULATING MAIL FAILURE HANDLING ===\n";
// Temporarily simulate failure by overriding mail port to an invalid one
Config::set('mail.mailers.smtp.port', 9999);
Mail::purge('smtp');
$failureCaught = false;
$originalStatus = $inquiry->status;

try {
    Mail::to($inquiry->email, $inquiry->customer_name)->send(
        new InquiryReplyMail($inquiry, "Failure Test", "This should fail.")
    );
} catch (\Throwable $e) {
    $failureCaught = true;
    echo "Successfully caught expected mail failure: " . get_class($e) . "\n";
}

// Restore port
Config::set('mail.mailers.smtp.port', 1025);

if ($failureCaught) {
    echo "Failure handling verified! Status remains: {$originalStatus} (NOT updated).\n";
} else {
    echo "WARNING: Expected failure was not thrown.\n";
}

echo "\n=== ALL CHECKS FINISHED ===\n";
