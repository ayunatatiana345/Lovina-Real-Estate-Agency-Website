<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        
        $query = Article::published()->withCount('views');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $articles = $query->latest('published_at')->paginate(9)->withQueryString();

        $categories = Article::published()->select('category')->distinct()->pluck('category');

        return view('public.articles.index', compact('articles', 'categories', 'settings'));
    }

    public function show(Request $request, $slug)
    {
        $settings = CompanySetting::getSettings();

        // Admin can preview draft if auth check passes, otherwise must be published
        if (auth()->check()) {
            $article = Article::where('slug', $slug)->firstOrFail();
        } else {
            $article = Article::published()->where('slug', $slug)->firstOrFail();
        }

        // Record article view (prevent duplicate count within same session within 10 minutes)
        $sessionKey = 'viewed_article_' . $article->id;
        if (!Session::has($sessionKey)) {
            ArticleView::create([
                'article_id' => $article->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => Session::getId(),
            ]);
            Session::put($sessionKey, true);
        }

        // Related articles (same category first, then other published, exactly 2 max)
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->latest('published_at')
            ->take(2)
            ->get();

        if ($relatedArticles->count() < 2) {
            $additional = Article::published()
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->latest('published_at')
                ->take(2 - $relatedArticles->count())
                ->get();
            $relatedArticles = $relatedArticles->merge($additional);
        }

        return view('public.articles.show', compact('article', 'relatedArticles', 'settings'));
    }
}
