@extends('layouts.admin')

@section('title', 'Inquiries Management')
@section('page_title', 'Customer Inquiries')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 700; color: #0F172A;">Customer Inquiries</h2>
        <p style="font-size: 14px; color: #64748B;">Manage property inquiries and contact form submissions.</p>
    </div>
</div>

<!-- Filters -->
<div class="admin-card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form action="{{ route('admin.inquiries.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center;">
        <input type="text" name="search" class="form-control" placeholder="Search customer, email..." value="{{ request('search') }}" style="max-width: 300px;">
        
        <select name="status" class="form-select" style="max-width: 200px;">
            <option value="">All Statuses</option>
            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
        <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline" style="padding: 10px 16px;">Reset</a>
    </form>
</div>

<!-- Inquiries Table -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Phone / WA</th>
                <th>Related Property</th>
                <th>Date Received</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
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
                        <span class="status-badge badge-{{ $inq->status }}">{{ ucfirst(str_replace('_', ' ', $inq->status)) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.inquiries.show', $inq->id) }}" class="btn btn-primary" style="padding: 6px 14px; font-size: 12px;">View Detail</a>
                        <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this inquiry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #DC2626; margin-left: 8px; font-size: 14px;">🗑️</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 32px; color: #64748B;">No inquiries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 24px;">
    {{ $inquiries->links() }}
</div>
@endsection
