<aside id="main-sidebar" class="w-72 h-screen flex flex-col bg-white border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-all duration-300 ease-in-out relative z-40 hidden md:flex shrink-0">
    
    <div>
        {{-- LOGO --}}
        <div class="h-20 flex items-center px-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-md shadow-blue-200">
                    <x-heroicon-o-shield-check class="w-5 h-5 text-white"/>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">
                    LAPI<span class="text-blue-600">SSO</span>
                </span>
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

        {{-- MENU --}}
        <nav class="p-4 space-y-1.5 mt-2 overflow-y-auto">
            
            <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 mt-2">
                Main Menu
            </p>

            @if(Auth::check() && Auth::user()->jabatan === 'admin')

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 border-transparent' }}
                   flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors border">
                    
                    <x-heroicon-o-home class="w-5 h-5"/>
                    System Overview
                </a>

                {{-- Applications --}}
                <a href="{{ route('applications.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-squares-2x2 class="w-5 h-5"/>
                    Application Management
                </a>

                {{-- Users --}}
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-users class="w-5 h-5"/>
                    User Management
                </a>

                {{-- Logs --}}
                <a href="{{ route('logs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-document-text class="w-5 h-5"/>
                    Activity Logs
                </a>

                <a href="{{ route('users.sessions') }}" class="group relative flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition-all duration-300 {{ request()->routeIs('users.sessions') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold shadow-md shadow-blue-500/25' : 'font-semibold text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:translate-x-1' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('users.sessions') ? 'text-white' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="sidebar-text whitespace-nowrap">Monitoring Sesi</span>
            </a>

            @else

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 border-transparent' }}
                   flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors border">
                    
                    <x-heroicon-o-home class="w-5 h-5"/>
                    My Dashboard
                </a>

                {{-- Apps --}}
                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-squares-2x2 class="w-5 h-5"/>
                    Applications
                </a>

                {{-- Favorites --}}
                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-heart class="w-5 h-5"/>
                    Favorites
                </a>

                {{-- History --}}
                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent">
                    
                    <x-heroicon-o-clock class="w-5 h-5"/>
                    Access History
                </a>

            @endif
    </nav>

    {{-- FOOTER --}}
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-white hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent hover:border-slate-200 hover:shadow-sm">
            
            <x-heroicon-o-question-mark-circle class="w-5 h-5"/>
            Help Center
        </a>
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