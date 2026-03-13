@extends('layouts.app')

@section('title', 'Admin Dashboard | LAPISSO')

@section('content')

<div class="mb-8 flex justify-between items-end">

    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
            System Overview
        </h1>

        <p class="text-slate-500 mt-1">
            Pantau lalu lintas autentikasi dan status sistem SSO.
        </p>
    </div>

    <div class="text-right">
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">
            Session ID
        </p>

        <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1.5 rounded border border-slate-200">
            {{ session()->getId() }}
        </p>
    </div>

</div>


{{-- ================= STATS ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">


    {{-- TOTAL USERS --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">

        <div class="flex justify-between items-start mb-4">

            <div class="p-2.5 bg-blue-50 rounded-xl border border-blue-100">
                <x-heroicon-o-users class="w-5 h-5 text-blue-600"/>
            </div>

            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded-md">
                +12 Hari Ini
            </span>

        </div>

        <p class="text-sm font-semibold text-slate-500">
            Total Users
        </p>

        <h3 class="text-2xl font-bold text-slate-900 mt-1">
            {{ $totalUsers ?? '1,420' }}
        </h3>

    </div>



    {{-- APPLICATIONS --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">

        <div class="flex justify-between items-start mb-4">

            <div class="p-2.5 bg-indigo-50 rounded-xl border border-indigo-100">
                <x-heroicon-o-squares-2x2 class="w-5 h-5 text-indigo-600"/>
            </div>

        </div>

        <p class="text-sm font-semibold text-slate-500">
            SSO Applications
        </p>

        <h3 class="text-2xl font-bold text-slate-900 mt-1">
            {{ $totalApps ?? '8' }}
        </h3>

    </div>



    {{-- ACTIVE SESSIONS --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">

        <div class="flex justify-between items-start mb-4">

            <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-100">
                <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600"/>
            </div>

        </div>

        <p class="text-sm font-semibold text-slate-500">
            Active Sessions
        </p>

        <h3 class="text-2xl font-bold text-slate-900 mt-1">
            {{ $activeSessions ?? '356' }}
        </h3>

    </div>



    {{-- FAILED LOGIN --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">

        <div class="flex justify-between items-start mb-4">

            <div class="p-2.5 bg-red-50 rounded-xl border border-red-100">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600"/>
            </div>

        </div>

        <p class="text-sm font-semibold text-slate-500">
            Failed Logins Today
        </p>

        <h3 class="text-2xl font-bold text-red-600 mt-1">
            {{ $failedLogins ?? '5' }}
        </h3>

    </div>

</div>



{{-- ================= SYSTEM STATUS ================= --}}
<div class="grid lg:grid-cols-2 gap-6 mb-10">


    {{-- KEYCLOAK STATUS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <h3 class="font-bold text-slate-900 mb-4">
            SSO Server Status
        </h3>

        <div class="flex items-center justify-between">

            <span class="text-slate-600">
                Keycloak Server
            </span>

            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                Online
            </span>

        </div>

    </div>



    {{-- QUICK ACTION --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <h3 class="font-bold text-slate-900 mb-4">
            Quick Actions
        </h3>

        <div class="flex flex-wrap gap-3">

            <a href="#"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                + Add User
            </a>

            <a href="#"
               class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                + Add Application
            </a>

            <a href="#"
               class="px-4 py-2 bg-slate-700 text-white text-sm font-semibold rounded-lg hover:bg-slate-800">
                View Logs
            </a>

        </div>

    </div>

</div>



{{-- ================= ACTIVITY LOG ================= --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">

        <h3 class="text-base font-bold text-slate-900">
            Aktivitas Autentikasi Terbaru
        </h3>

        <a href="#"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
            Lihat Semua
        </a>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">

                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Application</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">IP Address</th>
                    <th class="px-6 py-4 text-right">Time</th>

                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-sm">

                @foreach($recentLogs ?? [] as $log)

                <tr class="hover:bg-blue-50/40">

                    <td class="px-6 py-4 font-semibold text-slate-900">
                        {{ $log->user_email }}
                    </td>

                    <td class="px-6 py-4 text-slate-600">
                        {{ $log->application }}
                    </td>

                    <td class="px-6 py-4 text-slate-600">
                        {{ $log->action }}
                    </td>

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

                    <td class="px-6 py-4 text-slate-500">
                        {{ $log->ip_address }}
                    </td>

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