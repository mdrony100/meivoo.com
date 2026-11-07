<?php

namespace App\Services\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    public function login(array $data, Request $request): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (! $this->isUserActive($user)) {
            throw new AuthenticationException('Your account is inactive.');
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return $this->issueTokenPair($user, $request);
    }

    public function logout(User $user, Request $request): void
    {
        $currentAccessToken = $user->currentAccessToken();

        if ($currentAccessToken) {
            RefreshToken::where('sanctum_token_id', $currentAccessToken->id)
                ->update([
                    'revoked_at' => now(),
                ]);

            $currentAccessToken->delete();
        }
    }

    public function refreshToken(string $refreshToken, Request $request): array
    {
        if ($refreshToken === '') {
            throw new AuthenticationException('Invalid or expired refresh token.');
        }

        $refreshRecord = $this->findRefreshToken($refreshToken);

        if (! $refreshRecord || ! $refreshRecord->isValid()) {
            throw new AuthenticationException('Invalid or expired refresh token.');
        }

        $user = $refreshRecord->user;

        if (! $user || ! $this->isUserActive($user)) {
            throw new AuthenticationException('Your account is inactive.');
        }

        if ($refreshRecord->sanctum_token_id) {
            $user->tokens()->whereKey($refreshRecord->sanctum_token_id)->delete();
        }

        $refreshRecord->forceFill([
            'revoked_at' => now(),
            'last_used_at' => now(),
        ])->save();

        return $this->issueTokenPair($user, $request);
    }

    public function forgotPassword(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! $this->isUserActive($user)) {
            return;
        }

        $this->otpService->generate($email);
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $record = $this->otpService->verify($email, $otp);

        return [
            'email' => $record->email,
            'otp_verified' => true,
            'verified_at' => $record->verified_at,
            'expires_at' => $record->expires_at,
        ];
    }

    public function resetPassword(array $data): void
    {
        $this->otpService->resetPassword($data['email'], $data['otp'], $data['password']);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $this->revokeAllUserTokens($user);
        }
    }

    public function changePassword(User $user, array $data, Request $request): void
    {
        $currentAccessToken = $user->currentAccessToken();

        $user->forceFill([
            'password' => $data['password'],
        ])->save();

        if ($currentAccessToken) {
            $user->tokens()
                ->where('id', '!=', $currentAccessToken->id)
                ->delete();

            RefreshToken::where('user_id', $user->id)
                ->where('sanctum_token_id', '!=', $currentAccessToken->id)
                ->update([
                    'revoked_at' => now(),
                ]);
        }
    }

    public function resendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! $this->isUserActive($user)) {
            return;
        }

        $this->otpService->resend($email);
    }

    private function issueTokenPair(User $user, Request $request): array
    {
        $accessTokenExpiresAt = now()->addMinutes((int) config('auth_api.access_token_ttl'));
        $refreshTokenExpiresAt = now()->addMinutes((int) config('auth_api.refresh_token_ttl'));

        $deviceName = $request->userAgent() ?: 'api';

        $accessToken = $user->createToken(
            $this->makeTokenName($deviceName),
            ['*'],
            $accessTokenExpiresAt
        );

        $plainRefreshToken = Str::random(64);

        $refreshRecord = RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => hash_hmac('sha256', $plainRefreshToken, (string) config('app.key')),
            'sanctum_token_id' => $accessToken->accessToken->id,
            'expires_at' => $refreshTokenExpiresAt,
            'device_name' => $deviceName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return [
            'user' => $this->formatUser($user->fresh()),
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $plainRefreshToken,
        ];
    }

    private function revokeAllUserTokens(User $user): void
    {
        $user->tokens()->delete();

        RefreshToken::where('user_id', $user->id)->update([
            'revoked_at' => now(),
        ]);
    }

    private function findRefreshToken(string $plainToken): ?RefreshToken
    {
        $tokenHash = hash_hmac('sha256', $plainToken, (string) config('app.key'));

        return RefreshToken::with('user')
            ->where('token_hash', $tokenHash)
            ->first();
    }

    private function isUserActive(User $user): bool
    {
        return (bool) $user->status;
    }

    private function makeTokenName(string $deviceName): string
    {
        return Str::of($deviceName)->limit(40, '')->toString() ?: 'api-token';
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'family_id' => $user->family_id,
            'name' => $user->name,
            'email' => $user->email,
            'google_id' => $user->google_id,
            'google_email' => $user->google_email,
            'image' => $user->image,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'status' => (bool) $user->status,
            'is_trial_plan' => (int) $user->is_trial_plan,
        ];
    }
}
