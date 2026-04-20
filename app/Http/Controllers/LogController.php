<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = collect();

        return view('admin.logs.index', compact('logs'));
    }
}