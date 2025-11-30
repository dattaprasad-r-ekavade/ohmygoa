<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Processed</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            color: #10b981;
            margin: 20px 0;
        }
        .payout-box {
            background: #f0fdf4;
            border: 2px solid #10b981;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .payout-box .amount {
            font-size: 36px;
            color: #10b981;
            font-weight: bold;
            margin: 10px 0;
        }
        .payout-box .transaction {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .note {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payout Processed Successfully</h1>
        </div>
        <div class="content">
            <div class="success-icon">💰</div>
            
            <p>Hello {{ $userName }},</p>
            
            <p>Great news! Your payout request has been processed successfully.</p>
            
            <div class="payout-box">
                <p style="margin: 0; color: #666;">Amount Transferred</p>
                <div class="amount">₹{{ number_format($amount, 2) }}</div>
                @if($transactionId)
                <div class="transaction">Transaction ID: {{ $transactionId }}</div>
                @endif
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <span><strong>Payment Status:</strong></span>
                    <span style="color: #10b981;">Completed</span>
                </div>
                <div class="info-row">
                    <span><strong>Processing Date:</strong></span>
                    <span>{{ now()->format('d M Y, h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Payment Method:</strong></span>
                    <span>Bank Transfer</span>
                </div>
            </div>
            
            <div class="note">
                <strong>📝 Note:</strong> The amount should reflect in your bank account within 2-5 business days depending on your bank's processing time.
            </div>
            
            <p>You can view your complete payout history and earnings in your dashboard.</p>
            
            <center>
                <a href="{{ $dashboardUrl }}" class="button">View Dashboard</a>
            </center>
            
            <p>If you have any questions about this payout, please contact us at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Thank you for being a valued member of OhMyGoa!</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
