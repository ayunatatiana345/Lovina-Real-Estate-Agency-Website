<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsArticlesUiConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'admin@lovina.com',
        ]);
    }

    public function test_admin_news_articles_page_renders_add_article_button_matching_properties_style()
    {
        $response = $this->actingAs($this->user)->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertSee('id="btn-add-article"', false);
        $response->assertSee('class="btn btn-primary"', false);
        $response->assertSee('Add New Article', false);
    }

    public function test_admin_news_articles_page_displays_dismissible_success_notification()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['success' => 'Article "Test Article" created successfully.'])
            ->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertSee('id="articles-success-toast"', false);
        $response->assertSee('Article &quot;Test Article&quot; created successfully.', false);
        $response->assertSee('&times;', false);
        
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Article &quot;Test Article&quot; created successfully.'));
    }
}
