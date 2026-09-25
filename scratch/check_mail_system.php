<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

echo "=== MAIL CONFIGURATION AUDIT ===\n";
echo "Default Mailer: " . config('mail.default') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "SMTP Username: " . (config('mail.mailers.smtp.username') ?? '(null)') . "\n";
echo "SMTP Scheme: " . (config('mail.mailers.smtp.scheme') ?? '(null)') . "\n";
echo "Global From Address: " . config('mail.from.address') . "\n";
echo "Global From Name: " . config('mail.from.name') . "\n";

$settings = CompanySetting::getSettings();
echo "\n=== COMPANY SETTINGS AUDIT ===\n";
echo "Company Email in Settings: " . ($settings->email ?? '(null)') . "\n";
echo "Company Name in Settings: " . ($settings->company_name ?? '(null)') . "\n";
echo "Site Name in Settings: " . ($settings->site_name ?? '(null)') . "\n";

echo "\n=== PORT 1025 CHECK ===\n";
$fp = @fsockopen('127.0.0.1', 1025, $errno, $errstr, 2);
if ($fp) {
    echo "Port 1025 is OPEN (Mailpit / local SMTP is listening)\n";
    fclose($fp);
} else {
    echo "Port 1025 is CLOSED: {$errstr} (code: {$errno})\n";
}

echo "\n=== INQUIRIES AUDIT ===\n";
echo "Total Inquiries: " . Inquiry::count() . "\n";
$sampleInquiry = Inquiry::first();
if ($sampleInquiry) {
    echo "Sample Inquiry ID: {$sampleInquiry->id}\n";
    echo "Customer Name: {$sampleInquiry->customer_name}\n";
    echo "Customer Email: {$sampleInquiry->email}\n";
    echo "Customer Phone: {$sampleInquiry->phone}\n";
    echo "Subject: {$sampleInquiry->subject}\n";
    echo "Status: {$sampleInquiry->status}\n";
} else {
    echo "No inquiries found.\n";
}
