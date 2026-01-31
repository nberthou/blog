<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    // Disable Vite for testing (Vue components don't exist yet)
    $this->withoutVite();
});

// =========================================================================
// INDEX (Public)
// =========================================================================

test('posts index page displays published posts', function () {
    $publishedPost = Post::factory()->published()->create();
    $draftPost = Post::factory()->draft()->create();

    $response = $this->get(route('posts.index'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('posts/Index', false)
        ->has('posts.data', 1)
        ->where('posts.data.0.id', $publishedPost->id)
    );
});

test('posts index page is accessible without authentication', function () {
    $response = $this->get(route('posts.index'));

    $response->assertOk();
});

// =========================================================================
// SHOW (Public for published)
// =========================================================================

test('published post can be viewed by anyone', function () {
    $post = Post::factory()->published()->create();

    $response = $this->get(route('posts.show', $post));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('posts/Show', false)
        ->where('post.id', $post->id)
    );
});

test('viewing a post increments view count', function () {
    $post = Post::factory()->published()->create(['view_count' => 5]);

    $this->get(route('posts.show', $post));

    expect($post->fresh()->view_count)->toBe(6);
});

test('draft post cannot be viewed by guests', function () {
    $post = Post::factory()->draft()->create();

    $response = $this->get(route('posts.show', $post));

    $response->assertForbidden();
});

test('draft post can be viewed by its author', function () {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('posts.show', $post));

    $response->assertOk();
});

test('draft post cannot be viewed by other users', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->draft()->create(['user_id' => $author->id]);

    $response = $this->actingAs($otherUser)->get(route('posts.show', $post));

    $response->assertForbidden();
});

// =========================================================================
// CREATE (Authenticated)
// =========================================================================

test('create post page requires authentication', function () {
    $response = $this->get(route('posts.create'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can access create post page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('posts.create'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('posts/Create', false)
        ->has('statuses')
    );
});

// =========================================================================
// STORE (Authenticated)
// =========================================================================

test('authenticated user can create a post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Mon premier article',
        'excerpt' => 'Un extrait de mon article',
        'content' => 'Le contenu complet de mon article.',
    ]);

    $response->assertRedirect();

    $post = Post::first();
    expect($post)->not->toBeNull();
    expect($post->title)->toBe('Mon premier article');
    expect($post->excerpt)->toBe('Un extrait de mon article');
    expect($post->content)->toBe('Le contenu complet de mon article.');
    expect($post->user_id)->toBe($user->id);
    expect($post->status)->toBe(PostStatus::Draft);
    expect($post->slug)->toBe('mon-premier-article');
});

test('creating a published post sets published_at', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Article publié',
        'excerpt' => 'Un extrait',
        'content' => 'Le contenu.',
        'status' => 'published',
    ]);

    $post = Post::first();
    expect($post->status)->toBe(PostStatus::Published);
    expect($post->published_at)->not->toBeNull();
});

test('creating a scheduled post requires published_at', function () {
    $user = User::factory()->create();
    $futureDate = now()->addDays(7)->format('Y-m-d H:i:s');

    $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Article programmé',
        'excerpt' => 'Un extrait',
        'content' => 'Le contenu.',
        'status' => 'scheduled',
        'published_at' => $futureDate,
    ]);

    $post = Post::first();
    expect($post->status)->toBe(PostStatus::Scheduled);
    expect($post->published_at->format('Y-m-d'))->toBe(now()->addDays(7)->format('Y-m-d'));
});

test('post creation requires title', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'excerpt' => 'Un extrait',
        'content' => 'Le contenu.',
    ]);

    $response->assertSessionHasErrors('title');
});

test('post creation requires excerpt', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Un titre',
        'content' => 'Le contenu.',
    ]);

    $response->assertSessionHasErrors('excerpt');
});

test('post creation requires content', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Un titre',
        'excerpt' => 'Un extrait',
    ]);

    $response->assertSessionHasErrors('content');
});

test('post creation validates status', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Un titre',
        'excerpt' => 'Un extrait',
        'content' => 'Le contenu.',
        'status' => 'invalid-status',
    ]);

    $response->assertSessionHasErrors('status');
});

// =========================================================================
// EDIT (Authenticated + Author only)
// =========================================================================

test('edit post page requires authentication', function () {
    $post = Post::factory()->create();

    $response = $this->get(route('posts.edit', $post));

    $response->assertRedirect(route('login'));
});

test('author can access edit post page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('posts.edit', $post));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('posts/Edit', false)
        ->where('post.id', $post->id)
    );
});

test('non-author cannot access edit post page', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($otherUser)->get(route('posts.edit', $post));

    $response->assertForbidden();
});

// =========================================================================
// UPDATE (Authenticated + Author only)
// =========================================================================

test('author can update their post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('posts.update', $post), [
        'title' => 'Titre modifié',
        'excerpt' => 'Extrait modifié',
        'content' => 'Contenu modifié.',
    ]);

    $response->assertRedirect(route('posts.show', $post));

    $post->refresh();
    expect($post->title)->toBe('Titre modifié');
    expect($post->excerpt)->toBe('Extrait modifié');
    expect($post->content)->toBe('Contenu modifié.');
});

test('non-author cannot update post', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($otherUser)->put(route('posts.update', $post), [
        'title' => 'Titre modifié',
        'excerpt' => 'Extrait modifié',
        'content' => 'Contenu modifié.',
    ]);

    $response->assertForbidden();
});

test('publishing a draft sets published_at', function () {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create(['user_id' => $user->id]);

    $this->actingAs($user)->put(route('posts.update', $post), [
        'title' => $post->title,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'status' => 'published',
    ]);

    $post->refresh();
    expect($post->status)->toBe(PostStatus::Published);
    expect($post->published_at)->not->toBeNull();
});

// =========================================================================
// DESTROY (Authenticated + Author only)
// =========================================================================

test('author can delete their post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

    $response->assertRedirect(route('posts.index'));
    expect(Post::find($post->id))->toBeNull();
    expect(Post::withTrashed()->find($post->id))->not->toBeNull(); // Soft deleted
});

test('non-author cannot delete post', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($otherUser)->delete(route('posts.destroy', $post));

    $response->assertForbidden();
    expect(Post::find($post->id))->not->toBeNull();
});

// =========================================================================
// BATCH DESTROY (Authenticated + Author only)
// =========================================================================

test('author can batch delete their posts', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('posts.batch-destroy'), [
        'ids' => $posts->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('success', '3 articles supprimés avec succès.');

    foreach ($posts as $post) {
        expect(Post::find($post->id))->toBeNull();
        expect(Post::withTrashed()->find($post->id))->not->toBeNull();
    }
});

test('author can batch delete a single post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('posts.batch-destroy'), [
        'ids' => [$post->id],
    ]);

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('success', '1 article supprimé avec succès.');
});

test('batch delete only deletes posts owned by the user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userPosts = Post::factory()->count(2)->create(['user_id' => $user->id]);
    $otherPosts = Post::factory()->count(2)->create(['user_id' => $otherUser->id]);

    $allIds = $userPosts->pluck('id')->merge($otherPosts->pluck('id'))->toArray();

    $response = $this->actingAs($user)->delete(route('posts.batch-destroy'), [
        'ids' => $allIds,
    ]);

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('success', '2 articles supprimés avec succès.');

    foreach ($userPosts as $post) {
        expect(Post::find($post->id))->toBeNull();
    }
    foreach ($otherPosts as $post) {
        expect(Post::find($post->id))->not->toBeNull();
    }
});

test('batch delete returns error when no posts can be deleted', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherPosts = Post::factory()->count(2)->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('posts.batch-destroy'), [
        'ids' => $otherPosts->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('posts.index'));
    $response->assertSessionHas('error', 'Aucun article n\'a pu être supprimé.');
});

test('batch delete requires at least one id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('posts.batch-destroy'), [
        'ids' => [],
    ]);

    $response->assertSessionHasErrors('ids');
});

test('batch delete requires authentication', function () {
    $posts = Post::factory()->count(2)->create();

    $response = $this->delete(route('posts.batch-destroy'), [
        'ids' => $posts->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('login'));
});
