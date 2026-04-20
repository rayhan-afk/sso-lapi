<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin LAPISSO')</title>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ panelOpen: false }" class="bg-slate-50 text-slate-800 h-screen flex overflow-hidden antialiased">

    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">

        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-30">
            
            <div class="flex-1 max-w-xl relative hidden md:block">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"/>
                <input type="text" placeholder="Cari data atau log..." 
                    class="w-full bg-slate-100/50 border border-slate-200 text-sm rounded-full pl-11 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-3 sm:gap-4 ml-auto">
                
                <button @click="panelOpen = !panelOpen" class="text-slate-400 hover:text-blue-600 transition p-2 hover:bg-slate-50 rounded-lg relative group">
                    <x-heroicon-o-clock class="w-6 h-6"/>
                    <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Riwayat Akses</span>
                </button>

                <button class="text-slate-400 hover:text-blue-600 transition p-2 hover:bg-slate-50 rounded-lg relative">
                    <x-heroicon-o-bell class="w-6 h-6"/>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 text-sm font-medium text-slate-700 hover:text-blue-600 transition p-1 pr-3 rounded-full hover:bg-slate-50 border border-transparent hover:border-slate-100">
                        <div class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-sm">
                            {{ substr(Auth::user()->nama ?? 'A', 0, 1) }}
                        </div>
                        <span class="hidden lg:block">{{ Auth::user()->nama ?? 'Admin' }}</span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-3 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50">
                        <div class="px-3 py-2 border-b border-slate-50 mb-1">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Administrator</p>
                        </div>
                        <a href="#" class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-600 rounded-xl hover:bg-slate-50 transition">
                            <x-heroicon-o-user class="w-4 h-4"/>
                            Profil Saya
                        </a>
                        <hr class="my-1 border-slate-100">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-600 rounded-xl hover:bg-red-50 transition font-medium">
                                <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4"/>
                                Keluar SSO
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-10">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>

    </main>

    <div x-cloak x-show="panelOpen" class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm transition-opacity" @click="panelOpen = false"></div>
        <div class="absolute inset-y-0 right-0 pl-10 max-w-full flex">
            <aside x-show="panelOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                class="w-screen max-w-sm bg-white border-l border-slate-200 shadow-2xl flex flex-col">
                
                <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100 shrink-0">
                    <h2 class="text-lg font-bold text-slate-900">Aktivitas Admin</h2>
                    <button @click="panelOpen = false" class="text-slate-400 hover:text-slate-600 p-2 transition">
                        <x-heroicon-o-x-mark class="w-6 h-6"/>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-8">
                    <section>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-4">Informasi Sesi</p>
                        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-500 font-medium">Alamat IP</span>
                                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded">{{ request()->ip() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500 font-medium">Status</span>
                                <span class="flex items-center gap-1.5 text-xs font-bold text-green-600">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Terenkripsi
                                </span>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Log Aktivitas</p>
                        </div>
                        <div class="space-y-6 relative before:absolute before:inset-0 before:ml-3 before:-translate-x-px before:h-full before:w-0.5 before:bg-slate-100">
                            <div class="relative flex items-center justify-between gap-4 pl-8 group">
                                <div class="absolute left-0 w-6 h-6 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center -translate-x-1.5 shadow-sm">
                                    <div class="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                                </div>
                                <div class="flex flex-col">
                                    <p class="text-sm font-semibold text-slate-800">Update Konfigurasi</p>
                                    <p class="text-[11px] text-slate-400">Settings Page • 2 menit lalu</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </aside>
        </div>
    </div>

</body>
</html>