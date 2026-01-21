<?php

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\VerifyEmailWithCode;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->verificationService = app(EmailVerificationService::class);
});

test('verification code can be verified with valid code', function () {
    $user = User::factory()->unverified()->create();
    $plainCode = '123456';

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make($plainCode),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => $plainCode,
    ]);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeFalse();
});

test('verification fails with invalid code', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => '000000',
    ]);

    $response->assertSessionHasErrors('code');
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification fails with expired code', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->subMinutes(1),
        'created_at' => now()->subMinutes(16),
    ]);

    $response = $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => '123456',
    ]);

    $response->assertSessionHasErrors('code');
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification fails after too many attempts', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'attempts' => EmailVerificationCode::MAX_ATTEMPTS,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => '123456',
    ]);

    $response->assertSessionHasErrors('code');
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification increments attempts on failed verification', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'attempts' => 0,
        'created_at' => now(),
    ]);

    $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => '000000',
    ]);

    expect(EmailVerificationCode::where('user_id', $user->id)->first()->attempts)->toBe(1);
});

test('resend code sends new verification email', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now()->subMinutes(2),
    ]);

    $response = $this->actingAs($user)->post(route('verification.resend-code'));

    Notification::assertSentTo($user, VerifyEmailWithCode::class);
});

test('resend code deletes old code and creates new one', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $oldCode = EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now()->subMinutes(2),
    ]);

    $this->actingAs($user)->post(route('verification.resend-code'));

    expect(EmailVerificationCode::find($oldCode->id))->toBeNull();
    expect(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeTrue();
});

test('resend code is rate limited within cooldown period', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('verification.resend-code'));

    $response->assertSessionHasErrors('code');
});

test('verified user cannot access verification endpoints', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('verification.resend-code'));

    $response->assertRedirect();
});

test('guest cannot access verification endpoints', function () {
    $this->post(route('verification.verify-code'), ['code' => '123456'])
        ->assertRedirect(route('login'));

    $this->post(route('verification.resend-code'))
        ->assertRedirect(route('login'));
});

test('verification code must be 6 digits', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('verification.verify-code'), [
        'code' => '12345',
    ]);

    $response->assertSessionHasErrors('code');
});

test('service generates 6 digit code', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->verificationService->generateAndSendCode($user);

    $code = EmailVerificationCode::where('user_id', $user->id)->first();
    expect($code)->not->toBeNull();
    expect($code->expires_at->isFuture())->toBeTrue();
});
