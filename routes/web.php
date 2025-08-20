<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PusatController;
use App\Http\Controllers\TransactionController;

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
    // Mengarah ke method showProfile untuk menampilkan data
    Route::get('/profil', [AuthController::class, 'showProfile'])->name('profile.show');

    // Route untuk memproses update data profil
    Route::patch('/profil/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    // --- Halaman Utama Setelah Login ---
    Route::get('/dashboard', function () {
        return view('dashboard_page.menu.dashboard');
    })->name('dashboard'); // Beri nama untuk redirect setelah login

    // Laman Data P.Layang (Pusat)
    Route::get('/pusat', [PusatController::class, 'index'])
        ->name('pusat.index')
        ->middleware(['can:manage data playang']);

    // Laman untuk menampilkan form tambah material pusat
    Route::get('/pusat/create', [PusatController::class, 'create'])
        ->name('pusat.create')
        ->middleware(['can:manage data playang']);

    // Route untuk menyimpan data material baru dari pusat
    Route::post('/pusat', [PusatController::class, 'store'])
        ->name('pusat.store')
        ->middleware(['can:manage data playang']);

    // Route untuk mengambil data item spesifik (untuk modal edit)
    Route::get('/pusat/{item}/edit', [PusatController::class, 'edit'])
        ->name('pusat.edit')
        ->middleware(['auth', 'can:manage data playang']);

    // Route untuk memproses update data material
    Route::put('/pusat/{item}', [PusatController::class, 'update'])
        ->name('pusat.update')
        ->middleware(['auth', 'can:manage data playang']);

    // Route untuk menghapus data material
    Route::delete('/pusat/{item}', [PusatController::class, 'destroy'])
        ->name('pusat.destroy')
        ->middleware(['auth', 'can:manage data playang']);

    // --- Laman Transaksi SPBE/BPT ---
    Route::get('/transaksi', [TransactionController::class, 'index'])
        ->name('transaksi.index')
        ->middleware('can:manage transaksi');

    // Gunakan route ini untuk menampilkan halaman form tambah
    Route::get('/transaksi/tambah', [TransactionController::class, 'create'])
        ->name('transaksi.create')
        ->middleware('can:manage transaksi');

    // Route ini untuk memproses form saat disubmit
    Route::post('/transaksi', [TransactionController::class, 'store'])
        ->name('transaksi.store')
        ->middleware('can:manage transaksi');

    // Route untuk Update Facility (SPBE/BPT)
    Route::patch('/transaksi/{facility}', [TransactionController::class, 'update'])
        ->name('transaksi.update')
        ->middleware('can:manage transaksi');

    // Route untuk Delete Facility (SPBE/BPT)
    Route::delete('/transaksi/{facility}', [TransactionController::class, 'destroy'])
        ->name('transaksi.destroy')
        ->middleware('can:manage transaksi');


    // --- Menu Sidebar & Fitur Utama ---
    // ini dibiarkan dulu
    Route::get('/upp-material', function () {
        return view('dashboard_page.menu.data_upp-material');
    });
    Route::get('/laporan-grafik', function () {
        return view('dashboard_page.menu.data_laporan_grafik');
    });
    Route::get('/aktivitas', function () {
        return view('dashboard_page.menu.aktivitas_harian');
    });

    // Route ini secara otomatis membuat route untuk index, create, store, edit, update, destroy
    Route::resource('/pengguna', UserController::class)->middleware('can:manage user');

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