<?php

namespace App\Http\Responses;

use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function __construct(
        private readonly EmailVerificationService $verificationService
    ) {}

    public function toResponse($request): Response
    {
        $user = $request->user();

        $this->verificationService->generateAndSendCode($user);

        // For Inertia requests or standard web requests, use session flash
        if ($request instanceof Request && ($request->hasHeader('X-Inertia') || ! $request->wantsJson())) {
            return back()->with([
                'success' => __('verification.code_sent'),
                'requiresVerification' => true,
                'verificationEmail' => $user->email,
            ]);
        }

        // For pure API requests
        return new JsonResponse([
            'requiresVerification' => true,
            'email' => $user->email,
            'message' => __('verification.code_sent'),
        ], 201);
    }
}
