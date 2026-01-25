<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'view_count',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'view_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Configure the sluggable options.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Register media collections for the post.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    /**
     * Register media conversions for the post.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->nonQueued();
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the author of the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Only published posts.
     *
     * Usage: Post::published()->get()
     * This filters posts that have 'published' status AND have a published_at
     * date in the past (or now).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: Only draft posts.
     *
     * Usage: Post::draft()->get()
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft);
    }

    /**
     * Scope: Only scheduled posts.
     *
     * Usage: Post::scheduled()->get()
     * Returns posts that are scheduled for future publication.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Scheduled)
            ->where('published_at', '>', now());
    }

    /**
     * Scope: Only archived posts.
     *
     * Usage: Post::archived()->get()
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Archived);
    }

    /**
     * Scope: Posts by a specific author.
     *
     * Usage: Post::byAuthor($user)->get()
     *        Post::byAuthor($userId)->get()
     */
    public function scopeByAuthor(Builder $query, User|int $author): Builder
    {
        $authorId = $author instanceof User ? $author->id : $author;

        return $query->where('user_id', $authorId);
    }

    /**
     * Scope: Order by most recent first.
     *
     * Usage: Post::latest()->get()
     * Note: Laravel already provides latest(), but this is more explicit.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    /**
     * Scope: Most viewed posts.
     *
     * Usage: Post::popular()->take(10)->get()
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('view_count');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured_image');
    }

    /**
     * Get the featured image thumbnail URL.
     */
    public function getFeaturedImageThumbnailAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured_image', 'thumbnail');
    }

    /**
     * Check if the post is published.
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === PostStatus::Published
            && $this->published_at
            && $this->published_at->isPast();
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Publish the post immediately.
     */
    public function publish(): void
    {
        $this->update([
            'status' => PostStatus::Published,
            'published_at' => $this->published_at ?? now(),
        ]);
    }

    /**
     * Schedule the post for a future date.
     */
    public function schedule(\DateTimeInterface $publishAt): void
    {
        $this->update([
            'status' => PostStatus::Scheduled,
            'published_at' => $publishAt,
        ]);
    }

    /**
     * Archive the post.
     */
    public function archive(): void
    {
        $this->update([
            'status' => PostStatus::Archived,
        ]);
    }

    /**
     * Revert to draft.
     */
    public function unpublish(): void
    {
        $this->update([
            'status' => PostStatus::Draft,
        ]);
    }
}
