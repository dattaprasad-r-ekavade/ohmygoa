<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expiring Soon</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
        .alert-icon {
            text-align: center;
            font-size: 60px;
            color: #ef4444;
            margin: 20px 0;
        }
        .expiry-box {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .expiry-box h3 {
            margin: 0;
            color: #dc2626;
            font-size: 24px;
        }
        .expiry-box p {
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .benefits {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .benefits ul {
            list-style: none;
            padding: 0;
        }
        .benefits li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .benefits li:before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            position: absolute;
            left: 0;
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
            <h1>Subscription Expiring Soon</h1>
        </div>
        <div class="content">
            <div class="alert-icon">⏰</div>
            
            <p>Hello {{ $userName }},</p>
            
            <p>We wanted to remind you that your premium subscription is expiring soon.</p>
            
            <div class="expiry-box">
                <h3>{{ $daysRemaining }} Days Remaining</h3>
                <p>Your subscription expires on <strong>{{ $expiryDate }}</strong></p>
            </div>
            
            <p>Don't miss out on the benefits of being a premium member! Renew your subscription to continue enjoying:</p>
            
            <div class="benefits">
                <ul>
                    <li>Create unlimited business listings</li>
                    <li>Premium placement in search results</li>
                    <li>Advanced analytics and insights</li>
                    <li>Priority customer support</li>
                    <li>Remove ads from your profile</li>
                    <li>Earn commission from referrals</li>
                </ul>
            </div>
            
            <p>Renew now to avoid any interruption to your premium features.</p>
            
            <center>
                <a href="{{ $renewUrl }}" class="button">Renew Subscription</a>
            </center>
            
            <p>If you have any questions about your subscription, please contact us at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
