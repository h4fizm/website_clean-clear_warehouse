<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('landing');
});

Route::get('/login', function () {
    return view('dashboard_page.auth.login');
});

Route::get('/register', function () {
    return view('dashboard_page.auth.register');
});

Route::get('/dashboard', function () {
    return view('dashboard_page.menu.dashboard');
});

