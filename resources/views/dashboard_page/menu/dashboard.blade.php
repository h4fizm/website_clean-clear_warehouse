@extends('dashboard_page.main')
@section('title', 'Laman Dashboard Utama')
@section('content')

{{-- Welcome Section - Opsi 1: Icon di Atas (Mobile) / Icon di Samping (Desktop) --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative" style="
        background-color: white;
        color: #344767;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            {{-- Logo for mobile (order-md-2 to push it after text on desktop) --}}
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                    alt="Branch Icon"
                    style="height: 60px; width: auto; opacity: 0.9;">
            </div>

            {{-- Text Content (order-md-1 to put it first on desktop) --}}
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="welcome-title">
                    Selamat Datang, Nama User
                </h4>
                <p class="mb-2 opacity-8" id="welcome-text">
                    Lihat dan kelola data stok material serta riwayat transaksi untuk tiap Region/SA.
                </p>
                <span class="badge bg-primary text-white text-uppercase px-3 py-2 rounded-xl shadow-sm"
                        style="font-size: 0.8rem;">Nama Role</span>
            </div>
        </div>

        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            background-size: 60px 60px;
            opacity: 0.2;
            pointer-events: none;
        "></div>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="row gx-2"> @php
        $cards = [
            ['title' => 'Total SPBE', 'value' => '1,234', 'icon' => 'fas fa-industry', 'bg' => 'primary', 'link' => '#'],
            ['title' => 'Total BPT', 'value' => '24', 'icon' => 'fas fa-warehouse', 'bg' => 'info', 'link' => '#'],
            ['title' => 'Transaksi Penerimaan', 'value' => '12,567', 'icon' => 'fas fa-arrow-down', 'bg' => 'success', 'link' => '#'],
            ['title' => 'Transaksi Penyaluran', 'value' => '1,890', 'icon' => 'fas fa-arrow-up', 'bg' => 'danger', 'link' => '#'],
            ['title' => 'UPP Material', 'value' => '25', 'icon' => 'fas fa-sync-alt', 'bg' => 'warning', 'link' => '#upp-material-section'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-1" style="flex-basis: 20%; max-width: 20%;"> <a href="{{ $card['link'] }}" class="card h-100" style="text-decoration: none; color: inherit;">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="numbers">
                                <p class="text-xs text-uppercase font-weight-bold mb-1 text-wrap" style="min-height: 38px;">
                                    {{ \Illuminate\Support\Str::limit($card['title'], 40) }}
                                </p>
                                <h5 class="font-weight-bolder mb-0">{{ $card['value'] }}</h5>
                            </div>
                        </div>
                        <div class="col-12 text-end d-flex align-items-center justify-content-end">
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

{{-- Tabel Data Material (Row 1) --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Data Material - Regional Sumbagsel</h6>
                    <div class="input-group input-group-sm mb-2" style="width: 200px;">
                        <input type="text" id="search-input-cabang" class="form-control" placeholder="Search..." aria-label="Search">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table align-items-center mb-0" id="table-cabang-custom">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 40px;">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Custom Pagination --}}
                <div class="mt-2 d-flex justify-content-center">
                    <nav>
                        <ul id="custom-pagination-cabang" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Stok Material (Row 2) --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">STOCK MATERIAL REGION SUMBAGSEL</h6>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search-stock-material" class="form-control" placeholder="Cari Nama Material..." aria-label="Search Material">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                {{-- Container untuk daftar saran --}}
                <div id="material-suggestions" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; display: none;"></div>
                <p class="text-center text-dark mb-3 fw-bold fs-5" id="stock-title">Stok Juli 2025</p>
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
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Layak Edar (Baru + Baik)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data dan baris kapasitas akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel UPP Material (Row 3) --}}
<div class="row mt-4" id="upp-material-section">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">DAFTAR UPP MATERIAL</h6>
                    <div class="input-group input-group-sm mb-2" style="width: 200px;">
                        <input type="text" id="search-input-upp" class="form-control" placeholder="Cari Material UPP..." aria-label="Search">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2" style="padding-top: 0 !important;">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="table-upp-material">
                        <thead>
                            <tr class="bg-warning text-white">
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">No.</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Kode Material</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Nama BPT</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Kabupaten</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Tanggal Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>
                {{-- Custom Pagination untuk tabel UPP --}}
                <div class="mt-2 d-flex justify-content-center">
                    <nav>
                        <ul id="custom-pagination-upp" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function formatTanggal(tanggalString) {
            const date = new Date(tanggalString + 'T00:00:00'); // Tambahkan T00:00:00 agar tidak ada masalah zona waktu
            const hari = hariIndonesia[date.getDay()];
            const tanggal = date.getDate();
            const bulan = bulanIndonesia[date.getMonth()];
            const tahun = date.getFullYear();
            return `${hari}, ${tanggal} ${bulan} ${tahun}`;
        }
        
        // Function to generate a random code with a fixed number of digits
        function generateRandomMaterialCode(length) {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        // --- Data Dummy untuk Tabel Material ---
        const allMaterialData = [
            { id: 1, nama_material: 'Material A (Bahan Baku)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1500 },
            { id: 2, nama_material: 'Material B (Produk Jadi)', kode_material: generateRandomMaterialCode(5), stok_fisik: 800 },
            { id: 3, nama_material: 'Material C (Spare Part)', kode_material: generateRandomMaterialCode(5), stok_fisik: 250 },
            { id: 4, nama_material: 'Material D (Packaging)', kode_material: generateRandomMaterialCode(5), stok_fisik: 3000 },
            { id: 5, nama_material: 'Material E (Kimia Industri)', kode_material: generateRandomMaterialCode(5), stok_fisik: 120 },
            { id: 6, nama_material: 'Material F (Elektronik)', kode_material: generateRandomMaterialCode(5), stok_fisik: 75 },
            { id: 7, nama_material: 'Material G (Peralatan)', kode_material: generateRandomMaterialCode(5), stok_fisik: 30 },
            { id: 8, nama_material: 'Material H (Kertas Roll Besar)', kode_material: generateRandomMaterialCode(5), stok_fisik: 450 },
            { id: 9, nama_material: 'Material I (Pelumas)', kode_material: generateRandomMaterialCode(5), stok_fisik: 180 },
            { id: 10, nama_material: 'Material J (Seal Karet)', kode_material: generateRandomMaterialCode(5), stok_fisik: 900 },
            { id: 11, nama_material: 'Material K (Pipa PVC)', kode_material: generateRandomMaterialCode(5), stok_fisik: 600 },
            { id: 12, nama_material: 'Material L (Kabel Listrik)', kode_material: generateRandomMaterialCode(5), stok_fisik: 2200 },
            { id: 13, nama_material: 'Material M (Cat Industri)', kode_material: generateRandomMaterialCode(5), stok_fisik: 90 },
            { id: 14, nama_material: 'Material N (Baut & Mur)', kode_material: generateRandomMaterialCode(5), stok_fisik: 5000 },
            { id: 15, nama_material: 'Material O (Alat Ukur)', kode_material: generateRandomMaterialCode(5), stok_fisik: 15 },
            { id: 16, nama_material: 'Material P (Sensor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 40 },
            { id: 17, nama_material: 'Material Q (Filter Udara)', kode_material: generateRandomMaterialCode(5), stok_fisik: 100 },
            { id: 18, nama_material: 'Material R (Resistor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 300 },
            { id: 19, nama_material: 'Material S (Transistor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 200 },
            { id: 20, nama_material: 'Material T (Relay)', kode_material: generateRandomMaterialCode(5), stok_fisik: 60 },
            { id: 21, nama_material: 'Material U (Konektor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 700 },
            { id: 22, nama_material: 'Material V (Pelat Logam)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1000 },
            { id: 23, nama_material: 'Material W (Gasket)', kode_material: generateRandomMaterialCode(5), stok_fisik: 400 },
            { id: 24, nama_material: 'Material X (Klem Pipa)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1200 },
            { id: 25, nama_material: 'Material Y (Pompa Kecil)', kode_material: generateRandomMaterialCode(5), stok_fisik: 25 }
        ];

        // --- Custom Pagination & Search untuk Tabel Material --
        const itemsPerPage = 5;
        const tableBodyCabang = document.querySelector('#table-cabang-custom tbody');
        const paginationContainerCabang = document.getElementById('custom-pagination-cabang');
        let currentFilteredDataCabang = [];
        let currentPageCabang = 0;

        function renderTableCabang(data, page) {
            tableBodyCabang.innerHTML = '';
            const start = page * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = data.slice(start, end);

            if (paginatedItems.length === 0) {
                tableBodyCabang.innerHTML = '<tr><td colspan="4" class="text-center py-4">Tidak ada data material ditemukan.</td></tr>';
            } else {
                paginatedItems.forEach((material, index) => {
                    const rowIndex = start + index + 1;
                    const row = `
                        <tr>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                            </td>
                            <td class="w-30">
                                <div class="d-flex px-2 py-1 align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary shadow-secondary text-center rounded">
                                        <i class="fas fa-box text-white opacity-10"></i>
                                    </div>
                                    <div class="ms-1">
                                        <h6 class="text-sm mb-0">${material.nama_material}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <h6 class="text-sm mb-0">${material.kode_material}</h6>
                                </div>
                            </td>
                            <td class="text-center">
                                <h6 class="text-sm mb-0">${material.stok_fisik.toLocaleString('id-ID')} pcs</h6>
                            </td>
                        </tr>
                    `;
                    tableBodyCabang.insertAdjacentHTML('beforeend', row);
                });
            }
            updatePaginationButtons(paginationContainerCabang, page, Math.ceil(data.length / itemsPerPage), (newPage) => {
                currentPageCabang = newPage;
                renderTableCabang(currentFilteredDataCabang, currentPageCabang);
            });
        }

        function updatePaginationButtons(container, activePage, totalPages, callback) {
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const maxVisible = 5;
            let startPage = Math.max(0, activePage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible);

            if (endPage - startPage < maxVisible) {
                startPage = Math.max(0, totalPages - maxVisible);
            }

            // Tombol ke halaman pertama
            const firstPageButton = document.createElement('li');
            firstPageButton.classList.add('page-item');
            if (activePage === 0) firstPageButton.classList.add('disabled');
            firstPageButton.innerHTML = `<a class="page-link" href="javascript:;">&laquo;</a>`;
            firstPageButton.addEventListener('click', () => {
                if (activePage > 0) callback(0);
            });
            container.appendChild(firstPageButton);

            // Tombol halaman
            for (let i = startPage; i < endPage; i++) {
                const pageItem = document.createElement('li');
                pageItem.classList.add('page-item');
                if (i === activePage) pageItem.classList.add('active');
                pageItem.innerHTML = `<a class="page-link" href="javascript:;">${i + 1}</a>`;
                pageItem.addEventListener('click', () => callback(i));
                container.appendChild(pageItem);
            }

            // Tombol ke halaman terakhir
            const lastPageButton = document.createElement('li');
            lastPageButton.classList.add('page-item');
            if (activePage === totalPages - 1) lastPageButton.classList.add('disabled');
            lastPageButton.innerHTML = `<a class="page-link" href="javascript:;">&raquo;</a>`;
            lastPageButton.addEventListener('click', () => {
                if (activePage < totalPages - 1) callback(totalPages - 1);
            });
            container.appendChild(lastPageButton);
        }

        // Event listener untuk input search
        document.getElementById('search-input-cabang').addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            currentFilteredDataCabang = allMaterialData.filter(material =>
                material.nama_material.toLowerCase().includes(searchTerm) ||
                material.kode_material.toLowerCase().includes(searchTerm)
            );
            currentPageCabang = 0;
            renderTableCabang(currentFilteredDataCabang, currentPageCabang);
        });

        // Inisialisasi awal tabel dan paginasi
        currentFilteredDataCabang = allMaterialData;
        renderTableCabang(currentFilteredDataCabang, currentPageCabang);

        // --- Data Dummy dan Logika untuk Tabel Stok Material --
        const allMaterialStockData = {
            'Tabung LPG 3 Kg': [
                { material_name: 'Tabung LPG 3 Kg', gudang: 'Gudang Region Sumbagsel', baru: 840, baik: 0, rusak: 0, afkir: 28416, layak_edar: 840 },
                { material_name: 'Tabung LPG 3 Kg', gudang: 'SPBE/BPT Region Sumbagsel', baru: 33157, baik: 5533, rusak: 36515, afkir: 70813, layak_edar: 38690 }
            ],
            'Tabung LPG 12 Kg': [
                { material_name: 'Tabung LPG 12 Kg', gudang: 'Gudang Region Sumbagsel', baru: 120, baik: 50, rusak: 10, afkir: 1500, layak_edar: 170 },
                { material_name: 'Tabung LPG 12 Kg', gudang: 'SPBE/BPT Region Sumbagsel', baru: 1500, baik: 200, rusak: 50, afkir: 2500, layak_edar: 1700 }
            ],
            'Regulator Gas': [
                { material_name: 'Regulator Gas', gudang: 'Gudang Region Sumbagsel', baru: 500, baik: 300, rusak: 25, afkir: 500, layak_edar: 800 },
                { material_name: 'Regulator Gas', gudang: 'SPBE/BPT Region Sumbagsel', baru: 10000, baik: 5000, rusak: 750, afkir: 15000, layak_edar: 15000 }
            ],
            'Selang Gas': [
                { material_name: 'Selang Gas', gudang: 'Gudang Region Sumbagsel', baru: 250, baik: 150, rusak: 10, afkir: 200, layak_edar: 400 },
                { material_name: 'Selang Gas', gudang: 'SPBE/BPT Region Sumbagsel', baru: 5000, baik: 3000, rusak: 100, afkir: 10000, layak_edar: 8000 }
            ],
            'Seal Karet Tabung': [
                { material_name: 'Seal Karet Tabung', gudang: 'Gudang Region Sumbagsel', baru: 2000, baik: 1500, rusak: 100, afkir: 5000, layak_edar: 3500 },
                { material_name: 'Seal Karet Tabung', gudang: 'SPBE/BPT Region Sumbagsel', baru: 25000, baik: 10000, rusak: 500, afkir: 15000, layak_edar: 35000 }
            ],
            'Valve Tabung': [
                { material_name: 'Valve Tabung', gudang: 'Gudang Region Sumbagsel', baru: 75, baik: 25, rusak: 5, afkir: 100, layak_edar: 100 },
                { material_name: 'Valve Tabung', gudang: 'SPBE/BPT Region Sumbagsel', baru: 1200, baik: 300, rusak: 20, afkir: 800, layak_edar: 1500 }
            ]
        };

        const materialCapacityData = {
            'Tabung LPG 3 Kg': 50000,
            'Tabung LPG 12 Kg': 10000,
            'Regulator Gas': 20000,
            'Selang Gas': 15000,
            'Seal Karet Tabung': 60000,
            'Valve Tabung': 5000
        };

        const stockTableBody = document.querySelector('#table-stock-material-custom tbody');
        const stockSearchInput = document.getElementById('search-stock-material');
        const stockTitle = document.getElementById('stock-title');
        const materialSuggestionsContainer = document.getElementById('material-suggestions');

        // Sembunyikan daftar saran jika klik di luar search bar
        document.addEventListener('click', function(e) {
            if (!stockSearchInput.contains(e.target) && !materialSuggestionsContainer.contains(e.target)) {
                materialSuggestionsContainer.style.display = 'none';
            }
        });

        function renderStockTable(materialName) {
            const data = allMaterialStockData[materialName];
            stockTableBody.innerHTML = '';
            
            if (materialName) {
                stockTitle.innerText = `Stok ${materialName} Juli 2025`;
                stockSearchInput.value = materialName; // Mengisi search bar dengan nama material yang dipilih
            } else {
                stockTitle.innerText = `Stok Juli 2025`;
            }

            if (data && data.length > 0) {
                data.forEach((item, index) => {
                    const row = `
                        <tr>
                            ${index === 0 ? `<td class="ps-2 text-wrap align-middle" rowspan="${data.length}" style="width: 25%;">
                                <h6 class="text-sm font-weight-bold mb-0">${item.material_name}</h6>
                            </td>` : ''}
                            <td class="text-secondary text-center text-xs">
                                <span class="text-xs font-weight-bold">${item.gudang}</span>
                            </td>
                            <td class="text-secondary text-center text-xs">
                                <span class="text-xs font-weight-bold">${item.baru.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="text-secondary text-center text-xs">
                                <span class="text-xs font-weight-bold">${item.baik.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="text-secondary text-center text-xs">
                                <span class="text-xs font-weight-bold">${item.rusak.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="text-secondary text-center text-xs">
                                <span class="text-xs font-weight-bold">${item.afkir.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="text-secondary text-center text-xs">
                                <h6 class="text-sm font-weight-bolder mb-0">${item.layak_edar.toLocaleString('id-ID')}</h6>
                            </td>
                        </tr>
                    `;
                    stockTableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                 stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Pilih atau cari material untuk menampilkan data.</td></tr>';
            }

            // Tambahkan baris Kapasitas Daya Tampung di bagian bawah tabel jika ada material yang dipilih
            if (materialName && materialCapacityData[materialName]) {
                const capacityValue = materialCapacityData[materialName] ? materialCapacityData[materialName].toLocaleString('id-ID') : 'N/A';
                const capacityRowHtml = `
                    <tr class="bg-gray-200">
                        <td colspan="2" class="p-2 align-middle">
                            <p class="text-sm font-weight-bold mb-0">Kapasitas Daya Tampung ${materialName} :</p>
                        </td>
                        <td colspan="5" class="p-2 text-end">
                            <form id="capacity-form" class="d-flex align-items-center justify-content-end" onsubmit="return false;">
                                <input type="number" id="capacity-input" class="form-control form-control-sm me-2 text-end" value="${materialCapacityData[materialName]}" style="width: 150px;" disabled>
                                <button type="button" id="edit-capacity-btn" class="btn btn-sm btn-info me-2 text-white">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="submit" id="submit-capacity-btn" class="btn btn-sm btn-primary" disabled>
                                    <i class="fas fa-save"></i> Submit
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                stockTableBody.insertAdjacentHTML('beforeend', capacityRowHtml);
                
                // Re-attach event listeners after re-rendering the row
                const capacityInput = document.getElementById('capacity-input');
                const editCapacityBtn = document.getElementById('edit-capacity-btn');
                const submitCapacityBtn = document.getElementById('submit-capacity-btn');

                if (editCapacityBtn) {
                    editCapacityBtn.addEventListener('click', function() {
                        capacityInput.disabled = false;
                        editCapacityBtn.disabled = true;
                        submitCapacityBtn.disabled = false;
                    });
                }

                if (submitCapacityBtn) {
                    submitCapacityBtn.addEventListener('click', function() {
                        const newCapacity = parseInt(capacityInput.value);
                        if (isNaN(newCapacity) || newCapacity < 0) {
                            Swal.fire('Error!', 'Kapasitas harus berupa angka positif.', 'error');
                            return;
                        }

                        materialCapacityData[materialName] = newCapacity;
                        
                        // Re-render the table to show the updated value
                        renderStockTable(materialName);
                        
                        Swal.fire('Berhasil!', 'Kapasitas daya tampung berhasil diperbarui.', 'success');
                    });
                }
            }
        }

        // Fungsi untuk menampilkan daftar saran
        function showSuggestions(searchTerm) {
            const allMaterialNames = Object.keys(allMaterialStockData);
            const filteredNames = allMaterialNames.filter(name =>
                name.toLowerCase().includes(searchTerm.toLowerCase())
            );

            materialSuggestionsContainer.innerHTML = '';

            if (searchTerm !== '' && filteredNames.length > 0) {
                materialSuggestionsContainer.style.display = 'block';
                filteredNames.forEach(name => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.textContent = name;
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        renderStockTable(name);
                        materialSuggestionsContainer.style.display = 'none';
                    });
                    materialSuggestionsContainer.appendChild(item);
                });
            } else {
                materialSuggestionsContainer.style.display = 'none';
            }
        }
        
        stockSearchInput.addEventListener('keyup', function() {
            showSuggestions(this.value);
            if (this.value === '') {
                 // Tampilkan pesan default jika search bar kosong
                renderStockTable(null);
            }
        });
        
        // Inisialisasi: Tampilkan tabel kosong saat pertama kali dimuat
        renderStockTable(null);

        // --- Data Dummy dan Logika untuk Tabel UPP Material --
        const uppMaterialData = [
            { nama_material: 'Tabung LPG 3 Kg', kode_material: 'TBG3-001', nama_bpt: 'BPT Sejahtera', stok_akhir: 95, status: 'Diterima', kabupaten: 'Kab. Musi Banyuasin', tanggal_pengajuan: '2025-07-28' },
            { nama_material: 'Seal Karet', kode_material: 'SEAL-01', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 150, status: 'Pending', kabupaten: 'Kota Palembang', tanggal_pengajuan: '2025-07-29' },
            { nama_material: 'Regulator Gas', kode_material: 'REG-006', nama_bpt: 'BPT Sejahtera', stok_akhir: 35, status: 'Clear', kabupaten: 'Kab. Ogan Komering Ulu', tanggal_pengajuan: '2025-07-29' },
            { nama_material: 'Tabung LPG 5.5 Kg', kode_material: 'TBG5.5-001', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 170, status: 'Diterima', kabupaten: 'Kab. Banyuasin', tanggal_pengajuan: '2025-07-30' },
            { nama_material: 'Pipa PVC', kode_material: 'PPC-002', nama_bpt: 'BPT Sejahtera', stok_akhir: 25, status: 'Pending', kabupaten: 'Kota Palembang', tanggal_pengajuan: '2025-07-31' },
            { nama_material: 'Tabung LPG 3 Kg', kode_material: 'TBG3-002', nama_bpt: 'BPT Sejahtera', stok_akhir: 65, status: 'Diterima', kabupaten: 'Kab. Ogan Ilir', tanggal_pengajuan: '2025-08-01' },
            { nama_material: 'Gas 3kg', kode_material: 'LPG3-002', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 30, status: 'Clear', kabupaten: 'Kab. Muara Enim', tanggal_pengajuan: '2025-08-01' },
            { nama_material: 'Konektor Gas', kode_material: 'KNG-002', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 400, status: 'Pending', kabupaten: 'Kota Palembang', tanggal_pengajuan: '2025-08-02' },
            { nama_material: 'Regulator', kode_material: 'REG-006', nama_bpt: 'BPT Sejahtera', stok_akhir: 100, status: 'Diterima', kabupaten: 'Kab. Ogan Komering Ulu Timur', tanggal_pengajuan: '2025-08-02' },
            { nama_material: 'Tabung LPG 5.5 Kg', kode_material: 'TBG5.5-002', nama_bpt: 'BPT Sejahtera', stok_akhir: 170, status: 'Pending', kabupaten: 'Kota Prabumulih', tanggal_pengajuan: '2025-08-03' },
            { nama_material: 'Manometer', kode_material: 'MAN-003', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 95, status: 'Diterima', kabupaten: 'Kab. Ogan Komering Ilir', tanggal_pengajuan: '2025-08-03' },
            { nama_material: 'Konektor Gas', kode_material: 'KNG-004', nama_bpt: 'BPT Sejahtera', stok_akhir: 400, status: 'Clear', kabupaten: 'Kab. Empat Lawang', tanggal_pengajuan: '2025-08-04' },
            { nama_material: 'Gas 3kg', kode_material: 'LPG3-004', nama_bpt: 'BPT Jaya Abadi', stok_akhir: 85, status: 'Pending', kabupaten: 'Kab. Lahat', tanggal_pengajuan: '2025-08-04' },
            { nama_material: 'Seal Karet', kode_material: 'SEAL-02', nama_bpt: 'BPT Makmur', stok_akhir: 210, status: 'Diterima', kabupaten: 'Kota Lubuklinggau', tanggal_pengajuan: '2025-08-05' },
            { nama_material: 'Tabung LPG 3 Kg', kode_material: 'TBG3-003', nama_bpt: 'BPT Makmur', stok_akhir: 120, status: 'Pending', kabupaten: 'Kab. Musi Rawas', tanggal_pengajuan: '2025-08-05' }
        ];

        const uppTableBody = document.querySelector('#table-upp-material tbody');
        const uppSearchInput = document.getElementById('search-input-upp');
        const paginationContainerUpp = document.getElementById('custom-pagination-upp');
        let currentFilteredDataUpp = uppMaterialData;
        let currentPageUpp = 0;

        function getStatusBadge(status) {
            switch (status.toLowerCase()) {
                case 'pending':
                    return `<span class="badge bg-warning text-white text-xs">${status}</span>`;
                case 'diterima':
                    return `<span class="badge bg-success text-white text-xs">${status}</span>`;
                case 'clear':
                    return `<span class="badge bg-info text-white text-xs">${status}</span>`;
                default:
                    return `<span class="badge bg-secondary text-white text-xs">${status}</span>`;
            }
        }

        function renderUppTable(data, page) {
            uppTableBody.innerHTML = '';
            const start = page * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = data.slice(start, end);

            if (paginatedItems.length === 0) {
                uppTableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Tidak ada data UPP material.</td></tr>';
            } else {
                paginatedItems.forEach((item, index) => {
                    const rowIndex = start + index + 1;
                    const statusBadge = getStatusBadge(item.status);
                    const formattedDate = formatTanggal(item.tanggal_pengajuan);
                    const row = `
                        <tr>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                            </td>
                            <td class="ps-2 text-wrap">
                                <h6 class="text-sm font-weight-bold mb-0">${item.nama_material}</h6>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-weight-bold">${item.kode_material}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-weight-bold">${item.nama_bpt}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-weight-bold">${item.stok_akhir}</span>
                            </td>
                            <td class="text-center">
                                ${statusBadge}
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-weight-bold">${item.kabupaten}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-weight-bold">${formattedDate}</span>
                            </td>
                        </tr>
                    `;
                    uppTableBody.insertAdjacentHTML('beforeend', row);
                });
            }
            updatePaginationButtons(paginationContainerUpp, page, Math.ceil(data.length / itemsPerPage), (newPage) => {
                currentPageUpp = newPage;
                renderUppTable(currentFilteredDataUpp, currentPageUpp);
            });
        }

        uppSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            currentFilteredDataUpp = uppMaterialData.filter(item =>
                item.nama_material.toLowerCase().includes(searchTerm) ||
                item.nama_bpt.toLowerCase().includes(searchTerm) ||
                item.kode_material.toLowerCase().includes(searchTerm) ||
                item.kabupaten.toLowerCase().includes(searchTerm) ||
                item.status.toLowerCase().includes(searchTerm) ||
                formatTanggal(item.tanggal_pengajuan).toLowerCase().includes(searchTerm)
            );
            currentPageUpp = 0;
            renderUppTable(currentFilteredDataUpp, currentPageUpp);
        });

        // Inisialisasi awal tabel UPP
        renderUppTable(currentFilteredDataUpp, currentPageUpp);
    });
</script>
@endpush
@endsection