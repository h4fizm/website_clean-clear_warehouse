@extends('dashboard_page.main')
@section('title', 'Laman Transaksi')
@section('content')

{{-- Welcome Section: Pihak Pertama (Cabang Anda) --}}
<div class="col-12 mb-3">
    <div class="card p-3" style="
        background: linear-gradient(to right, #0F2027 0%, #203A43 50%, #2C5364 100%); /* Gradien Biru Tua */
        color: white; /* Text color for contrast */
        border-radius: 1rem; /* Slightly more rounded corners */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Soft shadow */
        overflow: hidden; /* To contain pseudo-elements if added */
        position: relative;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h4 class="mb-1 text-white fw-bold">Penginputan oleh, <strong style="color: #FFD700;">Nama User</strong></h4>
                <p class="mb-2 opacity-8">Informasi Data Stok Material dan Transaksi pada **Cabang Anda** : <strong style="color: #FFD700;">Nama Cabang</strong>.</p>
                <span class="badge bg-white text-primary text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8em;">Pihak Pertama</span>
            </div>

            <div class="text-center position-relative me-md-4">
                <i class="fas fa-warehouse text-white opacity-8" style="font-size: 4em;"></i>
                <i class="fas fa-box-open text-white opacity-5 position-absolute" style="font-size: 2em; top: 10px; right: 0;"></i>
            </div>
        </div>
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            background-size: 60px 60px;
            opacity: 0.2;
            pointer-events: none;
        "></div>
    </div>
</div>

{{-- Tabel Material Pihak Pertama --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h6>Tabel Stok Material Cabang Anda - Nama Cabang</h6>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                    {{-- Input Search --}}
                    <input type="text" id="search-input-material-1" class="form-control form-control-sm" placeholder="Cari Nama atau Kode Material..." style="width: 200px; min-width: 150px; height: 45px;">

                    {{-- Date Range Picker --}}
                    <div class="d-flex align-items-center gap-1">
                        <label for="start-date-material-1" class="text-xs mb-0 me-1">Dari</label>
                        <input type="date" id="start-date-material-1" class="form-control form-control-sm" style="height: 45px; width: 140px; min-width: 120px;">
                        <label for="end-date-material-1" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                        <input type="date" id="end-date-material-1" class="form-control form-control-sm" style="height: 45px; width: 140px; min-width: 120px;">
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material-1">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Hari/Tanggal</th>

                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-material-1" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation material 1">
                        <ul class="pagination pagination-sm mb-0" id="pagination-material-1">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Welcome Section: Pihak Kedua (SPBE/BPT Tujuan) --}}
<div class="col-12 mb-3">
    <div class="card p-3" style="
        background: linear-gradient(to right, #0F2027 0%, #203A43 50%, #2C5364 100%); /* Warna yang sama dengan Pihak Pertama */
        color: white;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h4 class="mb-1 text-white fw-bold">Transaksi dengan <strong style="color: #FFEB3B;">Cabang Pihak Kedua</strong></h4>
                <p class="mb-2 opacity-8">Informasi Data Material dan Transaksi ke Cabang Lain.</p>
                <span class="badge bg-white text-primary text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8em;">Pihak Kedua</span>
            </div>

            <div class="text-center position-relative me-md-4">
                <i class="fas fa-truck-loading text-white opacity-8" style="font-size: 4em;"></i>
                <i class="fas fa-exchange-alt text-white opacity-5 position-absolute" style="font-size: 2em; top: 10px; right: 0;"></i>
            </div>
        </div>
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            background-size: 60px 60px;
            opacity: 0.2;
            pointer-events: none;
        "></div>
    </div>
</div>

{{-- Tabel Material Pihak Kedua --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h6>Tabel Stok Material Cabang Lain</h6>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                    {{-- Input Search --}}
                    <input type="text" id="search-input-material-2" class="form-control form-control-sm" placeholder="Cari Nama Material, Cabang, Kode..." style="width: 200px; min-width: 150px; height: 45px;">

                    {{-- Date Range Picker --}}
                    <div class="d-flex align-items-center gap-1">
                        <label for="start-date-material-2" class="text-xs mb-0 me-1">Dari</label>
                        <input type="date" id="start-date-material-2" class="form-control form-control-sm" style="height: 45px; width: 140px; min-width: 120px;">
                        <label for="end-date-material-2" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                        <input type="date" id="end-date-material-2" class="form-control form-control-sm" style="height: 45px; width: 140px; min-width: 120px;">
                    </div>

                    {{-- **DROPDOWN 'Pilih Cabang Lain' TELAH DIHAPUS DARI SINI** --}}
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material-2">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Cabang</th> {{-- Kolom baru --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Hari/Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-material-2" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation material 2">
                        <ul class="pagination pagination-sm mb-0" id="pagination-material-2">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    const materialData1 = [
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', penerimaan: 1000, penyaluran: 800, stok: 200, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', penerimaan: 500, penyaluran: 350, stok: 150, tanggal: '2025-07-27' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', penerimaan: 1000, penyaluran: 500, stok: 500, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', penerimaan: 300, penyaluran: 150, stok: 150, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', penerimaan: 250, penyaluran: 100, stok: 150, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', penerimaan: 80, penyaluran: 30, stok: 50, tanggal: '2025-07-20' },
        { nama: 'Manometer', kode: 'MAN-001', penerimaan: 50, penyaluran: 10, stok: 40, tanggal: '2025-07-19' },
        { nama: 'Flow Meter', kode: 'FLM-002', penerimaan: 30, penyaluran: 10, stok: 20, tanggal: '2025-07-18' },
    ];

    // Data dummy untuk Tabel Material Cabang Lain
    const materialData2 = [
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Surabaya', penerimaan: 500, penyaluran: 450, stok: 50, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', cabang: 'Cabang Jakarta', penerimaan: 300, penyaluran: 200, stok: 100, tanggal: '2025-07-27' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', cabang: 'Cabang Bandung', penerimaan: 200, penyaluran: 100, stok: 100, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', cabang: 'Cabang Surabaya', penerimaan: 800, penyaluran: 400, stok: 400, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', cabang: 'Cabang Jakarta', penerimaan: 150, penyaluran: 70, stok: 80, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', cabang: 'Cabang Bandung', penerimaan: 250, penyaluran: 150, stok: 100, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', cabang: 'Cabang Medan', penerimaan: 100, penyaluran: 50, stok: 50, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', cabang: 'Cabang Makassar', penerimaan: 200, penyaluran: 80, stok: 120, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', cabang: 'Cabang Surabaya', penerimaan: 60, penyaluran: 20, stok: 40, tanggal: '2025-07-20' },
        { nama: 'Manometer', kode: 'MAN-001', cabang: 'Cabang Jakarta', penerimaan: 40, penyaluran: 5, stok: 35, tanggal: '2025-07-19' },
        { nama: 'Flow Meter', kode: 'FLM-002', cabang: 'Cabang Bandung', penerimaan: 20, penyaluran: 5, stok: 15, tanggal: '2025-07-18' },
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Medan', penerimaan: 300, penyaluran: 280, stok: 20, tanggal: '2025-07-17' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', cabang: 'Cabang Makassar', penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-16' },
        { nama: 'Seal Karet', kode: 'SEAL-01', cabang: 'Cabang Jakarta', penerimaan: 600, penyaluran: 300, stok: 300, tanggal: '2025-07-15' },
        { nama: 'Regulator', kode: 'REG-005', cabang: 'Cabang Surabaya', penerimaan: 100, penyaluran: 40, stok: 60, tanggal: '2025-07-14' },
        { nama: 'Selang Gas', kode: 'SLG-010', cabang: 'Cabang Medan', penerimaan: 200, penyaluran: 100, stok: 100, tanggal: '2025-07-13' },
    ];


    const perPage = 10;

    // State for Table 1
    let currentPage1 = 1;
    let searchQuery1 = '';

    // State for Table 2
    let currentPage2 = 1;
    let searchQuery2 = '';

    function formatTanggal(tgl) {
        const d = new Date(tgl);
        return d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    // --- Functions for Table 1 ---
    function filterData1() {
        if (!searchQuery1) return materialData1;
        return materialData1.filter(item =>
            item.nama.toLowerCase().includes(searchQuery1.toLowerCase()) ||
            item.kode.toLowerCase().includes(searchQuery1.toLowerCase())
        );
    }

    function renderTable1() {
        const tbody = document.querySelector('#table-material-1 tbody');
        const noData = document.querySelector('#no-data-material-1');
        const filtered = filterData1();
        const start = (currentPage1 - 1) * perPage;
        const dataPage = filtered.slice(start, start + perPage);

        tbody.innerHTML = '';
        if (dataPage.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            dataPage.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ url('/spbe-bpt') }}" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary" style="cursor: pointer;">
                                        ${item.nama}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="text-xs text-secondary mb-0">${item.kode}</p>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-primary text-white text-xs">${item.penerimaan} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-info text-white text-xs">${item.penyaluran} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-success text-white text-xs">${item.stok} pcs</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary font-weight-bold mb-0">${formatTanggal(item.tanggal)}</p>
                        </td>
                    </tr>
                `;
            });
        }
        renderPagination1(filtered.length);
    }

    function renderPagination1(totalItems) {
        const pagination = document.getElementById('pagination-material-1');
        const totalPages = Math.ceil(totalItems / perPage);
        pagination.innerHTML = '';

        function createButton(label, page, disabled = false, active = false) {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    currentPage1 = page;
                    renderTable1();
                });
            }
            return li;
        }

        pagination.appendChild(createButton('«', 1, currentPage1 === 1));
        pagination.appendChild(createButton('‹', currentPage1 - 1, currentPage1 === 1));

        for (let i = 1; i <= totalPages; i++) {
            pagination.appendChild(createButton(i, i, false, i === currentPage1));
        }

        pagination.appendChild(createButton('›', currentPage1 + 1, currentPage1 === totalPages));
        pagination.appendChild(createButton('»', totalPages, currentPage1 === totalPages));
    }

    // --- Functions for Table 2 ---
    function filterData2() {
        if (!searchQuery2) return materialData2;
        return materialData2.filter(item =>
            item.nama.toLowerCase().includes(searchQuery2.toLowerCase()) ||
            item.kode.toLowerCase().includes(searchQuery2.toLowerCase()) ||
            item.cabang.toLowerCase().includes(searchQuery2.toLowerCase())
        );
    }

    function renderTable2() {
        const tbody = document.querySelector('#table-material-2 tbody');
        const noData = document.querySelector('#no-data-material-2');
        const filtered = filterData2();
        const start = (currentPage2 - 1) * perPage;
        const dataPage = filtered.slice(start, start + perPage);

        tbody.innerHTML = '';
        if (dataPage.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            dataPage.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ url('/spbe-bpt') }}" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary" style="cursor: pointer;">
                                        ${item.nama}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="text-xs text-secondary mb-0">${item.kode}</p>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="text-xs text-secondary mb-0">${item.cabang}</p>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-primary text-white text-xs">${item.penerimaan} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-info text-white text-xs">${item.penyaluran} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-success text-white text-xs">${item.stok} pcs</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary font-weight-bold mb-0">${formatTanggal(item.tanggal)}</p>
                        </td>
                    </tr>
                `;
            });
        }
        renderPagination2(filtered.length);
    }

    function renderPagination2(totalItems) {
        const pagination = document.getElementById('pagination-material-2');
        const totalPages = Math.ceil(totalItems / perPage);
        pagination.innerHTML = '';

        function createButton(label, page, disabled = false, active = false) {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    currentPage2 = page;
                    renderTable2();
                });
            }
            return li;
        }

        pagination.appendChild(createButton('«', 1, currentPage2 === 1));
        pagination.appendChild(createButton('‹', currentPage2 - 1, currentPage2 === 1));

        for (let i = 1; i <= totalPages; i++) {
            pagination.appendChild(createButton(i, i, false, i === currentPage2));
        }

        pagination.appendChild(createButton('›', currentPage2 + 1, currentPage2 === totalPages));
        pagination.appendChild(createButton('»', totalPages, currentPage2 === totalPages));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event Listener for Table 1 Search
        document.getElementById('search-input-material-1').addEventListener('input', function () {
            searchQuery1 = this.value;
            currentPage1 = 1;
            renderTable1();
        });

        // Event Listener for Table 2 Search
        document.getElementById('search-input-material-2').addEventListener('input', function () {
            searchQuery2 = this.value;
            currentPage2 = 1;
            renderTable2();
        });

        // Initial render for both tables
        renderTable1();
        renderTable2();
    });
</script>
@endpush

@endsection