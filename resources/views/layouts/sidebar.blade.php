<aside id="main-sidebar" class="w-72 h-screen flex flex-col bg-white border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-all duration-300 ease-in-out relative z-40 hidden md:flex shrink-0">
    
    <div class="h-20 flex items-center justify-between px-5 border-b border-slate-100">
        <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <span class="sidebar-text font-black text-xl tracking-tight text-slate-900 transition-opacity duration-300">
                LAPI<span class="text-blue-600">SSO</span>
            </span>
        </div>
        
        <button id="toggle-sidebar" class="text-slate-400 hover:text-blue-600 transition-colors p-1.5 rounded-lg hover:bg-blue-50 shrink-0">
            <svg id="icon-expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            <svg id="icon-collapsed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-4 space-y-1.5 custom-scrollbar">
        
        <p class="sidebar-text px-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 mt-2 whitespace-nowrap">Menu Utama</p>

            @if(Auth::check() && Auth::user()->jabatan === 'admin')
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 border-transparent' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors border">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Overview System
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Manajemen Aplikasi
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Manajemen User
                </a>
                <a href="{{ route('log.activity') }}" class="{{ request()->routeIs('log.activity') ? 'bg-blue-50 text-blue-700 border-blue-100 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 border-transparent font-medium' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors border">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Monitoring Log
                </a>

            <a href="{{ route('users.sessions') }}" class="group relative flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.sessions') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold shadow-md shadow-blue-500/25' : 'font-semibold text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:translate-x-1' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('users.sessions') ? 'text-white' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="sidebar-text whitespace-nowrap">Monitoring Sesi</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold shadow-md shadow-blue-500/25' : 'font-semibold text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:translate-x-1' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="sidebar-text whitespace-nowrap">Dashboard Saya</span>
            </a>
            @endif
    </nav>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <div class="bg-white border border-slate-200 rounded-xl p-2.5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 font-bold border border-blue-200">
                    {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
                </div>
                <div class="sidebar-text flex flex-col">
                    <span class="text-sm font-bold text-slate-900 truncate w-[100px]">{{ Auth::user()->nama ?? 'User' }}</span>
                    <span class="text-xs font-medium text-slate-500 truncate w-[100px]">{{ Auth::user()->jabatan ?? 'Staff' }}</span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="sidebar-text shrink-0">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('main-sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const textElements = document.querySelectorAll('.sidebar-text');
        const iconExpanded = document.getElementById('icon-expanded');
        const iconCollapsed = document.getElementById('icon-collapsed');

        // Cek localStorage, apakah sebelumnya ditutup?
        const isCollapsed = localStorage.getItem('lapisso_sidebar_collapsed') === 'true';
        
        // Fungsi untuk Set Status Buka/Tutup
        const setSidebarState = (collapsed) => {
            if (collapsed) {
                sidebar.classList.replace('w-72', 'w-20');
                textElements.forEach(el => el.classList.add('hidden'));
                iconExpanded.classList.add('hidden');
                iconCollapsed.classList.remove('hidden');
            } else {
                sidebar.classList.replace('w-20', 'w-72');
                textElements.forEach(el => el.classList.remove('hidden'));
                iconExpanded.classList.remove('hidden');
                iconCollapsed.classList.add('hidden');
            }
        };

        // Aplikasikan state saat halaman pertama load
        if (isCollapsed) setSidebarState(true);

        // Event saat tombol diklik
        toggleBtn.addEventListener('click', () => {
            const willCollapse = sidebar.classList.contains('w-72');
            setSidebarState(willCollapse);
            localStorage.setItem('lapisso_sidebar_collapsed', willCollapse);
        });
    });
</script>