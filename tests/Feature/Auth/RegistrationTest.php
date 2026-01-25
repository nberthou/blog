<?php

use App\Models\EmailVerificationCode;
use App\Notifications\VerifyEmailWithCode;
use Illuminate\Support\Facades\Notification;

test('registration page redirects to home (auth via modal)', function () {
    $response = $this->get(route('register'));

    $response->assertRedirect(route('home'));
});

test('new users can register with valid password', function () {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

    // User should not be verified yet
    $user = auth()->user();
    expect($user->hasVerifiedEmail())->toBeFalse();

    // Verification code should be sent
    Notification::assertSentTo($user, VerifyEmailWithCode::class);
});

test('registration creates verification code', function () {
    Notification::fake();

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
    ]);

    $user = auth()->user();
    expect(EmailVerificationCode::where('user_id', $user->id)->exists())->toBeTrue();
});

test('registration fails with password missing uppercase', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password1',
        'password_confirmation' => 'password1',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration fails with password missing lowercase', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'PASSWORD1',
        'password_confirmation' => 'PASSWORD1',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration fails with password missing number', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Passwordd',
        'password_confirmation' => 'Passwordd',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration fails with password too short', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Pass1',
        'password_confirmation' => 'Pass1',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});
