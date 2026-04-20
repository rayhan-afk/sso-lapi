@extends('layouts.app')

@section('title', 'Edit Aplikasi | LAPISSO')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('applications.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
        <x-heroicon-o-arrow-left class="w-5 h-5"/>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit Aplikasi</h1>
        <p class="text-slate-500 mt-1">Perbarui konfigurasi integrasi untuk aplikasi <strong>{{ $app->app_name }}</strong>.</p>
    </div>
</div>

{{-- TAMPILAN ERROR VALIDASI --}}
@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-3xl">
    <form action="{{ route('applications.update', $app->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 space-y-6">
                
                {{-- Nama Aplikasi --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Nama Aplikasi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-rectangle-stack class="w-5 h-5 text-slate-400"/>
                        </div>
                        <input type="text" name="app_name" value="{{ old('app_name', $app->app_name) }}" 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all" required>
                    </div>
                </div>

                {{-- Client ID (Readonly karena ini ID unik di Keycloak) --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Client ID (Identitas SSO)</label>
                    <input type="text" value="{{ $app->client_id }}" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 font-mono text-sm cursor-not-allowed" readonly>
                    <p class="text-[10px] text-slate-400 italic">*Client ID di-generate otomatis dan tidak dapat diubah agar tidak memutus koneksi.</p>
                </div>

                {{-- URL Aplikasi --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">URL Aplikasi / Redirect URI</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-link class="w-5 h-5 text-slate-400"/>
                        </div>
                        <input type="url" name="url_aplikasi" value="{{ old('url_aplikasi', $app->url_aplikasi) }}" 
                               placeholder="https://aplikasi-anda.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all" required>
                    </div>
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Status Akses</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all cursor-pointer">
                        <option value="active" {{ $app->status == 'active' ? 'selected' : '' }}>Aktif (Bisa Login)</option>
                        <option value="disabled" {{ $app->status == 'disabled' ? 'selected' : '' }}>Nonaktif (Login Ditutup)</option>
                    </select>
                </div>

            </div>

            {{-- FOOTER TOMBOL --}}
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('applications.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-all text-sm">
                    Batal
                </a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center gap-2 text-sm">
                    <x-heroicon-o-check-circle class="w-5 h-5"/>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection