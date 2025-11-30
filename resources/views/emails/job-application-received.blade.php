<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
        .notification-icon {
            text-align: center;
            font-size: 60px;
            color: #3b82f6;
            margin: 20px 0;
        }
        .job-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
        }
        .job-box h3 {
            margin-top: 0;
            color: #2563eb;
        }
        .applicant-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .applicant-row {
            display: flex;
            padding: 8px 0;
        }
        .applicant-label {
            font-weight: bold;
            min-width: 120px;
            color: #666;
        }
        .applicant-value {
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
            <h1>New Job Application Received</h1>
        </div>
        <div class="content">
            <div class="notification-icon">📋</div>
            
            <p>Hello {{ $employerName }},</p>
            
            <p>You have received a new job application for your posting!</p>
            
            <div class="job-box">
                <h3>{{ $jobTitle }}</h3>
            </div>
            
            <h3>Applicant Details:</h3>
            <div class="applicant-box">
                <div class="applicant-row">
                    <span class="applicant-label">Name:</span>
                    <span class="applicant-value">{{ $applicantName }}</span>
                </div>
                <div class="applicant-row">
                    <span class="applicant-label">Email:</span>
                    <span class="applicant-value"><a href="mailto:{{ $applicantEmail }}">{{ $applicantEmail }}</a></span>
                </div>
                @if($applicantPhone)
                <div class="applicant-row">
                    <span class="applicant-label">Phone:</span>
                    <span class="applicant-value"><a href="tel:{{ $applicantPhone }}">{{ $applicantPhone }}</a></span>
                </div>
                @endif
            </div>
            
            <p>Login to your dashboard to review the complete application, including resume and cover letter.</p>
            
            <center>
                <a href="{{ $jobUrl }}" class="button">View Application</a>
            </center>
            
            <div class="tips">
                <h4>💡 Quick Tips:</h4>
                <ul>
                    <li>Review the candidate's profile and experience</li>
                    <li>Respond to applicants within 48 hours</li>
                    <li>Schedule interviews with promising candidates</li>
                    <li>Keep your job posting updated with the latest status</li>
                </ul>
            </div>
            
            <p>If you have any questions, contact us at <a href="mailto:info@ohmygoa.com">info@ohmygoa.com</a>.</p>
            
            <p>Best regards,<br>The OhMyGoa Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OhMyGoa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
