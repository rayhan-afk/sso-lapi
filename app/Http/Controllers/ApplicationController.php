<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = collect();

        return view('admin.applications.index', compact('applications'));
    }

    public function create()
    {
        return view('admin.applications.create');
    }

    public function store(Request $request)
    {
        // nanti kita simpan ke database
        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil dibuat');
    }
}