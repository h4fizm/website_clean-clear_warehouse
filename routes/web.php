<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan semua route untuk aplikasi web Anda.
|
*/

// ===================================================================
// ROUTE PUBLIK (Tidak Perlu Login)
// ===================================================================

Route::get('/', function () {
    return view('landing');
});

// --- Autentikasi ---
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

// --- Registrasi ---
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');


// ===================================================================
// ROUTE TERPROTEKSI (Wajib Login)
// ===================================================================

Route::middleware(['auth'])->group(function () {

    // --- Logout ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- Profil Pengguna ---
    Route::get('/profil', function () {
        return view('dashboard_page.auth.profil');
    });

    // --- Halaman Utama Setelah Login ---
    Route::get('/dashboard', function () {
        return view('dashboard_page.menu.dashboard');
    })->name('dashboard'); // Beri nama untuk redirect setelah login

    // --- Menu Sidebar & Fitur Utama ---
    Route::get('/pusat', function () {
        return view('dashboard_page.menu.data_pusat');
    });
    Route::get('/transaksi', function () {
        return view('dashboard_page.menu.data_transaksi');
    });
    Route::get('/tambah-spbe/bpt', function () {
        return view('dashboard_page.menu.tambah_spbe-bpt');
    });
    Route::get('/tambah-material', function () {
        return view('dashboard_page.menu.tambah_material');
    });
    Route::get('/upp-material', function () {
        return view('dashboard_page.menu.data_upp-material');
    });
    Route::get('/laporan-grafik', function () {
        return view('dashboard_page.menu.data_laporan_grafik');
    });
    Route::get('/aktivitas', function () {
        return view('dashboard_page.menu.aktivitas_harian');
    });
    Route::get('/pengguna', function () {
        return view('dashboard_page.menu.data_pengguna');
    });

    // --- Route Spesifik Lainnya ---
    Route::get('/material', function () {
        return view('dashboard_page.spbe-bpt_material.data_material');
    });
    Route::get('/keterangan-pemusnahan', function () {
        return view('dashboard_page.upp_material.keterangan_pemusnahan');
    });

    // --- Aktivitas Harian ---
    Route::get('/aktivitas-transaksi', function () {
        return view('dashboard_page.aktivitas_harian.data_transaksi');
    });
    Route::get('/aktivitas-upp', function () {
        return view('dashboard_page.aktivitas_harian.data_upp');
    });
    Route::get('/preview-upp', function () {
        return view('dashboard_page.aktivitas_harian.preview_upp');
    });

});