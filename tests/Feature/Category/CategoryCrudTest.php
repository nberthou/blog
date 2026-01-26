<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->withoutVite();
});

// =========================================================================
// INDEX (Public)
// =========================================================================

test('categories index page displays all categories', function () {
    $categories = Category::factory()->count(3)->create();

    $response = $this->get(route('categories.index'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('categories/Index', false)
        ->has('categories.data', 3)
    );
});

test('categories index page is accessible without authentication', function () {
    $response = $this->get(route('categories.index'));

    $response->assertOk();
});

// =========================================================================
// SHOW (Public)
// =========================================================================

test('category can be viewed by anyone', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('categories.show', $category));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('categories/Show', false)
        ->where('category.id', $category->id)
    );
});

// =========================================================================
// CREATE (Admin only)
// =========================================================================

test('create category page requires authentication', function () {
    $response = $this->get(route('categories.create'));

    $response->assertRedirect(route('login'));
});

test('non-admin user cannot access create category page', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('categories.create'));

    $response->assertForbidden();
});

test('admin can access create category page', function () {
    $admin = User::factory()->create(['id' => 1]);

    $response = $this->actingAs($admin)->get(route('categories.create'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('categories/Create', false)
    );
});

// =========================================================================
// STORE (Admin only)
// =========================================================================

test('admin can create a category', function () {
    $admin = User::factory()->create(['id' => 1]);

    $response = $this->actingAs($admin)->post(route('categories.store'), [
        'name' => 'Technologie',
        'description' => 'Articles sur la technologie',
    ]);

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('success');

    $category = Category::first();
    expect($category)->not->toBeNull();
    expect($category->name)->toBe('Technologie');
    expect($category->slug)->toBe('technologie');
    expect($category->description)->toBe('Articles sur la technologie');
});

test('non-admin user cannot create a category', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Technologie',
    ]);

    $response->assertForbidden();
    expect(Category::count())->toBe(0);
});

test('category creation requires name', function () {
    $admin = User::factory()->create(['id' => 1]);

    $response = $this->actingAs($admin)->post(route('categories.store'), [
        'description' => 'Une description',
    ]);

    $response->assertSessionHasErrors('name');
});

test('category slug is generated automatically from name', function () {
    $admin = User::factory()->create(['id' => 1]);

    $this->actingAs($admin)->post(route('categories.store'), [
        'name' => 'Mon Article Spécial',
    ]);

    $category = Category::first();
    expect($category->slug)->toBe('mon-article-special');
});

// =========================================================================
// EDIT (Admin only)
// =========================================================================

test('edit category page requires authentication', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('categories.edit', $category));

    $response->assertRedirect(route('login'));
});

test('non-admin user cannot access edit category page', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->get(route('categories.edit', $category));

    $response->assertForbidden();
});

test('admin can access edit category page', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->get(route('categories.edit', $category));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('categories/Edit', false)
        ->where('category.id', $category->id)
    );
});

// =========================================================================
// UPDATE (Admin only)
// =========================================================================

test('admin can update a category', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->put(route('categories.update', $category), [
        'name' => 'Nouveau Nom',
        'description' => 'Nouvelle description',
    ]);

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('success');

    $category->refresh();
    expect($category->name)->toBe('Nouveau Nom');
    expect($category->description)->toBe('Nouvelle description');
});

test('non-admin user cannot update a category', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Original']);

    $response = $this->actingAs($user)->put(route('categories.update', $category), [
        'name' => 'Modifié',
    ]);

    $response->assertForbidden();
    expect($category->fresh()->name)->toBe('Original');
});

test('category update requires name', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->put(route('categories.update', $category), [
        'description' => 'Une description',
    ]);

    $response->assertSessionHasErrors('name');
});

test('category slug must be unique on update', function () {
    $admin = User::factory()->create(['id' => 1]);
    $existingCategory = Category::factory()->create(['slug' => 'technologie']);
    $category = Category::factory()->create(['slug' => 'autre']);

    $response = $this->actingAs($admin)->put(route('categories.update', $category), [
        'name' => 'Test',
        'slug' => 'technologie',
    ]);

    $response->assertSessionHasErrors('slug');
});

test('category can keep its own slug on update', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create(['slug' => 'technologie']);

    $response = $this->actingAs($admin)->put(route('categories.update', $category), [
        'name' => 'Tech Modifié',
        'slug' => 'technologie',
    ]);

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHasNoErrors();
});

// =========================================================================
// DESTROY (Admin only)
// =========================================================================

test('admin can delete a category without posts', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('success');
    expect(Category::find($category->id))->toBeNull();
});

test('non-admin user cannot delete a category', function () {
    User::factory()->create(['id' => 1]); // Reserve admin ID
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertForbidden();
    expect(Category::find($category->id))->not->toBeNull();
});

test('cannot delete category with associated posts', function () {
    $admin = User::factory()->create(['id' => 1]);
    $category = Category::factory()->create();
    $post = Post::factory()->create(['user_id' => $admin->id]);
    $post->categories()->attach($category);

    $response = $this->actingAs($admin)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('error', 'Impossible de supprimer cette catégorie car elle contient des articles.');
    expect(Category::find($category->id))->not->toBeNull();
});

test('delete category page requires authentication', function () {
    $category = Category::factory()->create();

    $response = $this->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('login'));
});
