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

// menu sidebar
Route::get('/dashboard', function () {
    return view('dashboard_page.menu.dashboard');
});
Route::get('/transaksi', function () {
    return view('dashboard_page.menu.data_transaksi');
});
Route::get('/material', function () {
    return view('dashboard_page.menu.data_material');
});
Route::get('/upp-material', function () {
    return view('dashboard_page.menu.data_upp-material');
});
Route::get('/laporan-grafik', function () {
    return view('dashboard_page.menu.data_laporan_grafik');
});

// daftar spbe-bpt
Route::get('/spbe-bpt', function () {
    return view('dashboard_page.sales_area.daftar_spbe-bpt');
});


// form keterangan pemusnahan upp material
Route::get('/keterangan-pemusnahan', function () {
    return view('dashboard_page.upp_material.keterangan_pemusnahan');
});