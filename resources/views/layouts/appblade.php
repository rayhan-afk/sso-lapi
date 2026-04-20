<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title','LAPISSO Portal')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <style>

        body{
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .avatar{
            width:36px;
            height:36px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
        }
/* SIDEBAR */

.sidebar{
    width:260px;
    background:white;
    border-right:1px solid #e5e7eb;
}

/* logo */

.sidebar-logo{
    padding:20px;
    border-bottom:1px solid #f1f5f9;
}

.logo-icon{
    width:36px;
    height:36px;
    border-radius:10px;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
}

.logo-text{
    font-weight:700;
    font-size:18px;
}


/* menu */

.sidebar-menu{
    padding:20px;
}

.menu-label{
    font-size:11px;
    text-transform:uppercase;
    font-weight:600;
    color:#94a3b8;
    margin-bottom:10px;
}


/* item */

.sidebar-item{
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border-radius:10px;
    text-decoration:none;
    color:#475569;
    font-weight:500;
    transition:0.2s;
}

.sidebar-item i{
    font-size:16px;
}


/* hover */

.sidebar-item:hover{
    background:#f1f5f9;
    color:#2563eb;
}


/* active */

.sidebar-item.active{
    background:#eff6ff;
    color:#2563eb;
    font-weight:600;
}


/* help */

.sidebar-help{
    padding:20px;
    border-top:1px solid #f1f5f9;
}

.help-button{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    color:#475569;
    padding:10px;
    border-radius:10px;
}

.help-button:hover{
    background:#f1f5f9;
}

/* ===== GLOBAL ===== */

body{
    background:#f1f5f9;
}

/* ===== CARD ===== */

.card{
    border:none;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.card:hover{
    transform:translateY(-2px);
    transition:0.2s;
}

/* ===== STAT ICON ===== */

.stat-icon{
    width:40px;
    height:40px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

/* ===== TABLE ===== */

.table{
    border-collapse:separate;
    border-spacing:0;
}

.table thead th{
    font-size:13px;
    text-transform:uppercase;
    color:#64748b;
}

.table tbody tr:hover{
    background:#f8fafc;
}

/* ===== HEADER SECTION ===== */

.page-header h1{
    font-size:24px;
}

.page-header small{
    font-size:12px;
}

/* ===== BUTTONS ===== */

.btn-primary{
    background:#2563eb;
    border:none;
}

.btn-primary:hover{
    background:#1d4ed8;
}

    </style>

</head>



<body class="d-flex vh-100 overflow-hidden">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')



    {{-- MAIN CONTENT --}}
    <main class="flex-grow-1 d-flex flex-column">

        {{-- HEADER --}}
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">

            {{-- SEARCH --}}
            <div style="width:350px">

                <div class="input-group">

                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>

                    <input type="text"
                           class="form-control border-start-0"
                           placeholder="Cari aplikasi atau log...">

                </div>

            </div>



            {{-- RIGHT HEADER --}}
            <div class="d-flex align-items-center gap-3">

                {{-- NOTIFICATION --}}
                <button class="btn btn-light position-relative">

                    <i class="bi bi-bell"></i>

                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        1
                    </span>

                </button>



                {{-- USER AVATAR --}}
                <div class="d-flex align-items-center gap-2">

                    <div class="avatar bg-primary text-white fw-semibold">

                        {{ Auth::check() ? strtoupper(substr(Auth::user()->nama,0,1)) : 'U' }}

                    </div>

                </div>

            </div>

        </header>



        {{-- PAGE CONTENT --}}
        <div class="flex-grow-1 overflow-auto p-4">

            <div class="container-fluid">

                @yield('content')

            </div>

        </div>

    </main>



    {{-- RIGHT SIDEBAR SESSION INFO --}}
    <aside class="border-start bg-white p-3 d-flex flex-column"
           style="width:280px">

        {{-- LOGOUT --}}
        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button class="btn btn-danger w-100 mb-3">

                <i class="bi bi-box-arrow-right me-1"></i>
                Logout SSO

            </button>

        </form>



        {{-- SESSION INFO --}}
        <h6 class="fw-bold mb-3">

            <i class="bi bi-shield-lock me-1"></i>
            Informasi Sesi

        </h6>



        <div class="border rounded p-3">

            <p class="mb-1 fw-semibold">

                <i class="bi bi-check-circle text-success me-1"></i>
                Sesi Aktif

            </p>

            <small class="text-muted">

                IP: {{ request()->ip() }}

            </small>

        </div>



        {{-- OPTIONAL INFO --}}
        <div class="mt-4">

            <small class="text-muted">

                Sistem autentikasi menggunakan Single Sign-On (SSO).
                Semua akses aplikasi menggunakan sesi login terpusat.

            </small>

        </div>

    </aside>



    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>