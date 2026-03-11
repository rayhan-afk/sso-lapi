<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSO Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #bfdbfe; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #60a5fa; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 h-screen flex overflow-hidden antialiased">

    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between hidden md:flex z-10 shadow-sm">
        <div>
            <div class="h-16 flex items-center px-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-md shadow-blue-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-800">SSO <span class="text-blue-600">LAPI</span></span>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-semibold transition-colors border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Application Access
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    User Management
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Activity Logs
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-sm shadow-sm">
                    {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-slate-500 truncate">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 shadow-sm z-10">
            <div class="flex-1 max-w-xl relative hidden sm:block">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Search audit logs, users, or apps..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>

            <div class="flex items-center gap-4 ml-auto">
                <div class="text-right hidden md:block">
                    <p class="text-xs font-medium text-slate-500">{{ Auth::user()->email }}</p>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-lg border border-red-200 hover:bg-red-600 hover:text-white transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Overview</h2>
                        <p class="text-sm text-slate-500 mt-1">Monitoring real-time authentication traffic and system health.</p>
                    </div>
                    <div class="hidden lg:block text-right">
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Session UUID</p>
                        <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded border border-slate-200 mt-1">{{ Auth::user()->id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded-md">+2.4%</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Total Users</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">1,240</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Total Applications</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">8</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Active Sessions</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">112</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="text-base font-bold text-slate-900">Recent Activity Logs</h3>
                        <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">View All Logs</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                                    <th class="px-6 py-4">User</th>
                                    <th class="px-6 py-4">Action</th>
                                    <th class="px-6 py-4">Application</th>
                                    <th class="px-6 py-4">IP Address</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">AS</div>
                                            <span class="font-semibold text-slate-900">reza@lapi.com</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">Login</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">Pemanis</td>
                                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">192.168.1.45</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Success
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-500 font-medium">2 mins ago</td>
                                </tr>
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-900">{{ Auth::user()->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">SSO Authenticate</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">Aplikasi Absensi</td>
                                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">10.0.0.12</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Success
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-500 font-medium">14 mins ago</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>