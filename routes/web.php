<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// landing page
Route::get('/home', function () {
    return view('landing');
});

// auth
Route::get('/login', function () {
    return view('dashboard_page.auth.login');
});
Route::get('/register', function () {
    return view('dashboard_page.auth.register');
});
Route::get('/profil', function () {
    return view('dashboard_page.auth.profil');
});

// menu sidebar
Route::get('/dashboard', function () {
    return view('dashboard_page.menu.dashboard');
});
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

// daftar data material tiap spbe-bpt yang dipilih
Route::get('/material', function () {
    return view('dashboard_page.spbe-bpt_material.data_material');
});


// form keterangan pemusnahan upp material
Route::get('/keterangan-pemusnahan', function () {
    return view('dashboard_page.upp_material.keterangan_pemusnahan');
});

// pilihan 2 laman aktivitas transaksi dan upp
Route::get('/aktivitas-transaksi', function () {
    return view('dashboard_page.aktivitas_harian.data_transaksi');
});
Route::get('/aktivitas-upp', function () {
    return view('dashboard_page.aktivitas_harian.data_upp');
});
Route::get('/preview-upp', function () {
    return view('dashboard_page.aktivitas_harian.preview_upp');
});
