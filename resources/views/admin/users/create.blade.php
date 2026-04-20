<!-- resources\views\admin\users\create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah User | LAPISSO')

@section('content')

<div class="mb-8">
    <a href="{{ route('users.index') }}"
       class="text-sm text-blue-600 hover:underline flex items-center gap-2 mb-4">
        <x-heroicon-o-arrow-left class="w-4 h-4"/>
        Kembali ke Manajemen User
    </a>

    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
        Tambah User
    </h1>
    <p class="text-slate-500 mt-1">
        Tambahkan pengguna baru dan atur izin akses aplikasi SSO.
    </p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 max-w-3xl">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- NAMA --}}
            <div class="mb-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Kinan Ann"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            {{-- EMAIL --}}
            <div class="mb-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@email.com"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            {{-- PASSWORD --}}
            <div class="mb-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            {{-- ROLE --}}
            <div class="mb-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Role (Jabatan)</label>
                <select name="role" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </div>

        {{-- STATUS --}}
        <div class="mt-6 mb-8">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Status Akun</label>
            <select name="status" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
            </select>
        </div>

        <hr class="border-slate-100 my-8">

        {{-- IZIN AKSES APLIKASI --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <label class="text-sm font-bold text-slate-800">Izin Akses Aplikasi</label>
                <span class="text-xs text-slate-500 italic">* Pilih aplikasi yang boleh diakses user ini</span>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                @foreach($applications as $app)
                <label class="relative flex items-center p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50/50 transition-all group">
                    <input type="checkbox" name="apps[]" value="{{ $app->id }}" 
                        class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    
                    <div class="ml-4">
                        <span class="block text-sm font-bold text-slate-900 group-hover:text-blue-700 transition-colors">
                            {{ $app->nama_aplikasi ?? $app->app_name }}
                        </span>
                        <span class="block text-xs text-slate-500">
                            Client ID: <span class="font-mono text-blue-600">{{ $app->client_id }}</span>
                        </span>
                    </div>

                    <div class="ml-auto">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500">
                            OIDC Client
                        </span>
                    </div>
                </label>
                @endforeach
            </div>
            
            @if($applications->isEmpty())
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm">
                    Belum ada aplikasi yang terdaftar. <a href="{{ route('applications.create') }}" class="underline font-bold">Tambah aplikasi dulu</a>.
                </div>
            @endif
        </div>

        {{-- BUTTON ACTIONS --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('users.index') }}"
                class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                Batal
            </a>

            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 shadow-md shadow-blue-200 transition-all active:scale-95">
                Simpan User & Sinkron Keycloak
            </button>
        </div>
    </form>
</div>

@endsection