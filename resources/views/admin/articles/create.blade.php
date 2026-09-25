@extends('layouts.admin')

@section('title', 'Add New Article')
@section('page_title', 'Add New Article')

@section('head_extra')
<style>
    .editor-tab-btn {
        padding: 8px 16px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        background: none;
        color: #64748B;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .editor-tab-btn.active {
        color: #1E40AF;
        border-bottom-color: #1E40AF;
    }
    .editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        background-color: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-bottom: none;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .editor-btn {
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: background-color 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .editor-btn:hover {
        background-color: #EFF6FF;
        border-color: #93C5FD;
        color: #1D4ED8;
    }
    .editor-visual-box {
        width: 100%;
        min-height: 380px;
        border: 1px solid #CBD5E1;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        padding: 18px 20px;
        font-family: inherit;
        font-size: 15px;
        line-height: 1.8;
        color: #1E293B;
        background: #FFFFFF;
        outline: none;
        box-sizing: border-box;
        overflow-y: auto;
    }
    .editor-visual-box:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .editor-visual-box h1 {
        font-size: 26px;
        font-weight: 700;
        color: #0F172A;
        margin: 20px 0 10px 0;
    }
    .editor-visual-box h2 {
        font-size: 22px;
        font-weight: 700;
        color: #0F172A;
        margin: 18px 0 10px 0;
    }
    .editor-visual-box h3 {
        font-size: 18px;
        font-weight: 600;
        color: #0F172A;
        margin: 16px 0 8px 0;
    }
    .editor-visual-box p {
        margin: 0 0 16px 0;
    }
    .editor-visual-box ul, .editor-visual-box ol {
        padding-left: 24px;
        margin: 0 0 16px 0;
    }
    .editor-visual-box li {
        margin-bottom: 6px;
    }
    .editor-visual-box table {
        width: 100%;
        border-collapse: collapse;
        margin: 16px 0;
    }
    .editor-visual-box th, .editor-visual-box td {
        border: 1px solid #CBD5E1;
        padding: 8px 12px;
        text-align: left;
    }
    .editor-visual-box th {
        background-color: #F8FAFC;
        font-weight: 600;
    }
    .editor-visual-box img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 12px 0;
    }
    .editor-visual-box a {
        color: #2563EB;
        text-decoration: underline;
    }
    .editor-visual-box:empty:before {
        content: attr(data-placeholder);
        color: #94A3B8;
        pointer-events: none;
    }
    .image-dropzone {
        border: 2px dashed #CBD5E1;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        background-color: #F8FAFC;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .image-dropzone:hover {
        border-color: #2563EB;
        background-color: #EFF6FF;
    }
    .live-preview-box {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    /* Public detail simulation styles */
    .preview-hero {
        background-color: #F0F7FF;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .preview-body-text {
        font-size: 16px;
        line-height: 1.8;
        color: #334155;
    }
    .preview-body-text h2 {
        font-size: 24px;
        font-weight: 700;
        color: #0F172A;
        margin-top: 28px;
        margin-bottom: 12px;
    }
    .preview-body-text h3 {
        font-size: 19px;
        font-weight: 600;
        color: #0F172A;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .preview-body-text p {
        margin-bottom: 18px;
    }
    .preview-body-text ul, .preview-body-text ol {
        padding-left: 24px;
        margin-bottom: 18px;
    }
    .preview-body-text li {
        margin-bottom: 8px;
    }
    .preview-body-text table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .preview-body-text th, .preview-body-text td {
        border: 1px solid #CBD5E1;
        padding: 8px 12px;
        text-align: left;
    }
    .preview-body-text th {
        background-color: #F1F5F9;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline" style="border-color: #CBD5E1; color: #475569; padding: 8px 14px; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to Articles
            </a>
            <div>
                <h2 style="font-size: 22px; font-weight: 700; color: #0F172A; margin: 0;">Add New Article</h2>
                <span style="color: #64748B; font-size: 13px;">Create and publish news articles for the public site</span>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="submit" name="submit_action" value="draft" class="btn btn-outline" style="border-color: #CBD5E1; color: #334155; padding: 10px 20px;">
                Save Draft
            </button>
            <button type="submit" name="submit_action" value="publish" class="btn btn-primary" style="background-color: #1E40AF; border-color: #1E40AF; padding: 10px 24px;">
                Publish Article
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; color: #991B1B;">
            <strong style="font-size: 14px; display: block; margin-bottom: 4px;">Please fix the following errors:</strong>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid -->
    <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 24px; align-items: start;">
        <!-- Left Column: Main Editor -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Article Information Card -->
            <div class="admin-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 16px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">
                    Basic Information
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <label class="form-label" style="font-weight: 600; color: #0F172A; display: block; margin-bottom: 6px;">
                        Article Title <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="title" id="articleTitle" value="{{ old('title') }}" required placeholder="e.g. How to Choose the Right Property in Bali" class="form-control" style="width: 100%; font-size: 16px; font-weight: 600;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #0F172A; display: block; margin-bottom: 6px;">
                            Slug <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" name="slug" id="articleSlug" value="{{ old('slug') }}" placeholder="how-to-choose-property-in-bali" class="form-control" style="width: 100%;">
                        <div style="font-size: 11px; color: #64748B; margin-top: 4px;">Public URL path: /articles/{slug}</div>
                    </div>

                    <div>
                        <label class="form-label" style="font-weight: 600; color: #0F172A; display: block; margin-bottom: 6px;">
                            Category <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="category" id="articleCategory" required class="form-control" style="width: 100%;">
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 600; color: #0F172A; display: block; margin-bottom: 6px;">
                        Author Name
                    </label>
                    <input type="text" name="author_name" value="{{ old('author_name', auth()->user()->name ?? 'Lovina Agency') }}" placeholder="Lovina Agency" class="form-control" style="width: 100%;">
                </div>
            </div>

            <!-- Featured Hero Image Card -->
            <div class="admin-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 16px; border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">
                    Hero / Featured Image
                </h3>

                <div id="imagePreviewContainer" style="display: none; margin-bottom: 16px; text-align: center;">
                    <img id="imagePreviewImg" src="" alt="Hero Preview" style="max-width: 100%; max-height: 280px; object-fit: cover; border-radius: 8px; border: 1px solid #CBD5E1;">
                    <div style="margin-top: 8px; font-size: 13px; color: #16A34A; font-weight: 600;" id="imagePreviewFileName"></div>
                </div>

                <div class="image-dropzone" onclick="document.getElementById('featuredImageInput').click();">
                    <i data-lucide="image" style="width: 36px; height: 36px; color: #94A3B8; margin-bottom: 8px;"></i>
                    <div style="font-size: 14px; font-weight: 600; color: #2563EB;">Click to select hero image</div>
                    <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Recommended size: 1200 x 630px (JPG, PNG or WebP, max 5MB)</div>
                </div>
                <input type="file" name="featured_image" id="featuredImageInput" accept="image/*" style="display: none;" onchange="previewHeroImage(this)">
            </div>

            <!-- Excerpt Card -->
            <div class="admin-card" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="form-label" style="font-weight: 600; color: #0F172A; margin: 0;">
                        Article Excerpt / Summary <span style="color: #DC2626;">*</span>
                    </label>
                    <span id="excerptCharCount" style="font-size: 12px; color: #64748B;">0 / 500</span>
                </div>
                <textarea name="excerpt" id="articleExcerpt" maxlength="500" required rows="3" placeholder="Enter a compelling summary of the article..." class="form-control" style="width: 100%;" oninput="updateExcerptCount()">{{ old('excerpt') }}</textarea>
            </div>

            <!-- Content Editor & Live Preview Card -->
            <div class="admin-card" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 12px; margin-bottom: 16px;">
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="editor-tab-btn active" id="btnTabWrite" onclick="switchEditorTab('write')">
                            <i data-lucide="edit-3" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i> Write Content
                        </button>
                        <button type="button" class="editor-tab-btn" id="btnTabPreview" onclick="switchEditorTab('preview')">
                            <i data-lucide="eye" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i> Live Public Preview
                        </button>
                    </div>
                    <span id="contentWordCount" style="font-size: 12px; color: #64748B;">0 words</span>
                </div>

                <!-- WRITE PANEL -->
                <div id="panelWrite">
                    <div class="editor-toolbar">
                        <button type="button" class="editor-btn" onclick="execFormat('p')" title="Normal Paragraph">Paragraph</button>
                        <button type="button" class="editor-btn" onclick="execFormat('h1')" title="Heading 1"><b>H1</b></button>
                        <button type="button" class="editor-btn" onclick="execFormat('h2')" title="Heading 2"><b>H2</b></button>
                        <button type="button" class="editor-btn" onclick="execFormat('h3')" title="Heading 3"><b>H3</b></button>
                        <button type="button" class="editor-btn" onclick="execFormat('bold')" title="Bold (Ctrl+B)"><b>B</b></button>
                        <button type="button" class="editor-btn" onclick="execFormat('italic')" title="Italic (Ctrl+I)"><i>I</i></button>
                        <button type="button" class="editor-btn" onclick="execFormat('ul')" title="Bullet List">• List</button>
                        <button type="button" class="editor-btn" onclick="execFormat('ol')" title="Numbered List">1. List</button>
                        <button type="button" class="editor-btn" onclick="execFormat('link')" title="Insert Link">Link</button>
                        <button type="button" class="editor-btn" onclick="execFormat('table')" title="Insert Table">Table</button>
                        <button type="button" class="editor-btn" onclick="openArticleImageModal()" title="Insert or Replace Image" id="btnEditorImage">Image</button>
                    </div>
                    <div id="visualEditor" class="editor-visual-box" contenteditable="true" spellcheck="true" data-placeholder="Write main article body content here..." oninput="syncEditorContent()"></div>
                    <textarea name="content" id="articleContent" required style="display: none;">{{ old('content') }}</textarea>
                </div>

                <!-- LIVE PREVIEW PANEL -->
                <div id="panelPreview" style="display: none;">
                    <div class="live-preview-box">
                        <div class="preview-hero">
                            <span id="prevCategory" style="background-color: #DBEAFE; color: #1E40AF; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block; margin-bottom: 12px;">Category</span>
                            <h1 id="prevTitle" style="font-size: 28px; font-weight: 700; color: #0F172A; line-height: 1.3; margin-bottom: 12px;">Article Title</h1>
                            <p id="prevExcerpt" style="font-size: 15px; color: #475569; line-height: 1.6; margin-bottom: 16px;">Article summary will appear here.</p>
                            <div style="font-size: 13px; color: #64748B; display: flex; gap: 16px;">
                                <span><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> <span id="prevDate">Today</span></span>
                                <span><i data-lucide="clock" style="width: 14px; height: 14px;"></i> <span id="prevReadTime">1 min read</span></span>
                            </div>
                        </div>

                        <div id="prevHeroImgContainer" style="margin-bottom: 24px; text-align: center; display: none;">
                            <img id="prevHeroImg" src="" style="width: 100%; max-height: 360px; object-fit: cover; border-radius: 8px;">
                        </div>

                        <div class="preview-body-text" id="prevContent">
                            <em style="color: #94A3B8;">Content preview will appear here as you type.</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & SEO -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Publishing Settings Card -->
            <div class="admin-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 16px;">Publishing Controls</h3>

                <div style="margin-bottom: 16px;">
                    <label class="form-label" style="font-size: 13px; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">
                        Publication Status
                    </label>
                    <select name="status" id="statusSelect" class="form-control" style="width: 100%;">
                        <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Public)</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size: 13px; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">
                        Publish Date
                    </label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" class="form-control" style="width: 100%;">
                </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="admin-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 16px;">SEO Metadata <span style="font-size: 12px; font-weight: 400; color: #64748B;">(Optional)</span></h3>

                <div style="margin-bottom: 16px;">
                    <label class="form-label" style="font-size: 13px; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">
                        Meta Title
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="SEO title..." class="form-control" style="width: 100%;">
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #334155; margin: 0;">
                            Meta Description
                        </label>
                        <span id="metaDescCount" style="font-size: 11px; color: #64748B;">0 / 160</span>
                    </div>
                    <textarea name="meta_description" maxlength="160" rows="3" placeholder="SEO description..." class="form-control" style="width: 100%;" oninput="document.getElementById('metaDescCount').innerText = this.value.length + ' / 160';">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Article Body Image Modal (Upload Image + Use Image URL) -->
<div id="articleImageModal" class="danger-modal-overlay" style="display: none;" onclick="closeArticleImageModal(event)">
    <div class="danger-modal-box" style="text-align: left; max-width: 500px; padding: 24px;" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 id="imageModalTitle" style="font-size: 18px; font-weight: 700; color: #0F172A; margin: 0;">Insert Article Image</h3>
            <button type="button" class="danger-modal-close" onclick="closeArticleImageModal(event)">&times;</button>
        </div>

        <!-- Mode Switcher Tabs -->
        <div style="display: flex; border-bottom: 2px solid #E2E8F0; margin-bottom: 20px;">
            <button type="button" id="tabUploadBtn" class="editor-tab-btn active" onclick="switchImageModalTab('upload')" style="padding: 8px 16px; font-size: 14px; font-weight: 600; cursor: pointer; color: #1E40AF; border-bottom: 2px solid #1E40AF;">
                Upload Image
            </button>
            <button type="button" id="tabUrlBtn" class="editor-tab-btn" onclick="switchImageModalTab('url')" style="padding: 8px 16px; font-size: 14px; font-weight: 600; cursor: pointer; color: #64748B; border-bottom: 2px solid transparent;">
                Use Image URL
            </button>
        </div>

        <!-- TAB 1: UPLOAD IMAGE -->
        <div id="imageTabUpload">
            <div style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 22px; text-align: center; background-color: #F8FAFC; margin-bottom: 16px; cursor: pointer;" onclick="document.getElementById('bodyImageFileInput').click()">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="margin-bottom: 8px; display: inline-block;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <p id="bodyImageFileName" style="font-size: 13px; color: #475569; margin: 0; font-weight: 500;">Click to select an image from your computer</p>
                <span style="font-size: 11px; color: #94A3B8;">JPG, PNG, GIF, WebP (auto-converted to WebP with SEO filename)</span>
                <input type="file" id="bodyImageFileInput" accept="image/*" style="display: none;" onchange="handleBodyImageFileSelect(this)">
            </div>
            <div id="bodyImagePreviewBox" style="display: none; margin-bottom: 16px; text-align: center;">
                <img id="bodyImagePreviewImg" src="" style="max-height: 140px; max-width: 100%; border-radius: 6px; border: 1px solid #E2E8F0; object-fit: contain;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Image Caption / Alt Text (optional)</label>
                <input type="text" id="bodyImageAltText" class="form-control" placeholder="e.g. Luxury villa swimming pool in Lovina" style="width: 100%; font-size: 13px;">
            </div>
            <div id="bodyImageUploadError" style="display: none; color: #DC2626; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 6px; padding: 8px 12px; font-size: 12px; margin-bottom: 12px;"></div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-outline" onclick="closeArticleImageModal(event)" style="padding: 8px 16px; font-size: 13px;">Cancel</button>
                <button type="button" id="btnDoUploadImage" class="btn btn-primary" onclick="submitBodyImageUpload()" style="padding: 8px 18px; font-size: 13px; font-weight: 600;" disabled>
                    Upload & Insert
                </button>
            </div>
        </div>

        <!-- TAB 2: USE IMAGE URL -->
        <div id="imageTabUrl" style="display: none;">
            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">External Image URL *</label>
                <input type="url" id="bodyImageUrlInput" class="form-control" placeholder="https://example.com/property-bali.jpg" style="width: 100%; font-size: 13px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Image Caption / Alt Text (optional)</label>
                <input type="text" id="bodyImageUrlAlt" class="form-control" placeholder="e.g. Modern villa architecture" style="width: 100%; font-size: 13px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-outline" onclick="closeArticleImageModal(event)" style="padding: 8px 16px; font-size: 13px;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBodyImageUrl()" style="padding: 8px 18px; font-size: 13px; font-weight: 600;">
                    Insert Image
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('articleTitle').addEventListener('input', function() {
    const title = this.value;
    const slugInput = document.getElementById('articleSlug');
    if (!slugInput.dataset.userEdited) {
        slugInput.value = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

document.getElementById('articleSlug').addEventListener('input', function() {
    this.dataset.userEdited = "true";
});

function previewHeroImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreviewImg').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('imagePreviewFileName').innerText = 'Selected: ' + input.files[0].name;

            // Update live preview image as well
            document.getElementById('prevHeroImg').src = e.target.result;
            document.getElementById('prevHeroImgContainer').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function execFormat(command) {
    const editor = document.getElementById('visualEditor');
    editor.focus();

    if (command === 'p') {
        document.execCommand('formatBlock', false, '<p>');
    } else if (command === 'h1') {
        document.execCommand('formatBlock', false, '<h1>');
    } else if (command === 'h2') {
        document.execCommand('formatBlock', false, '<h2>');
    } else if (command === 'h3') {
        document.execCommand('formatBlock', false, '<h3>');
    } else if (command === 'bold') {
        document.execCommand('bold', false, null);
    } else if (command === 'italic') {
        document.execCommand('italic', false, null);
    } else if (command === 'ul') {
        document.execCommand('insertUnorderedList', false, null);
    } else if (command === 'ol') {
        document.execCommand('insertOrderedList', false, null);
    } else if (command === 'link') {
        const selection = window.getSelection();
        const selectedText = selection.toString();
        const url = prompt('Enter destination URL (e.g. https://example.com):', 'https://');
        if (url && url.trim() && url !== 'https://') {
            document.execCommand('createLink', false, url.trim());
        }
    } else if (command === 'table') {
        const tableHtml = `<table class="article-table"><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead><tbody><tr><td>Data cell 1</td><td>Data cell 2</td></tr><tr><td>Data cell 3</td><td>Data cell 4</td></tr></tbody></table><p><br></p>`;
        document.execCommand('insertHTML', false, tableHtml);
    } else if (command === 'img') {
        openArticleImageModal();
        return;
    }

    syncEditorContent();
}

let savedSelectionRange = null;
let activeEditorImage = null;

function saveEditorSelection() {
    const sel = window.getSelection();
    if (sel && sel.rangeCount > 0) {
        savedSelectionRange = sel.getRangeAt(0).cloneRange();
    }
}

function restoreEditorSelection() {
    if (savedSelectionRange) {
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedSelectionRange);
    }
}

function openArticleImageModal() {
    saveEditorSelection();
    const modal = document.getElementById('articleImageModal');
    const titleEl = document.getElementById('imageModalTitle');
    const altInput = document.getElementById('bodyImageAltText');
    const urlAltInput = document.getElementById('bodyImageUrlAlt');
    const urlInput = document.getElementById('bodyImageUrlInput');
    const articleTitle = document.querySelector('input[name="title"]')?.value || '';

    // Reset upload fields
    document.getElementById('bodyImageFileInput').value = '';
    document.getElementById('bodyImageFileName').innerText = 'Click to select an image from your computer';
    document.getElementById('bodyImagePreviewBox').style.display = 'none';
    document.getElementById('bodyImagePreviewImg').src = '';
    document.getElementById('btnDoUploadImage').disabled = true;
    document.getElementById('bodyImageUploadError').style.display = 'none';
    document.getElementById('bodyImageUploadError').innerText = '';

    if (activeEditorImage) {
        titleEl.innerText = 'Replace Selected Image';
        const currentAlt = activeEditorImage.alt || '';
        altInput.value = currentAlt;
        urlAltInput.value = currentAlt;
        urlInput.value = activeEditorImage.src || '';
    } else {
        titleEl.innerText = 'Insert Article Image';
        altInput.value = articleTitle ? `${articleTitle} illustration` : '';
        urlAltInput.value = articleTitle ? `${articleTitle} illustration` : '';
        urlInput.value = '';
    }

    switchImageModalTab('upload');
    modal.style.display = 'flex';
}

function closeArticleImageModal(e) {
    if (e) e.stopPropagation();
    const modal = document.getElementById('articleImageModal');
    if (modal) modal.style.display = 'none';
    if (activeEditorImage) {
        activeEditorImage.style.outline = 'none';
        activeEditorImage = null;
    }
}

function switchImageModalTab(tab) {
    const uploadTab = document.getElementById('imageTabUpload');
    const urlTab = document.getElementById('imageTabUrl');
    const uploadBtn = document.getElementById('tabUploadBtn');
    const urlBtn = document.getElementById('tabUrlBtn');

    if (tab === 'upload') {
        uploadTab.style.display = 'block';
        urlTab.style.display = 'none';
        uploadBtn.classList.add('active');
        urlBtn.classList.remove('active');
        uploadBtn.style.borderBottom = '2px solid #1E40AF';
        uploadBtn.style.color = '#1E40AF';
        urlBtn.style.borderBottom = '2px solid transparent';
        urlBtn.style.color = '#64748B';
    } else {
        uploadTab.style.display = 'none';
        urlTab.style.display = 'block';
        uploadBtn.classList.remove('active');
        urlBtn.classList.add('active');
        urlBtn.style.borderBottom = '2px solid #1E40AF';
        urlBtn.style.color = '#1E40AF';
        uploadBtn.style.borderBottom = '2px solid transparent';
        uploadBtn.style.color = '#64748B';
    }
}

function handleBodyImageFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('bodyImageFileName').innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('bodyImagePreviewImg').src = e.target.result;
            document.getElementById('bodyImagePreviewBox').style.display = 'block';
            document.getElementById('btnDoUploadImage').disabled = false;
        };
        reader.readAsDataURL(file);
    }
}

function submitBodyImageUpload() {
    const fileInput = document.getElementById('bodyImageFileInput');
    if (!fileInput.files || !fileInput.files[0]) return;

    const btn = document.getElementById('btnDoUploadImage');
    const errBox = document.getElementById('bodyImageUploadError');
    errBox.style.display = 'none';
    errBox.innerText = '';
    btn.disabled = true;
    btn.innerText = 'Converting to WebP & Uploading...';

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('title', document.querySelector('input[name="title"]')?.value || 'article');
    formData.append('slug', document.querySelector('input[name="slug"]')?.value || '');
    formData.append('alt', document.getElementById('bodyImageAltText').value.trim());

    fetch("{{ route('admin.articles.upload-content-image') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.message || 'Upload failed'); });
        }
        return response.json();
    })
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'Upload & Insert';

        const finalAlt = document.getElementById('bodyImageAltText').value.trim() || data.alt || 'Article image';

        if (activeEditorImage) {
            activeEditorImage.src = data.url;
            activeEditorImage.alt = finalAlt;
            if (activeEditorImage.nextElementSibling && activeEditorImage.nextElementSibling.classList.contains('article-img-caption')) {
                activeEditorImage.nextElementSibling.innerText = finalAlt;
            }
            activeEditorImage.style.outline = 'none';
            activeEditorImage = null;
        } else {
            insertImageIntoEditor(data.url, finalAlt);
        }

        closeArticleImageModal();
        syncEditorContent();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = 'Upload & Insert';
        errBox.style.display = 'block';
        errBox.innerText = 'Error: ' + err.message;
    });
}

function submitBodyImageUrl() {
    const urlInput = document.getElementById('bodyImageUrlInput');
    const url = urlInput.value.trim();
    if (!url) {
        alert('Please enter a valid image URL');
        return;
    }

    const finalAlt = document.getElementById('bodyImageUrlAlt').value.trim() || 'Article image';

    if (activeEditorImage) {
        activeEditorImage.src = url;
        activeEditorImage.alt = finalAlt;
        if (activeEditorImage.nextElementSibling && activeEditorImage.nextElementSibling.classList.contains('article-img-caption')) {
            activeEditorImage.nextElementSibling.innerText = finalAlt;
        }
        activeEditorImage.style.outline = 'none';
        activeEditorImage = null;
    } else {
        insertImageIntoEditor(url, finalAlt);
    }

    closeArticleImageModal();
    syncEditorContent();
}

function insertImageIntoEditor(src, alt) {
    const editor = document.getElementById('visualEditor');
    editor.focus();
    restoreEditorSelection();

    const captionHtml = alt ? `<p class="article-img-caption" style="font-size: 13px; color: #64748B; font-style: italic; margin-top: -8px; margin-bottom: 16px;">${alt}</p>` : '';
    const imgHtml = `<img src="${src}" alt="${alt}" class="article-content-img" style="max-width: 100%; border-radius: 8px; margin: 16px 0;">${captionHtml}<p><br></p>`;

    let inserted = false;
    try {
        inserted = document.execCommand('insertHTML', false, imgHtml);
    } catch (e) {
        inserted = false;
    }

    if (!inserted) {
        if (savedSelectionRange) {
            savedSelectionRange.deleteContents();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = imgHtml;
            const frag = document.createDocumentFragment();
            let node;
            while ((node = tempDiv.firstChild)) {
                frag.appendChild(node);
            }
            savedSelectionRange.insertNode(frag);
        } else {
            editor.insertAdjacentHTML('beforeend', imgHtml);
        }
    }
}

function sanitizeHtml(html) {
    return html
        .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
        .replace(/on\w+="[^"]*"/gi, '')
        .replace(/on\w+='[^']*'/gi, '')
        .replace(/javascript:[^"']*/gi, '#');
}

function syncEditorContent() {
    const editor = document.getElementById('visualEditor');
    const textarea = document.getElementById('articleContent');
    if (editor && textarea) {
        let contentHtml = editor.innerHTML;
        if (contentHtml === '<p><br></p>' || contentHtml === '<br>') {
            contentHtml = '';
        }
        textarea.value = sanitizeHtml(contentHtml);
        updateWordCount();
    }
}

function updateWordCount() {
    const editor = document.getElementById('visualEditor');
    const text = editor ? (editor.innerText || editor.textContent || '') : '';
    const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
    const wordCountEl = document.getElementById('contentWordCount');
    if (wordCountEl) wordCountEl.innerText = words + ' words';
    const prevReadTimeEl = document.getElementById('prevReadTime');
    if (prevReadTimeEl) prevReadTimeEl.innerText = Math.max(1, Math.ceil(words / 200)) + ' min read';
}

function updateExcerptCount() {
    const val = document.getElementById('articleExcerpt').value;
    document.getElementById('excerptCharCount').innerText = val.length + ' / 500';
}

function switchEditorTab(tab) {
    if (tab === 'write') {
        document.getElementById('btnTabWrite').classList.add('active');
        document.getElementById('btnTabPreview').classList.remove('active');
        document.getElementById('panelWrite').style.display = 'block';
        document.getElementById('panelPreview').style.display = 'none';
    } else {
        document.getElementById('btnTabPreview').classList.add('active');
        document.getElementById('btnTabWrite').classList.remove('active');
        document.getElementById('panelWrite').style.display = 'none';
        document.getElementById('panelPreview').style.display = 'block';

        // Render live values into preview
        document.getElementById('prevTitle').innerText = document.getElementById('articleTitle').value || 'Untitled Article';
        document.getElementById('prevCategory').innerText = document.getElementById('articleCategory').value || 'Uncategorized';
        document.getElementById('prevExcerpt').innerText = document.getElementById('articleExcerpt').value || 'No summary provided yet.';
        
        syncEditorContent();
        const contentVal = document.getElementById('articleContent').value;
        document.getElementById('prevContent').innerHTML = contentVal ? contentVal : '<em style="color:#94A3B8;">No article content entered yet.</em>';
    }
}

document.querySelectorAll('button[name="submit_action"]').forEach(btn => {
    btn.addEventListener('click', function() {
        const statusSelect = document.getElementById('statusSelect');
        if (this.value === 'draft') {
            statusSelect.value = 'draft';
        } else if (this.value === 'publish') {
            statusSelect.value = 'published';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('visualEditor');
    const textarea = document.getElementById('articleContent');
    if (editor && textarea && textarea.value) {
        editor.innerHTML = textarea.value;
    }
    updateWordCount();
    updateExcerptCount();

    const articleForm = document.getElementById('articleForm');
    if (articleForm) {
        articleForm.addEventListener('submit', function() {
            syncEditorContent();
        });
    }

    if (editor) {
        editor.addEventListener('click', function(e) {
            if (e.target && e.target.tagName === 'IMG' && e.target.classList.contains('article-content-img')) {
                if (activeEditorImage && activeEditorImage !== e.target) {
                    activeEditorImage.style.outline = 'none';
                }
                activeEditorImage = e.target;
                activeEditorImage.style.outline = '3px solid #2563EB';
                activeEditorImage.style.outlineOffset = '2px';
            } else if (e.target && e.target.tagName !== 'IMG') {
                if (activeEditorImage) {
                    activeEditorImage.style.outline = 'none';
                    activeEditorImage = null;
                }
            }
        });
    }
});
</script>
@endsection
