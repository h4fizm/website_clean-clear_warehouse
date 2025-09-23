@extends('dashboard_page.main')
@section('title', 'Laman Dashboard Utama')
@section('content')

{{-- 1. Welcome Card Dinamis --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative" style="background-color: white; color: #344767; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); overflow: hidden;">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                    alt="Branch Icon"
                    style="height: 60px; width: auto; opacity: 0.9;">
            </div>
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="welcome-title">
                    Selamat Datang, {{ $user->name }}
                </h4>
                <p class="mb-2 opacity-8" id="welcome-text">
                    Lihat dan kelola data stok material serta riwayat transaksi untuk tiap Region/SA.
                </p>
                <span class="badge bg-primary text-white text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8rem;">
                    {{ $roleName }}
                </span>
            </div>
        </div>
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px; opacity: 0.2; pointer-events: none;"></div>
    </div>
</div>

{{-- dashboard.blade.php --}}

{{-- Statistik Cards Dinamis --}}
<div class="row g-3">
    {{-- ✅ PERBAIKAN: Gunakan perulangan sederhana karena data sudah terurut dari controller --}}
    @foreach ($cards as $card)
        <div class="col-12 col-sm-6 col-md-4 col-lg">
            <a href="{{ $card['link'] }}" class="card h-100" style="text-decoration: none; color: inherit;">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs text-uppercase font-weight-bold mb-1 text-wrap" style="min-height: 28px;">
                                    {{ $card['title'] }}
                                </p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{-- Cek judul untuk menghilangkan kata 'Transaksi' pada kartu 'Transaksi Sales' --}}
                                    @if ($card['title'] === 'Transaksi Sales')
                                        {{ str_replace(' Transaksi', '', $card['value']) }}
                                    @else
                                        {{ $card['value'] }}
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end d-flex align-items-center justify-content-end">
                            <div class="icon icon-shape bg-gradient-{{ $card['bg'] }} shadow-{{ $card['bg'] }} text-center rounded-circle">
                                <i class="{{ $card['icon'] }} text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- 2. Tabel Data Material dari Controller --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <form action="{{ route('dashboard') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Data Material - Regional Sumbagsel</h6>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search_material" class="form-control" placeholder="Cari material..." value="{{ request('search_material') }}">
                            <button type="submit" class="btn btn-primary mb-0"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 40px;">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</p></td>
                                    <td class="w-30">
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary shadow-secondary text-center rounded">
                                                <i class="fas fa-box text-white opacity-10"></i>
                                            </div>
                                            <div class="ms-1">
                                                <h6 class="text-sm mb-0">{{ $item->nama_material }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><div class="text-center"><h6 class="text-sm mb-0">{{ $item->kode_material }}</h6></div></td>
                                    <td class="text-center">
                                        <h6 class="text-sm mb-0">{{ number_format($item->total_stok_akhir) }} pcs</h6>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data material ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION KUSTOM DITERAPKAN DI SINI --}}
                <div class="mt-2 d-flex justify-content-center">
                    @if ($items->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $items->lastPage();
                                $current = $items->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->url(1) }}">&laquo;</a>
                            </li>
                            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->previousPageUrl() }}">&lsaquo;</a>
                            </li>
                            @php $wasGap = false; @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    @if (!$wasGap)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor
                            <li class="page-item {{ $items->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $items->nextPageUrl() }}">&rsaquo;</a>
                            </li>
                            <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->url($total) }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Stok Material --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                {{-- Baris 1: Judul & Tombol Export --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">DAFTAR STOK MATERIAL SELURUH REGIONAL</h6>
                    <div class="col-12 col-md-auto">
                        <span id="openExportMaterialModalBtn" class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center"
                                style="cursor: pointer; font-size: 0.875rem; font-weight: bold; white-space: nowrap;">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </span>
                    </div>
                </div>

                {{-- Baris 2: Judul Utama --}}
                <p class="text-center text-dark mb-2 fw-bold fs-5" id="stock-title">Memuat data...</p>

                {{-- Baris 3: Filter Bulan/Tahun (kiri) & Search Bar (kanan) --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    {{-- ✅ PERBAIKAN: Seluruh div filter bulan & tahun DIHAPUS --}}
                    
                    {{-- Search Bar --}}
                    <div class="position-relative">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="search-stock-material" class="form-control" placeholder="Cari Nama Material..." aria-label="Search Material">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <div id="material-suggestions" class="list-group position-absolute"
                                style="width: 300px; top: 100%; z-index: 1000; display: none;"></div>
                    </div>
                </div>
            </div>

            <div class="card-body p-2" style="padding-top: 0 !important;">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="table-stock-material-custom">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 ps-2" style="width: 25%;">Material</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Gudang</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Baru</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Baik</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Rusak</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Afkir</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Layak Edar (Baru+Baik)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- ✅ TAMBAHAN: Form Kapasitas --}}
                <div class="mt-4 px-3 py-2 bg-light rounded-lg d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <h6 class="text-sm font-weight-bold me-2 mb-0">Kapasitas:</h6>
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="number" id="material-capacity-input" class="form-control" placeholder="Masukkan kapasitas" min="0" value="{{ $initialStockData['capacity'] ?? 0 }}">
                        </div>
                        <h6 class="text-sm font-weight-bold ms-2 mb-0" id="capacity-value">
                            / {{ number_format($initialStockData['capacity'] ?? 0) }} pcs
                        </h6>
                    </div>
                    <button class="btn btn-sm btn-info mb-0" id="save-capacity-btn">
                        <i class="fas fa-save me-1"></i> Simpan Kapasitas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export Excel --}}
<div class="modal fade" id="exportExcelMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exportExcelMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelMaterialModalLabel">Export Data Stok</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pilih rentang tanggal untuk data yang akan diexport.</p>
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="exportStartDateMaterial" class="form-label">Tanggal Mulai:</label>
                        <input type="date" class="form-control" id="exportStartDateMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="exportEndDateMaterial" class="form-label">Tanggal Selesai:</label>
                        <input type="date" class="form-control" id="exportEndDateMaterial" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn bg-gradient-success" id="confirmExportMaterialBtn">Export</button>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Data UPP Material --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="me-md-auto mb-2 mb-md-0">
                    <h4 class="mb-0">Tabel Data UPP Material</h4>
                    <p class="mt-3 text-xs font-italic text-secondary">
                        Tabel ini menampilkan data pengajuan UPP material.
                    </p>
                </div>
            </div>
            
            {{-- Form Pencarian dan Filter --}}
            <div class="px-4 py-2">
                <form method="GET" action="{{ route('dashboard') }}">
                    <div class="row mb-3 align-items-end">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search_upp" id="searchInput" 
                                    class="form-control" 
                                    placeholder="Cari No. Surat..." 
                                    value="{{ request('search_upp') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-8 d-flex flex-wrap justify-content-md-end">
                            <div class="d-flex align-items-center me-2">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" name="start_date_upp" id="startDateUpp" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ request('start_date_upp') }}">
                            </div>
                            <div class="d-flex align-items-center me-2">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" name="end_date_upp" id="endDateUpp" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ request('end_date_upp') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-3" style="margin-top: 15px;">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Tabel utama --}}
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Surat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahapan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Buat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upps as $upp)
                                <tr>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($upps->currentPage() - 1) * $upps->perPage() }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p class="mb-0 text-sm font-weight-bolder text-primary">{{ $upp->no_surat_persetujuan }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">{{ $upp->tahapan }}</p>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusText = strtolower($upp->status) === 'done' ? 'Done' : 'Proses';
                                            $statusColor = strtolower($upp->status) === 'done' ? 'bg-gradient-success' : 'bg-gradient-warning';
                                        @endphp
                                        <span class="badge {{ $statusColor }} text-white text-xs font-weight-bold">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs text-secondary font-weight-bold mb-0">
                                            {{ \Carbon\Carbon::parse($upp->tgl_buat)->translatedFormat('l, d F Y') }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs text-secondary font-weight-bold mb-0">
                                            {{ \Carbon\Carbon::parse($upp->tgl_update)->translatedFormat('l, d F Y') }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Data Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION YANG DIPERBAIKI --}}
                @if ($upps->hasPages())
                <div class="mt-4 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $upps->lastPage();
                                $current = $upps->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $upps->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url(1) }}">&laquo;</a>
                            </li>
                            <li class="page-item {{ $upps->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->previousPageUrl() }}">&lsaquo;</a>
                            </li>
                            @php $wasGap = false; @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    @if (!$wasGap)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor
                            <li class="page-item {{ $upps->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $upps->nextPageUrl() }}">&rsaquo;</a>
                            </li>
                            <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url($total) }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 🔹 Data awal dari backend
        const allMaterialNames = @json($materialList);
        const initialStockData = @json($initialStockData);
        const defaultMaterialName = @json($defaultMaterialName);

        // 🔹 Elemen DOM utama
        const stockTableBody = document.querySelector('#table-stock-material-custom tbody');
        const stockTable = document.getElementById('table-stock-material-custom');
        const stockSearchInput = document.getElementById('search-stock-material');
        const stockTitle = document.getElementById('stock-title');
        const materialSuggestionsContainer = document.getElementById('material-suggestions');
        
        // Elemen untuk Kapasitas
        const capacityInput = document.getElementById('material-capacity-input');
        const saveCapacityBtn = document.getElementById('save-capacity-btn');
        const capacityValueSpan = document.getElementById('capacity-value');

        // Modal Export Excel untuk Material
        const openExportMaterialModalBtn = document.getElementById('openExportMaterialModalBtn');
        const exportExcelMaterialModalEl = document.getElementById('exportExcelMaterialModal');
        const confirmExportMaterialBtn = document.getElementById('confirmExportMaterialBtn');
        const exportExcelMaterialModal = new bootstrap.Modal(exportExcelMaterialModalEl);

        // 📌 Buka modal export material
        openExportMaterialModalBtn.addEventListener('click', function() {
            exportExcelMaterialModal.show();
        });

        // 📌 Jalankan export Excel material
        confirmExportMaterialBtn.addEventListener('click', function() {
            const startDate = document.getElementById('exportStartDateMaterial').value;
            const endDate = document.getElementById('exportEndDateMaterial').value;

            if (!startDate || !endDate) {
                Swal.fire('Peringatan!', 'Silakan pilih rentang tanggal terlebih dahulu.', 'warning');
                return;
            }

            window.location.href = `/export-excel?start_date=${startDate}&end_date=${endDate}`;
            exportExcelMaterialModal.hide();
        });

        // 📌 Format angka dengan pemisah ribuan
        function formatNumber(value) {
            return (value ?? 0).toLocaleString('id-ID');
        }

        // 📌 Konversi nomor bulan → nama bulan
        function getMonthName(month) {
            const date = new Date(null, month - 1);
            return date.toLocaleString('id-ID', { month: 'long' });
        }

        // 📌 Render isi tabel stok material
        function renderStockTable(data) {
            stockTableBody.innerHTML = '';
            
            // Hapus footer lama jika ada
            const oldFooter = stockTable.querySelector('tfoot');
            if (oldFooter) {
                oldFooter.remove();
            }

            const materialName = data?.stock?.[0]?.material_name;
            const today = new Date();
            const currentMonth = today.getMonth() + 1;
            const currentYear = today.getFullYear();
            const bulanNama = getMonthName(currentMonth);

            // Menyesuaikan judul tabel
            if (materialName) {
                stockTitle.innerText = `Stok ${materialName} - ${bulanNama} ${currentYear}`;
                stockSearchInput.value = materialName;
            } else {
                stockTitle.innerText = `Stok Material Saat Ini - ${bulanNama} ${currentYear}`;
            }

            // Update nilai input kapasitas
            capacityInput.value = data?.capacity ?? 0;
            capacityValueSpan.innerText = `/ ${formatNumber(data?.capacity ?? 0)} pcs`;

            // Render data stok dan hitung total
            if (data && data.stock && data.stock.length > 0) {
                const stockData = data.stock;
                let firstRow = true;
                let totalBaru = 0;
                let totalBaik = 0;
                let totalRusak = 0;
                let totalAfkir = 0;
                let totalLayakEdar = 0;

                stockData.forEach((item) => {
                    const rowHtml = `
                        <tr>
                            ${firstRow ? `<td class="ps-2 text-wrap align-middle" rowspan="${stockData.length}">
                                <h6 class="text-sm font-weight-bold mb-0">${item.material_name}</h6>
                            </td>` : ''}
                            <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.gudang}</span></td>
                            <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${formatNumber(item.baru)}</span></td>
                            <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${formatNumber(item.baik)}</span></td>
                            <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${formatNumber(item.rusak)}</span></td>
                            <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${formatNumber(item.afkir)}</span></td>
                            <td class="text-secondary text-center text-xs"><h6 class="text-sm font-weight-bolder mb-0">${formatNumber(item.layak_edar)}</h6></td>
                        </tr>
                    `;
                    stockTableBody.insertAdjacentHTML('beforeend', rowHtml);
                    firstRow = false;

                    // Akumulasi total
                    totalBaru += item.baru;
                    totalBaik += item.baik;
                    totalRusak += item.rusak;
                    totalAfkir += item.afkir;
                    totalLayakEdar += item.layak_edar;
                });
                
                // Tambahkan baris total (footer)
                // ✅ PERBAIKAN: Menambahkan kelas border-0 untuk menghilangkan garis
                const footerHtml = `
                    <tfoot class="bg-gray-200 text-dark fw-bold border-0">
                        <tr>
                            <td colspan="2" class="text-start ps-2 text-sm">TOTAL</td>
                            <td class="text-center text-sm">${formatNumber(totalBaru)}</td>
                            <td class="text-center text-sm">${formatNumber(totalBaik)}</td>
                            <td class="text-center text-sm">${formatNumber(totalRusak)}</td>
                            <td class="text-center text-sm">${formatNumber(totalAfkir)}</td>
                            <td class="text-center text-sm text-primary">${formatNumber(totalLayakEdar)}</td>
                        </tr>
                    </tfoot>
                `;
                stockTable.insertAdjacentHTML('beforeend', footerHtml);

            } else {
                stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Pilih atau cari material untuk menampilkan data.</td></tr>';
            }
        }

        // 📌 Ambil data stok berdasarkan material saja (tanpa bulan & tahun)
        async function fetchStockData(materialName) {
            stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>';
            try {
                const response = await fetch(`/api/stock-data?material_base_name=${encodeURIComponent(materialName)}`);
                if (!response.ok) throw new Error('Gagal mengambil data.');
                const data = await response.json();
                renderStockTable(data);
            } catch (error) {
                console.error('Fetch error:', error);
                stockTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>`;
                stockTitle.innerText = `Data Error`;
            }
        }

        // 📌 Simpan kapasitas material
        saveCapacityBtn.addEventListener('click', async function() {
            const materialName = stockSearchInput.value;
            const capacity = capacityInput.value;
            const month = (new Date()).getMonth() + 1;
            const year = (new Date()).getFullYear();

            if (!materialName) {
                Swal.fire('Peringatan!', 'Silakan pilih material terlebih dahulu.', 'warning');
                return;
            }

            try {
                const response = await fetch('/api/stock-capacity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        material_base_name: materialName,
                        capacity: capacity,
                        month: month,
                        year: year,
                    })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    capacityValueSpan.innerText = `/ ${formatNumber(capacity)} pcs`;
                } else {
                    let errorMessage = 'Gagal menyimpan kapasitas.';
                    if (data.errors) {
                        const errors = Object.values(data.errors).flat();
                        errorMessage += '<br>' + errors.join('<br>');
                    }
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            } catch (error) {
                console.error('Save capacity error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });

        // 📌 Tampilkan suggestion nama material di search box
        function showSuggestions(searchTerm) {
            const filteredNames = allMaterialNames.filter(name =>
                name.toLowerCase().includes(searchTerm.toLowerCase())
            );
            materialSuggestionsContainer.innerHTML = '';

            if (searchTerm && filteredNames.length > 0) {
                materialSuggestionsContainer.style.display = 'block';
                filteredNames.forEach(name => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.textContent = name;
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        fetchStockData(name);
                        materialSuggestionsContainer.style.display = 'none';
                    });
                    materialSuggestionsContainer.appendChild(item);
                });
            } else {
                materialSuggestionsContainer.style.display = 'none';
            }
        }

        // 📌 Tutup suggestion kalau klik di luar
        document.addEventListener('click', (e) => {
            if (!stockSearchInput.contains(e.target) && !materialSuggestionsContainer.contains(e.target)) {
                materialSuggestionsContainer.style.display = 'none';
            }
        });
        
        // 📌 Event input search
        stockSearchInput.addEventListener('keyup', function() { 
            showSuggestions(this.value); 
        });

        // 📌 Inisialisasi awal
        if (defaultMaterialName && initialStockData.stock) {
            renderStockTable(initialStockData);
        } else {
            const today = new Date();
            const currentMonth = today.getMonth() + 1;
            const currentYear = today.getFullYear();
            const bulanNama = getMonthName(currentMonth);

            stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Belum ada material yang dapat ditampilkan.</td></tr>';
            stockTitle.innerText = `Stok Material Saat Ini - ${bulanNama} ${currentYear}`;
        }
    });
</script>
@endpush