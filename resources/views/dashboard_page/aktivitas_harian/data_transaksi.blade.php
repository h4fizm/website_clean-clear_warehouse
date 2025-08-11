@extends('dashboard_page.main')
@section('title', 'Aktivitas Log Harian Transaksi')
@section('content')

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="w-100 order-md-1 text-center text-md-start">
                <a href="/aktivitas" class="text-secondary me-3 d-inline-block">
                    <i class="fas fa-arrow-left fa-2x"></i>
                </a>
                <h4 class="mb-1 fw-bold d-inline-block" id="summary-title">
                    Aktivitas Log Harian Transaksi
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Laporan detail semua aktivitas penerimaan dan penyaluran material.
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
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h4>Tabel Aktivitas Transaksi Harian</h4>
                    <h6>Data riwayat penerimaan dan penyaluran material.</h6>
                </div>
                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center mt-2 mt-md-0">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </button>
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <div class="d-flex flex-wrap gap-2 mb-3 px-3 align-items-center justify-content-between">
                    {{-- Search (Pojok Kiri) --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari Nama, Kode, User..." style="width: 600px; height: 35px;">
                    {{-- Filter Range Tanggal (Pojok Kanan) --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="start-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                        <input type="date" id="start-date" class="form-control form-control-sm" style="width: 150px; height: 35px;">
                        <label for="end-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                        <input type="date" id="end-date" class="form-control form-control-sm" style="width: 150px; height: 35px;">
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-aktivitas-transaksi">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Material & Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Asal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tujuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jumlah Transaksi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aktivitas</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">User PJ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0" id="pagination-aktivitas-transaksi">
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
    // --- Data Dummy Aktivitas Transaksi ---
    const dataDummyTransaksi = [
        { id: 1, tanggal: '2025-08-10', user_pj: 'Budi Santoso', asal: 'P.Layang', tujuan: 'SPBE Sukamaju', material_nama: 'Gas LPG 3 Kg', material_kode: 'LPG-01', jumlah: 150, aktivitas: 'Penyaluran', stok_awal: 1050 },
        { id: 2, tanggal: '2025-08-10', user_pj: 'Rina Wijaya', asal: 'SPBE Sentosa', tujuan: 'P.Layang', material_nama: 'Pelumas Fastron', material_kode: 'PLS-02', jumlah: 50, aktivitas: 'Penerimaan', stok_awal: 100 },
        { id: 3, tanggal: '2025-08-09', user_pj: 'Adi Gunawan', asal: 'P.Layang', tujuan: 'SPBE Makmur', material_nama: 'Bright Gas 12 Kg', material_kode: 'BRG-03', jumlah: 90, aktivitas: 'Penyaluran', stok_awal: 800 },
        { id: 4, tanggal: '2025-08-09', user_pj: 'Rina Wijaya', asal: 'SPBE Jaya', tujuan: 'P.Layang', material_nama: 'Aspal Curah', material_kode: 'ASP-04', jumlah: 110, aktivitas: 'Penerimaan', stok_awal: 250 },
        { id: 5, tanggal: '2025-08-08', user_pj: 'Budi Santoso', asal: 'P.Layang', tujuan: 'SPBE Makmur', material_nama: 'Pertalite', material_kode: 'PRT-05', jumlah: 95, aktivitas: 'Penyaluran', stok_awal: 500 },
        { id: 6, tanggal: '2025-08-08', user_pj: 'Adi Gunawan', asal: 'SPBE Sukamaju', tujuan: 'P.Layang', material_nama: 'Pertamina Dex', material_kode: 'PDX-06', jumlah: 170, aktivitas: 'Penerimaan', stok_awal: 120 },
        { id: 7, tanggal: '2025-08-07', user_pj: 'Rina Wijaya', asal: 'P.Layang', tujuan: 'SPBE Sentosa', material_nama: 'Minyak Tanah', material_kode: 'MT-07', jumlah: 140, aktivitas: 'Penyaluran', stok_awal: 600 },
        { id: 8, tanggal: '2025-08-07', user_pj: 'Budi Santoso', asal: 'SPBE Makmur', tujuan: 'P.Layang', material_nama: 'Asphalt Pen 60/70', material_kode: 'AP-08', jumlah: 160, aktivitas: 'Penerimaan', stok_awal: 300 },
        { id: 9, tanggal: '2025-08-06', user_pj: 'Adi Gunawan', asal: 'P.Layang', tujuan: 'SPBE Jaya', material_nama: 'Bitumen', material_kode: 'BIT-09', jumlah: 130, aktivitas: 'Penyaluran', stok_awal: 200 },
        { id: 10, tanggal: '2025-08-06', user_pj: 'Rina Wijaya', asal: 'SPBE Sentosa', tujuan: 'P.Layang', material_nama: 'Gas LPG 3 Kg (Extra)', material_kode: 'LPG-10', jumlah: 200, aktivitas: 'Penerimaan', stok_awal: 800 },
        { id: 11, tanggal: '2025-08-05', user_pj: 'Budi Santoso', asal: 'P.Layang', tujuan: 'SPBE Makmur', material_nama: 'Elpiji Industri', material_kode: 'ELP-11', jumlah: 80, aktivitas: 'Penyaluran', stok_awal: 400 },
        { id: 12, tanggal: '2025-08-05', user_pj: 'Adi Gunawan', asal: 'SPBE Sukamaju', tujuan: 'P.Layang', material_nama: 'Pelumas Meditran', material_kode: 'PLM-12', jumlah: 190, aktivitas: 'Penerimaan', stok_awal: 200 },
        { id: 13, tanggal: '2025-08-04', user_pj: 'Budi Santoso', asal: 'P.Layang', tujuan: 'SPBE Sukamaju', material_nama: 'Dexlite', material_kode: 'DEX-13', jumlah: 70, aktivitas: 'Penyaluran', stok_awal: 350 },
        { id: 14, tanggal: '2025-08-04', user_pj: 'Rina Wijaya', asal: 'SPBE Sentosa', tujuan: 'P.Layang', material_nama: 'Solar Industri', material_kode: 'SOL-14', jumlah: 100, aktivitas: 'Penerimaan', stok_awal: 50 },
        { id: 15, tanggal: '2025-08-03', user_pj: 'Adi Gunawan', asal: 'P.Layang', tujuan: 'SPBE Makmur', material_nama: 'Gas LPG 3 Kg', material_kode: 'LPG-01', jumlah: 50, aktivitas: 'Penyaluran', stok_awal: 1200 },
        { id: 16, tanggal: '2025-08-03', user_pj: 'Rina Wijaya', asal: 'SPBE Jaya', tujuan: 'P.Layang', material_nama: 'Bright Gas 12 Kg', material_kode: 'BRG-03', jumlah: 40, aktivitas: 'Penerimaan', stok_awal: 760 },
    ];
    // --- END Data Dummy ---

    let searchQuery = '';
    let currentPage = 1;
    let startDate = null;
    let endDate = null;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    // Menambahkan fungsi format tanggal yang telah disesuaikan
    const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function formatTanggal(isoDate) {
        if (!isoDate) return '-';
        const date = new Date(isoDate + 'T00:00:00');
        const dayName = hariIndonesia[date.getDay()];
        const day = date.getDate();
        const monthName = bulanIndonesia[date.getMonth()];
        const year = date.getFullYear();
        return `${dayName}, ${day} ${monthName} ${year}`;
    }
    
    // Helper function to parse 'YYYY-MM-DD' to a Date object
    function parseDateString(dateStr) {
        if (!dateStr) return null;
        const [year, month, day] = dateStr.split('-');
        return new Date(year, month - 1, day);
    }

    function filterData() {
        return dataDummyTransaksi.filter(item => {
            const matchSearch = searchQuery ?
                (item.material_nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.material_kode.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.user_pj.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.asal.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.tujuan.toLowerCase().includes(searchQuery.toLowerCase()))
                : true;
            
            const itemDate = parseDateString(item.tanggal);
            const matchDate = (!startDate || (itemDate && itemDate >= startDate)) && (!endDate || (itemDate && itemDate <= endDate));

            return matchSearch && matchDate;
        });
    }

    function renderTable() {
        const tbody = document.querySelector('#table-aktivitas-transaksi tbody');
        const noData = document.getElementById('no-data');
        const data = filterData();
        const start = (currentPage - 1) * itemsPerPage;
        const paginated = data.slice(start, start + itemsPerPage);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                const rowIndex = start + index + 1;
                const isPenerimaan = item.aktivitas === 'Penerimaan';
                const activityColor = isPenerimaan ? 'bg-gradient-success' : 'bg-gradient-primary';
                
                const stokAkhir = isPenerimaan ? item.stok_awal + item.jumlah : item.stok_awal - item.jumlah;
                const stokAwalBadge = `<span class="badge bg-secondary text-white text-xs">${item.stok_awal} pcs</span>`;
                const stokAkhirBadge = `<span class="badge bg-info text-white text-xs">${stokAkhir} pcs</span>`;

                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm font-weight-bolder">${item.material_nama}</h6>
                                <p class="text-xs text-secondary mb-0">Kode: ${item.material_kode}</p>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.asal}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.tujuan}</p>
                        </td>
                        <td class="text-center">
                            ${stokAwalBadge}
                        </td>
                        <td class="text-center">
                            <span class="badge ${activityColor} text-white text-xs">${item.jumlah} pcs</span>
                        </td>
                        <td class="text-center">
                            ${stokAkhirBadge}
                        </td>
                        <td class="text-center">
                            <span class="badge ${activityColor} text-white text-xs">${item.aktivitas}</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary mb-0">${item.user_pj}</p>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary mb-0">${formatTanggal(item.tanggal)}</p>
                        </td>
                    </tr>
                `;
            });
        }

        renderPagination(data.length);
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-aktivitas-transaksi');
        ul.innerHTML = '';

        // Function to create a pagination button
        const createButton = (label, page, disabled = false, active = false) => {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = page;
                    renderTable();
                });
            }
            return li;
        };

        if (totalPages > 1) {
            ul.appendChild(createButton('«', 1, currentPage === 1));
            ul.appendChild(createButton('‹', currentPage - 1, currentPage === 1));

            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow && totalPages >= maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                ul.appendChild(createButton(i, i, false, i === currentPage));
            }

            ul.appendChild(createButton('›', currentPage + 1, currentPage === totalPages));
            ul.appendChild(createButton('»', totalPages, currentPage === totalPages));
        }
    }

    document.getElementById('search-input').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable();
    });

    document.getElementById('start-date').addEventListener('change', function () {
        startDate = this.value ? parseDateString(this.value) : null;
        currentPage = 1;
        renderTable();
    });

    document.getElementById('end-date').addEventListener('change', function () {
        endDate = this.value ? parseDateString(this.value) : null;
        currentPage = 1;
        renderTable();
    });

    document.addEventListener('DOMContentLoaded', renderTable);
</script>
@endpush
@endsection