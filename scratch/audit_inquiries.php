<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;

$inquiries = Inquiry::with(['property', 'statusLogs'])->get();
echo "Total Inquiries: " . $inquiries->count() . "\n\n";

foreach ($inquiries as $inq) {
    echo "ID: {$inq->id}\n";
    echo "Customer Name: {$inq->customer_name}\n";
    echo "Email: {$inq->email}\n";
    echo "Phone: {$inq->phone}\n";
    echo "Subject: {$inq->subject}\n";
    echo "Message: {$inq->message}\n";
    echo "Source: {$inq->source}\n";
    echo "Status: {$inq->status}\n";
    echo "Property ID: {$inq->property_id} (" . ($inq->property ? $inq->property->name : 'N/A') . ")\n";
    echo "Admin Notes: " . ($inq->admin_notes ?: 'NONE') . "\n";
    echo "Status Logs Count: " . $inq->statusLogs->count() . "\n";
    echo "----------------------------------------\n";
}
