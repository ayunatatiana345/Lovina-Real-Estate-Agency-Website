<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - PT Lovina North Bali</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('head_extra')
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar-header">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <div class="admin-sidebar-logo">
                PT LOVINA NORTH BALI<br>
                <span style="font-size: 11px; font-weight: 400; opacity: 0.8;">REAL ESTATE AGENCY</span>
            </div>
        </div>

        <div style="padding-top: 16px; flex-grow: 1;">
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="side-dashboard">
                        📊 Dashboard
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">WEBSITE MANAGEMENT</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.cms.index') }}" class="admin-nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}" id="side-cms">
                        💻 Website CMS
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" id="side-settings">
                        ⚙️ Company Settings
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">PROPERTY MANAGEMENT</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.properties.index') }}" class="admin-nav-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}" id="side-properties">
                        🏡 Properties
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">CUSTOMER & DISCOVERY</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.locations.index') }}" class="admin-nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" id="side-locations">
                        📍 Locations
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.inquiries.index') }}" class="admin-nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}" id="side-inquiries">
                        ✉️ Inquiries
                    </a>
                </li>
            </ul>
        </div>

        <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="#" class="admin-nav-link" id="sidebarLogoutBtn" style="color: #FCA5A5;">
                🚪 Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="admin-page-title">
                @yield('page_title', 'Dashboard Overview')
            </div>

            <div class="admin-user-menu">
                <a href="{{ route('home') }}" target="_blank" style="font-size: 14px; font-weight: 500; color: #2563EB; text-decoration: none;">
                    🌐 View Website
                </a>
                <div style="font-size: 18px; cursor: pointer;">🔔</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="admin-avatar">A</div>
                    <div>
                        <div style="font-size: 14px; font-weight: 600;">Lovina Agency</div>
                        <div style="font-size: 12px; color: #64748B;">Admin Portal</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="admin-content">
            @if(session('success'))
                <div style="background-color: #DCFCE7; border: 1px solid #86EFAC; color: #166534; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px;">
                    <ul style="margin-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Logout Confirmation Modal (Matching 4.10 prompt requirements) -->
    <div class="modal-overlay" id="logoutModal" style="display: none;">
        <div class="modal-box" style="max-width: 440px;">
            <h3 style="font-size: 24px; font-weight: 700; color: #0F172A; margin-bottom: 12px;">Confirm Logout</h3>
            <p style="color: #64748B; margin-bottom: 24px; font-size: 15px;">Are you sure you want to logout from the admin portal?</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button class="btn btn-outline" id="cancelLogoutBtn" style="padding: 10px 24px;">Cancel</button>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: #DC2626; border-color: #DC2626; padding: 10px 24px;">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    @yield('scripts')
</body>
</html>
