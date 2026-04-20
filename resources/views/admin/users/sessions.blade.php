@extends('layouts.app')

@section('title', 'Sesi Aktif | LAPISSO')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Monitoring Sesi Aktif</h1>
        <p class="text-slate-500 mt-1">Pantau pengguna yang sedang login dan kelola akses mereka secara real-time.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-800">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-800">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-900">Total: <span class="text-blue-600">{{ count($sessions) }} Sesi</span> Terdeteksi</h3>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.sessions') }}" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh
                </a>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-full animate-pulse">● Live Updates</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4">Mulai Login</th>
                        <th class="px-6 py-4">Aktivitas Terakhir</th>
                        <th class="px-6 py-4 text-right">Aksi (Kick)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $session['nama_user'] }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $session['username'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded inline-block">
                                    {{ $session['ipAddress'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ \Carbon\Carbon::createFromTimestampMs($session['start'])->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ \Carbon\Carbon::createFromTimestampMs($session['lastAccess'])->format('H:i:s') }} WIB
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('users.force-logout', $session['id']) }}" method="POST" 
                                      onsubmit="return confirm('Peringatan: Memutus sesi ini akan membuat {{ $session['nama_user'] }} logout secara paksa dari semua aplikasi SSO. Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 border border-red-200 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zm11-2h-4m2-2v4"></path></svg>
                                        Force Logout
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">Tidak Ada Sesi Aktif</h3>
                                <p class="text-sm text-slate-500 mt-1">Saat ini tidak ada pengguna yang sedang login ke sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection