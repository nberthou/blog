<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     * Public access is allowed.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the category.
     * Public access is allowed.
     */
    public function view(?User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create categories.
     * Only administrators can create categories.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the category.
     * Only administrators can update categories.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the category.
     * Only administrators can delete categories.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }
}
