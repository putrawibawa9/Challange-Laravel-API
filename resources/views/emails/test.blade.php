<?php

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Confirmation</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            padding: 20px;
            text-align: center;
            background-color: #28a745;
            color: #ffffff;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-footer {
            padding: 20px;
            text-align: center;
            background-color: #f4f4f4;
            color: #555555;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }
        .email-footer p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Payment Confirmation</h1>
        </div>
        <div class="email-body">
            <p>Dear Customer,</p>
            <p>We have received your payment. Here are the details:</p>
            <p><strong>Payer Email:</strong>{{ $payer_email }}</p>
      <p><strong>Amount:</strong> Rp.{{ number_format($amount, 0, ',', '.') }}</p>
            <p><strong>Payment Method:</strong>{{$payment_method}}</p>
            <p>Thank you for your payment!</p>
        </div>
        <div class="email-footer">
            <p>&copy; 2024 Your Company Name. All rights reserved.</p>
            <p>1234 Your Street, Your City, Your Country</p>
        </div>
    </div>
</body>
</html>
