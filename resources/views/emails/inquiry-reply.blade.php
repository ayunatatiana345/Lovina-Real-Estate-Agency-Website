<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F8FAFC;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1E293B;
            line-height: 1.6;
        }
        .email-wrapper {
            width: 100%;
            background-color: #F8FAFC;
            padding: 30px 15px;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%);
            padding: 28px 32px;
            text-align: center;
        }
        .email-header h1 {
            color: #FFFFFF;
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-header p {
            color: #93C5FD;
            margin: 4px 0 0 0;
            font-size: 13px;
        }
        .email-body {
            padding: 32px;
        }
        .message-content {
            font-size: 15px;
            color: #334155;
            white-space: pre-line;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .inquiry-summary-box {
            background-color: #F1F5F9;
            border-left: 4px solid #2563EB;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .inquiry-summary-box h4 {
            margin: 0 0 8px 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
        }
        .inquiry-summary-box p {
            margin: 0;
            font-size: 13px;
            color: #64748B;
            line-height: 1.5;
        }
        .email-footer {
            background-color: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
        }
        .email-footer a {
            color: #2563EB;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>{{ $settings->site_name ?? 'PT Lovina North Bali' }}</h1>
                <p>Real Estate Agency & Property Investment Services</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div class="message-content">
                    {{ $emailContent }}
                </div>

                @if($inquiry->property || !empty($inquiry->message))
                <div class="inquiry-summary-box">
                    <h4>Reference Inquiry Details</h4>
                    <p><strong>Inquiry ID:</strong> #{{ $inquiry->id }}</p>
                    @if($inquiry->property)
                    <p><strong>Interested Property:</strong> {{ $inquiry->property->name }}</p>
                    @endif
                    <p><strong>Date Received:</strong> {{ $inquiry->created_at->format('F d, Y') }}</p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 6px 0;"><strong>PT Lovina North Bali Real Estate Agency</strong></p>
                <p style="margin: 0 0 6px 0;">{{ $settings->address ?? 'Jl. Raya Kalibukbuk-Anturan, Lovina, Buleleng, Bali 81119, Indonesia' }}</p>
                <p style="margin: 0;">
                    Phone: {{ $settings->phone ?? '+62 812-3456-7890' }} | Email: {{ $settings->email ?? 'info@lovinanorthbali.com' }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
