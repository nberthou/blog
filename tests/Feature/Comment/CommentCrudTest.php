<?php

use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->withoutVite();
});

// =========================================================================
// STORE (Authenticated users)
// =========================================================================

test('authenticated user can create a comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => 'Ceci est un commentaire.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Comment::count())->toBe(1);
    $comment = Comment::first();
    expect($comment->content)->toBe('Ceci est un commentaire.');
    expect($comment->user_id)->toBe($user->id);
    expect($comment->post_id)->toBe($post->id);
    expect($comment->parent_id)->toBeNull();
});

test('unauthenticated user cannot create a comment', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->post(route('comments.store', $post), [
        'content' => 'Ceci est un commentaire.',
    ]);

    $response->assertRedirect(route('login'));
    expect(Comment::count())->toBe(0);
});

test('user can reply to a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);
    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => 'Ceci est une réponse.',
        'parent_id' => $parentComment->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Comment::count())->toBe(2);
    $reply = Comment::where('parent_id', $parentComment->id)->first();
    expect($reply->content)->toBe('Ceci est une réponse.');
    expect($reply->parent_id)->toBe($parentComment->id);
});

test('comment content is required', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => '',
    ]);

    $response->assertSessionHasErrors('content');
    expect(Comment::count())->toBe(0);
});

test('comment content must have minimum 3 characters', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => 'ab',
    ]);

    $response->assertSessionHasErrors('content');
    expect(Comment::count())->toBe(0);
});

test('comment content cannot exceed 1000 characters', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => str_repeat('a', 1001),
    ]);

    $response->assertSessionHasErrors('content');
    expect(Comment::count())->toBe(0);
});

test('parent_id must exist if provided', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('comments.store', $post), [
        'content' => 'Ceci est un commentaire.',
        'parent_id' => 999,
    ]);

    $response->assertSessionHasErrors('parent_id');
    expect(Comment::count())->toBe(0);
});

// =========================================================================
// UPDATE (Author or Admin only)
// =========================================================================

test('comment author can update their comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'Contenu original',
    ]);

    $response = $this->actingAs($user)->put(route('comments.update', $comment), [
        'content' => 'Contenu modifié',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($comment->fresh()->content)->toBe('Contenu modifié');
});

test('admin can update any comment', function () {
    $admin = User::factory()->create(['id' => 1]);
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'Contenu original',
    ]);

    $response = $this->actingAs($admin)->put(route('comments.update', $comment), [
        'content' => 'Modifié par admin',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($comment->fresh()->content)->toBe('Modifié par admin');
});

test('other users cannot update a comment', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'content' => 'Contenu original',
    ]);

    $response = $this->actingAs($otherUser)->put(route('comments.update', $comment), [
        'content' => 'Tentative de modification',
    ]);

    $response->assertForbidden();
    expect($comment->fresh()->content)->toBe('Contenu original');
});

test('unauthenticated user cannot update a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'Contenu original',
    ]);

    $response = $this->put(route('comments.update', $comment), [
        'content' => 'Tentative de modification',
    ]);

    $response->assertRedirect(route('login'));
    expect($comment->fresh()->content)->toBe('Contenu original');
});

test('comment update validates content', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->put(route('comments.update', $comment), [
        'content' => 'ab',
    ]);

    $response->assertSessionHasErrors('content');
});

// =========================================================================
// DESTROY (Author or Admin only)
// =========================================================================

test('comment author can delete their comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(Comment::find($comment->id))->toBeNull();
});

test('admin can delete any comment', function () {
    $admin = User::factory()->create(['id' => 1]);
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('comments.destroy', $comment));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(Comment::find($comment->id))->toBeNull();
});

test('other users cannot delete a comment', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $author->id,
    ]);

    $response = $this->actingAs($otherUser)->delete(route('comments.destroy', $comment));

    $response->assertForbidden();
    expect(Comment::find($comment->id))->not->toBeNull();
});

test('unauthenticated user cannot delete a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->delete(route('comments.destroy', $comment));

    $response->assertRedirect(route('login'));
    expect(Comment::find($comment->id))->not->toBeNull();
});

test('deleting a comment also deletes its replies', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
    $reply = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'parent_id' => $parentComment->id,
    ]);

    $this->actingAs($user)->delete(route('comments.destroy', $parentComment));

    expect(Comment::find($parentComment->id))->toBeNull();
    expect(Comment::find($reply->id))->toBeNull();
});

// =========================================================================
// MODEL RELATIONSHIPS
// =========================================================================

test('comment belongs to a post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    expect($comment->post->id)->toBe($post->id);
});

test('comment belongs to a user', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->author->id)->toBe($user->id);
});

test('comment can have replies', function () {
    $post = Post::factory()->create();
    $user = User::factory()->create();
    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
    Comment::factory()->count(3)->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'parent_id' => $parentComment->id,
    ]);

    expect($parentComment->replies)->toHaveCount(3);
});

test('reply has a parent', function () {
    $post = Post::factory()->create();
    $user = User::factory()->create();
    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
    $reply = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'parent_id' => $parentComment->id,
    ]);

    expect($reply->parent->id)->toBe($parentComment->id);
});

test('post has many comments', function () {
    $post = Post::factory()->create();
    $user = User::factory()->create();
    Comment::factory()->count(5)->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    expect($post->comments)->toHaveCount(5);
});

test('user has many comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    expect($user->comments)->toHaveCount(3);
});
