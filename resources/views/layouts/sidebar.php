<aside class="sidebar d-flex flex-column">

    {{-- LOGO --}}
    <div class="sidebar-logo">

        <div class="d-flex align-items-center gap-2">

            <div class="logo-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <span class="logo-text">
                LAPI<span class="text-primary">SSO</span>
            </span>

        </div>

    </div>



    {{-- MENU --}}
    <div class="sidebar-menu flex-grow-1">

        <p class="menu-label">
            Menu Utama
        </p>


        {{-- ADMIN MENU --}}
        @if(Auth::check() && Auth::user()->jabatan === 'admin')

        <a href="{{ route('dashboard') }}"
           class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>
            <span>System Overview</span>

        </a>


        <a href="{{ route('applications.index') }}" class="sidebar-item">

            <i class="bi bi-grid"></i>
            <span>Manajemen Aplikasi</span>

        </a>


        <a href="#" class="sidebar-item">

            <i class="bi bi-people"></i>
            <span>Manajemen User</span>

        </a>


        <a href="#" class="sidebar-item">

            <i class="bi bi-file-earmark-text"></i>
            <span>Monitoring Log</span>

        </a>

        {{-- USER MENU --}}
        @else

        <a href="{{ route('dashboard') }}"
           class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>
            <span>Dashboard Saya</span>

        </a>


        <a href="#" class="sidebar-item">

            <i class="bi bi-grid"></i>
            <span>Daftar Aplikasi</span>

        </a>


        <a href="#" class="sidebar-item">

            <i class="bi bi-star"></i>
            <span>Favorit Saya</span>

        </a>


        <a href="#" class="sidebar-item">

            <i class="bi bi-clock-history"></i>
            <span>Riwayat Akses</span>

        </a>

        @endif

    </div>



    {{-- HELP --}}
    <div class="sidebar-help">

        <a href="#" class="help-button">

            <i class="bi bi-question-circle"></i>
            Pusat Bantuan

        </a>

    </div>

</aside>