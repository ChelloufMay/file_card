<!doctype html>
<html>
<head>
    <meta charset="utf-8">
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
            background-color: #2d3748;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f7fafc;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 5px;
            display: block;
        }
        .field-value {
            background-color: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        .message-box {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #718096;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    <div class="content">
        <div class="field">
            <span class="field-label">Name:</span>
            <div class="field-value">{{ $name }}</div>
        </div>

        <div class="field">
            <span class="field-label">Email:</span>
            <div class="field-value">
                <a href="mailto:{{ $email }}">{{ $email }}</a>
            </div>
        </div>

        <div class="field">
            <span class="field-label">Subject:</span>
            <div class="field-value">{{ $subject }}</div>
        </div>

        <div class="field">
            <span class="field-label">Message:</span>
            <div class="message-box">{{ $message }}</div>
        </div>

        <div class="field">
            <span class="field-label">Submitted At:</span>
            <div class="field-value">{{ $timestamp }}</div>
        </div>
    </div>
    <div class="footer">
        <p>This email was sent from the contact form on your website.</p>
        <p>You can reply directly to this email to respond to {{ $name }}.</p>
    </div>
</body>
</html>

