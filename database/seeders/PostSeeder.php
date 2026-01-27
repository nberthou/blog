<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use League\CommonMark\CommonMarkConverter;

class PostSeeder extends Seeder
{
    private CommonMarkConverter $markdown;

    public function __construct()
    {
        $this->markdown = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Demo posts with realistic French content.
     */
    private array $demoPosts = [
        [
            'title' => 'Débuter avec Laravel 12 : Guide complet pour les débutants',
            'excerpt' => 'Découvrez comment démarrer votre premier projet Laravel 12 avec ce guide étape par étape.',
            'content' => "Laravel 12 est la dernière version du framework PHP le plus populaire. Dans cet article, nous allons explorer les nouveautés et comment bien démarrer.\n\n## Installation\n\nPour commencer, assurez-vous d'avoir PHP 8.2 ou supérieur installé sur votre machine. Ensuite, utilisez Composer pour créer un nouveau projet :\n\n```bash\ncomposer create-project laravel/laravel mon-projet\n```\n\n## Structure du projet\n\nLaravel suit une architecture MVC (Model-View-Controller) qui sépare clairement les responsabilités de votre application.\n\n## Conclusion\n\nLaravel 12 offre une expérience de développement moderne et agréable. N'hésitez pas à explorer la documentation officielle pour approfondir vos connaissances.",
        ],
        [
            'title' => 'Les meilleures pratiques Vue.js en 2026',
            'excerpt' => 'Optimisez vos applications Vue.js avec ces bonnes pratiques recommandées par la communauté.',
            'content' => "Vue.js continue d'évoluer et avec lui, les meilleures pratiques. Voici un tour d'horizon des recommandations actuelles.\n\n## Composition API\n\nLa Composition API est maintenant la norme pour les nouveaux projets. Elle offre une meilleure organisation du code et une réutilisabilité accrue.\n\n## TypeScript\n\nL'adoption de TypeScript avec Vue.js améliore significativement la maintenabilité de vos projets.\n\n## Gestion d'état\n\nPinia est devenu le standard pour la gestion d'état, remplaçant Vuex dans les nouveaux projets.",
        ],
        [
            'title' => 'Inertia.js : Le pont parfait entre Laravel et Vue',
            'excerpt' => 'Comment Inertia.js révolutionne le développement d\'applications monolithiques modernes.',
            'content' => "Inertia.js permet de créer des SPA sans la complexité d'une API. C'est le meilleur des deux mondes.\n\n## Pourquoi Inertia ?\n\nAvec Inertia, vous gardez vos routes et contrôleurs Laravel tout en bénéficiant de la réactivité de Vue.js.\n\n## Comment ça marche ?\n\nInertia remplace les vues Blade par des composants Vue, tout en gérant automatiquement la navigation et les requêtes AJAX.\n\n## Avantages\n\n- Pas besoin d'API REST\n- Partage de données simplifié\n- Navigation fluide sans rechargement de page",
        ],
        [
            'title' => 'Authentification sécurisée avec Laravel Fortify',
            'excerpt' => 'Implémentez une authentification robuste avec 2FA et vérification email.',
            'content' => "La sécurité est primordiale. Laravel Fortify offre une solution complète et personnalisable.\n\n## Configuration\n\nFortify s'installe facilement et offre toutes les fonctionnalités d'authentification nécessaires.\n\n## Double authentification\n\nL'ajout de la 2FA renforce considérablement la sécurité de vos utilisateurs.\n\n## Bonnes pratiques\n\n- Utilisez des mots de passe forts\n- Activez la vérification email\n- Implémentez le rate limiting",
        ],
        [
            'title' => 'Optimiser les performances de votre application Laravel',
            'excerpt' => 'Techniques avancées pour accélérer vos applications Laravel en production.',
            'content' => "Une application performante offre une meilleure expérience utilisateur. Voici comment optimiser Laravel.\n\n## Cache\n\nUtilisez le cache pour les requêtes coûteuses et les configurations.\n\n## Eager Loading\n\nÉvitez le problème N+1 en chargeant les relations à l'avance.\n\n## Queue\n\nDéléguez les tâches lourdes aux jobs en arrière-plan.\n\n## OPcache\n\nActivez OPcache en production pour des gains significatifs.",
        ],
        [
            'title' => 'Tests automatisés avec Pest PHP',
            'excerpt' => 'Découvrez pourquoi Pest est devenu le framework de test préféré de la communauté Laravel.',
            'content' => "Pest offre une syntaxe élégante et expressive pour vos tests PHP.\n\n## Syntaxe simple\n\n```php\nit('can create a post', function () {\n    expect(Post::count())->toBe(0);\n});\n```\n\n## Intégration Laravel\n\nPest s'intègre parfaitement avec Laravel grâce au plugin dédié.\n\n## Couverture de code\n\nGénérez facilement des rapports de couverture avec --coverage.",
        ],
        [
            'title' => 'Tailwind CSS : Au-delà des bases',
            'excerpt' => 'Techniques avancées pour maîtriser Tailwind CSS dans vos projets.',
            'content' => "Tailwind CSS a révolutionné notre façon d'écrire du CSS. Allons plus loin.\n\n## Configuration personnalisée\n\nLe fichier tailwind.config.js permet d'adapter Tailwind à votre design system.\n\n## Plugins\n\nDe nombreux plugins officiels et communautaires étendent les capacités de Tailwind.\n\n## Performance\n\nPurgeCSS intégré garantit des fichiers CSS minimaux en production.",
        ],
        [
            'title' => 'API RESTful avec Laravel : Guide complet',
            'excerpt' => 'Construisez des APIs robustes et bien documentées avec Laravel.',
            'content' => "Les APIs sont au cœur des applications modernes. Laravel excelle dans ce domaine.\n\n## Resources\n\nLes API Resources transforment vos modèles en réponses JSON cohérentes.\n\n## Sanctum\n\nLaravel Sanctum offre une authentification légère pour vos SPAs et applications mobiles.\n\n## Versioning\n\nPensez à versionner vos APIs dès le départ pour faciliter les évolutions.",
        ],
        [
            'title' => 'Gérer les médias avec Spatie Media Library',
            'excerpt' => 'Uploadez, transformez et servez des fichiers facilement avec ce package puissant.',
            'content' => "Spatie Media Library simplifie considérablement la gestion des fichiers.\n\n## Installation\n\nLe package s'intègre facilement à n'importe quel modèle Eloquent.\n\n## Conversions\n\nCréez automatiquement des thumbnails et différentes tailles d'images.\n\n## Collections\n\nOrganisez vos médias en collections pour une meilleure structure.",
        ],
        [
            'title' => 'Déploiement Laravel avec Docker',
            'excerpt' => 'Containerisez votre application Laravel pour un déploiement reproductible.',
            'content' => "Docker assure que votre application fonctionne de manière identique partout.\n\n## Dockerfile\n\nCréez un Dockerfile optimisé pour la production Laravel.\n\n## Docker Compose\n\nOrchestrez vos services (PHP, Nginx, MySQL, Redis) facilement.\n\n## CI/CD\n\nIntégrez Docker dans votre pipeline de déploiement continu.",
        ],
        [
            'title' => 'State Management avec Pinia et Vue 3',
            'excerpt' => 'Gérez l\'état de votre application Vue.js de manière simple et performante.',
            'content' => "Pinia est le successeur officiel de Vuex pour Vue 3.\n\n## Stores\n\nCréez des stores modulaires et typés avec une syntaxe intuitive.\n\n## Devtools\n\nL'intégration avec Vue DevTools offre un debugging efficace.\n\n## SSR\n\nPinia supporte nativement le rendu côté serveur.",
        ],
        [
            'title' => 'Sécurité web : Protégez votre application Laravel',
            'excerpt' => 'Les vulnérabilités courantes et comment s\'en prémunir avec Laravel.',
            'content' => "La sécurité ne doit jamais être négligée. Laravel intègre de nombreuses protections.\n\n## CSRF\n\nLa protection CSRF est activée par défaut sur toutes les routes POST.\n\n## XSS\n\nBlade échappe automatiquement les données affichées.\n\n## SQL Injection\n\nEloquent et Query Builder protègent contre les injections SQL.\n\n## Headers de sécurité\n\nConfigurez les headers HTTP appropriés pour renforcer la sécurité.",
        ],
        [
            'title' => 'Queues et Jobs : Traitement asynchrone avec Laravel',
            'excerpt' => 'Déléguez les tâches lourdes pour une application plus réactive.',
            'content' => "Les queues permettent de traiter des tâches en arrière-plan.\n\n## Configuration\n\nLaravel supporte plusieurs drivers : database, Redis, SQS, etc.\n\n## Jobs\n\nCréez des jobs pour encapsuler votre logique métier.\n\n## Supervision\n\nUtilisez Laravel Horizon pour monitorer vos queues Redis.",
        ],
        [
            'title' => 'Accessibilité web : Créer des interfaces inclusives',
            'excerpt' => 'Rendez vos applications accessibles à tous les utilisateurs.',
            'content' => "L'accessibilité n'est pas optionnelle, c'est une nécessité.\n\n## Sémantique HTML\n\nUtilisez les bonnes balises HTML pour leur signification.\n\n## ARIA\n\nLes attributs ARIA complètent l'accessibilité native du HTML.\n\n## Tests\n\nTestez régulièrement avec des lecteurs d'écran et des outils automatisés.",
        ],
        [
            'title' => 'WebSockets en temps réel avec Laravel Reverb',
            'excerpt' => 'Implémentez des fonctionnalités temps réel dans votre application Laravel.',
            'content' => "Laravel Reverb est le nouveau serveur WebSocket officiel de Laravel.\n\n## Installation\n\nReverb s'installe et se configure en quelques minutes.\n\n## Broadcasting\n\nDiffusez des événements en temps réel à vos utilisateurs.\n\n## Scaling\n\nReverb supporte le scaling horizontal pour les applications à fort trafic.",
        ],
        [
            'title' => 'Clean Architecture en PHP',
            'excerpt' => 'Organisez votre code pour une maintenabilité à long terme.',
            'content' => "Une bonne architecture facilite l'évolution et la maintenance de votre code.\n\n## Principes SOLID\n\nAppliquez les principes SOLID pour un code flexible et testable.\n\n## Domain-Driven Design\n\nLe DDD aide à modéliser des domaines métier complexes.\n\n## Actions\n\nLes Actions encapsulent la logique métier de manière réutilisable.",
        ],
        [
            'title' => 'Migration de Vue 2 vers Vue 3',
            'excerpt' => 'Guide pratique pour migrer votre application Vue.js en douceur.',
            'content' => "La migration vers Vue 3 apporte de nombreux avantages.\n\n## Outils de migration\n\nVue propose des outils pour faciliter la transition.\n\n## Breaking changes\n\nIdentifiez et corrigez les incompatibilités principales.\n\n## Composition API\n\nMigrez progressivement vers la Composition API.",
        ],
        [
            'title' => 'Monitoring et Logging avec Laravel',
            'excerpt' => 'Surveillez votre application en production pour détecter les problèmes rapidement.',
            'content' => "Un bon monitoring est essentiel pour maintenir une application saine.\n\n## Logging\n\nConfigurez les channels de log appropriés pour chaque environnement.\n\n## Exception tracking\n\nIntégrez des services comme Sentry pour tracker les erreurs.\n\n## Métriques\n\nCollectez des métriques pour comprendre le comportement de votre application.",
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create demo posts with predefined content
        foreach ($this->demoPosts as $index => $postData) {
            $daysAgo = count($this->demoPosts) - $index;
            $publishedAt = now()->subDays($daysAgo * 3);

            Post::factory()
                ->byAuthor($user)
                ->create([
                    'title' => $postData['title'],
                    'excerpt' => $postData['excerpt'],
                    'content' => $this->markdown->convert($postData['content'])->getContent(),
                    'published_at' => $publishedAt,
                    'created_at' => $publishedAt->copy()->subHours(rand(1, 24)),
                    'view_count' => fake()->numberBetween(50, 3000),
                ]);
        }

        // Add a few drafts
        Post::factory()
            ->count(2)
            ->byAuthor($user)
            ->draft()
            ->create();

        // Add a scheduled post
        Post::factory()
            ->byAuthor($user)
            ->scheduled()
            ->create([
                'title' => 'Article programmé : Les nouveautés de PHP 9',
                'excerpt' => 'Un aperçu des fonctionnalités attendues dans PHP 9.',
            ]);

        $this->command->info('Created '.count($this->demoPosts).' published posts, 2 drafts, and 1 scheduled post.');
    }
}
