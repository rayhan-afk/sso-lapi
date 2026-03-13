@extends('layouts.app')

@section('title', 'Manajemen User | LAPISSO')

@section('content')

<div class="mb-8 flex justify-between items-center">

    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            Manajemen User
        </h1>

        <p class="text-slate-500 mt-1">
            Kelola pengguna yang dapat mengakses sistem SSO.
        </p>
    </div>

    <a href="{{ route('users.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">

        <x-heroicon-o-plus class="w-5 h-5"/>
        Tambah User

    </a>

</div>



{{-- SEARCH --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">

    <div class="flex items-center gap-3">

        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400"/>

        <input
            type="text"
            placeholder="Cari user..."
            class="w-full outline-none text-sm text-slate-700 placeholder-slate-400"
        >

    </div>

</div>



{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">

        <h3 class="font-bold text-slate-900">
            Daftar Pengguna
        </h3>

        <span class="text-sm text-slate-500">
            {{ $users->count() ?? 0 }} user
        </span>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">

                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>

                </tr>
            </thead>


            <tbody class="divide-y divide-slate-100 text-sm">

                @foreach($users ?? [] as $user)

                <tr class="hover:bg-blue-50/40">

                    {{-- USER NAME --}}
                    <td class="px-6 py-4 font-semibold text-slate-900">
                        {{ $user->name }}
                    </td>


                    {{-- EMAIL --}}
                    <td class="px-6 py-4 text-slate-600">
                        {{ $user->email }}
                    </td>


                    {{-- ROLE --}}
                    <td class="px-6 py-4">

                        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 border border-blue-200">
                            {{ $user->role ?? 'User' }}
                        </span>

                    </td>


                    {{-- STATUS --}}
                    <td class="px-6 py-4">

                        @if($user->status == 'active')

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Active
                        </span>

                        @else

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                            Disabled
                        </span>

                        @endif

                    </td>



                    {{-- ACTIONS --}}
                    <td class="px-6 py-4">

                        <div class="flex justify-end gap-2">

                            <button class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                                <x-heroicon-o-eye class="w-5 h-5"/>
                            </button>

                            <button class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                            </button>

                            <button class="p-2 rounded-lg hover:bg-red-50 text-red-600">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection