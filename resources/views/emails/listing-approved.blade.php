<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Approved</title>
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
        .listing-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .listing-box h3 {
            margin-top: 0;
            color: #10b981;
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
        .tips {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
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
            <h1>Congratulations! 🎉</h1>
        </div>
        <div class="content">
            <div class="success-icon">✓</div>
            
            <p>Hello {{ $userName }},</p>
            
            <p>Great news! Your business listing has been approved and is now live on OhMyGoa.</p>
            
            <div class="listing-box">
                <h3>{{ $listing->business_name }}</h3>
                <p><strong>Category:</strong> {{ $listing->category->name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ $listing->location->name ?? 'N/A' }}</p>
                @if($listing->tagline)
                <p><em>"{{ $listing->tagline }}"</em></p>
                @endif
            </div>
            
            <p>Your listing is now visible to thousands of potential customers searching for businesses in Goa.</p>
            
            <center>
                <a href="{{ $listingUrl }}" class="button">View Your Listing</a>
            </center>
            
            <div class="tips">
                <h4>💡 Tips to Get More Visibility:</h4>
                <ul>
                    <li>Add high-quality photos to showcase your business</li>
                    <li>Respond promptly to customer enquiries</li>
                    <li>Keep your business hours and contact information updated</li>
                    <li>Encourage satisfied customers to leave reviews</li>
                    <li>Consider promoting your listing for premium placement</li>
                </ul>
            </div>
            
            <p>If you have any questions, feel free to contact us at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
