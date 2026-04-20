<!-- resources\views\admin\applications\index.blade.php -->
@extends('layouts.app')

@section('title', 'Manajemen Aplikasi | LAPISSO')

@section('content')

<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            Manajemen Aplikasi
        </h1>
        <p class="text-slate-500 mt-1">
            Kelola aplikasi yang terhubung dengan sistem SSO.
        </p>
    </div>

    <a href="{{ route('applications.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all">
        <x-heroicon-o-plus class="w-5 h-5"/>
        Tambah Aplikasi
    </a>
</div>

{{-- FLASH MESSAGES --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5"/>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5"/>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
@endif

{{-- SEARCH --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <div class="flex items-center gap-3">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400"/>
        <input
            type="text"
            id="searchApp"
            placeholder="Cari aplikasi berdasarkan nama atau Client ID..."
            class="w-full outline-none text-sm text-slate-700 placeholder-slate-400"
        >
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h3 class="font-bold text-slate-900">
            Daftar Aplikasi SSO
        </h3>
        <span class="text-sm text-slate-500">
            {{ $applications->count() }} aplikasi terdaftar
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                    <th class="px-6 py-4">Application</th>
                    <th class="px-6 py-4">Client ID & Secret</th>
                    <th class="px-6 py-4">Redirect URI</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($applications as $app)
                <tr class="hover:bg-blue-50/40 transition-colors">
                    {{-- APP NAME --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-400">
                                <x-heroicon-o-rectangle-stack class="w-5 h-5"/>
                            </div>
                            <span class="font-semibold text-slate-900">{{ $app->app_name }}</span>
                        </div>
                    </td>

                    {{-- CLIENT ID & SECRET --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">ID:</span>
                                <code class="text-xs text-blue-600 font-mono bg-blue-50 px-1 rounded">{{ $app->client_id }}</code>
                            </div>
                            @if($app->client_secret)
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Secret:</span>
                                <div class="flex items-center gap-1">
                                    <code id="secret-{{ $app->id }}" class="text-xs text-slate-500 font-mono">••••••••••••</code>
                                    <button onclick="copySecret('{{ $app->client_secret }}', this)" class="text-slate-400 hover:text-blue-600 transition-colors">
                                        <x-heroicon-o-clipboard-document class="w-4 h-4"/>
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </td>

                    {{-- REDIRECT URI --}}
                    <td class="px-6 py-4">
                        <span class="text-slate-600 truncate max-w-[200px] block" title="{{ $app->url_aplikasi }}">
                            {{ $app->url_aplikasi }}
                        </span>
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4">
                        @if($app->status == 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Disabled
                            </span>
                        @endif
                    </td>

                    {{-- ACTIONS --}}
                    {{-- ACTIONS --}}
<td class="px-6 py-4 text-right">
    <div class="flex justify-end gap-1">
        {{-- VIEW --}}
        <a href="{{ route('applications.show', $app->id) }}" 
           title="Lihat Detail" 
           class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
            <x-heroicon-o-eye class="w-5 h-5"/>
        </a>

        {{-- EDIT --}}
        <a href="{{ route('applications.edit', $app->id) }}" 
           title="Edit Aplikasi" 
           class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
            <x-heroicon-o-pencil-square class="w-5 h-5"/>
        </a>

        {{-- DELETE --}}
        <form action="{{ route('applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Menghapus aplikasi akan memutuskan koneksi SSO untuk aplikasi ini. Lanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" title="Hapus" class="p-2 rounded-lg hover:bg-red-50 text-red-500">
                <x-heroicon-o-trash class="w-5 h-5"/>
            </button>
        </form>
    </div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center">
                            <x-heroicon-o-folder-open class="w-12 h-12 text-slate-200 mb-3"/>
                            <p>Belum ada aplikasi yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT COPY TO CLIPBOARD --}}
<script>
    function copySecret(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>';
            
            setTimeout(() => {
                btn.innerHTML = originalIcon;
            }, 2000);
        });
    }

    // Simple Search Filter
    document.getElementById('searchApp').addEventListener('input', function(e) {
        const text = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const content = row.innerText.toLowerCase();
            row.style.display = content.includes(text) ? '' : 'none';
        });
    });
</script>

@endsection