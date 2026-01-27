<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(?User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     * Only the author or an admin can update.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the comment.
     * Only the author or an admin can delete.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }
}
