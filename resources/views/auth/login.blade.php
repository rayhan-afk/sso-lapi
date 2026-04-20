<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>LAPISSO Portal</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Inter', sans-serif; }
</style>

</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-5xl mx-auto grid md:grid-cols-2 gap-10 px-6">

    {{-- LEFT: BRANDING --}}
    <div class="flex flex-col justify-center">

        <h1 class="text-4xl font-bold text-slate-900 tracking-tight leading-tight">
            LAPISSO Portal
        </h1>

        <p class="mt-4 text-slate-500 text-sm leading-relaxed max-w-md">
            Centralized authentication system to securely access all integrated applications within one platform.
        </p>

        {{-- subtle feature --}}
        <div class="mt-8 space-y-3">

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <x-heroicon-o-shield-check class="w-5 h-5 text-blue-600"/>
                Secure authentication system
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <x-heroicon-o-bolt class="w-5 h-5 text-sky-500"/>
                Fast and seamless access
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <x-heroicon-o-circle-stack class="w-5 h-5 text-cyan-500"/>
                Integrated application ecosystem
            </div>

        </div>

    </div>


    {{-- RIGHT: LOGIN CARD --}}
    <div>

        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">

            {{-- HEADER --}}
            <h2 class="text-xl font-semibold text-slate-900 tracking-tight">
                Sign in to your account
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Continue using your institutional account
            </p>


            {{-- BUTTON --}}
            <a href="/auth/redirect"
               class="mt-8 w-full flex items-center justify-center gap-3
                      bg-gradient-to-r from-blue-600 via-sky-500 to-cyan-500
                      hover:opacity-90
                      text-white font-semibold text-sm
                      py-3 rounded-xl transition shadow-sm">

                <x-heroicon-o-arrow-right class="w-4 h-4"/>

                Continue with SSO

            </a>


            {{-- FOOTNOTE --}}
            <div class="mt-6 flex items-start gap-2 text-xs text-slate-400">

                <x-heroicon-o-lock-closed class="w-4 h-4 mt-0.5"/>

                <p>
                    Authentication is securely handled via centralized identity provider.
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>