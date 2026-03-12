@extends('layouts.app')

@section('title', 'Admin Dashboard | LAPISSO')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">System Overview</h1>
            <p class="text-slate-500 mt-1">Pantau lalu lintas autentikasi dan status aplikasi.</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Session UUID</p>
            <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1.5 rounded border border-slate-200">{{ Auth::user()->id }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 rounded-xl border border-blue-100">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded-md">+12 Hari Ini</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Total User Terdaftar</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">1,420</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 rounded-xl border border-blue-100">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Aplikasi Terintegrasi SSO</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">8</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-100">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">Status Normal</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Keycloak Server</p>
                <h3 class="text-lg font-bold text-emerald-600 mt-2">Online</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">Aktivitas Login Terbaru</h3>
            <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="px-6 py-4">User Email</th>
                        <th class="px-6 py-4">Aplikasi Tujuan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-900">andi.susanto@lapi.com</td>
                        <td class="px-6 py-4 text-slate-600">LAPI Venture Studio</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Berhasil</span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-500">2 menit lalu</td>
                    </tr>
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-900">budi_staff@lapi.com</td>
                        <td class="px-6 py-4 text-slate-600">Sistem Absensi Foto</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Berhasil</span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-500">14 menit lalu</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection