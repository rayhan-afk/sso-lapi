<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Aplikasi | SSO LAPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-800">LAPI <span class="text-blue-600">Portal</span></span>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-bold text-slate-800">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-6xl w-full mx-auto p-6 md:p-8 mt-4">
        
        <div class="mb-10 text-center sm:text-left">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Selamat datang, {{ explode(' ', Auth::user()->nama)[0] }} 👋</h1>
            <p class="text-slate-500 mt-2 text-lg">Pilih aplikasi yang ingin Anda akses hari ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            <a href="#" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">E-Doc</h3>
                    <p class="text-xs text-slate-500 mt-1">Manajemen Dokumen</p>
                </div>
            </a>

            <a href="#" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:amber-300 transition-all duration-200 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Sista</h3>
                    <p class="text-xs text-slate-500 mt-1">Manajemen Tenaga Ahli</p>
                </div>
            </a>

            <a href="#" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:emerald-300 transition-all duration-200 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">PEMANIS</h3>
                    <p class="text-xs text-slate-500 mt-1">Manajemen Proyek</p>
                </div>
            </a>

            <a href="#" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:indigo-300 transition-all duration-200 flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Sistem Absensi</h3>
                    <p class="text-xs text-slate-500 mt-1">Presensi Foto & Lokasi</p>
                </div>
            </a>

        </div>
    </main>

</body>
</html>