@extends('layouts.public')

@section('title', $article->seo_title . ' | ' . ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency'))
@section('meta_description', $article->seo_description)
@section('canonical', route('articles.show', $article->slug))
@section('og_type', 'article')
@section('og_image', $article->image_url)

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
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ $article->title }}",
      "item": "{{ route('articles.show', $article->slug) }}"
    }
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Article",
  "headline": "{{ $article->title }}",
  "description": "{{ $article->seo_description }}",
  "image": "{{ $article->image_url }}",
  "datePublished": "{{ $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String() }}",
  "dateModified": "{{ $article->updated_at ? $article->updated_at->toIso8601String() : $article->created_at->toIso8601String() }}",
  "mainEntityOfPage": {
    "@@type": "WebPage",
    "@@id": "{{ route('articles.show', $article->slug) }}"
  },
  "author": {
    "@@type": "Organization",
    "name": "{{ $article->author_name ?? ($settings->company_name ?? 'PT Lovina North Bali Real Estate Agency') }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "{{ $settings->company_name ?? 'PT Lovina North Bali Real Estate Agency' }}",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('images/black logo lovina.png') }}"
    }
  }
}
</script>
@endsection

@section('head_extra')
<style>
    .article-detail-page .article-hero {
        background-color: var(--light-blue);
        padding: 40px 0 50px 0;
    }
    .article-detail-page .article-hero-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
        align-items: center;
    }
    .article-detail-page .article-hero-img {
        width: 100%;
        height: 320px;
        object-fit: cover;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    .article-detail-page .article-content-container {
        max-width: 840px;
        margin: 0 auto;
        padding: 56px 24px 72px 24px;
    }
    .article-detail-page .article-body-text {
        font-size: 17px;
        line-height: 1.85;
        color: #334155;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    .article-detail-page .article-body-text p {
        text-align: justify;
        text-justify: inter-word;
        margin-bottom: 24px;
        overflow-wrap: break-word;
        word-break: normal;
    }
    .article-detail-page .article-body-text h1,
    .article-detail-page .article-body-text h2, 
    .article-detail-page .article-body-text h3,
    .article-detail-page .article-body-text h4,
    .article-detail-page .article-body-text h5,
    .article-detail-page .article-body-text h6 {
        text-align: left;
        color: var(--primary-navy);
    }
    .article-detail-page .article-body-text h2 {
        font-size: 26px;
        font-weight: 700;
        margin-top: 40px;
        margin-bottom: 16px;
        line-height: 1.3;
    }
    .article-detail-page .article-body-text h3 {
        font-size: 20px;
        font-weight: 600;
        margin-top: 28px;
        margin-bottom: 12px;
        line-height: 1.35;
    }
    .article-detail-page .article-body-text ul, 
    .article-detail-page .article-body-text ol {
        margin-bottom: 24px;
        padding-left: 24px;
    }
    .article-detail-page .article-body-text li {
        margin-bottom: 10px;
        line-height: 1.75;
        text-align: justify;
        text-justify: inter-word;
        overflow-wrap: break-word;
        word-break: normal;
    }
    .article-detail-page .article-body-text a {
        color: #2563EB;
        text-decoration: underline;
    }
    .article-detail-page .article-body-text a:hover {
        color: var(--primary-navy);
    }
    .article-detail-page .article-content-img {
        width: 100%;
        border-radius: var(--radius-md);
        margin: 32px 0 8px 0;
        object-fit: cover;
        display: block;
    }
    .article-detail-page .article-img-caption {
        font-size: 14px;
        color: var(--text-muted);
        font-style: italic;
        text-align: center;
        margin-bottom: 32px;
    }
    .article-detail-page .article-table {
        width: 100%;
        border-collapse: collapse;
        margin: 32px 0;
        font-size: 15px;
    }
    .article-detail-page .article-table th {
        background-color: #F1F5F9;
        color: var(--primary-navy);
        font-weight: 600;
        text-align: left;
        padding: 12px 16px;
        border-bottom: 2px solid #CBD5E1;
    }
    .article-detail-page .article-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #E2E8F0;
        color: #334155;
    }
    .article-detail-page .article-table tr:hover td {
        background-color: #F8FAFC;
    }

    /* Related Articles List Style (Not cards) */
    .article-detail-page .related-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .article-detail-page .related-item-row {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 16px 20px;
        background-color: var(--white);
        border: 1px solid #E2E8F0;
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: inherit;
    }
    .article-detail-page .related-item-row:hover {
        border-color: #93C5FD;
    }
    .article-detail-page .related-thumb {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
        background-color: #E2E8F0;
    }
    .article-detail-page .related-item-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .article-detail-page .related-item-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    .article-detail-page .category-badge-sm {
        color: #0369A1;
        font-weight: 600;
    }
    .article-detail-page .related-item-date {
        color: var(--text-muted);
    }
    .article-detail-page .related-item-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--primary-navy);
        line-height: 1.35;
        margin: 0;
        text-align: left;
    }
    .article-detail-page .related-item-link {
        font-size: 14px;
        font-weight: 600;
        color: #2563EB;
        display: inline-flex;
        align-items: center;
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .article-detail-page .article-hero-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .article-detail-page .related-item-row {
            flex-direction: column;
            align-items: flex-start;
        }
        .article-detail-page .related-thumb {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endsection

@section('content')
<div class="article-detail-page">
    <!-- 1. Article Hero Banner -->
    <section class="article-hero">
        <div class="container">
            <!-- Breadcrumbs -->
            <div style="font-size: 14px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                <a href="{{ route('home') }}" style="color: var(--text-muted);">Home</a> &gt;
                <a href="{{ route('articles.index') }}" style="color: var(--text-muted);">Articles</a> &gt;
                <span style="color: var(--text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 300px;">{{ $article->title }}</span>
            </div>

            <div class="article-hero-grid">
                <div>
                    <span class="category-badge" style="margin-bottom: 16px; display: inline-block;">{{ $article->category }}</span>
                    <h1 style="font-size: 40px; font-weight: 700; color: var(--primary-navy); line-height: 1.25; margin-bottom: 16px; text-align: left;">
                        {{ $article->title }}
                    </h1>
                    <p style="font-size: 17px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px; text-align: left;">
                        {{ $article->excerpt }}
                    </p>
                    <div style="display: flex; align-items: center; gap: 24px; font-size: 14px; color: var(--text-muted);">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="calendar" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                            {{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="clock" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                            {{ $article->reading_time }}
                        </span>
                    </div>
                </div>

                <div>
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }} - North Bali Property Guide" class="article-hero-img" onError="this.onerror=null;this.src='{{ asset('images/sample-article.jpg') }}';">
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Main Article Content (Centered, single column, justified paragraphs) -->
    <section class="bg-white">
        <div class="article-content-container">
            <div class="article-body-text">
                {!! $article->content !!}
            </div>
        </div>
    </section>

    <!-- 3. Related Articles Section (Exactly 2 items, simple horizontal list format, no cards/sidebar) -->
    @if(isset($relatedArticles) && $relatedArticles->count() > 0)
    <section class="section-spacing" style="background-color: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 48px 0;">
        <div class="container" style="max-width: 840px;">
            <div style="margin-bottom: 24px;">
                <h2 style="font-size: 26px; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px; text-align: left;">Related Articles</h2>
                <p style="color: var(--text-secondary); font-size: 15px; margin: 0; text-align: left;">Explore more helpful articles about property in Bali.</p>
            </div>

            <div class="related-list">
                @foreach($relatedArticles as $rel)
                    <a href="{{ route('articles.show', $rel->slug) }}" class="related-item-row">
                        <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" class="related-thumb" onError="this.onerror=null;this.src='{{ asset('images/sample-article.jpg') }}';">
                        <div class="related-item-content">
                            <div class="related-item-meta">
                                <span class="category-badge-sm">{{ $rel->category }}</span>
                                <span class="related-item-date">&bull; {{ $rel->published_at ? $rel->published_at->format('M d, Y') : $rel->created_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="related-item-title">{{ $rel->title }}</h3>
                            <span class="related-item-link">Read More &rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 4. Simple Contact CTA (Centered text with generous whitespace, no cards/boxes/borders) -->
    <section class="section-spacing bg-white" style="text-align: center; padding: 72px 24px;">
        <div class="container" style="max-width: 680px; margin: 0 auto;">
            <span style="font-size: 13px; font-weight: 700; color: #2563EB; letter-spacing: 1.5px; text-transform: uppercase;">HAVE QUESTIONS?</span>
            <h2 style="font-size: 36px; font-weight: 700; color: var(--primary-navy); margin-top: 10px; margin-bottom: 14px;">We're here to help</h2>
            <p style="color: var(--text-secondary); font-size: 16px; line-height: 1.6; margin-bottom: 32px;">
                Get in touch with our team for more information about properties in North Bali or to discuss your real estate goals.
            </p>
            <a href="{{ route('contact') }}" class="btn btn-primary" style="background-color: #1E3A8A; border-color: #1E3A8A; padding: 14px 36px;">
                Contact Us &rarr;
            </a>
        </div>
    </section>
</div>
@endsection
