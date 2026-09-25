<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleHeroImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'admin@lovina.com',
        ]);

        Storage::fake('public');
    }

    public function test_uploaded_article_hero_image_is_converted_to_seo_webp_with_unique_suffix()
    {
        // Create a fake test image with non-SEO name
        $image = UploadedFile::fake()->image('IMG_8392.JPG', 1200, 800);

        $response = $this->actingAs($this->user)->post(route('admin.articles.store'), [
            'title' => 'How to Choose the Right Property in Bali',
            'slug' => 'how-to-choose-the-right-property-in-bali',
            'category' => 'Buying Guide',
            'featured_image' => $image,
            'excerpt' => 'A complete guide to choosing the right property in beautiful North Bali.',
            'content' => '<p>Article content with lots of great insights.</p>',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success');

        $article = Article::where('slug', 'how-to-choose-the-right-property-in-bali')->first();
        $this->assertNotNull($article);
        $this->assertNotNull($article->featured_image);

        // 1. Check filename format: articles/how-to-choose-the-right-property-in-bali-{4-hex}.webp
        $this->assertMatchesRegularExpression(
            '/^articles\/how-to-choose-the-right-property-in-bali-[a-z0-9]{4}\.webp$/',
            $article->featured_image
        );

        // 2. Check that file actually exists on public disk
        Storage::disk('public')->assertExists($article->featured_image);

        // 3. Verify that the file content is genuinely WebP (RIFF....WEBP header)
        $fileContent = Storage::disk('public')->get($article->featured_image);
        $this->assertStringStartsWith('RIFF', $fileContent);
        $this->assertStringContainsString('WEBP', substr($fileContent, 0, 16));

        // 4. Verify public image_url attribute resolves correctly
        $this->assertStringContainsString($article->featured_image, $article->image_url);
    }

    public function test_updating_article_hero_image_replaces_old_image_with_new_seo_webp()
    {
        $oldImage = UploadedFile::fake()->image('old_photo.png', 600, 400);

        $this->actingAs($this->user)->post(route('admin.articles.store'), [
            'title' => 'Initial Article Title',
            'slug' => 'initial-article-title',
            'category' => 'Market Insights',
            'featured_image' => $oldImage,
            'excerpt' => 'Initial excerpt',
            'content' => '<p>Initial content</p>',
            'status' => 'published',
        ]);

        $article = Article::where('slug', 'initial-article-title')->first();
        $oldStoredPath = $article->featured_image;
        Storage::disk('public')->assertExists($oldStoredPath);

        // Update with new image
        $newImage = UploadedFile::fake()->image('DSC_0091.JPEG', 800, 600);

        $updateResponse = $this->actingAs($this->user)->put(route('admin.articles.update', $article->id), [
            'title' => 'Updated Article Title',
            'slug' => 'updated-article-title',
            'category' => 'Market Insights',
            'featured_image' => $newImage,
            'excerpt' => 'Updated excerpt',
            'content' => '<p>Updated content</p>',
            'status' => 'published',
        ]);

        $updateResponse->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $newStoredPath = $article->featured_image;

        $this->assertNotEquals($oldStoredPath, $newStoredPath);
        $this->assertMatchesRegularExpression(
            '/^articles\/updated-article-title-[a-z0-9]{4}\.webp$/',
            $newStoredPath
        );

        Storage::disk('public')->assertExists($newStoredPath);
        Storage::disk('public')->assertMissing($oldStoredPath);
    }
}
