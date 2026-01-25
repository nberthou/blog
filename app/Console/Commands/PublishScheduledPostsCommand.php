<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all scheduled posts that have reached their publication date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $posts = Post::where('status', PostStatus::Scheduled)
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Aucun article programmé à publier.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($posts as $post) {
            $post->update(['status' => PostStatus::Published]);
            $this->line("Publié : {$post->title}");
            $count++;
        }

        $this->info("{$count} article(s) publié(s) avec succès.");

        return self::SUCCESS;
    }
}
