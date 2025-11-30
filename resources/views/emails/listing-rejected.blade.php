<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Requires Attention</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        .warning-icon {
            text-align: center;
            font-size: 60px;
            color: #f59e0b;
            margin: 20px 0;
        }
        .listing-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .reason-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .reason-box h4 {
            margin-top: 0;
            color: #d97706;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .guidelines {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin: 20px 0;
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
            <h1>Listing Requires Attention</h1>
        </div>
        <div class="content">
            <div class="warning-icon">⚠️</div>
            
            <p>Hello {{ $userName }},</p>
            
            <p>Thank you for submitting your business listing. After reviewing your submission, we found some issues that need to be addressed before we can approve it.</p>
            
            <div class="listing-box">
                <h3>{{ $listing->business_name }}</h3>
                <p><strong>Category:</strong> {{ $listing->category->name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $listing->location->name ?? 'N/A' }}</p>
            </div>
            
            <div class="reason-box">
                <h4>Reason for Rejection:</h4>
                <p>{{ $reason }}</p>
            </div>
            
            <p>Please review and update your listing according to our guidelines. Once you've made the necessary changes, your listing will be re-reviewed by our team.</p>
            
            <center>
                <a href="{{ $editUrl }}" class="button">Edit Your Listing</a>
            </center>
            
            <div class="guidelines">
                <h4>📋 Listing Guidelines:</h4>
                <ul>
                    <li>Use clear and accurate business information</li>
                    <li>Provide a valid business address and contact details</li>
                    <li>Upload high-quality, relevant images</li>
                    <li>Write a complete and professional description</li>
                    <li>Ensure all information complies with our terms of service</li>
                </ul>
            </div>
            
            <p>If you have any questions or need assistance, please contact us at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
