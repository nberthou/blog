<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Requests\Post\BatchDeletePostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of posts.
     * Shows published posts + user's own drafts/scheduled/archived posts.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $posts = Post::query()
            ->where(function ($query) use ($user) {
                $query->published();

                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->recent()
            ->paginate(10);

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): Response
    {
        return Inertia::render('posts/Create', [
            'statuses' => collect(PostStatus::cases())->map(fn ($status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ]),
        ]);
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $status = PostStatus::tryFrom($validated['status'] ?? 'draft') ?? PostStatus::Draft;

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $status,
            'published_at' => $this->determinePublishedAt($status, $validated['published_at'] ?? null),
        ]);

        if ($request->hasFile('featured_image')) {
            $post->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_image');
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post, Request $request): Response
    {
        $this->authorize('view', $post);

        $post->incrementViewCount();

        $post->load(['author:id,name', 'categories:id,name,slug']);

        $comments = $post->comments()
            ->with(['author:id,name', 'replies.author:id,name'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $canEdit = $request->user()?->id === $post->user_id;

        return Inertia::render('posts/Show', [
            'post' => $post->append(['featured_image_url', 'is_published']),
            'canEdit' => $canEdit,
            'comments' => $comments,
            'commentsCount' => $post->comments()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        $post->load('categories:id,name,slug');

        return Inertia::render('posts/Edit', [
            'post' => $post->append(['featured_image_url']),
            'statuses' => collect(PostStatus::cases())->map(fn ($status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ]),
        ]);
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validated();

        $status = PostStatus::tryFrom($validated['status'] ?? $post->status->value) ?? $post->status;

        $post->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $status,
            'published_at' => $this->determinePublishedAt($status, $validated['published_at'] ?? null, $post),
        ]);

        if ($request->boolean('remove_featured_image')) {
            $post->clearMediaCollection('featured_image');
        } elseif ($request->hasFile('featured_image')) {
            $post->addMediaFromRequest('featured_image')
                ->toMediaCollection('featured_image');
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Remove multiple posts from storage.
     */
    public function batchDestroy(BatchDeletePostRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');

        $posts = Post::whereIn('id', $ids)->get();

        $deletedCount = 0;
        foreach ($posts as $post) {
            if ($request->user()->can('delete', $post)) {
                $post->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {
            return redirect()
                ->route('posts.index')
                ->with('error', 'Aucun article n\'a pu être supprimé.');
        }

        $message = $deletedCount === 1
            ? '1 article supprimé avec succès.'
            : "{$deletedCount} articles supprimés avec succès.";

        return redirect()
            ->route('posts.index')
            ->with('success', $message);
    }

    /**
     * Determine the published_at date based on status.
     */
    private function determinePublishedAt(PostStatus $status, ?string $publishedAt, ?Post $existingPost = null): ?\DateTimeInterface
    {
        return match ($status) {
            PostStatus::Published => $existingPost?->published_at ?? ($publishedAt ? new \DateTime($publishedAt) : now()),
            PostStatus::Scheduled => $publishedAt ? new \DateTime($publishedAt) : null,
            default => null,
        };
    }
}
