@extends('layouts.admin')

@section('title', 'Inquiry Details')
@section('page_title', 'Inquiry Details #' . $inquiry->id)

@section('content')
<div style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.inquiries.index') }}" style="color: #2563EB; font-weight: 600; text-decoration: none;">&larr; Back to Inquiries List</a>
    </div>

    <div class="admin-card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 22px; font-weight: 700; color: #0F172A;">{{ $inquiry->subject ?? 'Customer Inquiry' }}</h2>
                <div style="font-size: 13px; color: #64748B;">Received on {{ $inquiry->created_at->format('F d, Y \a\t H:i A') }}</div>
            </div>
            <span class="status-badge badge-{{ $inquiry->status }}" style="font-size: 14px; padding: 6px 14px;">
                {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background-color: #F8FAFC; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Customer Name</div>
                <div style="font-size: 16px; font-weight: 700; color: #0F172A;">{{ $inquiry->customer_name }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Email Address</div>
                <div style="font-size: 16px; font-weight: 600; color: #2563EB;">{{ $inquiry->email }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Phone / WhatsApp</div>
                <div style="font-size: 16px; font-weight: 600; color: #0F172A;">
                    {{ $inquiry->phone }} 
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inquiry->phone) }}" target="_blank" style="font-size: 13px; color: #16A34A; margin-left: 8px; text-decoration: none;">💬 Open WhatsApp</a>
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Interested Property</div>
                <div style="font-size: 16px; font-weight: 600; color: #1E3A8A;">
                    @if($inquiry->property)
                        <a href="{{ route('properties.show', $inquiry->property->slug) }}" target="_blank" style="color: #1E3A8A;">
                            {{ $inquiry->property->name }} (${{ number_format($inquiry->property->price) }})
                        </a>
                    @else
                        General Inquiry
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <div style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Full Customer Message:</div>
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; padding: 20px; border-radius: 8px; font-size: 15px; line-height: 1.7; color: #334155; white-space: pre-line;">
                {{ $inquiry->message }}
            </div>
        </div>

        <!-- Update Status & Admin Notes Form -->
        <div style="background-color: #EFF6FF; border: 1px solid #BFDBFE; padding: 24px; border-radius: 8px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1E40AF; margin-bottom: 16px;">Update Inquiry Status & Internal Notes</h3>
            
            <form action="{{ route('admin.inquiries.update', $inquiry->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="status">Status *</label>
                    <select name="status" id="status" class="form-select" style="max-width: 300px;">
                        <option value="new" {{ $inquiry->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="in_progress" {{ $inquiry->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="responded" {{ $inquiry->status == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="closed" {{ $inquiry->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="admin_notes">Internal Admin Notes</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control" placeholder="Add private notes regarding phone call, appointment time, or client preferences...">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Inquiry Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
