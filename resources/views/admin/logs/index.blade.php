@extends('layouts.app')

@section('title', 'Monitoring Log | LAPISSO')

@section('content')

<div class="mb-8 flex justify-between items-center">

    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            Monitoring Log
        </h1>

        <p class="text-slate-500 mt-1">
            Pantau aktivitas autentikasi pengguna pada sistem SSO.
        </p>
    </div>

</div>



{{-- FILTER --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">

    <div class="grid md:grid-cols-4 gap-4">

        <div class="flex items-center gap-2 border rounded-lg px-3 py-2">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400"/>
            <input
                type="text"
                placeholder="Cari user..."
                class="w-full outline-none text-sm"
            >
        </div>

        <select class="border rounded-lg px-3 py-2 text-sm">
            <option>Status</option>
            <option>Success</option>
            <option>Failed</option>
        </select>

        <select class="border rounded-lg px-3 py-2 text-sm">
            <option>Aplikasi</option>
            <option>Portal</option>
            <option>Dashboard</option>
        </select>

        <input
            type="date"
            class="border rounded-lg px-3 py-2 text-sm"
        >

    </div>

</div>



{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">

        <h3 class="font-bold text-slate-900">
            Log Aktivitas
        </h3>

        <span class="text-sm text-slate-500">
            {{ $logs->count() ?? 0 }} log
        </span>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">

                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Application</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">IP Address</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Time</th>

                </tr>
            </thead>


            <tbody class="divide-y divide-slate-100 text-sm">

                @foreach($logs ?? [] as $log)

                <tr class="hover:bg-blue-50/40">

                    {{-- USER --}}
                    <td class="px-6 py-4 font-semibold text-slate-900">
                        {{ $log->user_email }}
                    </td>


                    {{-- APPLICATION --}}
                    <td class="px-6 py-4 text-slate-600">
                        {{ $log->application }}
                    </td>


                    {{-- ACTION --}}
                    <td class="px-6 py-4 text-slate-600">
                        {{ $log->action }}
                    </td>


                    {{-- IP --}}
                    <td class="px-6 py-4 text-slate-500">
                        {{ $log->ip_address }}
                    </td>


                    {{-- STATUS --}}
                    <td class="px-6 py-4">

                        @if($log->status == 'success')

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Success
                        </span>

                        @else

                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                            Failed
                        </span>

                        @endif

                    </td>


                    {{-- TIME --}}
                    <td class="px-6 py-4 text-right text-slate-500">
                        {{ $log->created_at->diffForHumans() }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection