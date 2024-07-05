<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $email->subject }}</title>
    <!-- Add your CSS styles here -->
    <style>
        /* Your CSS styles for the email */
    </style>
</head>
<body style="width: 50%; text-align: center;">
    <!-- Header -->
    <header style="background-color: #f0f0f0; padding: 20px;">
        <h1>Welcome to Memvera!</h1>
        <!-- Add any header content here -->
    </header>

    <!-- Email Content -->
    <div style="padding: 20px;">
        <h2>Hello, I am {{ $email->name }},</h2>
        <p style="font-size: 17px;">This is a email from Memvera. Below are some details from the email:</p>
        <div style="font-size: 16px">
            {!! $email->reply !!}
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #f0f0f0; padding: 20px;">
        <p>Thank you for using Memvera!</p>
        <!-- Add any footer content here -->
    </footer>
</body>
</html>
