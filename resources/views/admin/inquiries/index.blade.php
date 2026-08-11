@extends('layouts.admin')

@section('title', 'Inquiries Management')
@section('page_title', 'Customer Inquiries')

@section('content')
<!-- Success Notification Banner -->
<div class="settings-success-alert" id="success-toast-banner" style="display: none; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span id="success-toast-text">Inquiry deleted successfully.</span>
    </div>
    <button type="button" onclick="document.getElementById('success-toast-banner').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>

@if(session('success'))
<div class="settings-success-alert" id="success-session-banner" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #16A34A; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">✓</div>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" onclick="document.getElementById('success-session-banner').remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #15803D;">&times;</button>
</div>
@endif

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 26px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Customer Inquiries</h2>
        <p style="font-size: 14px; color: #64748B;">Manage property inquiries and contact from potential customers.</p>
    </div>
</div>

<!-- Filters & Add Inquiry -->
<div class="admin-card" style="margin-bottom: 24px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <form action="{{ route('admin.inquiries.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; flex-grow: 1;">
        <input type="text" name="search" class="form-control" placeholder="Search customer, email, property..." value="{{ request('search') }}" style="max-width: 360px; width: 100%;">
        
        <select name="status" class="form-select" style="max-width: 200px;">
            <option value="">All Statuses</option>
            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>

        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; background-color: #1E3A8A !important; border-color: #1E3A8A !important; color: #FFFFFF !important; font-weight: 600;">Filter</button>
        <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline" style="padding: 10px 16px; text-decoration: none; color: #2563EB;">Reset</a>
    </form>
</div>

<!-- Inquiries Table -->
<div class="admin-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="admin-table-container" style="border: none; border-radius: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Phone / WA</th>
                    <th>Related Property</th>
                    <th>Date Received</th>
                    <th>Status</th>
                    <th style="text-align: right; width: 180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                    <tr>
                        <td style="font-weight: 700; color: #0F172A;">{{ $inq->customer_name }}</td>
                        <td style="font-size: 13px; color: #2563EB;">{{ $inq->email }}</td>
                        <td style="font-size: 13px;">{{ $inq->phone }}</td>
                        <td style="font-size: 13px; font-weight: 600;">
                            {{ $inq->property->name ?? 'General Inquiry' }}
                        </td>
                        <td style="font-size: 12px; color: #64748B;">{{ $inq->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="status-badge badge-{{ $inq->status }}">
                                {{ ucfirst(str_replace('_', ' ', $inq->status)) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <a href="{{ route('admin.inquiries.show', $inq->id) }}" class="btn btn-outline" style="padding: 6px 10px; font-size: 12px; border-color: #BFDBFE; color: #1E3A8A; font-weight: 600; text-decoration: none;">View Detail</a>
                                <button type="button" class="btn btn-outline" style="padding: 6px 6px; font-size: 12px; color: #DC2626; border-color: #FCA5A5; display: inline-flex; align-items: center; justify-content: center;" onclick="triggerDeleteInquiry(this)" data-inquiry="{{ json_encode([
                                    'id' => $inq->id,
                                    'customer_name' => $inq->customer_name,
                                    'email' => $inq->email,
                                    'property_name' => $inq->property->name ?? 'General Inquiry',
                                    'date_received' => $inq->created_at->format('M d, Y H:i')
                                ]) }}">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748B; padding: 24px;">No inquiries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination & Entries Summary -->
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; margin-top: 24px; width: 100%;">
    <div style="font-size: 13px; color: #64748B; text-align: center;">
        Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() ?? 0 }} entries
    </div>
    <div style="width: 100%; display: flex; justify-content: center;">
        {{ $inquiries->links('vendor.pagination.admin') }}
    </div>
</div>

<!-- Delete Confirmation Modal (Detailed Pixel-Accurate Replication) -->
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
        
        <!-- 3. Deskripsi peringatan -->
        <div style="font-size: 14px; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
            <div>Are you sure you want to delete this inquiry?</div>
            <div>This action cannot be undone.</div>
        </div>
        
        <!-- 4. Card Preview Inquiry -->
        <div style="background-color: #F8F9FA; border-radius: 12px; padding: 18px 20px; text-align: left; margin-bottom: 24px; border: 1px solid #E5E7EB; font-family: 'Poppins', sans-serif;">
            <div id="inq-preview-name" style="font-weight: 700; font-size: 16px; color: #1F2937; margin-bottom: 4px;">-</div>
            <div id="inq-preview-email" style="font-size: 14px; color: #2563EB; margin-bottom: 8px; word-break: break-all;">-</div>
            <div id="inq-preview-meta" style="font-size: 12px; color: #6B7280;">-</div>
        </div>
        
        <!-- 5. Tombol aksi -->
        <div class="danger-modal-buttons">
            <button type="button" class="btn btn-cancel" onclick="closeDeleteModal(event)">
                Cancel
            </button>
            <button type="button" class="btn btn-delete" onclick="confirmDeleteInquiryLocal(event)">
                Delete
            </button>
        </div>
        
    </div>
</div>

@endsection

@section('scripts')
<script>
// Local delete tracking variables
let activeDeleteRowEl = null;

// Trigger Delete Modal
function triggerDeleteInquiry(btn) {
    const data = JSON.parse(btn.getAttribute('data-inquiry'));
    activeDeleteRowEl = btn.closest('tr');

    // Fill preview card dynamically
    document.getElementById('inq-preview-name').textContent = data.customer_name;
    document.getElementById('inq-preview-email').textContent = data.email;
    document.getElementById('inq-preview-meta').textContent = `${data.property_name} \u2022 ${data.date_received}`;

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

// Local Delete action (UI State update only as required)
function confirmDeleteInquiryLocal(e) {
    if (e) e.preventDefault();
    
    if (activeDeleteRowEl) {
        activeDeleteRowEl.remove();
    }
    
    // Close modal & unlock scroll
    closeDeleteModal(e);
    
    // Show success notification banner
    const banner = document.getElementById('success-toast-banner');
    if (banner) {
        banner.style.display = 'flex';
        document.getElementById('success-toast-text').textContent = "Inquiry deleted successfully.";
        
        // Auto scroll to top to see notification clearly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>
@endsection
