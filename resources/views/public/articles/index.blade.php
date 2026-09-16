@extends('layouts.public')

@section('title', 'North Bali Real Estate Guides & Insights | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', 'Helpful guides, investment tips, legal insights, and area guides for buying and owning real estate in North Bali.')
@section('canonical', route('articles.index'))

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ route('home') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Articles",
      "item": "{{ route('articles.index') }}"
    }
  ]
}
</script>
@endsection

@section('head_extra')
<style>
    .articles-index-page .articles-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
        margin-bottom: 48px;
    }
    .articles-index-page .article-card {
        background-color: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: var(--shadow-sm);
    }
    .articles-index-page .article-card-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background-color: var(--light-gray);
    }
    .articles-index-page .article-card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .articles-index-page .article-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .articles-index-page .category-badge {
        display: inline-block;
        background-color: #E0F2FE;
        color: #0369A1;
        font-size: 13px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .articles-index-page .article-date {
        font-size: 13px;
        color: var(--text-muted);
    }
    .articles-index-page .article-card-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-navy);
        line-height: 1.35;
        margin-bottom: 12px;
    }
    .articles-index-page .article-card-excerpt {
        font-size: 15px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    @media (max-width: 1024px) {
        .articles-index-page .articles-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .articles-index-page .articles-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="articles-index-page">
    <!-- Hero Section -->
    <section class="section-spacing bg-light-blue" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="container">
            <h1 style="margin-bottom: 12px;">Property Articles</h1>
            <p class="body-text" style="color: var(--text-secondary); max-width: 720px; margin-bottom: 20px;">
                Helpful information, tips, and insights about buying, owning, and investing in property in North Bali.
            </p>
            <div style="font-size: 14px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                <a href="{{ route('home') }}" style="color: var(--text-muted);">Home</a> &gt; <span>Articles</span>
            </div>
        </div>
    </section>

    <!-- Latest Articles Section -->
    <section class="section-spacing bg-white">
        <div class="container">
            <div style="text-align: center; max-width: 650px; margin: 0 auto 48px auto;">
                <h2>Latest Articles</h2>
                <p class="body-text" style="color: var(--text-secondary); margin-top: 8px;">
                    Explore useful guides and insights to help you make the right property decisions in Bali.
                </p>
            </div>

            @if($articles->count() > 0)
                <div class="articles-grid">
                    @foreach($articles as $article)
                        <div class="article-card">
                            <a href="{{ route('articles.show', $article->slug) }}">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }} - North Bali Real Estate Guide" class="article-card-img" loading="lazy" onError="this.onerror=null;this.src='{{ asset('images/sample-article.jpg') }}';">
                            </a>
                            <div class="article-card-body">
                                <div class="article-meta-row">
                                    <span class="category-badge">{{ $article->category }}</span>
                                    <span class="article-date">{{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}</span>
                                </div>
                                <h3 class="article-card-title">
                                    <a href="{{ route('articles.show', $article->slug) }}" style="color: var(--primary-navy); text-decoration: none;">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="article-card-excerpt">
                                    {{ Str::limit($article->excerpt, 120) }}
                                </p>
                                <a href="{{ route('articles.show', $article->slug) }}" class="btn btn-outline" style="width: 100%; border-color: #2563EB; color: #2563EB;">
                                    Read More &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($articles->hasPages())
                    <div style="display: flex; justify-content: center; margin-bottom: 48px;">
                        {{ $articles->links() }}
                    </div>
                @endif
            @else
                <div style="text-align: center; padding: 60px 20px; background-color: var(--light-gray); border-radius: var(--radius-md);">
                    <i data-lucide="file-text" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px;">No articles found</h3>
                    <p style="color: var(--text-secondary);">Check back soon for new property guides and North Bali market updates.</p>
                </div>
            @endif

            <!-- Bottom CTA Box -->
            <div style="background-color: #EFF6FF; border-radius: var(--radius-lg); padding: 48px 32px; text-align: center; margin-top: 48px;">
                <h2 style="font-size: 32px; color: var(--primary-navy); margin-bottom: 12px;">Still Have Questions?</h2>
                <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 24px auto;">
                    Our team is here to help. Get in touch with us for more information about properties in North Bali.
                </p>
                <a href="{{ route('contact') }}" class="btn btn-primary" style="background-color: #1E3A8A; border-color: #1E3A8A; padding: 12px 32px;">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
