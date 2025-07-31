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
Route::get('/cabang', function () {
    return view('dashboard_page.menu.data_cabang');
});
Route::get('/spbe-bpt', function () {
    return view('dashboard_page.menu.data_spbe-bpt');
});
Route::get('/material', function () {
    return view('dashboard_page.menu.data_material');
});

// transaksi
Route::get('/cabang/spbe-bpt1', function () {
    return view('dashboard_page.cabang.daftar_spbe-bpt_pihak1');
});
Route::get('/cabang/spbe-bpt2', function () {
    return view('dashboard_page.cabang.daftar_spbe-bpt_pihak2');
});

