@extends('layouts.app')

@section('title', 'Tambah User | LAPISSO')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-slate-500 mt-1">Buat akun untuk akses ke ekosistem LAPI dan SSO Keycloak.</p>
        </div>
        <div class="text-right">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-800 shadow-sm">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden max-w-3xl">
        <div class="bg-blue-50/50 border-b border-blue-100 p-6 flex gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-blue-900">Sinkronisasi Identitas Terpusat</h3>
                <p class="text-sm text-blue-700 mt-1">Akun dan Role yang dibuat di sini akan otomatis terdaftar di Database lokal dan server otentikasi Keycloak.</p>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nama" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all @error('nama') border-red-500 bg-red-50 @enderror">
                    @error('nama')
                        <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email (Username SSO)</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="budi@lapi.com"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all @error('email') border-red-500 bg-red-50 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div>
                    <label for="jabatan" class="block text-sm font-bold text-slate-700 mb-1.5">Posisi Pekerjaan</label>
                    <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Staff IT, Direktur"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all @error('jabatan') border-red-500 bg-red-50 @enderror">
                    @error('jabatan')
                        <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-bold text-slate-700 mb-1.5">Hak Akses Sistem</label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all cursor-pointer @error('role') border-red-500 bg-red-50 @enderror">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator SSO</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password Awal</label>
                    <input type="password" name="password" id="password" required minlength="8" placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all @error('password') border-red-500 bg-red-50 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-8 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <label class="flex items-center cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="peer sr-only">
                        <div class="h-6 w-11 bg-slate-300 rounded-full peer-checked:bg-emerald-500 transition-colors"></div>
                        <div class="absolute left-1 top-1 h-4 w-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                    </div>
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-slate-800">Aktifkan Akun</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Pengguna dapat langsung login setelah akun dibuat.</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
@endsection