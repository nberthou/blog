<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(rand(4, 8)),
            'excerpt' => fake()->paragraph(2),
            'content' => $this->generateContent(),
            'status' => PostStatus::Published,
            'view_count' => fake()->numberBetween(0, 5000),
            'published_at' => $createdAt,
            'created_at' => $createdAt,
            'updated_at' => fake()->dateTimeBetween($createdAt, 'now'),
        ];
    }

    /**
     * Generate realistic blog content with multiple paragraphs.
     */
    private function generateContent(): string
    {
        $paragraphs = fake()->paragraphs(rand(5, 10));

        return implode("\n\n", $paragraphs);
    }

    /**
     * Set the post as a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    /**
     * Set the post as published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Set the post as scheduled for future publication.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Scheduled,
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }

    /**
     * Set the post as archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Archived,
            'published_at' => fake()->dateTimeBetween('-1 year', '-3 months'),
        ]);
    }

    /**
     * Set a specific author for the post.
     */
    public function byAuthor(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Set the post as popular (high view count).
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(5000, 50000),
        ]);
    }

    /**
     * Set the post with zero views.
     */
    public function withNoViews(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => 0,
        ]);
    }
}
