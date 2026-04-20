@extends('layouts.app')

@section('title', 'Manajemen User | LAPISSO')

@section('content')

<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            Manajemen User
        </h1>
        <p class="text-slate-500 mt-1">
            Kelola pengguna dan atur izin akses ke aplikasi SSO.
        </p>
    </div>

    <a href="{{ route('users.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md shadow-blue-100">
        <x-heroicon-o-plus class="w-5 h-5"/>
        Tambah User
    </a>
</div>

{{-- SEARCH & FILTER --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
    <div class="flex items-center gap-3">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400"/>
        <input
            type="text"
            placeholder="Cari nama atau email user..."
            class="w-full outline-none text-sm text-slate-700 placeholder-slate-400"
        >
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h3 class="font-bold text-slate-900 text-lg">
            Daftar Pengguna
        </h3>
        <span class="px-3 py-1 bg-slate-200 text-slate-700 text-xs font-bold rounded-full">
            {{ $users->count() ?? 0 }} TOTAL USER
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                    <th class="px-6 py-4">User Info</th>
                    <th class="px-6 py-4">Akses Aplikasi</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-sm">
                @foreach($users ?? [] as $user)
                <tr class="hover:bg-blue-50/40 transition-colors">
                    
                    {{-- USER INFO --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 text-base">{{ $user->nama }}</span>
                            <span class="text-slate-500 text-xs">{{ $user->email }}</span>
                        </div>
                    </td>

                    {{-- AKSES APLIKASI --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->applications as $app)
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 rounded text-[10px] font-medium uppercase">
                                    {{ $app->app_name }}
                                </span>
                            @empty
                                <span class="text-slate-400 text-xs italic">Tidak ada akses</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- ROLE --}}
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $user->jabatan == 'admin' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
                            {{ strtoupper($user->jabatan) }}
                        </span>
                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-4">
                        @if($user->is_active == 1)
                            <div class="flex items-center gap-1.5 text-emerald-600 font-bold text-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-slate-400 font-bold text-xs">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                Disabled
                            </div>
                        @endif
                    </td>

                    {{-- ACTIONS --}}
                    {{-- ACTIONS --}}
<td class="px-6 py-4 text-right">
    <div class="flex justify-end gap-1">
        {{-- VIEW --}}
        <a href="{{ route('users.show', $user->id) }}" 
           class="p-2 rounded-lg hover:bg-blue-100 text-blue-600 transition-colors" title="Lihat Detail">
            <x-heroicon-o-eye class="w-5 h-5"/>
        </a>

        {{-- EDIT --}}
        <a href="{{ route('users.edit', $user->id) }}" 
           class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors" title="Edit User">
            <x-heroicon-o-pencil-square class="w-5 h-5"/>
        </a>

        {{-- DELETE --}}
        <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini dari sistem dan Keycloak?');" 
              class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Hapus User">
                <x-heroicon-o-trash class="w-5 h-5"/>
            </button>
        </form>
    </div>
</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->isEmpty())
        <div class="p-12 text-center">
            <x-heroicon-o-user-group class="w-12 h-12 text-slate-300 mx-auto mb-4"/>
            <p class="text-slate-500 font-medium">Belum ada pengguna yang terdaftar.</p>
        </div>
    @endif
</div>

@endsection