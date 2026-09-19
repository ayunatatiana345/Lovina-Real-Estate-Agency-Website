@extends('layouts.admin')

@section('title', 'News Articles Management')
@section('page_title', 'News Articles')

@section('content')
<!-- Page Header + Add Button -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">News Articles</h2>
        <p style="color: #64748B; font-size: 14px;">Manage news, articles, and property insights for your website.</p>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: #1E40AF; border-color: #1E40AF; text-decoration: none;">
        <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Add New Article
    </a>
</div>

<!-- Filter Section -->
<div class="admin-card" style="margin-bottom: 24px; padding: 20px;">
    <form action="{{ route('admin.articles.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
        <div style="flex: 2; min-width: 240px; position: relative;">
            <i data-lucide="search" style="width: 16px; height: 16px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, category, or keyword..." class="form-control" style="padding-left: 38px; width: 100%;">
        </div>

        <div style="flex: 1; min-width: 160px;">
            <select name="category" class="form-control">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <select name="status" class="form-control">
                <option value="all">All Statuses</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="background-color: #1E40AF; border-color: #1E40AF; padding: 10px 20px;">
            Filter
        </button>

        @if(request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.articles.index') }}" style="color: #2563EB; font-size: 14px; font-weight: 500; text-decoration: underline;">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Articles Data Table -->
<div class="admin-card">
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox"></th>
                    <th style="width: 80px;">Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Published Date</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td><input type="checkbox" value="{{ $article->id }}"></td>
                        <td>
                            <img src="{{ $article->image_url }}" alt="" style="width: 64px; height: 44px; object-fit: cover; border-radius: 6px; background-color: #E2E8F0;" onError="this.onerror=null;this.src='{{ asset('images/sample-article.jpg') }}';">
                        </td>
                        <td style="font-weight: 600; color: #0F172A; max-width: 260px;">
                            <a href="{{ route('admin.articles.edit', $article->id) }}" style="color: #0F172A; text-decoration: none;">
                                {{ $article->title }}
                            </a>
                        </td>
                        <td>
                            <span class="category-badge" style="background-color: #EFF6FF; color: #1D4ED8; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px;">
                                {{ $article->category }}
                            </span>
                        </td>
                        <td style="font-size: 13px; color: #475569;">{{ $article->author_name ?? 'Lovina Agency' }}</td>
                        <td style="font-size: 13px; color: #64748B; white-space: nowrap;">
                            {{ $article->published_at ? $article->published_at->format('d M Y, h:i A \W\I\T\A') : $article->created_at->format('d M Y, h:i A \W\I\T\A') }}
                        </td>
                        <td style="font-size: 14px; font-weight: 600; color: #0F172A;">
                            {{ number_format($article->views_count) }}
                        </td>
                        <td>
                            @if($article->status === 'published')
                                <span style="background-color: #DCFCE7; color: #15803D; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                                    Published
                                </span>
                            @else
                                <span style="background-color: #FEF9C3; color: #A16207; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 13px; border-color: #CBD5E1; color: #334155; text-decoration: none;">
                                    Edit
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 6px 10px; font-size: 13px; border-color: #FCA5A5; color: #DC2626; background-color: #FEF2F2;">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #64748B;">
                            No news articles found. Click "+ Add New Article" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
        <div style="font-size: 13px; color: #64748B;">
            Showing {{ $articles->firstItem() ?? 0 }} to {{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} articles
        </div>
        <div>
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection
