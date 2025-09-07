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
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px; opacity: 0.2; pointer-events: none;"></div>
    </div>
</div>

{{-- Statistik Cards Dinamis --}}
<div class="row g-3">
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
                                <h5 class="font-weight-bolder mb-0">{{ $card['value'] }}</h5>
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
                                        <h6 class="text-sm mb-0">{{ number_format($item->total_stok_awal) }} pcs</h6>
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

{{-- ✅ Tabel Stok Material --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                {{-- Baris 1: Judul & Tombol Export --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">STOCK MATERIAL REGION</h6>
                    <div class="col-12 col-md-auto">
                        <span id="openExportModalBtn" class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center"
                              style="cursor: pointer; font-size: 0.875rem; font-weight: bold; white-space: nowrap;">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </span>
                    </div>
                </div>

                {{-- Baris 2: Judul Utama --}}
                <p class="text-center text-dark mb-2 fw-bold fs-5" id="stock-title">Memuat data...</p>

                {{-- Baris 3: Filter Bulan/Tahun (kiri) & Search Bar (kanan) --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    
                    {{-- Pilihan Bulan & Tahun --}}
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <select id="month-select" class="form-select form-select-sm me-2" style="width: auto;">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>

                        <select id="year-select" class="form-select form-select-sm" style="width: auto;">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

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
            </div>
        </div>
    </div>
</div>

{{-- [DIKEMBALIKAN] Modal untuk Export Excel --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Data ke Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-sm text-secondary">Pilih rentang tanggal untuk data yang ingin Anda export.</p>
                <div class="mb-3">
                    <label for="exportStartDate" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="exportStartDate">
                </div>
                <div class="mb-3">
                    <label for="exportEndDate" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="exportEndDate">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmExportBtn">
                    <i class="fas fa-file-excel me-2"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Tabel UPP Material (Baru) --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">TABEL UPP MATERIAL</h6>
                    {{-- Search Bar --}}
                    <div class="input-group input-group-sm ms-auto" style="width: 250px;">
                        <input type="text" id="uppSearchInput" class="form-control" placeholder="Cari No.Surat...">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2" style="padding-top: 0 !important;">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-upp-material">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">No.</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 ps-2">No. Surat</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 ps-2">Tahapan</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Tanggal Buat</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Tanggal Update Terakhir</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-upp" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>
                <div class="mt-4 mb-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0" id="pagination-upp-material">
                            {{-- Pagination links will be rendered here --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Detail UPP Material <span id="modal-upp-surat"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary text-xxs font-weight-bolder">Daftar Material:</h6>
                    <div class="table-responsive rounded shadow-sm">
                        <table class="table table-bordered table-striped align-items-center mb-0" style="min-width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stok Saat Ini</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stok Diambil</th>
                                </tr>
                            </thead>
                            <tbody id="material-list-table">
                                {{-- Material details will be rendered here --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan:</h6>
                    <p id="modal-keterangan" class="form-control-plaintext text-sm text-muted p-2 border rounded" style="background-color: #f8f9fa;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="lakukan-pemusnahan-btn">
                    <i class="fas fa-times me-2"></i> Lakukan Pemusnahan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- custom js --}}
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
        const stockSearchInput = document.getElementById('search-stock-material');
        const stockTitle = document.getElementById('stock-title');
        const materialSuggestionsContainer = document.getElementById('material-suggestions');

        // 🔹 Dropdown bulan & tahun
        const monthSelect = document.getElementById('month-select');
        const yearSelect = document.getElementById('year-select');

        // 🔹 Modal Export Excel
        const openExportModalBtn = document.getElementById('openExportModalBtn');
        const exportExcelModalEl = document.getElementById('exportExcelModal');
        const confirmExportBtn = document.getElementById('confirmExportBtn');
        const exportExcelModal = new bootstrap.Modal(exportExcelModalEl);

        // 📌 Buka modal export
        openExportModalBtn.addEventListener('click', function() {
            exportExcelModal.show();
        });

        // 📌 Jalankan export Excel (fungsi export backend sudah berjalan)
        confirmExportBtn.addEventListener('click', function() {
            const startDate = document.getElementById('exportStartDate').value;
            const endDate = document.getElementById('exportEndDate').value;

            if (!startDate || !endDate) {
                Swal.fire('Peringatan!', 'Silakan pilih rentang tanggal terlebih dahulu.', 'warning');
                return;
            }

            window.location.href = `/export-excel?start_date=${startDate}&end_date=${endDate}`;
        });

        // 📌 Format angka dengan pemisah ribuan
        function formatNumber(value) {
            return (value ?? 0).toLocaleString('id-ID');
        }

        // 📌 Konversi nomor bulan → nama bulan
        function getMonthName(month) {
            return new Date(2000, month - 1, 1).toLocaleString('id-ID', { month: 'long' });
        }

        // 📌 Render isi tabel stok material
        function renderStockTable(data) {
            stockTableBody.innerHTML = '';
            const materialName = data?.stock?.[0]?.material_name;
            const month = monthSelect.value;
            const year = yearSelect.value;
            const bulanNama = getMonthName(parseInt(month));

            // Set judul tabel
            if (materialName) {
                stockTitle.innerText = `Stok ${materialName} - ${bulanNama} ${year}`;
                stockSearchInput.value = materialName;
            } else {
                stockTitle.innerText = `Stok ${bulanNama} ${year}`;
            }
            
            // Render data stok
            if (data && data.stock && data.stock.length > 0) {
                const stockData = data.stock;
                stockData.forEach((item, index) => {
                    const rowHtml = `
                        <tr>
                            ${index === 0 ? `<td class="ps-2 text-wrap align-middle" rowspan="${stockData.length}">
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
                });
            } else {
                stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Pilih atau cari material untuk menampilkan data.</td></tr>';
            }

            // Render kapasitas material
            if (materialName) {
                const capacity = data.capacity;
                const capacityDisplay = (capacity === null || capacity === undefined) ? '-' : capacity.toLocaleString('id-ID');
                const capacityRowHtml = `
                    <tr class="bg-gray-200">
                        <td colspan="2" class="p-2 align-middle">
                            <p class="text-sm font-weight-bold mb-0">Kapasitas Daya Tampung ${materialName} :</p>
                        </td>
                        <td colspan="5" class="p-2 text-end">
                            <form id="capacity-form" class="d-flex align-items-center justify-content-end" onsubmit="return false;">
                                <input type="text" id="capacity-input" class="form-control form-control-sm me-2 text-end" value="${capacityDisplay}" style="width: 150px;" disabled>
                                <button type="button" id="edit-capacity-btn" class="btn btn-sm btn-info me-2 text-white"><i class="fas fa-edit"></i> Edit</button>
                                <button type="submit" id="submit-capacity-btn" class="btn btn-sm btn-primary" style="display: none;"><i class="fas fa-save"></i> Submit</button>
                            </form>
                        </td>
                    </tr>
                `;
                stockTableBody.insertAdjacentHTML('beforeend', capacityRowHtml);
                setupCapacityFormEvents(materialName);
            }
        }

        // 📌 Ambil data stok berdasarkan material + bulan + tahun
        async function fetchStockData(materialName) {
            stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>';
            try {
                const month = monthSelect.value;
                const year = yearSelect.value;

                const response = await fetch(`/api/stock-data/${encodeURIComponent(materialName)}?month=${month}&year=${year}`);
                if (!response.ok) throw new Error('Gagal mengambil data.');
                const data = await response.json();

                renderStockTable(data);
            } catch (error) {
                console.error('Fetch error:', error);
                stockTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${error.message}</td></tr>`;
            }
        }

        // 📌 Event form kapasitas (edit & submit)
        function setupCapacityFormEvents(materialName) {
            const capacityInput = document.getElementById('capacity-input');
            const editCapacityBtn = document.getElementById('edit-capacity-btn');
            const submitCapacityBtn = document.getElementById('submit-capacity-btn');
            
            editCapacityBtn.addEventListener('click', function() {
                capacityInput.disabled = false;
                if (capacityInput.value === '-') {
                    capacityInput.value = '';
                }
                capacityInput.focus();
                editCapacityBtn.style.display = 'none';
                submitCapacityBtn.style.display = 'inline-block';
            });

            submitCapacityBtn.addEventListener('click', async function() {
                submitCapacityBtn.disabled = true;
                submitCapacityBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    Swal.fire('Error Kritis!', 'CSRF Token tidak ditemukan.', 'error');
                    submitCapacityBtn.disabled = false;
                    submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
                    return;
                }
                const rawValue = capacityInput.value.replace(/\./g, '');
                let newCapacity = (rawValue.trim() === '-' || rawValue.trim() === '') ? 0 : parseInt(rawValue, 10);
                if (isNaN(newCapacity) || newCapacity < 0) {
                    Swal.fire('Error!', 'Kapasitas harus berupa angka positif atau tanda "-".', 'error');
                    submitCapacityBtn.disabled = false;
                    submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
                    return;
                }
                try {
                    const response = await fetch('/api/stock-capacity', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        },
                        body: JSON.stringify({ material_name: materialName, capacity: newCapacity })
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal memperbarui kapasitas.');
                    Swal.fire('Berhasil!', result.message, 'success');
                    fetchStockData(materialName);
                } catch (error) {
                    console.error('Submit error:', error);
                    Swal.fire('Error!', error.message, 'error');
                } finally {
                    submitCapacityBtn.disabled = false;
                    submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
                }
            });
        }

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
        stockSearchInput.addEventListener('keyup', function() { showSuggestions(this.value); });

        // 📌 Event filter bulan & tahun
        monthSelect.addEventListener('change', function () {
            if (stockSearchInput.value) {
                fetchStockData(stockSearchInput.value);
            }
        });

        yearSelect.addEventListener('change', function () {
            if (stockSearchInput.value) {
                fetchStockData(stockSearchInput.value);
            }
        });

        // 📌 Inisialisasi awal
        if (defaultMaterialName && initialStockData.stock) {
            renderStockTable(initialStockData);
        } else {
            const bulanNama = getMonthName(parseInt(monthSelect.value));
            stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Belum ada material yang dapat ditampilkan.</td></tr>';
            stockTitle.innerText = `Stok ${bulanNama} ${yearSelect.value}`;
        }
    });
</script>
{{-- UPP Material JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Data Dummy Utama dengan Detail Material ---
        const dataDummy = [
            { id: 1, tgl_buat: '2025-08-01', no_surat: 'UPP-202508-001', tahapan: 'Pengajuan', status: 'Proses', tgl_update: '2025-08-05', keterangan: 'Usulan pemusnahan material Gas LPG 3 Kg dan Bright Gas 12 Kg.', materials: [{ nama: 'Gas LPG 3 Kg', kode: 'LPG001', stok_saat_ini: 150, stok_diambil: 50 }, { nama: 'Bright Gas 12 Kg', kode: 'BRG002', stok_saat_ini: 80, stok_diambil: 30 }, { nama: 'Gas LPG 12 Kg', kode: 'LPG003', stok_saat_ini: 65, stok_diambil: 15 }] },
            { id: 2, tgl_buat: '2025-07-25', no_surat: 'UPP-202507-005', tahapan: 'Verifikasi', status: 'Proses', tgl_update: '2025-07-28', keterangan: 'Menunggu persetujuan tim verifikasi untuk pemusnahan Pelumas Fastron.', materials: [{ nama: 'Pelumas Fastron Gold', kode: 'PFS001', stok_saat_ini: 250, stok_diambil: 100 }, { nama: 'Pelumas Fastron Diesel', kode: 'PFS002', stok_saat_ini: 180, stok_diambil: 50 }, { nama: 'Pelumas Fastron Eco Green', kode: 'PFS003', stok_saat_ini: 210, stok_diambil: 70 }] },
            { id: 3, tgl_buat: '2025-07-15', no_surat: 'UPP-202507-003', tahapan: 'Pemusnahan', status: 'Done', tgl_update: '2025-07-18', keterangan: 'Pemusnahan Aspal Curah telah selesai dilaksanakan.', materials: [{ nama: 'Aspal Curah', kode: 'ASP001', stok_saat_ini: 0, stok_diambil: 500 }] },
            { id: 4, tgl_buat: '2025-07-10', no_surat: 'UPP-202507-002', tahapan: 'Pengajuan', status: 'Proses', tgl_update: '2025-07-11', keterangan: 'Usulan pemusnahan Avtur dan Pertalite.', materials: [{ nama: 'Avtur', kode: 'AVT001', stok_saat_ini: 5000, stok_diambil: 1000 }, { nama: 'Pertalite', kode: 'PRT001', stok_saat_ini: 8000, stok_diambil: 2500 }] },
            { id: 5, tgl_buat: '2025-06-20', no_surat: 'UPP-202506-001', tahapan: 'Pemusnahan', status: 'Done', tgl_update: '2025-06-25', keterangan: 'Pemusnahan Pertamina Dex dan Minyak Tanah telah selesai.', materials: [{ nama: 'Pertamina Dex', kode: 'PDX001', stok_saat_ini: 0, stok_diambil: 300 }, { nama: 'Minyak Tanah', kode: 'MTA001', stok_saat_ini: 0, stok_diambil: 200 }] },
            { id: 6, tgl_buat: '2025-08-10', no_surat: 'UPP-202508-002', tahapan: 'Verifikasi', status: 'Proses', tgl_update: '2025-08-11', keterangan: 'Usulan pemusnahan material Asphalt Pen 60/70.', materials: [{ nama: 'Asphalt Pen 60/70', kode: 'ASPH67', stok_saat_ini: 900, stok_diambil: 150 }, { nama: 'Asphalt Pen 80/100', kode: 'ASPH80', stok_saat_ini: 750, stok_diambil: 100 }] },
            { id: 7, tgl_buat: '2025-08-08', no_surat: 'UPP-202508-003', tahapan: 'Pemusnahan', status: 'Done', tgl_update: '2025-08-10', keterangan: 'Pemusnahan Bitumen telah selesai.', materials: [{ nama: 'Bitumen', kode: 'BITU01', stok_saat_ini: 0, stok_diambil: 200 }] },
            { id: 8, tgl_buat: '2025-08-12', no_surat: 'UPP-202508-004', tahapan: 'Pengajuan', status: 'Proses', tgl_update: '2025-08-12', keterangan: 'Usulan pemusnahan Elpiji Industri.', materials: [{ nama: 'Elpiji Industri 50 Kg', kode: 'ELI050', stok_saat_ini: 100, stok_diambil: 20 }, { nama: 'Elpiji Industri 12 Kg', kode: 'ELI012', stok_saat_ini: 150, stok_diambil: 50 }, { nama: 'Elpiji Industri 3 Kg', kode: 'ELI003', stok_saat_ini: 200, stok_diambil: 80 }] },
            { id: 9, tgl_buat: '2025-07-01', no_surat: 'UPP-202507-001', tahapan: 'Pemusnahan', status: 'Done', tgl_update: '2025-07-03', keterangan: 'Pemusnahan Pelumas Meditran telah selesai.', materials: [{ nama: 'Pelumas Meditran', kode: 'PMT001', stok_saat_ini: 0, stok_diambil: 80 }, { nama: 'Pelumas Meditran SX', kode: 'PMT002', stok_saat_ini: 0, stok_diambil: 45 }] },
            { id: 10, tgl_buat: '2025-08-11', no_surat: 'UPP-202508-005', tahapan: 'Pengajuan', status: 'Proses', tgl_update: '2025-08-11', keterangan: 'Usulan pemusnahan Solar Industri.', materials: [{ nama: 'Solar Industri', kode: 'SIL001', stok_saat_ini: 1500, stok_diambil: 500 }] },
            { id: 11, tgl_buat: '2025-08-15', no_surat: 'UPP-202508-006', tahapan: 'Pengajuan', status: 'Proses', tgl_update: '2025-08-15', keterangan: 'Dokumen pengajuan untuk material baru.', materials: [{ nama: 'Oli Mesin', kode: 'OLM001', stok_saat_ini: 300, stok_diambil: 120 }, { nama: 'Oli Transmisi', kode: 'OLT002', stok_saat_ini: 250, stok_diambil: 80 }, { nama: 'BBM Pertamax', kode: 'BBM001', stok_saat_ini: 5000, stok_diambil: 100 }] }
        ];

        // Urutkan data berdasarkan tanggal buat secara menurun (terbaru ke terlama)
        dataDummy.sort((a, b) => new Date(b.tgl_buat) - new Date(a.tgl_buat));

        // --- Variabel & Elemen DOM Khusus UPP ---
        const uppSearchInput = document.getElementById('uppSearchInput');
        const uppTableBody = document.querySelector('#table-upp-material tbody');
        const noDataUpp = document.getElementById('no-data-upp');
        const paginationUpp = document.getElementById('pagination-upp-material');

        let uppSearchQuery = '';
        let uppCurrentPage = 1;
        const uppItemsPerPage = 5;
        const uppMaxPagesToShow = 5;

        // --- Helper Function Umum ---
        const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function formatTanggal(tanggalString) {
            if (!tanggalString) return 'N/A';
            const date = new Date(tanggalString);
            const hari = hariIndonesia[date.getDay()];
            const tanggal = date.getDate();
            const bulan = bulanIndonesia[date.getMonth()];
            const tahun = date.getFullYear();
            return `${hari}, ${tanggal} ${bulan} ${tahun}`;
        }
        
        function parseDateString(dateStr) {
            if (!dateStr || dateStr === 'N/A') return null;
            return new Date(dateStr);
        }

        // --- Fungsi Utama untuk Tabel UPP Material ---
        function filterUppData() {
            return dataDummy.filter(item => {
                const matchSearch = uppSearchQuery ? item.no_surat.toLowerCase().includes(uppSearchQuery.toLowerCase()) : true;
                return matchSearch;
            });
        }
        
        function renderUppTable() {
            const data = filterUppData();
            const start = (uppCurrentPage - 1) * uppItemsPerPage;
            const paginated = data.slice(start, start + uppItemsPerPage);

            uppTableBody.innerHTML = '';
            if (paginated.length === 0) {
                noDataUpp.style.display = 'block';
            } else {
                noDataUpp.style.display = 'none';
                paginated.forEach((item, index) => {
                    const statusText = item.status.toLowerCase() === 'done' ? 'Done' : 'Proses';
                    const statusColor = item.status.toLowerCase() === 'proses' ? 'bg-gradient-warning' : 'bg-gradient-success';
                    const statusBadge = `<span class="badge ${statusColor} text-white text-xs font-weight-bold status-badge" style="cursor: pointer;" data-id="${item.id}" data-status="${item.status}">${statusText}</span>`;
                    
                    const previewBadge = `<span class="badge bg-gradient-info text-white text-xs preview-keterangan-btn" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#previewModal" data-id="${item.id}">
                                            <i class="fas fa-eye me-1"></i> Preview
                                          </span>`;

                    uppTableBody.innerHTML += `
                        <tr>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">${item.no_surat}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">${item.tahapan}</p>
                            </td>
                            <td class="text-center">
                                ${statusBadge}
                            </td>
                            <td class="text-center">
                                <p class="text-xs text-secondary mb-0">${formatTanggal(item.tgl_buat)}</p>
                            </td>
                            <td class="text-center">
                                <p class="text-xs text-secondary mb-0">${formatTanggal(item.tgl_update)}</p>
                            </td>
                            <td class="align-middle text-center">
                                ${previewBadge}
                            </td>
                        </tr>
                    `;
                });
            }
            renderUppPagination(data.length);
        }

        function renderUppPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / uppItemsPerPage);
            paginationUpp.innerHTML = '';

            if (totalItems === 0) return;

            const firstPageItem = document.createElement('li');
            firstPageItem.classList.add('page-item');
            if (uppCurrentPage === 1) firstPageItem.classList.add('disabled');
            firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">&laquo;</a>`;
            firstPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (uppCurrentPage !== 1) {
                    uppCurrentPage = 1;
                    renderUppTable();
                }
            });
            paginationUpp.appendChild(firstPageItem);

            const prevPageItem = document.createElement('li');
            prevPageItem.classList.add('page-item');
            if (uppCurrentPage === 1) prevPageItem.classList.add('disabled');
            prevPageItem.innerHTML = `<a class="page-link" href="#">&lsaquo;</a>`;
            prevPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (uppCurrentPage > 1) {
                    uppCurrentPage--;
                    renderUppTable();
                }
            });
            paginationUpp.appendChild(prevPageItem);

            let startPage = Math.max(1, uppCurrentPage - Math.floor(uppMaxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + uppMaxPagesToShow - 1);

            if (endPage - startPage + 1 < uppMaxPagesToShow && totalPages >= uppMaxPagesToShow) {
                startPage = Math.max(1, endPage - uppMaxPagesToShow + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (i === uppCurrentPage) li.classList.add('active');
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    uppCurrentPage = i;
                    renderUppTable();
                });
                paginationUpp.appendChild(li);
            }

            const nextPageItem = document.createElement('li');
            nextPageItem.classList.add('page-item');
            if (uppCurrentPage === totalPages) nextPageItem.classList.add('disabled');
            nextPageItem.innerHTML = `<a class="page-link" href="#">&rsaquo;</a>`;
            nextPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (uppCurrentPage < totalPages) {
                    uppCurrentPage++;
                    renderUppTable();
                }
            });
            paginationUpp.appendChild(nextPageItem);

            const lastPageItem = document.createElement('li');
            lastPageItem.classList.add('page-item');
            if (uppCurrentPage === totalPages) lastPageItem.classList.add('disabled');
            lastPageItem.innerHTML = `<a class="page-link" href="#" aria-label="Last">&raquo;</a>`;
            lastPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (uppCurrentPage !== totalPages) {
                    uppCurrentPage = totalPages;
                    renderUppTable();
                }
            });
            paginationUpp.appendChild(lastPageItem);
        }

        // --- Event Listeners untuk Tabel UPP Material ---
        uppSearchInput.addEventListener('input', function () {
            uppSearchQuery = this.value;
            uppCurrentPage = 1;
            renderUppTable();
        });

        document.getElementById('table-upp-material').addEventListener('click', function(event) {
            const badge = event.target.closest('.status-badge');
            if (badge) {
                const id = parseInt(badge.getAttribute('data-id'));
                const currentStatus = badge.getAttribute('data-status');
                
                const inputOptions = {
                    'Proses': `<span class="font-weight-bolder text-warning">PROSES</span>`,
                    'Done': `<span class="font-weight-bolder text-success">DONE</span>`
                };
                
                Swal.fire({
                    title: '<h5 class="font-weight-bolder text-uppercase">Ubah Status UPP</h5>',
                    html: `<p class="text-muted text-center font-weight-bolder">Pilih status baru untuk UPP ini:</p>`,
                    icon: 'warning',
                    input: 'radio',
                    inputOptions: inputOptions,
                    inputValue: currentStatus,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    preConfirm: (newStatus) => {
                        if (!newStatus) {
                            Swal.showValidationMessage('Anda harus memilih salah satu status.');
                            return false;
                        }
                        return newStatus;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const newStatus = result.value;
                        const itemToUpdate = dataDummy.find(item => item.id === id);
                        
                        if (itemToUpdate) {
                            itemToUpdate.status = newStatus;
                            if (newStatus === 'Done') {
                                itemToUpdate.tahapan = 'Pemusnahan';
                            } else {
                                itemToUpdate.tahapan = 'Pengajuan';
                            }
                            itemToUpdate.tgl_update = 'Senin, 08 September 2025';
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `Status berhasil diperbarui menjadi '${newStatus}'.`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            renderUppTable();
                        }
                    }
                });
            }

            if (event.target.closest('.preview-keterangan-btn')) {
                const button = event.target.closest('.preview-keterangan-btn');
                const id = parseInt(button.getAttribute('data-id'));
                const item = dataDummy.find(item => item.id === id);

                if (item) {
                    document.getElementById('modal-upp-surat').innerText = `(${item.no_surat})`;
                    document.getElementById('modal-keterangan').innerText = item.keterangan;
                    
                    const materialTableBody = document.getElementById('material-list-table');
                    materialTableBody.innerHTML = '';
                    if (item.materials && item.materials.length > 0) {
                        item.materials.forEach(material => {
                            materialTableBody.innerHTML += `
                                <tr>
                                    <td class="text-xs text-secondary mb-0">${material.nama}</td>
                                    <td class="text-xs text-secondary mb-0">${material.kode}</td>
                                    <td class="text-xs text-secondary mb-0">${material.stok_saat_ini}</td>
                                    <td class="text-xs text-secondary mb-0">${material.stok_diambil}</td>
                                </tr>
                            `;
                        });
                    } else {
                        materialTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada data material.</td></tr>`;
                    }
                    
                    const pemusnahanBtn = document.getElementById('lakukan-pemusnahan-btn');
                    if (item.status.toLowerCase() === 'done') {
                        pemusnahanBtn.style.display = 'none';
                    } else {
                        pemusnahanBtn.style.display = 'block';
                    }
                }
            }
        });

        document.getElementById('lakukan-pemusnahan-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Status UPP akan berubah menjadi 'Done' dan stok material akan disesuaikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lakukan Pemusnahan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const uppNoSurat = document.getElementById('modal-upp-surat').innerText.replace(/[()]/g, '');
                    const itemToUpdate = dataDummy.find(item => item.no_surat === uppNoSurat);
                    
                    if (itemToUpdate) {
                        itemToUpdate.status = 'Done';
                        itemToUpdate.tahapan = 'Pemusnahan';
                        // Menggunakan format tanggal sesuai permintaan yang telah disimpan
                        const now = new Date();
                        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        const formattedDate = new Intl.DateTimeFormat('id-ID', dateOptions).format(now);
                        itemToUpdate.tgl_update = formattedDate;
                    }

                    const myModalEl = document.getElementById('previewModal');
                    const modal = bootstrap.Modal.getInstance(myModalEl);
                    modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Proses pemusnahan berhasil dilakukan.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    renderUppTable();
                }
            });
        });

        // 📌 Inisialisasi awal
        renderUppTable();
    });
</script>
@endpush






