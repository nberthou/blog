<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailCodeRequest;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationCodeController extends Controller
{
    public function __construct(
        private readonly EmailVerificationService $verificationService
    ) {}

    public function verify(VerifyEmailCodeRequest $request): Response
    {
        $user = $request->user();
        $code = $this->verificationService->getCodeForUser($user);

        if (! $code) {
            return $this->errorResponse($request, __('verification.code_invalid'));
        }

        if ($code->isExpired()) {
            return $this->errorResponse($request, __('verification.code_expired'));
        }

        if ($code->hasExceededAttempts()) {
            return $this->errorResponse($request, __('verification.too_many_attempts'));
        }

        if (! $this->verificationService->verifyCode($user, $request->code)) {
            return $this->errorResponse($request, __('verification.code_invalid'));
        }

        return $request->wantsJson()
            ? new JsonResponse(['message' => __('verification.verified')], 200)
            : back()->with('success', __('verification.verified'));
    }

    public function resend(Request $request): Response
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse(['message' => __('verification.already_verified')], 200)
                : back();
        }

        $existingCode = $this->verificationService->getCodeForUser($user);
        if ($existingCode && $existingCode->created_at->diffInSeconds(now()) < 60) {
            return $this->errorResponse($request, __('verification.resend_cooldown'));
        }

        $this->verificationService->generateAndSendCode($user);

        return $request->wantsJson()
            ? new JsonResponse(['message' => __('verification.resent')], 200)
            : back()->with('success', __('verification.resent'));
    }

    private function errorResponse(Request $request, string $message): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => $message], 422);
        }

        return back()->withErrors(['code' => $message]);
    }
}
