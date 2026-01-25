<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Published => 'Publié',
            self::Scheduled => 'Programmé',
            self::Archived => 'Archivé',
        };
    }

    /**
     * Get a color for UI display (Tailwind classes).
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'green',
            self::Scheduled => 'blue',
            self::Archived => 'orange',
        };
    }

    /**
     * Check if the post is visible to the public.
     */
    public function isPublic(): bool
    {
        return $this === self::Published;
    }
}
