<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin Dashboard') - PT Lovina North Bali</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://unpkg.com/lucide@0.428.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('head_extra')
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar-header" style="display: flex; justify-content: center; align-items: center; padding: 20px 15px;">
            <img src="{{ asset('images/white logo lovina.png') }}" alt="PT LOVINA NORTH BALI" style="height: 115px; max-width: 100%; object-fit: contain;">
        </div>

        <div style="padding-top: 16px; flex-grow: 1;">
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="side-dashboard" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i> Dashboard
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">WEBSITE MANAGEMENT</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.cms.index') }}" class="admin-nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}" id="side-cms" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="monitor" style="width: 18px; height: 18px;"></i> Website CMS
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" id="side-settings" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="settings" style="width: 18px; height: 18px;"></i> Company Settings
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">CONTENT MANAGEMENT</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.articles.index') }}" class="admin-nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" id="side-articles" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="newspaper" style="width: 18px; height: 18px;"></i> News Articles
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">PROPERTY MANAGEMENT</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.properties.index') }}" class="admin-nav-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}" id="side-properties" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="home" style="width: 18px; height: 18px;"></i> Properties
                    </a>
                </li>
            </ul>

            <div class="admin-nav-section">CUSTOMER & DISCOVERY</div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.locations.index') }}" class="admin-nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" id="side-locations" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i> Locations
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.inquiries.index') }}" class="admin-nav-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}" id="side-inquiries" style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="mail" style="width: 18px; height: 18px;"></i> Inquiries
                    </a>
                </li>
            </ul>
        </div>

        <div style="padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <form action="{{ route('admin.logout') }}" method="POST" id="sidebarLogoutForm" style="margin: 0;">
                @csrf
                <button type="submit" class="admin-nav-link" id="sidebarLogoutBtn" style="background: none; border: none; width: 100%; cursor: pointer; text-align: left; padding: 10px 14px; font: inherit; color: #FCA5A5; display: flex; align-items: center; gap: 10px; border-radius: 6px;">
                    <i data-lucide="log-out" style="width: 18px; height: 18px;"></i> Logout
                </button>
            </form>
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
                <a href="{{ route('home') }}" target="_blank" style="font-size: 14px; font-weight: 500; color: #2563EB; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="globe" style="width: 16px; height: 16px;"></i> View Website
                </a>
                <div style="cursor: pointer; display: flex; align-items: center;">
                    <i data-lucide="bell" style="width: 20px; height: 20px; color: #64748B;"></i>
                </div>
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
                <div style="background-color: #DCFCE7; border: 1px solid #86EFAC; color: #166534; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="check" style="width: 16px; height: 16px;"></i> {{ session('success') }}
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

    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
