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
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">

        <x-heroicon-o-plus class="w-5 h-5"/>

        Tambah Aplikasi
    </a>

</div>



{{-- SEARCH --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">

    <div class="flex items-center gap-3">

        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400"/>

        <input
            type="text"
            placeholder="Cari aplikasi..."
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
            {{ $applications->count() ?? 0 }} aplikasi
        </span>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">

                    <th class="px-6 py-4">Application</th>
                    <th class="px-6 py-4">Client ID</th>
                    <th class="px-6 py-4">Redirect URI</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>

                </tr>
            </thead>


            <tbody class="divide-y divide-slate-100 text-sm">

                @foreach($applications ?? [] as $app)

                <tr class="hover:bg-blue-50/40">

                    {{-- APP NAME --}}
                    <td class="px-6 py-4 font-semibold text-slate-900">
                        {{ $app->name }}
                    </td>


                    {{-- CLIENT ID --}}
                    <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                        {{ $app->client_id }}
                    </td>


                    {{-- REDIRECT URI --}}
                    <td class="px-6 py-4 text-slate-600">
                        {{ $app->redirect_uri }}
                    </td>


                    {{-- STATUS --}}
                    <td class="px-6 py-4">

                        @if($app->status == 'active')

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Active
                        </span>

                        @else

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-slate-200 text-slate-700 border border-slate-300">
                            Disabled
                        </span>

                        @endif

                    </td>



                    {{-- ACTIONS --}}
                    <td class="px-6 py-4">

                        <div class="flex justify-end gap-2">

                            <button
                                class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">

                                <x-heroicon-o-eye class="w-5 h-5"/>

                            </button>


                            <button
                                class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">

                                <x-heroicon-o-pencil-square class="w-5 h-5"/>

                            </button>


                            <button
                                class="p-2 rounded-lg hover:bg-red-50 text-red-600">

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