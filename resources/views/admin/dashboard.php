@extends('layouts.app')

@section('title', 'Admin Dashboard | LAPISSO')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="fw-bold mb-1">System Overview</h1>

        <p class="text-muted mb-0">
            Pantau lalu lintas autentikasi dan status sistem SSO
        </p>

    </div>

    <div class="text-end">

        <small class="text-uppercase text-muted fw-semibold">
            Session ID
        </small>

        <div class="bg-white border rounded px-3 py-1 font-monospace small">
            {{ session()->getId() }}
        </div>

    </div>

</div>



{{-- STATISTICS --}}
<div class="row g-4 mb-4">

    {{-- USERS --}}
    <div class="col-md-6 col-lg-3">

        <div class="card h-100">

            <div class="card-body d-flex justify-content-between">

                <div>

                    <p class="text-muted mb-1">Total Users</p>

                    <h3 class="fw-bold">
                        {{ $totalUsers ?? '1,420' }}
                    </h3>

                </div>

                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>

            </div>

        </div>

    </div>



    {{-- APPS --}}
    <div class="col-md-6 col-lg-3">

        <div class="card h-100">

            <div class="card-body d-flex justify-content-between">

                <div>

                    <p class="text-muted mb-1">SSO Applications</p>

                    <h3 class="fw-bold">
                        {{ $totalApps ?? '8' }}
                    </h3>

                </div>

                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-grid"></i>
                </div>

            </div>

        </div>

    </div>



    {{-- ACTIVE SESSIONS --}}
    <div class="col-md-6 col-lg-3">

        <div class="card h-100">

            <div class="card-body d-flex justify-content-between">

                <div>

                    <p class="text-muted mb-1">Active Sessions</p>

                    <h3 class="fw-bold">
                        {{ $activeSessions ?? '356' }}
                    </h3>

                </div>

                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>

            </div>

        </div>

    </div>



    {{-- FAILED LOGIN --}}
    <div class="col-md-6 col-lg-3">

        <div class="card h-100">

            <div class="card-body d-flex justify-content-between">

                <div>

                    <p class="text-muted mb-1">Failed Logins</p>

                    <h3 class="fw-bold text-danger">
                        {{ $failedLogins ?? '5' }}
                    </h3>

                </div>

                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-shield-exclamation"></i>
                </div>

            </div>

        </div>

    </div>

</div>



{{-- SYSTEM STATUS --}}
<div class="row g-4 mb-4">

    <div class="col-lg-6">

        <div class="card">

            <div class="card-body">

                <h5 class="fw-bold mb-3">

                    <i class="bi bi-server me-2 text-primary"></i>
                    SSO Server Status

                </h5>

                <div class="d-flex justify-content-between align-items-center">

                    <span>Keycloak Server</span>

                    <span class="badge bg-success">
                        Online
                    </span>

                </div>

            </div>

        </div>

    </div>



    <div class="col-lg-6">

        <div class="card">

            <div class="card-body">

                <h5 class="fw-bold mb-3">

                    <i class="bi bi-lightning-charge me-2 text-primary"></i>
                    Quick Actions

                </h5>

                <div class="d-flex gap-2 flex-wrap">

                    <button class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i>
                        Add User
                    </button>

                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-square me-1"></i>
                        Add Application
                    </button>

                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        View Logs
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ACTIVITY LOG --}}
<div class="card">

    <div class="card-header bg-white border-bottom d-flex justify-content-between">

        <h6 class="fw-bold mb-0">

            <i class="bi bi-clock-history me-2 text-primary"></i>
            Aktivitas Autentikasi Terbaru

        </h6>

        <a href="#" class="text-primary small">
            Lihat Semua
        </a>

    </div>



    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead>

                <tr>
                    <th>User</th>
                    <th>Application</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>IP</th>
                    <th class="text-end">Time</th>
                </tr>

            </thead>

            <tbody>

                @foreach($recentLogs ?? [] as $log)

                <tr>

                    <td class="fw-semibold">
                        {{ $log->user_email }}
                    </td>

                    <td>
                        {{ $log->application }}
                    </td>

                    <td>
                        {{ $log->action }}
                    </td>

                    <td>

                        @if($log->status == 'success')

                        <span class="badge bg-success">
                            Success
                        </span>

                        @else

                        <span class="badge bg-danger">
                            Failed
                        </span>

                        @endif

                    </td>

                    <td class="text-muted">
                        {{ $log->ip_address }}
                    </td>

                    <td class="text-end text-muted">
                        {{ $log->created_at->diffForHumans() }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection