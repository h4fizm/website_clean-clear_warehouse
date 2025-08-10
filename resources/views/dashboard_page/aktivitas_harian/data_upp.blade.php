@extends('dashboard_page.main')
@section('title', 'Aktivitas Log Harian UPP')
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
                    Aktivitas Log Harian UPP
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Laporan detail semua aktivitas pengajuan dan persetujuan pemusnahan material.
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
                    <h4>Tabel Aktivitas Pengajuan UPP</h4>
                    <h6>Data riwayat pengajuan pemusnahan material.</h6>
                </div>
                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center mt-2 mt-md-0">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </button>
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <div class="d-flex flex-wrap gap-2 mb-3 px-3 align-items-center justify-content-between">
                    {{-- Search (Pojok Kiri) --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari Nama, Kode, BPT, Sales Area..." style="width: 600px; height: 35px;">
                    {{-- Filter Range Tanggal (Pojok Kanan) --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="start-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                        <input type="date" id="start-date" class="form-control form-control-sm" style="width: 150px; height: 35px;">
                        <label for="end-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                        <input type="date" id="end-date" class="form-control form-control-sm" style="width: 150px; height: 35px;">
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-aktivitas-upp">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Material & Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sales Area/Region</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Pengajuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Preview</th>
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
                        <ul class="pagination pagination-sm mb-0" id="pagination-aktivitas-upp">
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
    // --- Data Dummy Aktivitas UPP ---
    const dataDummyUPP = [
        { id: 1, material_nama: 'Gas LPG 3 Kg', material_kode: 'LPG-01', nama_bpt: 'BPT Jakarta Timur A', cabang: 'SA Jambi', stok_akhir: 150, tgl_pengajuan: '2025-08-10', status: 'pending' },
        { id: 2, material_nama: 'Pelumas Fastron', material_kode: 'PLS-02', nama_bpt: 'BPT Bekasi A', cabang: 'SA Bengkulu', stok_akhir: 50, tgl_pengajuan: '2025-08-09', status: 'ditolak' },
        { id: 3, material_nama: 'Bright Gas 12 Kg', material_kode: 'BRG-03', nama_bpt: 'BPT Jakarta Timur B', cabang: 'SA Lampung', stok_akhir: 90, tgl_pengajuan: '2025-08-08', status: 'diterima' },
        { id: 4, material_nama: 'Aspal Curah', material_kode: 'ASP-04', nama_bpt: 'BPT Bekasi B', cabang: 'SA Jambi', stok_akhir: 110, tgl_pengajuan: '2025-08-07', status: 'pending' },
        { id: 5, material_nama: 'Pertalite', material_kode: 'PRT-05', nama_bpt: 'BPT Bandung B', cabang: 'SA Bengkulu', stok_akhir: 95, tgl_pengajuan: '2025-08-06', status: 'ditolak' },
        { id: 6, material_nama: 'Pertamina Dex', material_kode: 'PDX-06', nama_bpt: 'BPT Surabaya A', cabang: 'SA Lampung', stok_akhir: 170, tgl_pengajuan: '2025-08-05', status: 'diterima' },
        { id: 7, material_nama: 'Minyak Tanah', material_kode: 'MT-07', nama_bpt: 'BPT Surabaya B', cabang: 'SA Jambi', stok_akhir: 140, tgl_pengajuan: '2025-08-04', status: 'pending' },
        { id: 8, material_nama: 'Asphalt Pen 60/70', material_kode: 'AP-08', nama_bpt: 'BPT Malang A', cabang: 'SA Bengkulu', stok_akhir: 160, tgl_pengajuan: '2025-08-03', status: 'diterima' },
        { id: 9, material_nama: 'Bitumen', material_kode: 'BIT-09', nama_bpt: 'BPT Malang B', cabang: 'SA Lampung', stok_akhir: 130, tgl_pengajuan: '2025-08-02', status: 'pending' },
        { id: 10, material_nama: 'Elpiji Industri', material_kode: 'ELP-11', nama_bpt: 'BPT Tangerang B', cabang: 'SA Jambi', stok_akhir: 80, tgl_pengajuan: '2025-08-01', status: 'ditolak' },
        { id: 11, material_nama: 'Pelumas Meditran', material_kode: 'PLM-12', nama_bpt: 'BPT Bogor A', cabang: 'SA Bengkulu', stok_akhir: 190, tgl_pengajuan: '2025-07-31', status: 'diterima' },
        { id: 12, material_nama: 'Dexlite', material_kode: 'DEX-13', nama_bpt: 'BPT Bogor B', cabang: 'SA Lampung', stok_akhir: 70, tgl_pengajuan: '2025-07-30', status: 'pending' },
        { id: 13, material_nama: 'Solar Industri', material_kode: 'SOL-14', nama_bpt: 'BPT Cirebon A', cabang: 'SA Jambi', stok_akhir: 100, tgl_pengajuan: '2025-07-29', status: 'ditolak' },
    ];
    // --- END Data Dummy ---

    let searchQuery = '';
    let currentPage = 1;
    let startDate = null;
    let endDate = null;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function formatTanggal(isoDate) {
        if (!isoDate) return '-';
        const date = new Date(isoDate);
        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const dayName = days[date.getDay()];
        const day = String(date.getDate()).padStart(2, '0');
        const monthName = months[date.getMonth()];
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
        return dataDummyUPP.filter(item => {
            const matchSearch = searchQuery ?
                                (item.material_nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.material_kode.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.nama_bpt.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.cabang.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            
            const itemDate = parseDateString(item.tgl_pengajuan);
            const matchDate = (!startDate || (itemDate && itemDate >= startDate)) && (!endDate || (itemDate && itemDate <= endDate));

            return matchSearch && matchDate;
        });
    }

    function renderTable() {
        const tbody = document.querySelector('#table-aktivitas-upp tbody');
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
                
                // Logic for status badge and color
                let statusColor;
                switch(item.status) {
                    case 'pending':
                        statusColor = 'bg-gradient-warning';
                        break;
                    case 'diterima':
                        statusColor = 'bg-gradient-success';
                        break;
                    case 'ditolak':
                        statusColor = 'bg-gradient-danger';
                        break;
                }
                const statusText = item.status.charAt(0).toUpperCase() + item.status.slice(1);
                const statusBadge = `<span class="badge ${statusColor} text-white text-xs font-weight-bold">${statusText}</span>`;

                // Buat URL untuk halaman preview dengan query parameter
                const previewUrl = `/preview-upp?material_id=${item.id}&material_nama=${encodeURIComponent(item.material_nama)}&nama_bpt=${encodeURIComponent(item.nama_bpt)}&cabang=${encodeURIComponent(item.cabang)}&tgl_pengajuan=${encodeURIComponent(item.tgl_pengajuan)}&status=${encodeURIComponent(item.status)}`;

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
                            <p class="text-xs font-weight-bold mb-0">${item.nama_bpt}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-secondary text-white text-xs">${item.stok_akhir} pcs</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary mb-0">${formatTanggal(item.tgl_pengajuan)}</p>
                        </td>
                        <td class="text-center">
                            ${statusBadge}
                        </td>
                        <td class="text-center">
                            <a href="${previewUrl}" class="btn btn-sm btn-primary text-white">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        renderPagination(data.length);
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-aktivitas-upp');
        ul.innerHTML = '';

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