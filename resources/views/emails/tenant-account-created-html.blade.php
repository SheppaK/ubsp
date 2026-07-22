<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Tenant Account</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #5b5f97;">Welcome to {{ $business->name }}</h2>

    <p>Hello {{ $user->name }},</p>

    <p>A tenant account has been created for you on the Universal Business Systems Platform (Boarding House Finder).</p>

    <div style="background: #f5f5f5; padding: 16px; border-radius: 8px; margin: 20px 0;">
        <p style="margin: 0 0 8px;"><strong>Login URL:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
        <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $user->email }}</p>
        <p style="margin: 0;"><strong>Temporary Password:</strong> <code>{{ $password }}</code></p>
    </div>

    <p>Please log in and change your password. You can also use the password reset link if you prefer to set your own password:</p>
    <p><a href="{{ $resetUrl }}" style="color: #ff6b6c;">Reset your password</a></p>

    <p style="color: #666; font-size: 14px; margin-top: 32px;">— {{ config('app.name') }}</p>
</body>
</html>
