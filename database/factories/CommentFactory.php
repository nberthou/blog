<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'content' => fake()->paragraph(),
        ];
    }

    /**
     * Set a specific author for the comment.
     */
    public function byAuthor(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Set this comment as a reply to another comment.
     */
    public function replyTo(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
        ]);
    }
}
