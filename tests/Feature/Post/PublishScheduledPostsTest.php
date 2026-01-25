<?php

use App\Enums\PostStatus;
use App\Models\Post;

test('scheduled posts are published when their date is reached', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->subHour(),
    ]);

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput("Publié : {$post->title}")
        ->assertSuccessful();

    expect($post->fresh()->status)->toBe(PostStatus::Published);
});

test('scheduled posts in the future are not published', function () {
    $post = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->addHour(),
    ]);

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('Aucun article programmé à publier.')
        ->assertSuccessful();

    expect($post->fresh()->status)->toBe(PostStatus::Scheduled);
});

test('multiple scheduled posts are published at once', function () {
    $post1 = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->subHours(2),
    ]);
    $post2 = Post::factory()->create([
        'status' => PostStatus::Scheduled,
        'published_at' => now()->subMinutes(30),
    ]);

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('2 article(s) publié(s) avec succès.')
        ->assertSuccessful();

    expect($post1->fresh()->status)->toBe(PostStatus::Published);
    expect($post2->fresh()->status)->toBe(PostStatus::Published);
});

test('draft and archived posts are not affected by the command', function () {
    $draftPost = Post::factory()->draft()->create();
    $archivedPost = Post::factory()->archived()->create();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    expect($draftPost->fresh()->status)->toBe(PostStatus::Draft);
    expect($archivedPost->fresh()->status)->toBe(PostStatus::Archived);
});
