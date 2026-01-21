<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password update page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('user-password.edit'));

    $response->assertStatus(200);
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('user-password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'Password1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('user-password.edit'));

    expect(Hash::check('NewPassword2', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('user-password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect(route('user-password.edit'));
});

test('new password must meet complexity requirements', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('user-password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'Password1',
            'password' => 'weakpass',
            'password_confirmation' => 'weakpass',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('user-password.edit'));
});
