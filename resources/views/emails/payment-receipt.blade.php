<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
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
        .receipt-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .receipt-row:last-child {
            border-bottom: none;
        }
        .receipt-label {
            font-weight: bold;
            color: #666;
        }
        .receipt-value {
            color: #333;
        }
        .total-row {
            font-size: 18px;
            color: #10b981;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #10b981;
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
            <h1>Payment Receipt</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            
            <p>Thank you for your payment. Here are your payment details:</p>
            
            <div class="receipt-box">
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID:</span>
                    <span class="receipt-value">{{ $payment->transaction_id }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Date:</span>
                    <span class="receipt-value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Method:</span>
                    <span class="receipt-value">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Type:</span>
                    <span class="receipt-value">{{ ucfirst($payment->payment_type) }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Status:</span>
                    <span class="receipt-value">{{ ucfirst($payment->status) }}</span>
                </div>
                <div class="receipt-row total-row">
                    <span class="receipt-label">Total Amount:</span>
                    <span class="receipt-value">₹{{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
            
            @if($payment->description)
            <p><strong>Description:</strong> {{ $payment->description }}</p>
            @endif
            
            <p>This receipt has been sent to your registered email address for your records.</p>
            
            <center>
                <a href="{{ url('/dashboard') }}" class="button">View Dashboard</a>
            </center>
            
            <p>If you have any questions about this payment, please contact our support team at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
            <p>This is an automated receipt. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
