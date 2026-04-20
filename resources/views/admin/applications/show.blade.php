@extends('layouts.app')

@section('title', 'Detail Aplikasi | LAPISSO')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('applications.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
        <x-heroicon-o-arrow-left class="w-5 h-5"/>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Detail Aplikasi</h1>
        <p class="text-slate-500 mt-1">Konfigurasi teknis untuk integrasi SSO.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Info Utama --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                    <x-heroicon-o-rectangle-stack class="w-10 h-10"/>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $app->app_name }}</h2>
                    <span class="text-sm font-mono text-slate-400 uppercase tracking-widest">ID: {{ $app->client_id }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Redirect URI / URL Aplikasi</label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-medium text-slate-700 break-all">
                        {{ $app->url_aplikasi }}
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Status Koneksi</label>
                    <div>
                        @if($app->status == 'active')
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">ACTIVE</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">DISABLED</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Credentials Area --}}
        <div class="bg-slate-900 rounded-2xl p-8 text-white shadow-xl shadow-slate-200">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                <x-heroicon-o-lock-closed class="w-5 h-5 text-blue-400"/>
                Credentials Security
            </h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Client Secret</label>
                    <div class="flex items-center gap-3 p-4 bg-slate-800 rounded-xl border border-slate-700">
                        <code class="text-blue-300 font-mono text-sm flex-1 truncate">{{ $app->client_secret ?? 'No Secret Provided' }}</code>
                        <button onclick="copyToClipboard('{{ $app->client_secret }}')" class="p-2 hover:bg-slate-700 rounded-lg transition-colors text-slate-400 hover:text-white">
                            <x-heroicon-o-clipboard-document class="w-5 h-5"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('applications.edit', $app->id) }}" class="w-full flex items-center justify-center gap-2 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all text-sm">
                    <x-heroicon-o-pencil-square class="w-4 h-4"/>
                    Edit Konfigurasi
                </a>
                <button class="w-full py-2.5 bg-slate-50 text-slate-600 rounded-xl font-bold hover:bg-slate-100 transition-all text-sm">
                    Cek Koneksi (Ping)
                </button>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-blue-50 border border-blue-100">
            <p class="text-xs text-blue-700 leading-relaxed italic">
                <strong>Tips:</strong> Client ID dan Secret ini digunakan untuk mengkonfigurasi driver Socialite di aplikasi client agar bisa terhubung ke server SSO ini.
            </p>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Secret copied to clipboard!');
    }
</script>
@endsection