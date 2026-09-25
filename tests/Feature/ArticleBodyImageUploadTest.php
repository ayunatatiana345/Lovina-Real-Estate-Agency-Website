<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleBodyImageUploadTest extends TestCase
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

    public function test_upload_content_image_converts_to_seo_webp_with_content_sequence_and_unique_suffix()
    {
        $file = UploadedFile::fake()->image('IMG_8392.JPG', 1200, 800);

        $response = $this->actingAs($this->user)->postJson(route('admin.articles.upload-content-image'), [
            'image' => $file,
            'title' => 'How to Choose the Right Property in Bali',
            'slug' => 'how-to-choose-the-right-property-in-bali',
            'alt' => 'Luxury Bali Villa',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['url']);
        $this->assertNotEmpty($data['filename']);

        // Check filename matches: how-to-choose-the-right-property-in-bali-content-01-[a-z0-9]{4}.webp
        $this->assertMatchesRegularExpression(
            '/^how-to-choose-the-right-property-in-bali-content-01-[a-z0-9]{4}\.webp$/',
            $data['filename']
        );

        // Verify storage file exists and is genuine WebP
        Storage::disk('public')->assertExists($data['path']);
        $rawBytes = Storage::disk('public')->get($data['path']);
        $this->assertStringStartsWith('RIFF', $rawBytes);
        $this->assertStringContainsString('WEBP', substr($rawBytes, 0, 16));
    }

    public function test_create_article_persists_processed_content_image_in_database_and_displays_publicly()
    {
        // 1. Simulate body image upload
        $file = UploadedFile::fake()->image('IMG_8392.JPG', 1200, 800);
        $uploadResp = $this->actingAs($this->user)->postJson(route('admin.articles.upload-content-image'), [
            'image' => $file,
            'title' => 'How to Choose the Right Property in Bali',
        ]);
        $imageUrl = $uploadResp->json('url');

        // 2. Submit new article with the processed WebP in content
        $contentHtml = '<p>Welcome to our guide.</p><img src="' . $imageUrl . '" alt="Bali Villa" class="article-content-img"><p>More details...</p>';

        $postResponse = $this->actingAs($this->user)->post(route('admin.articles.store'), [
            'title' => 'How to Choose the Right Property in Bali',
            'slug' => 'how-to-choose-the-right-property-in-bali',
            'category' => 'Buying Guide',
            'excerpt' => 'Guide summary',
            'content' => $contentHtml,
            'status' => 'published',
        ]);

        $postResponse->assertRedirect(route('admin.articles.index'));

        // 3. Verify database content contains the final processed WebP path
        $article = Article::where('slug', 'how-to-choose-the-right-property-in-bali')->first();
        $this->assertNotNull($article);
        $this->assertStringContainsString($imageUrl, $article->content);
        $this->assertStringNotContainsString('IMG_8392.JPG', $article->content);

        // 4. Verify public article page loads the processed image
        $publicResponse = $this->get(route('articles.show', $article->slug));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($imageUrl, false);
    }

    public function test_edit_article_allows_replacing_image_with_new_processed_webp()
    {
        // Initial article with an external image URL
        $initialContent = '<p>Intro</p><img src="https://example.com/external-bali.jpg" alt="Initial image" class="article-content-img"><p>Outro</p>';
        $article = Article::create([
            'title' => 'Investment in Lovina Bali',
            'slug' => 'investment-in-lovina-bali',
            'category' => 'Investment Tips',
            'excerpt' => 'Investment overview',
            'content' => $initialContent,
            'status' => 'published',
        ]);

        // Upload replacement image
        $newFile = UploadedFile::fake()->image('camera_photo_01.PNG', 1000, 700);
        $uploadResp = $this->actingAs($this->user)->postJson(route('admin.articles.upload-content-image'), [
            'image' => $newFile,
            'title' => $article->title,
            'slug' => $article->slug,
        ]);
        $uploadResp->assertStatus(200);
        $newWebpUrl = $uploadResp->json('url');

        // Replace the external image with the newly processed local WebP
        $updatedContent = str_replace('https://example.com/external-bali.jpg', $newWebpUrl, $initialContent);

        $updateResponse = $this->actingAs($this->user)->put(route('admin.articles.update', $article->id), [
            'title' => $article->title,
            'slug' => $article->slug,
            'category' => $article->category,
            'excerpt' => $article->excerpt,
            'content' => $updatedContent,
            'status' => 'published',
        ]);

        $updateResponse->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertStringContainsString($newWebpUrl, $article->content);
        $this->assertStringNotContainsString('https://example.com/external-bali.jpg', $article->content);

        // Verify public article page loads the replacement image
        $publicResponse = $this->get(route('articles.show', $article->slug));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($newWebpUrl, false);
    }

    public function test_external_image_url_is_preserved_without_local_conversion()
    {
        $externalUrl = 'https://images.unsplash.com/photo-1537996194471-e657df975ab4';
        $contentHtml = '<p>Intro</p><img src="' . $externalUrl . '" alt="Unsplash Bali" class="article-content-img"><p>End</p>';

        $this->actingAs($this->user)->post(route('admin.articles.store'), [
            'title' => 'Bali Culture and Living',
            'slug' => 'bali-culture-and-living',
            'category' => 'Location Guide',
            'excerpt' => 'Culture summary',
            'content' => $contentHtml,
            'status' => 'published',
        ]);

        $article = Article::where('slug', 'bali-culture-and-living')->first();
        $this->assertNotNull($article);
        $this->assertStringContainsString($externalUrl, $article->content);

        // Confirm no local download was performed for external URL
        Storage::disk('public')->assertMissing('articles/bali-culture-and-living-content-01.webp');

        // Confirm public page renders the external URL unchanged
        $publicResponse = $this->get(route('articles.show', $article->slug));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($externalUrl, false);
    }
}
