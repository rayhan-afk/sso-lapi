<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = collect();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // nanti kita integrasikan ke Keycloak
        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }
}