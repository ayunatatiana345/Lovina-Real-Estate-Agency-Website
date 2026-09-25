<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleViewCounterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Article $articleA;
    protected Article $articleB;
    protected Article $articleC;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@lovina.com',
        ]);

        $this->articleA = Article::create([
            'title' => 'Article One Title',
            'slug' => 'article-one-title',
            'category' => 'Buying Guide',
            'excerpt' => 'Excerpt for article one',
            'content' => '<p>Content for article one</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'Lovina Team',
        ]);

        $this->articleB = Article::create([
            'title' => 'Article Two Title',
            'slug' => 'article-two-title',
            'category' => 'Investment Tips',
            'excerpt' => 'Excerpt for article two',
            'content' => '<p>Content for article two</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'Lovina Team',
        ]);

        $this->articleC = Article::create([
            'title' => 'Article Three Title',
            'slug' => 'article-three-title',
            'category' => 'Market Insights',
            'excerpt' => 'Excerpt for article three',
            'content' => '<p>Content for article three</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'Lovina Team',
        ]);
    }

    public function test_view_counter_increments_only_target_article_when_visited()
    {
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleA->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleB->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleC->id)->count());

        // Visit Article A
        $responseA = $this->get(route('articles.show', $this->articleA->slug));
        $responseA->assertStatus(200);

        $this->assertEquals(1, ArticleView::where('article_id', $this->articleA->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleB->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleC->id)->count());

        // Refresh Article A in same session (Duplicate prevention)
        $responseARefresh = $this->get(route('articles.show', $this->articleA->slug));
        $responseARefresh->assertStatus(200);

        $this->assertEquals(1, ArticleView::where('article_id', $this->articleA->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleB->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleC->id)->count());

        // Visit Article B in same session (should increment Article B, leave Article A at 1)
        $responseB = $this->get(route('articles.show', $this->articleB->slug));
        $responseB->assertStatus(200);

        $this->assertEquals(1, ArticleView::where('article_id', $this->articleA->id)->count());
        $this->assertEquals(1, ArticleView::where('article_id', $this->articleB->id)->count());
        $this->assertEquals(0, ArticleView::where('article_id', $this->articleC->id)->count());

        // Visit Article C in same session (should increment Article C)
        $responseC = $this->get(route('articles.show', $this->articleC->slug));
        $responseC->assertStatus(200);

        $this->assertEquals(1, ArticleView::where('article_id', $this->articleA->id)->count());
        $this->assertEquals(1, ArticleView::where('article_id', $this->articleB->id)->count());
        $this->assertEquals(1, ArticleView::where('article_id', $this->articleC->id)->count());
    }

    public function test_admin_views_polling_api_returns_accurate_database_counts()
    {
        // Add 3 views to A, 5 views to B, 1 view to C
        ArticleView::create(['article_id' => $this->articleA->id]);
        ArticleView::create(['article_id' => $this->articleA->id]);
        ArticleView::create(['article_id' => $this->articleA->id]);

        for ($i = 0; $i < 5; $i++) {
            ArticleView::create(['article_id' => $this->articleB->id]);
        }

        ArticleView::create(['article_id' => $this->articleC->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.articles.views-counts'));
        $response->assertStatus(200);
        $response->assertJson([
            $this->articleA->id => 3,
            $this->articleB->id => 5,
            $this->articleC->id => 1,
        ]);

        // Admin page table displays exact counts
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.articles.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('data-article-id="' . $this->articleA->id . '"', false);
        $indexResponse->assertSee('data-article-id="' . $this->articleB->id . '"', false);
        $indexResponse->assertSee('data-article-id="' . $this->articleC->id . '"', false);
    }
}
