<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between hidden md:flex shrink-0 z-20 h-screen">
    
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
    </div>

    {{-- FOOTER --}}
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-white hover:text-blue-600 rounded-xl text-sm font-medium transition-colors border border-transparent hover:border-slate-200 hover:shadow-sm">
            
            <x-heroicon-o-question-mark-circle class="w-5 h-5"/>
            Help Center
        </a>
    </div>
</aside>