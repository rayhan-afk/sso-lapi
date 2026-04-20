@extends('layouts.app')

@section('title', 'Monitoring Log | LAPISSO')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Monitoring Log</h1>
            <p class="text-slate-500 mt-1">Pantau jejak digital, aktivitas autentikasi, dan keamanan sistem.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">Riwayat Aktivitas Pengguna</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Aplikasi</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">IP & Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $log->user->nama ?? 'Sistem / Guest' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $log->application_name }}
                            </td>
                            <td class="px-6 py-4">
                                @if(str_contains($log->action_type, 'LOGIN'))
                                    <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $log->action_type }}</span>
                                @elseif(str_contains($log->action_type, 'LOGOUT'))
                                    <span class="px-2 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">{{ $log->action_type }}</span>
                                @else
                                    <span class="px-2 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">{{ $log->action_type }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $log->location ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <div class="font-mono">{{ $log->ip_address }}</div>
                                <div class="mt-1 truncate max-w-[150px]" title="{{ $log->device }}">{{ $log->device }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                Belum ada aktivitas yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection