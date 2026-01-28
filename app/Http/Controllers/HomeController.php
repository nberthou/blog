<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // 5 derniers articles publies pour le carousel
        $featuredPosts = Post::query()
            ->published()
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->recent()
            ->take(5)
            ->get()
            ->append(['featured_image_url']);

        // Filtres
        $categorySlug = $request->query('category');
        $searchQuery = $request->query('search');

        // Query des articles
        $postsQuery = Post::query()
            ->published()
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->recent();

        if ($categorySlug) {
            $postsQuery->whereHas('categories', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($searchQuery) {
            $postsQuery->where(fn ($q) => $q
                ->where('title', 'like', "%{$searchQuery}%")
                ->orWhere('excerpt', 'like', "%{$searchQuery}%"));
        }

        $posts = $postsQuery->paginate(12)->withQueryString();
        $posts->getCollection()->transform(fn ($post) => $post->append(['featured_image_url']));

        // Categories avec comptage d'articles (filter in PHP to support SQLite)
        $categories = Category::query()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->posts_count > 0)
            ->values();

        return Inertia::render('Welcome', [
            'featuredPosts' => $featuredPosts,
            'posts' => $posts,
            'categories' => $categories,
            'filters' => [
                'category' => $categorySlug,
                'search' => $searchQuery,
            ],
        ]);
    }
}
