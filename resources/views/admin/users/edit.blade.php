@extends('layouts.app')

@section('title', 'Edit User | LAPISSO')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('users.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
        <x-heroicon-o-arrow-left class="w-5 h-5"/>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit User</h1>
        <p class="text-slate-500 mt-1">Perbarui informasi profil dan hak akses aplikasi untuk <strong>{{ $user->nama }}</strong>.</p>
    </div>
</div>

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- SISI KIRI: FORM DATA UTAMA --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <x-heroicon-o-user-circle class="w-6 h-6 text-blue-600"/>
                    Informasi Profil
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->nama) }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all" required>
                    </div>

                    {{-- Email (Read Only karena ID di Keycloak pakai Email) --}}
                    <div class="space-y-2 opacity-70">
                        <label class="text-sm font-bold text-slate-700">Alamat Email (Permanen)</label>
                        <input type="email" value="{{ $user->email }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 cursor-not-allowed outline-none" readonly>
                        <p class="text-[10px] text-slate-400 font-medium italic">*Email tidak dapat diubah untuk menjaga sinkronisasi SSO.</p>
                    </div>

                    {{-- Password Baru --}}
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Password Baru (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all">
                    </div>

                    {{-- Role/Jabatan --}}
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Jabatan / Role</label>
                        <select name="role" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all">
                            <option value="user" {{ $user->jabatan == 'user' ? 'selected' : '' }}>User (Karyawan)</option>
                            <option value="admin" {{ $user->jabatan == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- AKSES APLIKASI --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <x-heroicon-o-key class="w-6 h-6 text-blue-600"/>
                    Izin Akses Aplikasi
                </h3>
                <p class="text-slate-500 text-sm mb-6">Pilih aplikasi mana saja yang boleh diakses oleh user ini.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($applications as $app)
                    <label class="relative flex items-center gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50 cursor-pointer hover:bg-blue-50 transition-colors group">
                        <input type="checkbox" name="apps[]" value="{{ $app->id }}" 
                               {{ $user->applications->contains($app->id) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 text-sm group-hover:text-blue-700">{{ $app->app_name }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $app->client_id }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SISI KANAN: STATUS & SUBMIT --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider text-center">Status Akun</h3>
                
                <div class="flex flex-col gap-3">
                    <label class="flex items-center justify-between p-3 rounded-xl border border-emerald-100 bg-emerald-50/50 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-bold text-emerald-700">Aktifkan Akun</span>
                        </div>
                        <input type="radio" name="status" value="active" {{ $user->is_active ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    </label>

                    <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                            <span class="text-sm font-bold text-slate-600">Nonaktifkan Akun</span>
                        </div>
                        <input type="radio" name="status" value="disabled" {{ !$user->is_active ? 'checked' : '' }} class="text-slate-600 focus:ring-slate-500">
                    </label>
                </div>

                <hr class="my-6 border-slate-100">

                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5"/>
                    Simpan Perubahan
                </button>

                <p class="text-[11px] text-slate-400 text-center mt-4 px-4 text-balance">
                    Perubahan status akan langsung berdampak pada kemampuan user untuk login di Keycloak.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection