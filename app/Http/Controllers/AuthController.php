<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Import model Role

class AuthController extends Controller
{
    // ... (metode index, authenticate, logout biarkan seperti sebelumnya)

    /**
     * Menampilkan halaman form login.
     */
    public function index()
    {
        return view('dashboard_page.auth.login');
    }

    /**
     * Menangani proses autentikasi.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman form registrasi.
     */
    public function register()
    {
        // Ambil roles, sekarang termasuk 'Admin P.Layang'
        $roles = Role::whereNotIn('name', ['Manager'])->get();
        return view('dashboard_page.auth.register', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        // 2. Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Berikan role kepada user
        $user->assignRole($request->role);

        // 4. Cek jika ini adalah request AJAX dari JavaScript
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Anda akan diarahkan ke halaman login.'
            ]);
        }

        // Fallback jika JavaScript non-aktif (tetap redirect seperti biasa)
        return redirect('/login')->with('success', 'Registrasi berhasil! Silahkan login.');
    }


    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}