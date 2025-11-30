<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to OhMyGoa</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .content h2 {
            color: #667eea;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .features {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .features ul {
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .features li:before {
            content: "✓";
            color: #667eea;
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
            <h1>Welcome to OhMyGoa!</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $userName }}! 🌴</h2>
            
            <p>Thank you for joining OhMyGoa - Goa's premier business and community platform! We're excited to have you as part of our growing community.</p>
            
            <div class="features">
                @if($userRole === 'free')
                    <h3>As a Free User, you can:</h3>
                    <ul>
                        <li>Browse thousands of businesses and services</li>
                        <li>Discover local events and activities</li>
                        <li>Find job opportunities in Goa</li>
                        <li>Shop local products and handicrafts</li>
                        <li>Post classified ads</li>
                    </ul>
                @else
                    <h3>As a Business User, you can:</h3>
                    <ul>
                        <li>Create and manage business listings</li>
                        <li>Post jobs and hire talent</li>
                        <li>Promote events and activities</li>
                        <li>Sell products online</li>
                        <li>Engage with customers through reviews</li>
                        <li>Access business analytics and insights</li>
                    </ul>
                @endif
            </div>
            
            <p>Ready to explore Goa's vibrant business community?</p>
            
            <center>
                <a href="{{ url('/dashboard') }}" class="button">Go to Dashboard</a>
            </center>
            
            <p>If you have any questions, feel free to reach out to our support team at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
            <p>Panaji, Goa, India</p>
        </div>
    </div>
</body>
</html>
