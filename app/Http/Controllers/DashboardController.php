<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Cek jabatannya
        if ($user->jabatan === 'admin') {
            // Jika admin, arahkan ke folder admin, file dashboard.blade.php
            return view('admin.dashboard', compact('user'));
        } else {
            // Jika user biasa, arahkan ke folder user, file dashboard.blade.php
            return view('user.dashboard', compact('user'));
        }
    }
}