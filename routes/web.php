<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PusatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AktivitasHarianController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Laman Data P.Layang (Pusat)
    Route::get('/pusat', [PusatController::class, 'index'])
        ->name('pusat.index')
        ->middleware(['can:manage data playang']);

    // Route baru untuk export Excel
    Route::get('/pusat/export', [PusatController::class, 'exportExcel'])
        ->name('pusat.export')
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

    // Route untuk memproses transfer stok dari pusat ke facility
    Route::post('/pusat/transfer', [PusatController::class, 'transfer'])
        ->name('pusat.transfer')
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

    // Route Melihat List Material SPBE/BPT
    Route::get('/facilities/{facility}/materials', [MaterialController::class, 'index'])
        ->name('materials.index')
        ->middleware('can:manage transaksi');

    // Route untuk memproses update data material
    Route::patch('/materials/{item}', [MaterialController::class, 'update'])
        ->name('materials.update')
        ->middleware('can:manage transaksi');

    // Route untuk menghapus data material
    Route::delete('/materials/{item}', [MaterialController::class, 'destroy'])
        ->name('materials.destroy')
        ->middleware('can:manage transaksi');

    // Route untuk memproses transaksi material dari facility
    Route::post('/materials/transaction', [MaterialController::class, 'processTransaction'])
        ->name('materials.transaction')
        ->middleware('can:manage transaksi');

    // Route untuk halaman menu utama aktivitas
    Route::get('/aktivitas', [AktivitasHarianController::class, 'index'])
        ->name('aktivitas.index') // Menambahkan nama route
        ->middleware('can:manage aktivitas harian'); // Middleware di sini

    // ===== TAMBAHKAN ROUTE DI BAWAH INI =====
    Route::get('/aktivitas/transaksi/export', [AktivitasHarianController::class, 'exportTransaksiExcel'])
        ->name('aktivitas.transaksi.export')
        ->middleware('can:manage aktivitas harian');

    // Route untuk halaman log transaksi
    Route::get('/aktivitas-transaksi', [AktivitasHarianController::class, 'logTransaksi'])
        ->name('aktivitas.transaksi') // Menambahkan nama route
        ->middleware('can:manage aktivitas harian'); // Middleware di sini

    // Route ini secara otomatis membuat route untuk index, create, store, edit, update, destroy
    Route::resource('/pengguna', UserController::class)->middleware('can:manage user');

    // ini dibiarkan dulu
    Route::get('/upp-material', function () {
        return view('dashboard_page.menu.data_upp-material');
    });

    // --- Route Spesifik Lainnya ---
    Route::get('/material', function () {
        return view('dashboard_page.spbe-bpt_material.data_material');
    });
    Route::get('/keterangan-pemusnahan', function () {
        return view('dashboard_page.upp_material.keterangan_pemusnahan');
    });

    // --- Aktivitas UPP ---
    Route::get('/aktivitas-upp', function () {
        return view('dashboard_page.aktivitas_harian.data_upp');
    });
    Route::get('/preview-upp', function () {
        return view('dashboard_page.aktivitas_harian.preview_upp');
    });

});