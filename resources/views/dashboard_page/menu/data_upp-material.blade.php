@extends('dashboard_page.main')
@section('title', 'UPP Material')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h3>Tabel Data UPP Material</h3>
                    <h6>Daftar Usulan Pemusnahan Material</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto">
                    {{-- Tombol Tambah UPP --}}
                    <a href="{{ url('/upp-material/tambah') }}" class="px-3 py-2 bg-primary text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;" id="tambah-upp-btn">
                        <i class="fas fa-plus me-2"></i>Tambah UPP
                    </a>
                    {{-- Tombol Export Excel --}}
                    <span class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;" id="export-excel-btn" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </span>
                </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2">
                <div class="d-flex justify-content-between align-items-center px-4 py-2 flex-wrap">
                    <div class="row mb-3 align-items-center w-100">
                        {{-- Input Search --}}
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" 
                                    class="form-control" 
                                    placeholder="Cari No.Surat...">
                            </div>
                        </div>

                        {{-- Date Range + Filter Button --}}
                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-md-end">
                            {{-- Start Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" id="startDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;">
                            </div>

                            {{-- End Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" id="endDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;">
                            </div>

                            {{-- Button Filter --}}
                            <div class="align-self-end">
                                <button id="filter-btn" class="btn btn-primary btn-sm px-3">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Table contents --}}
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-upp-material">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Surat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahapan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Buat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Update Terakhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here --}}
                        </tbody>
                    </table>
                    <div id="no-data" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
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

{{-- MODAL PREVIEW BARU --}}
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

{{-- Modal untuk Export Excel --}}
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

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Data Dummy Utama dengan Detail Material ---
        const dataDummy = [
            {
                id: 1, 
                tgl_buat: '2025-08-01', 
                no_surat: 'UPP-202508-001', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-05', 
                keterangan: 'Usulan pemusnahan material Gas LPG 3 Kg dan Bright Gas 12 Kg.',
                materials: [
                    { nama: 'Gas LPG 3 Kg', kode: 'LPG001', stok_saat_ini: 150, stok_diambil: 50 },
                    { nama: 'Bright Gas 12 Kg', kode: 'BRG002', stok_saat_ini: 80, stok_diambil: 30 },
                    { nama: 'Gas LPG 12 Kg', kode: 'LPG003', stok_saat_ini: 65, stok_diambil: 15 }
                ]
            },
            {
                id: 2, 
                tgl_buat: '2025-07-25', 
                no_surat: 'UPP-202507-005', 
                tahapan: 'Verifikasi', 
                status: 'Proses', 
                tgl_update: '2025-07-28', 
                keterangan: 'Menunggu persetujuan tim verifikasi untuk pemusnahan Pelumas Fastron.',
                materials: [
                    { nama: 'Pelumas Fastron Gold', kode: 'PFS001', stok_saat_ini: 250, stok_diambil: 100 },
                    { nama: 'Pelumas Fastron Diesel', kode: 'PFS002', stok_saat_ini: 180, stok_diambil: 50 },
                    { nama: 'Pelumas Fastron Eco Green', kode: 'PFS003', stok_saat_ini: 210, stok_diambil: 70 }
                ]
            },
            {
                id: 3, 
                tgl_buat: '2025-07-15', 
                no_surat: 'UPP-202507-003', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-07-18', 
                keterangan: 'Pemusnahan Aspal Curah telah selesai dilaksanakan.',
                materials: [
                    { nama: 'Aspal Curah', kode: 'ASP001', stok_saat_ini: 0, stok_diambil: 500 }
                ]
            },
            {
                id: 4, 
                tgl_buat: '2025-07-10', 
                no_surat: 'UPP-202507-002', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-07-11', 
                keterangan: 'Usulan pemusnahan Avtur dan Pertalite.',
                materials: [
                    { nama: 'Avtur', kode: 'AVT001', stok_saat_ini: 5000, stok_diambil: 1000 },
                    { nama: 'Pertalite', kode: 'PRT001', stok_saat_ini: 8000, stok_diambil: 2500 }
                ]
            },
            {
                id: 5, 
                tgl_buat: '2025-06-20', 
                no_surat: 'UPP-202506-001', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-06-25', 
                keterangan: 'Pemusnahan Pertamina Dex dan Minyak Tanah telah selesai.',
                materials: [
                    { nama: 'Pertamina Dex', kode: 'PDX001', stok_saat_ini: 0, stok_diambil: 300 },
                    { nama: 'Minyak Tanah', kode: 'MTA001', stok_saat_ini: 0, stok_diambil: 200 }
                ]
            },
            {
                id: 6, 
                tgl_buat: '2025-08-10', 
                no_surat: 'UPP-202508-002', 
                tahapan: 'Verifikasi', 
                status: 'Proses', 
                tgl_update: '2025-08-11', 
                keterangan: 'Usulan pemusnahan material Asphalt Pen 60/70.',
                materials: [
                    { nama: 'Asphalt Pen 60/70', kode: 'ASPH67', stok_saat_ini: 900, stok_diambil: 150 },
                    { nama: 'Asphalt Pen 80/100', kode: 'ASPH80', stok_saat_ini: 750, stok_diambil: 100 }
                ]
            },
            {
                id: 7, 
                tgl_buat: '2025-08-08', 
                no_surat: 'UPP-202508-003', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-08-10', 
                keterangan: 'Pemusnahan Bitumen telah selesai.',
                materials: [
                    { nama: 'Bitumen', kode: 'BITU01', stok_saat_ini: 0, stok_diambil: 200 }
                ]
            },
            {
                id: 8, 
                tgl_buat: '2025-08-12', 
                no_surat: 'UPP-202508-004', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-12', 
                keterangan: 'Usulan pemusnahan Elpiji Industri.',
                materials: [
                    { nama: 'Elpiji Industri 50 Kg', kode: 'ELI050', stok_saat_ini: 100, stok_diambil: 20 },
                    { nama: 'Elpiji Industri 12 Kg', kode: 'ELI012', stok_saat_ini: 150, stok_diambil: 50 },
                    { nama: 'Elpiji Industri 3 Kg', kode: 'ELI003', stok_saat_ini: 200, stok_diambil: 80 }
                ]
            },
            {
                id: 9, 
                tgl_buat: '2025-07-01', 
                no_surat: 'UPP-202507-001', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-07-03', 
                keterangan: 'Pemusnahan Pelumas Meditran telah selesai.',
                materials: [
                    { nama: 'Pelumas Meditran', kode: 'PMT001', stok_saat_ini: 0, stok_diambil: 80 },
                    { nama: 'Pelumas Meditran SX', kode: 'PMT002', stok_saat_ini: 0, stok_diambil: 45 }
                ]
            },
            {
                id: 10, 
                tgl_buat: '2025-08-11', 
                no_surat: 'UPP-202508-005', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-11', 
                keterangan: 'Usulan pemusnahan Solar Industri.',
                materials: [
                    { nama: 'Solar Industri', kode: 'SIL001', stok_saat_ini: 1500, stok_diambil: 500 }
                ]
            },
            {
                id: 11,
                tgl_buat: '2025-08-15',
                no_surat: 'UPP-202508-006',
                tahapan: 'Pengajuan',
                status: 'Proses',
                tgl_update: '2025-08-15',
                keterangan: 'Dokumen pengajuan untuk material baru.',
                materials: [
                    { nama: 'Oli Mesin', kode: 'OLM001', stok_saat_ini: 300, stok_diambil: 120 },
                    { nama: 'Oli Transmisi', kode: 'OLT002', stok_saat_ini: 250, stok_diambil: 80 },
                    { nama: 'BBM Pertamax', kode: 'BBM001', stok_saat_ini: 5000, stok_diambil: 100 }
                ]
            }
        ];

        dataDummy.sort((a, b) => new Date(b.tgl_buat) - new Date(a.tgl_buat));

        const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function formatTanggal(tanggalString) {
            if (!tanggalString) return 'N/A';
            const date = new Date(tanggalString + 'T00:00:00');
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

        let searchQuery = '';
        let currentPage = 1;
        let startDate = null;
        let endDate = null;
        const itemsPerPage = 10;
        const maxPagesToShow = 5;

        function filterData(dataToFilter = dataDummy) {
            return dataToFilter.filter(item => {
                const matchSearch = searchQuery ?
                    item.no_surat.toLowerCase().includes(searchQuery.toLowerCase()) : true;
                
                const itemDate = parseDateString(item.tgl_buat);
                const matchDate = (!startDate || (itemDate && itemDate >= startDate)) && (!endDate || (itemDate && itemDate <= endDate));

                return matchSearch && matchDate;
            });
        }
        
        function renderTable() {
            const tbody = document.querySelector('#table-upp-material tbody');
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
                    const statusText = item.status.toLowerCase() === 'done' ? 'Done' : 'Proses';
                    const statusColor = item.status.toLowerCase() === 'proses' ? 'bg-gradient-warning' : 'bg-gradient-success';
                    const statusBadge = `<span class="badge ${statusColor} text-white text-xs font-weight-bold status-badge" style="cursor: pointer;" data-id="${item.id}" data-status="${item.status}">${statusText}</span>`;
                    
                    const previewBadge = `<span class="badge bg-gradient-info text-white text-xs preview-keterangan-btn" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#previewModal" data-id="${item.id}">
                                             <i class="fas fa-eye me-1"></i> Preview
                                           </span>`;

                    tbody.innerHTML += `
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
            renderPagination(data.length);
        }

        function renderPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const ul = document.getElementById('pagination-upp-material');
            ul.innerHTML = '';

            const firstPageItem = document.createElement('li');
            firstPageItem.classList.add('page-item');
            if (currentPage === 1 || totalPages === 0) firstPageItem.classList.add('disabled');
            firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">«</a>`;
            firstPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage !== 1) {
                    currentPage = 1;
                    renderTable();
                }
            });
            ul.appendChild(firstPageItem);

            const prevPageItem = document.createElement('li');
            prevPageItem.classList.add('page-item');
            if (currentPage === 1 || totalPages === 0) prevPageItem.classList.add('disabled');
            prevPageItem.innerHTML = `<a class="page-link" href="#"><</a>`;
            prevPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            });
            ul.appendChild(prevPageItem);

            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow && totalPages >= maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }
            
            if (totalPages < maxPagesToShow) {
                startPage = 1;
                endPage = totalPages;
            }

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.classList.add('page-item');
                if (i === currentPage) li.classList.add('active');
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = i;
                    renderTable();
                });
                ul.appendChild(li);
            }

            const nextPageItem = document.createElement('li');
            nextPageItem.classList.add('page-item');
            if (currentPage === totalPages || totalPages === 0) nextPageItem.classList.add('disabled');
            nextPageItem.innerHTML = `<a class="page-link" href="#">></a>`;
            nextPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            });
            ul.appendChild(nextPageItem);

            const lastPageItem = document.createElement('li');
            lastPageItem.classList.add('page-item');
            if (currentPage === totalPages || totalPages === 0) lastPageItem.classList.add('disabled');
            lastPageItem.innerHTML = `<a class="page-link" href="#" aria-label="Last">»</a>`;
            lastPageItem.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage !== totalPages) {
                    currentPage = totalPages;
                    renderTable();
                }
            });
            ul.appendChild(lastPageItem);
        }

        document.getElementById('searchInput').addEventListener('input', function () {
            searchQuery = this.value;
            currentPage = 1;
            renderTable();
        });

        document.getElementById('filter-btn').addEventListener('click', function() {
            const startDateInput = document.getElementById('startDate').value;
            const endDateInput = document.getElementById('endDate').value;
            
            startDate = startDateInput ? new Date(startDateInput) : null;
            endDate = endDateInput ? new Date(endDateInput) : null;

            if (startDate && endDate && startDate > endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Filter',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.',
                });
                return;
            }
            currentPage = 1;
            renderTable();
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
                    html: `
                        <p class="text-muted text-center font-weight-bolder">Pilih status baru untuk UPP ini :</p>
                    `,
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
                            const now = new Date();
                            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                            const formattedDate = new Intl.DateTimeFormat('id-ID', dateOptions).format(now);
                            itemToUpdate.tgl_update = formattedDate;

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `Status berhasil diperbarui menjadi '${newStatus}'.`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            renderTable();
                        }
                    }
                });
            }
        });

        document.getElementById('table-upp-material').addEventListener('click', function(event) {
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
                        // Menambahkan data ID ke tombol "Lakukan Pemusnahan"
                        pemusnahanBtn.setAttribute('data-id', item.id);
                    }
                }
            }
        });

        // Modifikasi Event listener untuk tombol "Lakukan Pemusnahan" di modal
        document.getElementById('lakukan-pemusnahan-btn').addEventListener('click', function() {
            const id = this.getAttribute('data-id'); // Mengambil ID dari tombol
            window.location.href = `/upp-material/preview?id=${id}`;
        });

        document.getElementById('confirmExportBtn').addEventListener('click', function() {
            const exportStartDateInput = document.getElementById('exportStartDate').value;
            const exportEndDateInput = document.getElementById('exportEndDate').value;

            const exportStartDate = exportStartDateInput ? new Date(exportStartDateInput) : null;
            const exportEndDate = exportEndDateInput ? new Date(exportEndDateInput) : null;
            
            if (exportStartDate && exportEndDate && exportStartDate > exportEndDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Export',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.',
                });
                return;
            }
            
            const filteredDataForExport = dataDummy.filter(item => {
                const itemDate = parseDateString(item.tgl_buat);
                return (!exportStartDate || (itemDate && itemDate >= exportStartDate)) && (!exportEndDate || (itemDate && itemDate <= exportEndDate));
            });

            if (filteredDataForExport.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Kosong',
                    text: 'Tidak ada data yang dapat diekspor pada rentang tanggal tersebut.',
                });
                return;
            }
            
            const exportData = filteredDataForExport.map(item => ({
                'No. Surat': item.no_surat,
                'Tahapan': item.tahapan,
                'Status': item.status,
                'Tanggal Buat': formatTanggal(item.tgl_buat),
                'Tanggal Update Terakhir': formatTanggal(item.tgl_update),
                'Keterangan': item.keterangan
            }));

            const worksheet = XLSX.utils.json_to_sheet(exportData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Data UPP Material');

            const now = new Date();
            const dateStr = now.getFullYear() + '-' + (now.getMonth() + 1).toString().padStart(2, '0') + '-' + now.getDate().toString().padStart(2, '0');
            const filename = `Data_UPP_Material_${dateStr}.xlsx`;
            
            XLSX.writeFile(workbook, filename);

            const myModalEl = document.getElementById('exportExcelModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diekspor ke Excel.',
                showConfirmButton: false,
                timer: 1500
            });
        });

        renderTable();
    });
</script>
@endpush