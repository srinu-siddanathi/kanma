<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: white;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #0d6efd;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 New Contact Form Submission</h1>
        <p>KANMA Support Team</p>
    </div>
    
    <div class="content">
        <p><strong>A new contact form has been submitted on the KANMA website.</strong></p>
        
        <div class="field">
            <div class="field-label">👤 Full Name:</div>
            <div class="field-value">{{ $contactData['name'] }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">📧 Email Address:</div>
            <div class="field-value">{{ $contactData['email'] }}</div>
        </div>
        
        @if(!empty($contactData['phone']))
        <div class="field">
            <div class="field-label">📞 Phone Number:</div>
            <div class="field-value">{{ $contactData['phone'] }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="field-label">📋 Subject:</div>
            <div class="field-value">{{ $contactData['subject'] }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">💬 Message:</div>
            <div class="field-value">{{ $contactData['message'] }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">⏰ Submitted At:</div>
            <div class="field-value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">🌐 IP Address:</div>
            <div class="field-value">{{ request()->ip() }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>This email was sent from the KANMA contact form.</p>
        <p>Please respond to the customer at: <a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a></p>
    </div>
</body>
</html> 