<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();

        $query = Article::withCount('views');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $articles = $query->latest('created_at')->paginate(10)->withQueryString();

        $categories = Article::select('category')->distinct()->pluck('category');

        return view('admin.articles.index', compact('articles', 'categories', 'settings'));
    }

    public function create()
    {
        $settings = CompanySetting::getSettings();
        $categories = ['Buying Guide', 'Investment Tips', 'Property Tips', 'Market Insights', 'Location Guide'];

        return view('admin.articles.create', compact('settings', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'category' => 'required|string|max:100',
            'featured_image' => 'nullable|image|max:5120',
            'excerpt' => 'required|string|max:1000',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|string|max:100',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated) {
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
            } else {
                $validated['slug'] = Str::slug($validated['slug']);
            }

            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Article::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }

            if ($request->hasFile('featured_image')) {
                $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
            }

            if ($validated['status'] === 'published' && empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }

            if (empty($validated['author_name'])) {
                $validated['author_name'] = auth()->user()->name ?? 'Lovina Agency';
            }

            $article = Article::create($validated);

            return redirect()->route('admin.articles.index')->with('success', 'Article "' . $article->title . '" created successfully.');
        });
    }

    public function edit($id)
    {
        $settings = CompanySetting::getSettings();
        $article = Article::findOrFail($id);
        $categories = ['Buying Guide', 'Investment Tips', 'Property Tips', 'Market Insights', 'Location Guide'];

        return view('admin.articles.edit', compact('article', 'settings', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . $id,
            'category' => 'required|string|max:100',
            'featured_image' => 'nullable|image|max:5120',
            'excerpt' => 'required|string|max:1000',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|string|max:100',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $article, $validated) {
            $validated['slug'] = Str::slug($validated['slug']);

            if ($request->hasFile('featured_image')) {
                if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                    Storage::disk('public')->delete($article->featured_image);
                }
                $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
            }

            if ($validated['status'] === 'published' && empty($validated['published_at'])) {
                $validated['published_at'] = $article->published_at ?? now();
            }

            $article->update($validated);

            return redirect()->route('admin.articles.index')->with('success', 'Article "' . $article->title . '" updated successfully.');
        });
    }

    public function destroy($id)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $article = Article::findOrFail($id);
            $title = $article->title;
            $article->delete(); // Soft delete

            return redirect()->route('admin.articles.index')->with('success', 'Article "' . $title . '" deleted successfully.');
        });
    }
}
