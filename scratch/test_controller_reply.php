<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;
use App\Models\InquiryStatusLog;
use App\Http\Controllers\Admin\InquiryController;
use Illuminate\Http\Request;

// Select inquiry 24 (or first 'new' inquiry)
$inquiry = Inquiry::where('status', 'new')->whereNotNull('email')->first();
if (!$inquiry) {
    die("No 'new' inquiry found.\n");
}

echo "Testing controller replyEmail on Inquiry #{$inquiry->id}\n";
echo "Initial Status: {$inquiry->status}\n";
echo "Recipient: {$inquiry->email}\n";

$initialLogCount = InquiryStatusLog::where('inquiry_id', $inquiry->id)->count();

$request = Request::create(
    "/admin/inquiries/{$inquiry->id}/reply-email",
    'POST',
    [
        'subject' => 'Follow up on your inquiry #' . $inquiry->id,
        'message' => "Hello {$inquiry->customer_name},\n\nWe are responding to your inquiry. Please let us know if you need further details.\n\nBest regards,\nPT Lovina North Bali Real Estate Agency",
    ]
);

// Bind session
$session = $app->make('session')->driver();
$request->setLaravelSession($session);

$controller = new InquiryController();
$response = $controller->replyEmail($request, $inquiry->id);

echo "Controller Response Status: " . $response->getStatusCode() . "\n";
$sessionSuccess = $session->get('success');
$sessionErrors = $session->get('errors');

if ($sessionSuccess) {
    echo "Session Success Flash: " . $sessionSuccess . "\n";
}
if ($sessionErrors) {
    echo "Session Errors: " . json_encode($sessionErrors->all()) . "\n";
}

$inquiry->refresh();
echo "Updated Inquiry Status: {$inquiry->status}\n";

$newLogCount = InquiryStatusLog::where('inquiry_id', $inquiry->id)->count();
echo "Status Logs Count: {$initialLogCount} -> {$newLogCount}\n";

$latestLog = InquiryStatusLog::where('inquiry_id', $inquiry->id)->latest('id')->first();
if ($latestLog) {
    echo "Latest Log Status: {$latestLog->status} at {$latestLog->changed_at}\n";
}

echo "\nDone!\n";
