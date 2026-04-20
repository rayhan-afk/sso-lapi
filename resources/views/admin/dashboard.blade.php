@extends('layouts.app')

@section('title', 'Admin Dashboard | LAPISSO')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-6 flex justify-between items-center">

    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
        System Overview
    </h1>

    <div class="text-right">
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">
            Session ID
        </p>

        <p class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1.5 rounded-lg border border-slate-200">
            {{ session()->getId() }}
        </p>
    </div>

</div>


{{-- ================= WELCOME CARD ================= --}}
<div class="mb-10">

    <div class="relative overflow-hidden rounded-3xl p-8
                bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-500
                text-white shadow-md">

        {{-- decorative blur --}}
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-white/20 blur-3xl rounded-full"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <div>

                <h2 class="text-3xl font-bold tracking-tight">
                    Hello {{ auth()->user()->name ?? 'Admin' }} 👋
                </h2>

                <p class="text-blue-100 mt-2 text-sm md:text-base">
                    Welcome back. Monitor authentication traffic and system health today.
                </p>

                <p class="text-blue-200 text-xs mt-2">
                    {{ now()->format('l, d F Y') }}
                </p>

            </div>

        </div>

    </div>

</div>
{{-- ================= STATS ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">


{{-- USERS --}}
<div class="group relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm
            p-6 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">

    {{-- gradient accent --}}
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-sky-400"></div>

    <div class="flex items-center justify-between mb-6">

        <div class="p-3 rounded-xl bg-blue-100 group-hover:scale-110 transition">
            <x-heroicon-o-users class="w-5 h-5 text-blue-600"/>
        </div>

        <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-1 rounded-md">
            +12 today
        </span>

    </div>

    <p class="text-sm text-slate-500 font-medium">
        Total Users
    </p>

    <h3 class="text-3xl font-bold text-slate-900 mt-1">
        {{ $totalUsers ?? '1,420' }}
    </h3>

</div>



{{-- APPLICATIONS --}}
<div class="group relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm
            p-6 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">

    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-400"></div>

    <div class="mb-6">

        <div class="p-3 w-fit rounded-xl bg-indigo-100 group-hover:scale-110 transition">
            <x-heroicon-o-squares-2x2 class="w-5 h-5 text-indigo-600"/>
        </div>

    </div>

    <p class="text-sm text-slate-500 font-medium">
        Connected Applications
    </p>

    <h3 class="text-3xl font-bold text-slate-900 mt-1">
        {{ $totalApps ?? '8' }}
    </h3>

</div>



{{-- ACTIVE SESSIONS --}}
<div class="group relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm
            p-6 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">

    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-green-400"></div>

    <div class="flex items-center justify-between mb-6">

        <div class="p-3 rounded-xl bg-emerald-100 group-hover:scale-110 transition">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600"/>
        </div>

        <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">
            +24 last hour
        </span>

    </div>

    <p class="text-sm text-slate-500 font-medium">
        Active Sessions
    </p>

    <h3 class="text-3xl font-bold text-slate-900 mt-1">
        {{ $activeSessions ?? '356' }}
    </h3>

</div>



{{-- SECURITY THREATS --}}
<div class="group relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm
            p-6 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">

    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-500 to-rose-400"></div>

    <div class="flex items-center justify-between mb-6">

        <div class="p-3 rounded-xl bg-red-100 group-hover:scale-110 transition">
            <x-heroicon-o-shield-exclamation class="w-5 h-5 text-red-600"/>
        </div>

        <span class="text-xs font-semibold text-red-700 bg-red-100 px-2 py-1 rounded-md">
            Alert
        </span>

    </div>

    <p class="text-sm text-slate-500 font-medium">
        Threat Attempts
    </p>

    <h3 class="text-3xl font-bold text-red-600 mt-1">
        {{ $failedLogins ?? '5' }}
    </h3>

    <p class="text-xs text-slate-400 mt-1">
        Suspicious login activity detected
    </p>

</div>


</div>

{{-- ================= LOWER SECTION ================= --}}
<div class="grid lg:grid-cols-3 gap-6 mb-12">


{{-- SECURITY ALERTS --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

    <h3 class="text-lg font-semibold text-slate-800 mb-6">
        Security Alerts
    </h3>

    <div class="space-y-4">

        {{-- ALERT 1 --}}
        <div class="flex items-start gap-3 bg-red-50 border border-red-100 p-3 rounded-lg">

            <div class="p-2 bg-red-100 rounded-lg">
                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-600"/>
            </div>

            <div class="flex-1">

                <p class="text-sm font-semibold text-slate-800">
                    Multiple Failed Login Attempts
                </p>

                <p class="text-xs text-slate-500">
                    5 failed login attempts detected from IP 103.45.67.89
                </p>

            </div>

            <span class="text-xs text-red-600 font-semibold">
                High
            </span>

        </div>

    </div>

</div>


{{-- QUICK ACTIONS --}}
<div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

    <h3 class="text-lg font-semibold text-slate-800 mb-6">
        Quick Management
    </h3>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

        <a href="#" class="group flex flex-col items-center justify-center p-5
                          bg-slate-50 rounded-xl hover:bg-blue-50 transition">

            <x-heroicon-o-user-plus class="w-6 h-6 text-blue-600 mb-2 group-hover:scale-110 transition"/>

            <span class="text-xs font-medium text-slate-700">
                New User
            </span>

        </a>


        <a href="#" class="group flex flex-col items-center justify-center p-5
                          bg-slate-50 rounded-xl hover:bg-indigo-50 transition">

            <x-heroicon-o-plus-circle class="w-6 h-6 text-indigo-600 mb-2 group-hover:scale-110 transition"/>

            <span class="text-xs font-medium text-slate-700">
                Add Application
            </span>

        </a>


        <a href="#" class="group flex flex-col items-center justify-center p-5
                          bg-slate-50 rounded-xl hover:bg-slate-200 transition">

            <x-heroicon-o-document-magnifying-glass class="w-6 h-6 text-slate-600 mb-2 group-hover:scale-110 transition"/>

            <span class="text-xs font-medium text-slate-700">
                Audit Logs
            </span>

        </a>

    </div>

</div>

</div>


{{-- ================= ACTIVITY LOG ================= --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">

        <h3 class="text-base font-semibold text-slate-900">
            Recent Authentication Activity
        </h3>

        <a href="#"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">

            View All

        </a>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>

                <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">

                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Application</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">IP Address</th>
                    <th class="px-6 py-4 text-right">Time</th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100 text-sm">

@forelse($recentLogs ?? [] as $log)

<tr class="hover:bg-blue-50/40 transition">

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

        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
            Success
        </span>

        @else

        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
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

@empty

{{-- Example Row 1 --}}
<tr class="hover:bg-blue-50/40 transition">

    <td class="px-6 py-4 font-semibold text-slate-900">
        admin@lapisso.id
    </td>

    <td class="px-6 py-4 text-slate-600">
        Admin Dashboard
    </td>

    <td class="px-6 py-4 text-slate-600">
        Login
    </td>

    <td class="px-6 py-4">

        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
            Success
        </span>

    </td>

    <td class="px-6 py-4 text-slate-500">
        192.168.1.12
    </td>

    <td class="px-6 py-4 text-right text-slate-500">
        2 minutes ago
    </td>

</tr>


{{-- Example Row 2 --}}
<tr class="hover:bg-blue-50/40 transition">

    <td class="px-6 py-4 font-semibold text-slate-900">
        user@student.univ.id
    </td>

    <td class="px-6 py-4 text-slate-600">
        Academic Portal
    </td>

    <td class="px-6 py-4 text-slate-600">
        Login Attempt
    </td>

    <td class="px-6 py-4">

        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
            Failed
        </span>

    </td>

    <td class="px-6 py-4 text-slate-500">
        103.45.67.89
    </td>

    <td class="px-6 py-4 text-right text-slate-500">
        10 minutes ago
    </td>

</tr>

@endforelse

</tbody>

        </table>

    </div>

</div>

@endsection