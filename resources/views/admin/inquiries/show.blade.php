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

<!-- Error Notification Banner -->
@if($errors->any())
<div class="settings-danger-alert" id="error-session-banner" style="margin-bottom: 24px; background-color: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 14px 18px; color: #991B1B; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
    <div style="display: flex; align-items: flex-start; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #DC2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; flex-shrink: 0; margin-top: 2px;">!</div>
        <div>
            <strong style="font-size: 14px; display: block; margin-bottom: 4px;">There were problems with your request:</strong>
            <ul style="margin: 0; padding-left: 18px; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" onclick="document.getElementById('error-session-banner').remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #991B1B; line-height: 1;">&times;</button>
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
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 2px;">
                                <span style="font-size: 15px; font-weight: 600; color: #1F2937;">{{ $inquiry->phone ?: 'Not provided' }}</span>
                                @if($inquiry->whatsapp_url)
                                    <a href="{{ $inquiry->whatsapp_url }}" target="_blank" rel="noopener noreferrer" style="font-size: 12px; font-weight: 600; color: #15803D; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; background-color: #DCFCE7; padding: 2px 8px; border-radius: 4px;">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true" style="flex-shrink: 0;">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        Open WhatsApp
                                    </a>
                                @else
                                    <span style="font-size: 11px; font-weight: 500; color: #94A3B8; background-color: #F1F5F9; padding: 2px 6px; border-radius: 4px;">
                                        No WhatsApp
                                    </span>
                                @endif
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
                                        {{ $inquiry->property->name }} ({{ $inquiry->property->formatted_price_admin }})
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
        
        <!-- Card: Reply via WhatsApp -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon" style="background-color: #DCFCE7; color: #15803D; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </div>
                <h3 class="detail-card-title">Reply via WhatsApp</h3>
            </div>
            
            <div style="font-size: 13px; font-family: 'Poppins', sans-serif;">
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase; margin-bottom: 2px;">Customer</div>
                    <div style="font-weight: 700; color: #1F2937;">{{ $inquiry->customer_name }}</div>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase; margin-bottom: 2px;">WhatsApp Number</div>
                    @if($inquiry->whatsapp_number)
                        <div style="font-weight: 600; color: #1F2937;">{{ $inquiry->phone }} <span style="font-size: 12px; color: #64748B; font-weight: 400;">(+{{ $inquiry->whatsapp_number }})</span></div>
                    @else
                        <div style="color: #94A3B8; font-style: italic; font-size: 12px;">No valid WhatsApp phone number available for this inquiry.</div>
                    @endif
                </div>

                @if($inquiry->whatsapp_url)
                    <a href="{{ $inquiry->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="btn" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #16A34A; color: #FFFFFF; font-weight: 600; font-size: 13px; padding: 10px 16px; border-radius: 8px; text-decoration: none; border: none; box-sizing: border-box;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" style="flex-shrink: 0;">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Open WhatsApp
                    </a>
                @else
                    <button type="button" class="btn" disabled style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #E2E8F0; color: #94A3B8; font-weight: 600; font-size: 13px; padding: 10px 16px; border-radius: 8px; cursor: not-allowed; border: 1px solid #CBD5E1; box-sizing: border-box;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" style="flex-shrink: 0;">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        WhatsApp Unavailable
                    </button>
                @endif
            </div>
        </div>

        <!-- Card: Reply via Email -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-icon" style="background-color: #EFF6FF; color: #2563EB;">
                    <i data-lucide="mail" style="width: 20px; height: 20px;"></i>
                </div>
                <h3 class="detail-card-title">Reply via Email</h3>
            </div>
            
            @if(empty($inquiry->email))
                <div style="background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 8px; padding: 16px; text-align: center; color: #64748B; font-size: 13px; font-family: 'Poppins', sans-serif;">
                    Customer email address is not available for this inquiry.
                </div>
            @else
                <form action="{{ route('admin.inquiries.reply-email', $inquiry->id) }}" method="POST" id="inquiry-email-reply-form" onsubmit="return handleEmailReplySubmit(event)">
                    @csrf
                    
                    <!-- To (Read Only Recipient) -->
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 4px; color: #4B5563;">To</label>
                        <input type="text" class="form-control" value="{{ $inquiry->email }} ({{ $inquiry->customer_name }})" readonly style="background-color: #F8FAFC; color: #475569; font-size: 13px; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 12px; width: 100%; box-sizing: border-box; cursor: not-allowed; font-family: 'Poppins', sans-serif;">
                        @error('email')
                            <div style="color: #DC2626; font-size: 11px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label for="email_subject" style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 4px; color: #4B5563;">Subject *</label>
                        <input type="text" name="subject" id="email_subject" class="form-control" required value="{{ old('subject', $inquiry->default_reply_subject) }}" placeholder="Subject..." style="font-size: 13px; border: 1px solid #CBD5E1; border-radius: 6px; padding: 8px 12px; width: 100%; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                        @error('subject')
                            <div style="color: #DC2626; font-size: 11px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="email_message" style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 4px; color: #4B5563;">Message *</label>
                        <textarea name="message" id="email_message" rows="6" class="form-control" required placeholder="Type your reply to the customer..." style="font-size: 13px; border: 1px solid #CBD5E1; border-radius: 6px; padding: 10px 12px; width: 100%; box-sizing: border-box; resize: vertical; font-family: 'Poppins', sans-serif; line-height: 1.5;">{{ old('message', $inquiry->default_reply_message) }}</textarea>
                        @error('message')
                            <div style="color: #DC2626; font-size: 11px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="send-email-btn" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #1E3A8A !important; border-color: #1E3A8A !important; color: #FFFFFF !important; font-weight: 600; font-size: 13px; padding: 10px 16px; border-radius: 8px; cursor: pointer; height: 40px; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
                        <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                        <span id="send-email-text">Send Email</span>
                    </button>
                    
                    @if($inquiry->mailto_url)
                    <div style="text-align: center; margin-top: 10px;">
                        <a href="{{ $inquiry->mailto_url }}" style="font-size: 12px; color: #64748B; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="external-link" style="width: 12px; height: 12px;"></i> Open in Mail App (Desktop Client)
                        </a>
                    </div>
                    @endif
                </form>
            @endif
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

// Handle Email Reply submission state
let isSendingInquiryEmail = false;
function handleEmailReplySubmit(e) {
    if (isSendingInquiryEmail) {
        if (e) e.preventDefault();
        return false;
    }
    const subjectEl = document.getElementById('email_subject');
    const messageEl = document.getElementById('email_message');
    if (!subjectEl || !messageEl || !subjectEl.value.trim() || !messageEl.value.trim()) {
        return true; // Let browser HTML5 validation handle empty required fields
    }
    
    isSendingInquiryEmail = true;
    const btn = document.getElementById('send-email-btn');
    const textSpan = document.getElementById('send-email-text');
    if (btn) {
        btn.style.opacity = '0.75';
        btn.style.pointerEvents = 'none';
        btn.style.cursor = 'not-allowed';
    }
    if (textSpan) {
        textSpan.textContent = 'Sending Email...';
    }
    return true;
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
