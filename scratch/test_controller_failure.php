<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\InquiryController;
use App\Models\Inquiry;
use App\Models\InquiryStatusLog;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

// Select inquiry 24 (which is still 'new')
$inquiry = Inquiry::where('id', 24)->first();
if (! $inquiry) {
    exit("Inquiry #24 not found.\n");
}

echo "Testing failure handling on Inquiry #{$inquiry->id}\n";
echo "Initial Status: {$inquiry->status}\n";

$initialLogCount = InquiryStatusLog::where('inquiry_id', $inquiry->id)->count();

// Set invalid SMTP port and purge mailer
Config::set('mail.mailers.smtp.port', 9999);
Mail::purge('smtp');

$request = Request::create(
    "/admin/inquiries/{$inquiry->id}/reply-email",
    'POST',
    [
        'subject' => 'This should fail',
        'message' => 'Testing failure behavior.',
    ]
);

$session = $app->make('session')->driver();
$request->setLaravelSession($session);

$controller = new InquiryController;
$response = $controller->replyEmail($request, $inquiry->id);

echo 'Controller Response Status: '.$response->getStatusCode()."\n";
$sessionSuccess = $session->get('success');
$errors = $session->get('errors');

echo 'Success Flash present? '.($sessionSuccess ? 'YES (Error!)' : 'NO (Correct)')."\n";
if ($errors) {
    echo 'Errors Flash: '.json_encode($errors->all())."\n";
}

$inquiry->refresh();
echo "Status after failure: {$inquiry->status}\n";

$newLogCount = InquiryStatusLog::where('inquiry_id', $inquiry->id)->count();
echo 'Log count changed? '.($newLogCount > $initialLogCount ? 'YES (Error!)' : 'NO (Correct, stayed '.$newLogCount.')')."\n";

// Reset port
Config::set('mail.mailers.smtp.port', 1025);
Mail::purge('smtp');

echo "Done!\n";
