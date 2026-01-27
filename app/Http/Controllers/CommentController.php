<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('create', Comment::class);

        $validated = $request->validated();

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Commentaire ajouté avec succès.');
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $comment->update([
            'content' => $request->validated('content'),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Commentaire mis à jour avec succès.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->back()
            ->with('success', 'Commentaire supprimé avec succès.');
    }
}
