<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PusatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UppMaterialController;
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

// Landing Page
// Route::get('/', function () {
//     return view('landing');
// });

// --- Autentikasi ---
// Login
Route::get('/', [AuthController::class, 'index'])->name('login');
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

    // --- API untuk data stok berdasarkan nama material, bulan, dan tahun ---
    Route::get('/api/stock-data', [DashboardController::class, 'getStockDataApi'])->name('api.stock.data');

    // --- API untuk MENYIMPAN kapasitas ---
    Route::post('/api/stock-capacity', [DashboardController::class, 'updateCapacityApi'])->name('api.capacity.update');

    // ALL MATERIAL EXPORT EXCEL
    Route::get('/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.exportExcel');

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

    // ===== TAMBAHKAN ROUTE DI BAWAH INI =====
    Route::get('/aktivitas/transaksi/export', [AktivitasHarianController::class, 'exportTransaksiExcel'])
        ->name('aktivitas.transaksi.export')
        ->middleware('can:manage aktivitas harian');

    // Route untuk halaman log transaksi
    Route::get('/aktivitas-transaksi', [AktivitasHarianController::class, 'index'])
        ->name('aktivitas.transaksi')
        ->middleware('can:manage aktivitas harian');

    // Route untuk memproses data transaksi (misalnya, dari POST request)
    Route::post('/aktivitas-transaksi/log', [AktivitasHarianController::class, 'logTransaksi'])
        ->name('aktivitas.transaksi.log')
        ->middleware('can:manage aktivitas harian');

    // Halaman data UPP
    Route::get('/upp-material', [UppMaterialController::class, 'index'])
        ->name('upp-material.index')
        ->middleware('can:manage data playang');

    // Halaman tambah UPP
    Route::get('/upp-material/tambah', [UppMaterialController::class, 'create'])
        ->name('upp-material.create')
        ->middleware('can:manage data playang');

    // Simpan pengajuan UPP
    Route::post('/upp-material/store', [UppMaterialController::class, 'store'])
        ->name('upp-material.store')
        ->middleware('can:manage data playang');

    Route::get('/upp-material/preview/{no_surat}', [UppMaterialController::class, 'preview'])
        ->name('upp-material.preview')
        ->where('no_surat', '.*');

    // Halaman edit UPP (memuat form dengan data yang sudah ada)
    Route::get('/upp-material/edit/{no_surat}', [UppMaterialController::class, 'edit'])
        ->name('upp-material.edit')
        ->where('no_surat', '.*')
        ->middleware('can:manage data playang');

    // Proses update UPP
    Route::put('/upp-material/update/{no_surat}', [UppMaterialController::class, 'update'])
        ->name('upp-material.update')
        ->where('no_surat', '.*')
        ->middleware('can:manage data playang');

    // Ambil data material untuk modal
    Route::get('/upp-material/afkir', [UppMaterialController::class, 'getMaterials'])
        ->name('upp-material.afkir')
        ->middleware('can:manage data playang');

    Route::post('/upp-material/change-status/{no_surat}', [App\Http\Controllers\UppMaterialController::class, 'changeStatus'])
        ->name('upp-material.change-status')
        ->where('no_surat', '.*')
        ->middleware('can:manage data playang');

    // BARIS BARU UNTUK EKSPOR EXCEL
    Route::get('/upp-material/export-excel', [UppMaterialController::class, 'exportExcel'])
        ->name('upp-material.export')
        ->middleware('can:manage data playang');

    // Route ini secara otomatis membuat route untuk index, create, store, edit, update, destroy
    Route::resource('/pengguna', UserController::class)->middleware('can:manage user');

});