@extends('layouts.admin')

@section('title', 'Inquiry Details')
@section('page_title', 'Inquiry Details #' . $inquiry->id)

@section('content')
<!-- Success Notification Banner -->
@if(session('success'))
<div class="settings-success-alert" id="success-session-banner" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('success-session-banner').remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>
@endif

<!-- Page Header Controls -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <a href="{{ route('admin.inquiries.index') }}" style="color: #2563EB; font-weight: 600; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
            &larr; Back to Inquiries List
        </a>
    </div>
    <div style="display: flex; gap: 12px; align-items: center;">
        <button type="button" class="btn" onclick="window.print()" style="padding: 10px 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; border: 1px solid #E5E7EB; color: #1F2937; background-color: #FFFFFF; border-radius: 8px; cursor: pointer; font-family: 'Poppins', sans-serif; height: 40px; box-sizing: border-box;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print
        </button>
        <button type="button" class="btn" onclick="openDeleteModal()" style="padding: 10px 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; border: 1px solid #FCA5A5; color: #DC2626; background-color: #FFFFFF; border-radius: 8px; cursor: pointer; font-family: 'Poppins', sans-serif; height: 40px; box-sizing: border-box;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
            Delete Inquiry
        </button>
    </div>
</div>

@php
    // Auto-generate subject according to Bagian 1 logic
    $generatedSubject = $inquiry->subject ?: ($inquiry->property ? "Inquiry about " . $inquiry->property->name : "General Inquiry");
@endphp

<!-- Main Content Responsive Columns -->
<div class="inquiry-details-grid">
    
    <!-- Left Column -->
    <div class="inquiry-left-col">
        
        <!-- Card 1: Card Utama (Inquiry Detail Card) -->
        <div class="detail-card">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                <div style="display: flex; gap: 16px; align-items: center;">
                    <div class="detail-card-icon detail-card-icon-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size: 22px; font-weight: 700; color: #1F2937; margin: 0; line-height: 1.3;">{{ $generatedSubject }}</h1>
                        <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">Received on {{ $inquiry->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                <div>
                    <span class="status-badge badge-{{ $inquiry->status }}" style="font-size: 13px; padding: 6px 14px;">
                        {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                    </span>
                </div>
            </div>
            
            <div style="height: 1px; background-color: #E5E7EB; margin-bottom: 24px; width: 100%;"></div>
            
            <!-- Contact and Property Info splits into 2 columns -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 32px;" class="split-cols-wrapper">
                
                <!-- Left Split: Contact Info -->
                <div class="grid-divider-left">
                    <div style="font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 16px;">Customer Contact Info</div>
                    
                    <!-- Customer Name -->
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; color: #64748B; width: 20px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <div>
                            <div style="font-size: 11px; color: #64748B;">Customer Name</div>
                            <div style="font-size: 15px; font-weight: 700; color: #1F2937;">{{ $inquiry->customer_name }}</div>
                        </div>
                    </div>
                    
                    <!-- Email Address -->
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; color: #64748B; width: 20px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                            </svg>
                        </span>
                        <div>
                            <div style="font-size: 11px; color: #64748B;">Email Address</div>
                            <a href="mailto:{{ $inquiry->email }}" style="font-size: 15px; font-weight: 600; color: #2563EB; text-decoration: none;">{{ $inquiry->email }}</a>
                        </div>
                    </div>
                    
                    <!-- Phone / WhatsApp -->
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; color: #64748B; width: 20px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </span>
                        <div>
                            <div style="font-size: 11px; color: #64748B;">Phone / WhatsApp</div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 15px; font-weight: 600; color: #1F2937;">{{ $inquiry->phone }}</span>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inquiry->phone) }}" target="_blank" style="font-size: 12px; font-weight: 600; color: #15803D; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background-color: #DCFCE7; padding: 2px 8px; border-radius: 4px;">
                                    <i data-lucide="message-circle" style="width: 14px; height: 14px; color: #15803D;"></i> Open WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Split: Property Info -->
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 16px;">Interested Property</div>
                    
                    @if($inquiry->property)
                        <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; color: #64748B; width: 20px; margin-top: 2px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </span>
                            <div>
                                <div style="font-size: 11px; color: #64748B;">Property Name & Price</div>
                                <div style="margin-bottom: 12px;">
                                    <a href="{{ route('properties.show', $inquiry->property->slug) }}" target="_blank" style="font-size: 15px; font-weight: 700; color: #1E3A8A; text-decoration: none; hover: underline;">
                                        {{ $inquiry->property->name }} (${{ number_format($inquiry->property->price) }})
                                    </a>
                                </div>
                                <a href="{{ route('properties.show', $inquiry->property->slug) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600; color: #1E3A8A; border: 1px solid #BFDBFE; border-radius: 6px; text-decoration: none; background-color: #FFFFFF;">
                                    View Property
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 6px; background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                            <span style="font-size: 20px;">ℹ️</span>
                            <span style="font-size: 13px; color: #64748B; font-weight: 500;">No specific property (General Inquiry)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Card Customer Message -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon detail-card-icon-purple">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3 class="detail-card-title">Customer Message</h3>
            </div>
            
            <div style="background-color: #F8F9FA; border: 1px solid #E5E7EB; padding: 20px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #374151; white-space: pre-line; font-family: 'Poppins', sans-serif;">{{ $inquiry->message }}</div>
        </div>

        <!-- Card 3: Card Update Inquiry Status & Internal Notes -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon detail-card-icon-orange">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </div>
                <h3 class="detail-card-title">Update Inquiry Status & Internal Notes</h3>
            </div>
            
            <form action="{{ route('admin.inquiries.update', $inquiry->id) }}" method="POST" id="update-inquiry-form">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="status" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 8px; color: #4B5563;">Status *</label>
                    <select name="status" id="status" class="form-select" style="max-width: 300px; width: 100%; height: 40px; border-radius: 6px; padding: 0 12px; border: 1px solid #CBD5E1;">
                        <option value="new" {{ $inquiry->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="in_progress" {{ $inquiry->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="responded" {{ $inquiry->status == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="closed" {{ $inquiry->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px; position: relative;">
                    <label class="form-label" for="admin_notes" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 8px; color: #4B5563;">Internal Admin Notes</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control" placeholder="Add private notes regarding phone call, appointment time, or client preferences..." style="min-height: 120px; width: 100%; resize: vertical; padding: 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 14px; box-sizing: border-box;" maxlength="500" oninput="updateNotesCounter(this)">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                    <div id="notes-counter" style="text-align: right; font-size: 11px; color: #64748B; margin-top: 4px;">0 / 500</div>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; background-color: #1E3A8A !important; border-color: #1E3A8A !important; color: #FFFFFF !important; border-radius: 6px; cursor: pointer; height: 40px; box-sizing: border-box;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Changes
                </button>
            </form>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="inquiry-right-col">
        
        <!-- Card 4: Card Inquiry Summary (kanan atas) -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon detail-card-icon-green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <h3 class="detail-card-title">Inquiry Summary</h3>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; font-family: 'Poppins', sans-serif;">
                <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 0; color: #64748B; font-weight: 500;">Inquiry ID</td>
                    <td style="padding: 10px 0; color: #1F2937; font-weight: 700; text-align: right;">#{{ $inquiry->id }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 0; color: #64748B; font-weight: 500;">Date Received</td>
                    <td style="padding: 10px 0; color: #1F2937; font-weight: 600; text-align: right;">{{ $inquiry->created_at->format('M d, Y H:i') }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 0; color: #64748B; font-weight: 500;">Status</td>
                    <td style="padding: 10px 0; text-align: right;">
                        <span class="status-badge badge-{{ $inquiry->status }}" style="font-size: 11px; padding: 3px 8px;">
                            {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                        </span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 0; color: #64748B; font-weight: 500;">Source</td>
                    <td style="padding: 10px 0; color: #1F2937; font-weight: 600; text-align: right;">{{ $inquiry->source }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; color: #64748B; font-weight: 500;">Related To</td>
                    <td style="padding: 10px 0; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;">
                        @if($inquiry->property)
                            <a href="{{ route('properties.show', $inquiry->property->slug) }}" target="_blank" style="color: #2563EB; font-weight: 600; text-decoration: none;">
                                {{ $inquiry->property->name }}
                            </a>
                        @else
                            <span style="color: #64748B;">General Inquiry</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Card 5: Card Inquiry Timeline (kanan bawah) -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon detail-card-icon-blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="detail-card-title">Inquiry Timeline</h3>
            </div>
            
            <div class="timeline-container">
                @forelse($inquiry->statusLogs as $index => $log)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div style="font-weight: 600; color: #1F2937;">
                                @if($index === 0)
                                    Inquiry received
                                @else
                                    Status updated to <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $log->status) }}</span>
                                @endif
                            </div>
                            <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                {{ $log->changed_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div style="font-weight: 600; color: #1F2937;">Inquiry received</div>
                            <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                {{ $inquiry->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
        
    </div>
</div>

<!-- Reused Delete Confirmation Modal -->
<div class="danger-modal-overlay" id="deleteInquiryModal" style="display: none;" onclick="closeDeleteModal(event)">
    <div class="danger-modal-box" onclick="event.stopPropagation()">
        
        <!-- Close Button × -->
        <button type="button" class="danger-modal-close" onclick="closeDeleteModal(event)">&times;</button>
        
        <!-- 1. Icon Trash Circle -->
        <div style="width: 56px; height: 56px; border-radius: 50%; background-color: #FEE2E2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="color: #DC2626;">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
            </svg>
        </div>
        
        <!-- 2. Judul -->
        <h3 style="font-size: 22px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">Delete Inquiry</h3>
        
        <!-- 3. Deskripsi -->
        <div style="font-size: 14px; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
            <div>Are you sure you want to delete this inquiry?</div>
            <div>This action cannot be undone.</div>
        </div>
        
        <!-- 4. Card Preview Inquiry -->
        <div style="background-color: #F8F9FA; border-radius: 12px; padding: 18px 20px; text-align: left; margin-bottom: 24px; border: 1px solid #E5E7EB; font-family: 'Poppins', sans-serif;">
            <div style="font-weight: 700; font-size: 16px; color: #1F2937; margin-bottom: 4px;">{{ $inquiry->customer_name }}</div>
            <div style="font-size: 14px; color: #2563EB; margin-bottom: 8px; word-break: break-all;">{{ $inquiry->email }}</div>
            <div style="font-size: 12px; color: #6B7280;">{{ $inquiry->property->name ?? 'General Inquiry' }} &bull; {{ $inquiry->created_at->format('M d, Y H:i') }}</div>
        </div>
        
        <!-- 5. Tombol aksi -->
        <div class="danger-modal-buttons">
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal(event)">
                Cancel
            </button>
            <button type="button" class="btn btn-delete" onclick="confirmDeleteInquiry()">
                Delete
            </button>
        </div>
        
    </div>
</div>

<form id="delete-inquiry-form" action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
// Initialize character count and layouts
document.addEventListener('DOMContentLoaded', function() {
    const adminNotesEl = document.getElementById('admin_notes');
    if (adminNotesEl) {
        updateNotesCounter(adminNotesEl);
    }
});

// Update character counter for Internal Notes
function updateNotesCounter(el) {
    const count = el.value.length;
    document.getElementById('notes-counter').textContent = `${count} / 500`;
}

// Open Delete Modal
function openDeleteModal() {
    // Lock background scroll
    document.body.style.overflow = 'hidden';
    document.getElementById('deleteInquiryModal').style.display = 'flex';
}

// Close Delete Modal
function closeDeleteModal(e) {
    if (e) e.preventDefault();
    document.getElementById('deleteInquiryModal').style.display = 'none';
    
    // Unlock background scroll
    document.body.style.overflow = '';
}

// Confirm Delete - Form Submit
function confirmDeleteInquiry() {
    document.getElementById('delete-inquiry-form').submit();
}
</script>

<style>
/* Print Layout Override Styles */
@media print {
    .admin-sidebar,
    .admin-topbar,
    .btn,
    .danger-modal-overlay,
    #update-inquiry-form,
    .inquiry-details-grid > div:last-child {
        display: none !important;
    }
    
    .admin-main-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .inquiry-details-grid {
        grid-template-columns: 1fr !important;
    }
    
    .detail-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    
    body {
        background-color: #FFFFFF !important;
        color: #000000 !important;
    }
}

/* Tablet columns layout adjust styling */
@media (max-width: 1024px) {
    .split-cols-wrapper {
        grid-template-columns: 1fr !important;
    }
    .grid-divider-left {
        border-right: none !important;
        padding-right: 0 !important;
        border-bottom: 1px solid #E5E7EB !important;
        padding-bottom: 24px !important;
    }
}

@media (min-width: 768px) and (max-width: 1024px) {
    .split-cols-wrapper {
        grid-template-columns: 1fr 1fr !important;
    }
    .grid-divider-left {
        border-right: 1px solid #E5E7EB !important;
        padding-right: 24px !important;
        border-bottom: none !important;
        padding-bottom: 0 !important;
    }
}

@media (min-width: 1025px) {
    .split-cols-wrapper {
        grid-template-columns: 1fr 1fr !important;
    }
}
</style>
@endsection
