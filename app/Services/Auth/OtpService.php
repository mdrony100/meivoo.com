<?php

namespace App\Services\Auth;

use App\Mail\PasswordOtpMail;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function generate(string $email): PasswordOtp
    {
        $this->invalidateActiveOtps($email);

        [$otp, $plainOtp] = $this->createOtpRecord($email);
        $this->sendOtpMail($email, $plainOtp);

        return $otp;
    }

    public function verify(string $email, string $otp): PasswordOtp
    {
        $record = $this->findActiveOtp($email);

        if (! $record) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP is invalid or has expired.',
            ]);
        }

        if ($record->attempts >= (int) config('auth_api.otp_max_attempts')) {
            $this->invalidate($record);

            throw ValidationException::withMessages([
                'otp' => 'Too many invalid attempts. Please request a new OTP.',
            ]);
        }

        if (! Hash::check($otp, $record->otp_hash)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => 'The OTP is invalid or has expired.',
            ]);
        }

        $record->forceFill([
            'verified_at' => now(),
        ])->save();

        return $record;
    }

    public function resetPassword(string $email, string $otp, string $password): void
    {
        $record = $this->findVerifiedOtp($email, $otp);

        if (! $record) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP is invalid, expired, or not verified.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'No user account found for the provided email address.',
            ]);
        }

        $user->forceFill([
            'password' => $password,
        ])->save();

        $record->forceFill([
            'used_at' => now(),
            'invalidated_at' => now(),
        ])->save();
    }

    public function resend(string $email): PasswordOtp
    {
        return $this->generate($email);
    }

    public function findVerifiedOtp(string $email, string $otp): ?PasswordOtp
    {
        $record = $this->findActiveOtp($email);

        if (! $record || ! $record->verified_at) {
            return null;
        }

        return Hash::check($otp, $record->otp_hash) ? $record : null;
    }

    public function invalidate(PasswordOtp|string $target): void
    {
        if ($target instanceof PasswordOtp) {
            $target->forceFill(['invalidated_at' => now()])->save();

            return;
        }

        PasswordOtp::where('email', $target)
            ->whereNull('invalidated_at')
            ->whereNull('used_at')
            ->update([
                'invalidated_at' => now(),
            ]);
    }

    private function findActiveOtp(string $email): ?PasswordOtp
    {
        return PasswordOtp::where('email', $email)
            ->where('expires_at', '>', now())
            ->whereNull('invalidated_at')
            ->whereNull('used_at')
            ->latest('id')
            ->first();
    }

    private function invalidateActiveOtps(string $email): void
    {
        PasswordOtp::where('email', $email)
            ->whereNull('invalidated_at')
            ->whereNull('used_at')
            ->update([
                'invalidated_at' => now(),
            ]);
    }

    private function createOtpRecord(string $email): array
    {
        $plainOtp = $this->generateOtp();

        $otp = PasswordOtp::create([
            'email' => $email,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes((int) config('auth_api.otp_ttl')),
            'attempts' => 0,
        ]);

        return [$otp, $plainOtp];
    }

    private function sendOtpMail(string $email, string $otp): void
    {
        Mail::to($email)->send(new PasswordOtpMail($otp, (int) config('auth_api.otp_ttl')));
    }

    private function generateOtp(): string
    {
        $digits = (int) config('auth_api.otp_length', 6);
        $min = (int) str_pad('1', $digits, '0');
        $max = (int) str_pad('', $digits, '9');

        return (string) random_int($min, $max);
    }
}
