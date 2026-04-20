<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    public function index()
    {
        $logs = LogActivity::with(['user', 'application'])
            ->latest()
            ->paginate(15);

        // UBAH BARIS INI: Sesuaikan dengan struktur folder baru (admin.log_activity)
        return view('admin.log_activity', compact('logs')); 
    }
}