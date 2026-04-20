@extends('layouts.app')

@section('title', 'Detail User | LAPISSO')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('users.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
        <x-heroicon-o-arrow-left class="w-5 h-5"/>
    </a>
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Detail Pengguna</h1>
        <p class="text-slate-500 mt-1">Informasi lengkap akun dan hak akses aplikasi.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Profil Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
            <div class="px-6 pb-6">
                <div class="-mt-12 mb-4 relative">
                    <div class="w-24 h-24 bg-white rounded-2xl border-4 border-white shadow-md flex items-center justify-center text-3xl font-bold text-blue-600">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-slate-900">{{ $user->nama }}</h3>
                <p class="text-slate-500 text-sm mb-4">{{ $user->email }}</p>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Status Akun</span>
                        @if($user->is_active)
                            <span class="text-emerald-600 font-bold">Active</span>
                        @else
                            <span class="text-slate-400 font-bold">Disabled</span>
                        @endif
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Jabatan</span>
                        <span class="text-slate-900 font-semibold uppercase">{{ $user->jabatan }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Internal ID</span>
                        <span class="text-slate-400 text-[10px] font-mono">{{ $user->id }}</span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="flex justify-center items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all">
                        <x-heroicon-o-pencil-square class="w-4 h-4"/>
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Akses Aplikasi Card --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Izin Akses Aplikasi</h3>
            </div>
            
            <div class="p-6">
                @if($user->applications->isEmpty())
                    <div class="text-center py-8">
                        <x-heroicon-o-shield-exclamation class="w-12 h-12 text-slate-200 mx-auto mb-3"/>
                        <p class="text-slate-500 italic">User ini tidak memiliki akses ke aplikasi manapun.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->applications as $app)
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                                <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-sm">
                                    <x-heroicon-o-cpu-chip class="w-6 h-6 text-blue-500"/>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $app->app_name }}</h4>
                                    <p class="text-slate-500 text-[11px] font-mono uppercase tracking-tighter">{{ $app->client_id }}</p>
                                </div>
                                <div class="ml-auto">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 block"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-slate-50 p-6 border-t border-slate-100">
                <div class="flex items-start gap-3 text-sm text-slate-600">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mt-0.5 shrink-0"/>
                    <p>Hak akses ini disinkronkan secara otomatis. User hanya dapat login ke aplikasi yang terdaftar di atas melalui portal SSO.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection