@extends('layouts.app')

@section('title', 'Manajemen User | LAPISSO')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manajemen User</h1>
            <p class="text-slate-500 mt-1">Kelola data pengguna, hak akses, dan sinkronisasi dengan Keycloak.</p>
        </div>
        <div class="text-right">
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah User
            </a>
        </div>
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

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" 
         x-data="{ 
            showModal: false, 
            targetId: null, 
            targetName: '', 
            targetStatus: null,
            
            openModal(id, name, isActive) {
                this.targetId = id;
                this.targetName = name;
                this.targetStatus = isActive;
                this.showModal = true;
            },
            
            submitForm() {
                document.getElementById('toggle-form-' + this.targetId).submit();
            }
         }">
         
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">Daftar Pengguna SSO</h3>
            
            <div class="relative">
                <input type="text" placeholder="Cari email..." class="pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="px-6 py-4">Informasi User</th>
                        <th class="px-6 py-4">Jabatan / Role</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $user->nama }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 font-medium">{{ $user->jabatan }}</div>
                                
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Admin SSO
                                    </span>
                                @else
                                    <span class="inline-block mt-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        User 
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <form id="toggle-form-{{ $user->id }}" action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                
                                <button type="button" 
                                        @click="openModal('{{ $user->id }}', '{{ addslashes($user->nama) }}', {{ $user->is_active ? 'true' : 'false' }})"
                                        class="group relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 {{ $user->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                    
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                                
                                <span class="ml-2 text-xs font-bold {{ $user->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold p-1 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                        Edit
                                    </a>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus permanen {{ $user->nama }} dari Database dan Keycloak?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold p-1 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">Belum Ada Pengguna</h3>
                                <p class="text-sm text-slate-500 mt-1">Sistem belum memiliki data pengguna yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="showModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
          
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
              <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <div x-show="showModal" 
                     @click.away="showModal = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                  
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                 :class="targetStatus ? 'bg-red-100' : 'bg-emerald-100'">
                                <svg class="h-6 w-6" :class="targetStatus ? 'text-red-600' : 'text-emerald-600'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title" x-text="targetStatus ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?'"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">
                                        Anda akan <span class="font-bold text-slate-700" x-text="targetStatus ? 'menonaktifkan' : 'mengaktifkan'"></span> akun atas nama <strong class="text-slate-900" x-text="targetName"></strong>.
                                    </p>
                                    <div class="mt-3 p-3 rounded-lg flex gap-3 text-sm" :class="targetStatus ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800'">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p x-text="targetStatus ? 'User tidak akan bisa login ke aplikasi mana pun via SSO.' : 'User akan dapat login kembali menggunakan kredensial mereka.'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                        <button type="button" @click="submitForm()" 
                                class="inline-flex w-full justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-colors"
                                :class="targetStatus ? 'bg-red-600 hover:bg-red-500 shadow-red-200' : 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-200'"
                                x-text="targetStatus ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'">
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
              </div>
            </div>
        </div>
        </div>
@endsection