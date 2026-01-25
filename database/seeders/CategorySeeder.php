<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Demo categories with French content.
     */
    private array $demoCategories = [
        [
            'name' => 'Laravel',
            'description' => 'Tutoriels et astuces sur le framework PHP Laravel.',
        ],
        [
            'name' => 'Vue.js',
            'description' => 'Articles sur Vue.js et son écosystème.',
        ],
        [
            'name' => 'PHP',
            'description' => 'Actualités et bonnes pratiques PHP.',
        ],
        [
            'name' => 'JavaScript',
            'description' => 'Tout sur JavaScript, ES6+ et les frameworks modernes.',
        ],
        [
            'name' => 'DevOps',
            'description' => 'Docker, CI/CD, déploiement et infrastructure.',
        ],
        [
            'name' => 'Sécurité',
            'description' => 'Sécurité web, authentification et bonnes pratiques.',
        ],
        [
            'name' => 'Performance',
            'description' => 'Optimisation et amélioration des performances.',
        ],
        [
            'name' => 'Tests',
            'description' => 'Tests unitaires, fonctionnels et bonnes pratiques.',
        ],
        [
            'name' => 'CSS',
            'description' => 'CSS, Tailwind et design responsive.',
        ],
        [
            'name' => 'Architecture',
            'description' => 'Patterns, clean code et architecture logicielle.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect($this->demoCategories)->map(function ($data) {
            return Category::factory()->create($data);
        });

        $this->assignCategoriesToPosts($categories);

        $this->command->info('Created '.count($this->demoCategories).' categories and assigned them to posts.');
    }

    /**
     * Assign categories to existing posts based on their content.
     */
    private function assignCategoriesToPosts($categories): void
    {
        $categoryMap = [
            'Laravel' => ['Laravel', 'Fortify', 'Eloquent', 'Artisan'],
            'Vue.js' => ['Vue', 'Pinia', 'Composition API', 'Vuex'],
            'PHP' => ['PHP', 'Composer'],
            'JavaScript' => ['JavaScript', 'TypeScript', 'ES6'],
            'DevOps' => ['Docker', 'CI/CD', 'déploiement', 'Reverb'],
            'Sécurité' => ['sécurité', 'authentification', 'CSRF', 'XSS', 'Fortify'],
            'Performance' => ['performance', 'optimis', 'cache', 'Queue'],
            'Tests' => ['test', 'Pest', 'PHPUnit'],
            'CSS' => ['CSS', 'Tailwind', 'design'],
            'Architecture' => ['architecture', 'SOLID', 'DDD', 'Clean'],
        ];

        $categoriesByName = $categories->keyBy('name');

        Post::all()->each(function ($post) use ($categoryMap, $categoriesByName) {
            $postCategories = [];
            $searchText = $post->title.' '.$post->content;

            foreach ($categoryMap as $categoryName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (stripos($searchText, $keyword) !== false) {
                        $postCategories[] = $categoriesByName[$categoryName]->id;
                        break;
                    }
                }
            }

            if (empty($postCategories)) {
                $postCategories[] = $categoriesByName->random()->id;
            }

            $post->categories()->sync(array_unique($postCategories));
        });
    }
}
