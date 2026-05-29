<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ImageGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer les répertoires
        Storage::disk('public')->deleteDirectory('articles');
        Storage::disk('public')->deleteDirectory('categories');
        Storage::disk('public')->makeDirectory('articles');
        Storage::disk('public')->makeDirectory('categories');

        // UTILISATEURS
        $admin = User::create([
            'name'      => 'Admin Principal',
            'email'     => 'admin@blogmoderne.fr',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'bio'       => 'Administrateur et éditeur en chef du blog.',
            'is_active' => true,
        ]);

        $author1 = User::create([
            'name'      => 'Sophie Martin',
            'email'     => 'sophie@blogmoderne.fr',
            'password'  => Hash::make('password'),
            'role'      => 'author',
            'bio'       => 'Développeuse fullstack et passionnée de JavaScript.',
            'is_active' => true,
        ]);

        $author2 = User::create([
            'name'      => 'Thomas Dubois',
            'email'     => 'thomas@blogmoderne.fr',
            'password'  => Hash::make('password'),
            'role'      => 'author',
            'bio'       => 'Expert Laravel et architecte logiciel senior.',
            'is_active' => true,
        ]);

        // CATÉGORIES
        $categories = [
            ['name' => 'Laravel', 'description' => 'Framework PHP moderne pour applications web robustes.', 'color' => '#FF2D20'],
            ['name' => 'Vue.js', 'description' => 'Framework progressif JavaScript pour interfaces interactives.', 'color' => '#42B883'],
            ['name' => 'Nuxt.js', 'description' => 'Framework Vue.js avec SSR et génération statique.', 'color' => '#00DC82'],
            ['name' => 'Architecture', 'description' => 'Design patterns, clean code et bonnes pratiques.', 'color' => '#6366F1'],
            ['name' => 'DevOps', 'description' => 'CI/CD, containerisation et orchestration.', 'color' => '#F59E0B'],
            ['name' => 'Sécurité', 'description' => 'Sécurisation des applications et authentification.', 'color' => '#EF4444'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $imagePath = ImageGeneratorService::generateCategoryImage($cat['name'], $cat['color']);
            $categoryModels[$cat['name']] = Category::create([
                ...$cat,
                'image' => $imagePath,
            ]);
        }

        // ARTICLES
        $articles = [
            [
                'title'       => 'Maîtriser Laravel 11 : Guide complet des nouveautés',
                'category'    => 'Laravel',
                'author'      => $author2,
                'excerpt'     => 'Découvrez toutes les nouvelles fonctionnalités de Laravel 11.',
                'content'     => '<h2>Introduction</h2><p>Laravel 11 apporte son lot de nouveautés passionnantes.</p>',
                'tags'        => ['laravel', 'php', 'framework'],
                'is_featured' => true,
                'views'       => 1250,
                'likes'       => 89,
            ],
            [
                'title'       => 'Vue 3 Composition API : Patterns avancés',
                'category'    => 'Vue.js',
                'author'      => $author1,
                'excerpt'     => 'Explorez les patterns avancés de la Composition API.',
                'content'     => '<h2>La révolution Composition API</h2><p>La Composition API représente une approche différente.</p>',
                'tags'        => ['vue', 'javascript', 'frontend'],
                'is_featured' => true,
                'views'       => 980,
                'likes'       => 67,
            ],
            [
                'title'       => 'Nuxt 3 : SSR et performance optimale',
                'category'    => 'Nuxt.js',
                'author'      => $author1,
                'excerpt'     => 'Comment tirer parti du SSR de Nuxt 3 pour des applications rapides.',
                'content'     => '<h2>Pourquoi Nuxt 3 ?</h2><p>Nuxt 3 est une refonte complète du framework.</p>',
                'tags'        => ['nuxt', 'ssr', 'performance'],
                'is_featured' => true,
                'views'       => 1120,
                'likes'       => 78,
            ],
            [
                'title'       => 'Architecture hexagonale en PHP',
                'category'    => 'Architecture',
                'author'      => $author2,
                'excerpt'     => 'Implémentez une architecture hexagonale robuste.',
                'content'     => '<h2>Architecture hexagonale : principes</h2><p>L\'architecture hexagonale isole la logique métier.</p>',
                'tags'        => ['architecture', 'design-patterns'],
                'is_featured' => false,
                'views'       => 756,
                'likes'       => 52,
            ],
            [
                'title'       => 'Docker & Kubernetes : Déploiement moderne',
                'category'    => 'DevOps',
                'author'      => $admin,
                'excerpt'     => 'Guide pratique pour containeriser et orchestrer vos applications.',
                'content'     => '<h2>Docker : conteneurisation moderne</h2><p>Docker révolutionne le déploiement.</p>',
                'tags'        => ['docker', 'kubernetes', 'devops'],
                'is_featured' => false,
                'views'       => 892,
                'likes'       => 61,
            ],
            [
                'title'       => 'Sécuriser vos APIs REST avec Laravel Sanctum',
                'category'    => 'Sécurité',
                'author'      => $author2,
                'excerpt'     => 'Implémentation complète de l\'authentification API.',
                'content'     => '<h2>Laravel Sanctum expliqué</h2><p>Sanctum offre une solution d\'authentification légère.</p>',
                'tags'        => ['security', 'api', 'authentication'],
                'is_featured' => false,
                'views'       => 1045,
                'likes'       => 73,
            ],
            [
                'title'       => 'Pinia : State management moderne pour Vue 3',
                'category'    => 'Vue.js',
                'author'      => $author1,
                'excerpt'     => 'Gestion d\'état simplifiée avec Pinia.',
                'content'     => '<h2>Pinia : le nouveau standard</h2><p>Pinia est la bibliothèque officielle de gestion d\'état.</p>',
                'tags'        => ['vue', 'pinia', 'state-management'],
                'is_featured' => false,
                'views'       => 678,
                'likes'       => 45,
            ],
            [
                'title'       => 'Tests automatisés avec PHPUnit et Pest',
                'category'    => 'Laravel',
                'author'      => $author2,
                'excerpt'     => 'Stratégies de tests pour garantir la qualité.',
                'content'     => '<h2>Tests automatisés en PHP</h2><p>Les tests sont essentiels pour la qualité.</p>',
                'tags'        => ['testing', 'phpunit', 'quality'],
                'is_featured' => false,
                'views'       => 534,
                'likes'       => 38,
            ],
            [
                'title'       => 'Tailwind CSS : Design system scalable',
                'category'    => 'Architecture',
                'author'      => $author1,
                'excerpt'     => 'Créez un design system maintenable avec Tailwind CSS.',
                'content'     => '<h2>Tailwind CSS : revolution du design</h2><p>Tailwind offre une approche utility-first.</p>',
                'tags'        => ['css', 'tailwind', 'design'],
                'is_featured' => false,
                'views'       => 823,
                'likes'       => 59,
            ],
            [
                'title'       => 'CI/CD avec GitHub Actions pour Laravel',
                'category'    => 'DevOps',
                'author'      => $admin,
                'excerpt'     => 'Pipeline complet d\'intégration et déploiement continu.',
                'content'     => '<h2>CI/CD : automatiser tout</h2><p>GitHub Actions permet d\'automatiser vos workflows.</p>',
                'tags'        => ['ci-cd', 'github-actions', 'automation'],
                'is_featured' => false,
                'views'       => 712,
                'likes'       => 48,
            ],
            [
                'title'       => 'API Platform : REST et GraphQL avec Symfony',
                'category'    => 'Architecture',
                'author'      => $author2,
                'excerpt'     => 'Construisez des APIs robustes avec API Platform.',
                'content'     => '<h2>API Platform expliqué</h2><p>API Platform simplifie la création d\'APIs.</p>',
                'tags'        => ['api', 'rest', 'graphql'],
                'is_featured' => false,
                'views'       => 445,
                'likes'       => 31,
            ],
            [
                'title'       => 'WebSockets temps réel avec Laravel Echo',
                'category'    => 'Laravel',
                'author'      => $author2,
                'excerpt'     => 'Implémentez des fonctionnalités temps réel.',
                'content'     => '<h2>WebSockets : communication temps réel</h2><p>Laravel Echo facilite l\'implémentation.</p>',
                'tags'        => ['websockets', 'real-time', 'laravel'],
                'is_featured' => false,
                'views'       => 598,
                'likes'       => 42,
            ],
        ];

        foreach ($articles as $index => $articleData) {
            $imagePath = ImageGeneratorService::generateArticleImage(
                $articleData['title'],
                $articleData['category'],
                $index + 1
            );

            Article::create([
                'title'        => $articleData['title'],
                'excerpt'      => $articleData['excerpt'],
                'content'      => $articleData['content'],
                'status'       => 'published',
                'author_id'    => $articleData['author']->id,
                'category_id'  => $categoryModels[$articleData['category']]->id,
                'cover_image'  => $imagePath,
                'tags'         => $articleData['tags'],
                'views_count'  => $articleData['views'],
                'likes_count'  => $articleData['likes'],
                'is_featured'  => $articleData['is_featured'],
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('✅ Database seeded with PNG images!');
        $this->command->info('📧 Admin: admin@blogmoderne.fr / password');
        $this->command->info('📧 Author 1: sophie@blogmoderne.fr / password');
        $this->command->info('📧 Author 2: thomas@blogmoderne.fr / password');
    }
}
