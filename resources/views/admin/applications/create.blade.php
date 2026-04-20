<!-- resources\views\admin\applications\create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Aplikasi | LAPISSO')

@section('content')

<div class="mb-8">

    <a href="{{ route('applications.index') }}"
       class="text-sm text-blue-600 hover:underline flex items-center gap-2 mb-4">

        <x-heroicon-o-arrow-left class="w-4 h-4"/>

        Kembali ke Manajemen Aplikasi

    </a>

    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
        Tambah Aplikasi SSO
    </h1>

    <p class="text-slate-500 mt-1">
        Daftarkan aplikasi baru yang akan menggunakan layanan Single Sign-On.
    </p>

</div>



<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 max-w-3xl">

<form method="POST" action="{{ route('applications.store') }}">

@csrf

{{-- APPLICATION NAME --}}
<div class="mb-6">

    <label class="block text-sm font-semibold text-slate-700 mb-2">
        Nama Aplikasi
    </label>

    <input
        type="text"
        name="name"
        required
        placeholder="Contoh: Portal Akademik"
        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
    >

</div>



{{-- REDIRECT URI --}}
<div class="mb-6">

    <label class="block text-sm font-semibold text-slate-700 mb-2">
        Redirect URI
    </label>

    <input
        type="text"
        name="redirect_uri"
        required
        placeholder="https://example.com/callback"
        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
    >

</div>



{{-- STATUS --}}
<div class="mb-8">

    <label class="block text-sm font-semibold text-slate-700 mb-2">
        Status
    </label>

    <select
        name="status"
        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
    >

        <option value="active">Active</option>
        <option value="disabled">Disabled</option>

    </select>

</div>



{{-- BUTTON --}}
<div class="flex items-center justify-end gap-3">

    <a href="{{ route('applications.index') }}"
       class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200">

        Batal

    </a>

    <button
        type="submit"
        class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">

        Simpan Aplikasi

    </button>

</div>

</form>

</div>

@endsection