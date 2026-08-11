@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Overview')

@section('content')
<!-- Welcome Banner -->
<div style="background-color: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
    <h2 style="font-size: 24px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">Welcome back, Admin!</h2>
    <p style="color: #64748B; font-size: 14px;">Here's what's happening with your real estate website today.</p>
</div>

<!-- 4 Analytics Stat Cards -->
<div class="admin-stats-grid">
    <!-- Card 1: Total Properties -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="color: #2563EB; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="home" style="width: 28px; height: 28px; color: #2563EB;"></i>
        </div>
        <div>
            <div class="admin-stat-lbl">Total Properties</div>
            <div class="admin-stat-val">{{ $totalProperties }}</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                <span style="color: #16A34A; font-weight: 600;">Published {{ $publishedProperties }}</span> &bull; 
                <span style="color: #DC2626; font-weight: 600;">Draft {{ $draftProperties }}</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Inquiries -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="color: #16A34A; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="message-square" style="width: 28px; height: 28px; color: #16A34A;"></i>
        </div>
        <div>
            <div class="admin-stat-lbl">Total Inquiries</div>
            <div class="admin-stat-val">{{ $totalInquiries }}</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                <span style="color: #2563EB; font-weight: 600;">New {{ $newInquiries }}</span> &bull; 
                <span>Read {{ $readInquiries }}</span> &bull; 
                <span>Replied {{ $repliedInquiries }}</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Views -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="color: #8B5CF6; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="eye" style="width: 28px; height: 28px; color: #8B5CF6;"></i>
        </div>
        <div>
            <div class="admin-stat-lbl">Total Views (30 Days)</div>
            <div class="admin-stat-val">{{ number_format($totalViews) }}</div>
            <div style="font-size: 12px; color: #16A34A; font-weight: 600; margin-top: 4px;">
                ↑ 24.5% vs last 30 days
            </div>
        </div>
    </div>

    <!-- Card 4: Unique Visitors -->
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="color: #EA580C; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="users" style="width: 28px; height: 28px; color: #EA580C;"></i>
        </div>
        <div>
            <div class="admin-stat-lbl">Unique Visitors (30 Days)</div>
            <div class="admin-stat-val">{{ number_format($uniqueVisitors) }}</div>
            <div style="font-size: 12px; color: #16A34A; font-weight: 600; margin-top: 4px;">
                ↑ 18.2% vs last 30 days
            </div>
        </div>
    </div>
</div>

<!-- Charts Row: Line Chart + Inquiry Status Donut + Top Properties -->
<div class="admin-charts-grid">
    <!-- Line Chart: Page Views vs Unique Visitors -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Overview (Last 30 Days)</div>
            <span style="font-size: 13px; color: #64748B;">Last 30 Days</span>
        </div>
        <div style="height: 260px;">
            <canvas id="overviewLineChart"></canvas>
        </div>
    </div>

    <!-- Donut Chart: Inquiry Status -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Inquiry Status</div>
        </div>
        <div style="height: 220px; position: relative;">
            <canvas id="inquiryDonutChart"></canvas>
        </div>
    </div>

    <!-- Top Properties -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Top Properties (By Views)</div>
        </div>
        <ul style="list-style: none; padding: 0;">
            @foreach($topProperties as $tp)
                <li style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9;">
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #0F172A;">{{ $tp->name }}</div>
                        <div style="font-size: 12px; color: #64748B;">{{ $tp->location->name ?? 'Lovina' }}</div>
                    </div>
                    <div style="font-size: 13px; font-weight: 600; color: #2563EB; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="eye" style="width: 14px; height: 14px; color: #2563EB;"></i> {{ $tp->views_count }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Bottom Row: Recent Inquiries Table + Properties Status Donut + Quick Actions -->
<div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px;">
    <!-- Recent Inquiries Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Recent Inquiries</div>
            <a href="{{ route('admin.inquiries.index') }}" style="font-size: 13px; font-weight: 600; color: #2563EB; text-decoration: none;">View All &rarr;</a>
        </div>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Property</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentInquiries as $inq)
                        <tr>
                            <td style="font-weight: 600;">{{ $inq->customer_name }}</td>
                            <td>{{ $inq->phone }}</td>
                            <td style="font-size: 13px;">{{ $inq->property->name ?? 'General Inquiry' }}</td>
                            <td style="font-size: 12px; color: #64748B;">{{ $inq->created_at->format('M d, H:i') }}</td>
                            <td>
                                <span class="status-badge badge-{{ $inq->status }}">{{ ucfirst(str_replace('_', ' ', $inq->status)) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.inquiries.show', $inq->id) }}" style="color: #2563EB; font-weight: 600; text-decoration: none;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Properties by Status Donut -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Properties by Status</div>
        </div>
        <div style="height: 220px; position: relative;">
            <canvas id="propertiesDonutChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Quick Actions</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="{{ route('admin.properties.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background-color: #EFF6FF; border-radius: 8px; text-decoration: none; color: #1E40AF; font-weight: 600;">
                <i data-lucide="plus" style="width: 18px; height: 18px; color: #1E40AF;"></i> Add New Property
            </a>
            <a href="{{ route('admin.properties.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; text-decoration: none; color: #334155; font-weight: 600;">
                <i data-lucide="clipboard-list" style="width: 18px; height: 18px; color: #334155;"></i> View Properties List
            </a>
            <a href="{{ route('admin.inquiries.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; text-decoration: none; color: #334155; font-weight: 600;">
                <i data-lucide="mail" style="width: 18px; height: 18px; color: #334155;"></i> View Inquiries
            </a>
            <a href="{{ route('admin.settings.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background-color: #FEF3C7; border-radius: 8px; text-decoration: none; color: #92400E; font-weight: 600;">
                <i data-lucide="settings" style="width: 18px; height: 18px; color: #92400E;"></i> General Settings
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart Overview
    const ctxLine = document.getElementById('overviewLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['May 13', 'May 20', 'May 27', 'Jun 3', 'Jun 9'],
            datasets: [
                {
                    label: 'Page Views',
                    data: [1200, 1900, 1500, 2100, 1842],
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Unique Visitors',
                    data: [500, 750, 620, 910, 856],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // 2. Inquiry Status Donut Chart
    const ctxInq = document.getElementById('inquiryDonutChart').getContext('2d');
    new Chart(ctxInq, {
        type: 'doughnut',
        data: {
            labels: ['New', 'In Progress / Read', 'Replied'],
            datasets: [{
                data: [{{ $newInquiries }}, {{ $readInquiries }}, {{ $repliedInquiries }}],
                backgroundColor: ['#2563EB', '#F59E0B', '#10B981']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Properties Status Donut Chart
    const ctxProp = document.getElementById('propertiesDonutChart').getContext('2d');
    new Chart(ctxProp, {
        type: 'doughnut',
        data: {
            labels: ['Published', 'Draft'],
            datasets: [{
                data: [{{ $publishedProperties }}, {{ $draftProperties }}],
                backgroundColor: ['#16A34A', '#EF4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endsection
