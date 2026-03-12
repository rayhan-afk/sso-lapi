@extends('layouts.app')

@section('title', 'Dashboard | LAPISSO')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Selamat Datang, {{ Auth::user()->nama ?? 'Pengguna' }} 👋</h1>
        <p class="text-slate-500 mt-1">Akses cepat aplikasi Anda dengan aman.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-blue-300 transition-colors">
            <div class="p-3.5 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Aplikasi</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-0.5">28</h3>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-emerald-300 transition-colors">
            <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Aplikasi Aktif Anda</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-0.5">12</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-amber-300 transition-colors">
            <div class="p-3.5 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Login Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-0.5">5</h3>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-end mb-6">
        <h2 class="text-xl font-bold text-slate-900">Semua Aplikasi</h2>
        <div class="hidden md:flex bg-slate-200/50 p-1 rounded-lg">
            <button class="px-4 py-1.5 text-sm font-semibold bg-white text-blue-600 shadow-sm rounded-md">Internal</button>
            <button class="px-4 py-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 rounded-md">Operasional</button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 pb-10">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-slate-800 text-yellow-400 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Pemanis</h3>
                    <p class="text-xs text-slate-500">Pemanis Aplikasi</p>
                </div>
            </div>
            <a href="#" class="w-full block text-center py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold rounded-lg transition-colors text-sm border border-blue-100">Masuk</a>
        </div>
        
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:emerald-300 transition-all flex flex-col justify-between group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-inner font-bold text-2xl group-hover:scale-105 transition-transform">e</div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">E-Nomor</h3>
                    <p class="text-xs text-slate-500">Sistem E-Nomor</p>
                </div>
            </div>
            <a href="#" class="w-full block text-center py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold rounded-lg transition-colors text-sm border border-blue-100">Masuk</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:blue-300 transition-all flex flex-col justify-between group">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-slate-800 text-blue-400 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">SISTA</h3>
                    <p class="text-xs text-slate-500">Keuangan & Pajak</p>
                </div>
            </div>
            <a href="#" class="w-full block text-center py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold rounded-lg transition-colors text-sm border border-blue-100">Masuk</a>
        </div>
    </div>
@endsection

@section('right_panel')
    <h3 class="font-bold text-slate-900 mb-6">Aktivitas & Keamanan</h3>

    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">Login Terakhir</p>
                <p class="text-xs text-slate-500 mt-0.5">Bandung, WIB</p>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-200 space-y-2 text-xs text-slate-600">
            <p>Perangkat: <span class="font-semibold text-slate-900">Chrome - Windows</span></p>
            <p>MFA Status: <span class="font-semibold text-emerald-600">Aktif</span></p>
        </div>
    </div>

    <div class="bg-red-50 p-4 rounded-xl border border-red-200 flex items-start gap-3">
        <div class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-900">Akses mencurigakan terdeteksi!</p>
            <p class="text-xs text-red-700 mt-1 hover:underline cursor-pointer">Segera periksa keamanan Anda.</p>
        </div>
    </div>
@endsection