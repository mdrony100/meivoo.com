<?php

return [
    'access_token_ttl' => env('AUTH_API_ACCESS_TOKEN_TTL', 15),
    'refresh_token_ttl' => env('AUTH_API_REFRESH_TOKEN_TTL', 43200),
    'otp_ttl' => env('AUTH_API_OTP_TTL', 10),
    'otp_length' => 6,
    'refresh_token_cookie' => env('AUTH_API_REFRESH_TOKEN_COOKIE', 'meivoo_refresh_token'),
    'otp_max_attempts' => env('AUTH_API_OTP_MAX_ATTEMPTS', 5),
    'rate_limits' => [
        'login' => env('AUTH_API_LOGIN_RATE_LIMIT', 5),
        'forgot_password' => env('AUTH_API_FORGOT_PASSWORD_RATE_LIMIT', 3),
        'verify_otp' => env('AUTH_API_VERIFY_OTP_RATE_LIMIT', 10),
        'resend_otp' => env('AUTH_API_RESEND_OTP_RATE_LIMIT', 3),
        'refresh_token' => env('AUTH_API_REFRESH_TOKEN_RATE_LIMIT', 10),
    ],
];
