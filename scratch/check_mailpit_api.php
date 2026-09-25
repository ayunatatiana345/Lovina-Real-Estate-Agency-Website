<?php

$ch = curl_init('http://127.0.0.1:8025/api/v1/messages');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Mailpit HTTP API status: " . $httpCode . "\n";
if ($httpCode === 200 && $res) {
    $data = json_decode($res, true);
    echo "Total messages in Mailpit: " . ($data['total'] ?? count($data['messages'] ?? [])) . "\n";
    if (!empty($data['messages'])) {
        $latest = $data['messages'][0];
        echo "Latest Message ID: " . ($latest['ID'] ?? '') . "\n";
        echo "Subject: " . ($latest['Subject'] ?? '') . "\n";
        echo "From: " . json_encode($latest['From'] ?? '') . "\n";
        echo "To: " . json_encode($latest['To'] ?? '') . "\n";
    }
} else {
    echo "Could not reach Mailpit on 8025 or different port.\n";
}
