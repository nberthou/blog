<?php

namespace App\Services;

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\VerifyEmailWithCode;
use Illuminate\Support\Facades\Hash;

class EmailVerificationService
{
    public function generateAndSendCode(User $user): void
    {
        $this->deleteCodesForUser($user);

        $plainCode = $this->generateCode();

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(EmailVerificationCode::EXPIRATION_MINUTES),
            'created_at' => now(),
        ]);

        $user->notify(new VerifyEmailWithCode($plainCode));
    }

    public function verifyCode(User $user, string $code): bool
    {
        $verificationCode = EmailVerificationCode::where('user_id', $user->id)->first();

        if (! $verificationCode) {
            return false;
        }

        $verificationCode->increment('attempts');

        if ($verificationCode->isExpired()) {
            return false;
        }

        if ($verificationCode->hasExceededAttempts()) {
            return false;
        }

        if (! Hash::check($code, $verificationCode->code)) {
            return false;
        }

        $user->markEmailAsVerified();
        $this->deleteCodesForUser($user);

        return true;
    }

    public function deleteCodesForUser(User $user): void
    {
        EmailVerificationCode::where('user_id', $user->id)->delete();
    }

    public function getCodeForUser(User $user): ?EmailVerificationCode
    {
        return EmailVerificationCode::where('user_id', $user->id)->first();
    }

    private function generateCode(): string
    {
        return str_pad(
            (string) random_int(0, (int) str_repeat('9', EmailVerificationCode::CODE_LENGTH)),
            EmailVerificationCode::CODE_LENGTH,
            '0',
            STR_PAD_LEFT
        );
    }
}
