<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(): View
    {
        // Mengambil user dengan pagination, 10 data per halaman
        $users = User::latest()->paginate(10);

        // Kirim data users (yang sudah dalam format pagination) ke view
        return view('dashboard_page.menu.data_pengguna', compact('users'));
    }
}