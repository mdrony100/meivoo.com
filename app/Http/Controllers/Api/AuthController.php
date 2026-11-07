<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\LogoutRequest;
use App\Http\Requests\Api\Auth\RefreshTokenRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ResendOtpRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Services\Auth\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function login(LoginRequest $request)
    {
        $payload = $this->authService->login($request->validated(), $request);

        return $this->respondWithRefreshCookie($payload, 'Login successful.');
    }

    public function logout(LogoutRequest $request)
    {
        $this->authService->logout($request->user(), $request);

        return $this->respondWithoutRefreshCookie('Logout successful.');
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        $payload = $this->authService->refreshToken($this->refreshTokenFromRequest($request), $request);

        return $this->respondWithRefreshCookie($payload, 'Token refreshed successfully.');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->validated()['email']);

       return ApiResponse::success('OTP sent successfully.', null, 200);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $payload = $this->authService->verifyOtp(
            $request->validated()['email'],
            $request->validated()['otp']
        );

        return ApiResponse::success('OTP verified successfully.', $payload, 200);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());

        return ApiResponse::success('Password reset successful.', null, 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword($request->user(), $request->validated(), $request);

        return $this->respondWithoutRefreshCookie('Password changed successfully.');
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $this->authService->resendOtp($request->validated()['email']);

        return ApiResponse::success('If the email exists, a new OTP has been sent.', null, 200);
    }

    private function respondWithRefreshCookie(array $payload, string $message)
    {
        $response = ApiResponse::success($message, [
            'user' => $payload['user'],
            'access_token' => $payload['access_token'],
        ], 200);

        return $response->cookie($this->refreshTokenCookie($payload['refresh_token']));
    }

    private function respondWithoutRefreshCookie(string $message)
    {
        return ApiResponse::success($message, null, 200)
            ->withoutCookie(config('auth_api.refresh_token_cookie'));
    }

    private function refreshTokenCookie(string $refreshToken)
    {
        $ttl = (int) config('auth_api.refresh_token_ttl');
        $cookieName = config('auth_api.refresh_token_cookie');

        return cookie(
            $cookieName,
            $refreshToken,
            $ttl,
            '/',
            null,
            request()->isSecure(),
            true,
            false,
            'strict'
        );
    }

    private function refreshTokenFromRequest(RefreshTokenRequest $request): string
    {
        return (string) ($request->validated()['refresh_token'] ?? $request->cookie(config('auth_api.refresh_token_cookie')) ?? '');
    }
}
