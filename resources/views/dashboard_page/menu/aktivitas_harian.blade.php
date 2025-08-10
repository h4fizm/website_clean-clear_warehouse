@extends('dashboard_page.main')
@section('title', 'Laporan Aktivitas Harian')
@section('content')

{{-- Welcome Section (Card Sesuai Gambar) --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="summary-title">
                    Laporan Aktivitas Harian
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Pilih jenis laporan yang ingin Anda lihat untuk memantau aktivitas harian.
                </p>
            </div>
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                    alt="Pertamina Patra Niaga Logo"
                    class="welcome-card-icon"
                    style="height: 60px;">
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                <div class="icon-container mb-3">
                    <span class="badge bg-gradient-success rounded-circle p-4">
                        <i class="fas fa-file-invoice fa-2x text-white"></i>
                    </span>
                </div>
                <h5 class="font-weight-bolder">Aktivitas Log Harian Transaksi</h5>
                <p class="text-secondary text-sm">Lihat laporan harian untuk semua transaksi yang tercatat.</p>
                <a href="/aktivitas-transaksi" class="btn bg-gradient-success mt-3">Pilih</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                <div class="icon-container mb-3">
                    <span class="badge bg-gradient-danger rounded-circle p-4">
                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                    </span>
                </div>
                <h5 class="font-weight-bolder">Aktivitas Log Harian UPP</h5>
                <p class="text-secondary text-sm">Lihat laporan harian untuk aktivitas pemusnahan material.</p>
                <a href="/aktivitas-upp" class="btn bg-gradient-danger mt-3">Pilih</a>
            </div>
        </div>
    </div>
</div>

@endsection