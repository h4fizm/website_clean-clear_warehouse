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
                    <a href="{{ url('/upp-material/tambah') }}" class="btn btn-sm bg-gradient-primary mt-1 mb-0" id="tambah-upp-btn">
                        <i class="fas fa-plus me-2"></i>Tambah UPP
                    </a>
                    {{-- Tombol Export Excel --}}
                    <button class="btn btn-sm bg-gradient-success mt-1 mb-0" id="export-excel-btn">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2">
                <div class="d-flex justify-content-between align-items-center px-4 py-2 flex-wrap">
                    {{-- Search Bar --}}
                    <div class="d-flex flex-wrap gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari No.Surat...">
                        </div>
                    </div>
                    {{-- Date Picker and Filter Button on the right --}}
                    <div class="d-flex align-items-end gap-3 ms-auto">
                        <label for="start-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari :</label>
                        <input type="date" id="start-date" class="form-control form-control-sm" style="width: 150px;">
                        <label for="end-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai :</label>
                        <input type="date" id="end-date" class="form-control form-control-sm" style="width: 150px;">
                        <button id="filter-btn" class="btn btn-sm btn-primary mb-0">Filter</button>
                    </div>
                </div>

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
                <div class="mt-3 px-3 d-flex justify-content-center">
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

{{-- MODAL PREVIEW TIDAK SAYA UBAH KARENA SUDAH SESUAI DENGAN PERMINTAAN SEBELUMNYA --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Detail Keterangan UPP Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-keterangan-form">
                    <input type="hidden" id="modal-material-id">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Material:</label>
                        <p id="modal-material-name" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label for="modal-keterangan" class="form-label font-weight-bold">Keterangan:</label>
                        <textarea class="form-control" id="modal-keterangan" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="save-keterangan-btn">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Data Dummy Baru Sesuai Kolom Baru (Total 11) dan Diurutkan ---
        const dataDummy = [
            {
                id: 1, 
                tgl_buat: '2025-08-01', 
                no_surat: 'UPP-202508-001', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-05', 
                keterangan: 'Usulan pemusnahan material Gas LPG 3 Kg dan Bright Gas 12 Kg.'
            },
            {
                id: 2, 
                tgl_buat: '2025-07-25', 
                no_surat: 'UPP-202507-005', 
                tahapan: 'Verifikasi', 
                status: 'Proses', 
                tgl_update: '2025-07-28', 
                keterangan: 'Menunggu persetujuan tim verifikasi untuk pemusnahan Pelumas Fastron.'
            },
            {
                id: 3, 
                tgl_buat: '2025-07-15', 
                no_surat: 'UPP-202507-003', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-07-18', 
                keterangan: 'Pemusnahan Aspal Curah telah selesai dilaksanakan.'
            },
            {
                id: 4, 
                tgl_buat: '2025-07-10', 
                no_surat: 'UPP-202507-002', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-07-11', 
                keterangan: 'Usulan pemusnahan Avtur dan Pertalite.'
            },
            {
                id: 5, 
                tgl_buat: '2025-06-20', 
                no_surat: 'UPP-202506-001', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-06-25', 
                keterangan: 'Pemusnahan Pertamina Dex dan Minyak Tanah telah selesai.'
            },
            {
                id: 6, 
                tgl_buat: '2025-08-10', 
                no_surat: 'UPP-202508-002', 
                tahapan: 'Verifikasi', 
                status: 'Proses', 
                tgl_update: '2025-08-11', 
                keterangan: 'Usulan pemusnahan material Asphalt Pen 60/70.'
            },
            {
                id: 7, 
                tgl_buat: '2025-08-08', 
                no_surat: 'UPP-202508-003', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-08-10', 
                keterangan: 'Pemusnahan Bitumen telah selesai.'
            },
            {
                id: 8, 
                tgl_buat: '2025-08-12', 
                no_surat: 'UPP-202508-004', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-12', 
                keterangan: 'Usulan pemusnahan Elpiji Industri.'
            },
            {
                id: 9, 
                tgl_buat: '2025-07-01', 
                no_surat: 'UPP-202507-001', 
                tahapan: 'Pemusnahan', 
                status: 'Done', 
                tgl_update: '2025-07-03', 
                keterangan: 'Pemusnahan Pelumas Meditran telah selesai.'
            },
            {
                id: 10, 
                tgl_buat: '2025-08-11', 
                no_surat: 'UPP-202508-005', 
                tahapan: 'Pengajuan', 
                status: 'Proses', 
                tgl_update: '2025-08-11', 
                keterangan: 'Usulan pemusnahan Solar Industri.'
            },
            // Tambahan data dummy baru
            {
                id: 11,
                tgl_buat: '2025-08-15',
                no_surat: 'UPP-202508-006',
                tahapan: 'Pengajuan',
                status: 'Proses',
                tgl_update: '2025-08-15',
                keterangan: 'Dokumen pengajuan untuk material baru.'
            }
        ];

        // Urutkan data berdasarkan tanggal buat secara menurun (terbaru ke terlama)
        dataDummy.sort((a, b) => new Date(b.tgl_buat) - new Date(a.tgl_buat));

        // --- Helper Functions ---
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

        function filterData() {
            return dataDummy.filter(item => {
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
                    const statusColor = item.status.toLowerCase() === 'proses' ? 'bg-gradient-warning' : 'bg-gradient-success';
                    const statusBadge = `<span class="badge ${statusColor} text-white text-xs font-weight-bold">${item.status}</span>`;
                    
                    // Badge Preview
                    const previewBadge = `<span class="badge bg-gradient-info text-white text-xs preview-keterangan-btn" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#previewModal" data-id="${item.id}"><i class="fas fa-eye me-1"></i> Preview</span>`;

                    // Badge Lakukan Pemusnahan yang baru ditambahkan
                    const pemusnahanBadge = `<span class="badge bg-gradient-danger text-white text-xs ms-1" style="cursor:pointer;">Lakukan Pemusnahan</span>`;

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
                            <td class="align-middle text-center d-flex justify-content-center">
                                ${previewBadge}
                                ${pemusnahanBadge}
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

        // --- Event Listeners ---
        document.getElementById('search-input').addEventListener('input', function () {
            searchQuery = this.value;
            currentPage = 1;
            renderTable();
        });

        document.getElementById('filter-btn').addEventListener('click', function() {
            const startDateInput = document.getElementById('start-date').value;
            const endDateInput = document.getElementById('end-date').value;
            
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
        
        // Event listener for Preview button using event delegation
        document.getElementById('table-upp-material').addEventListener('click', function(event) {
            if (event.target.closest('.preview-keterangan-btn')) {
                const button = event.target.closest('.preview-keterangan-btn');
                const id = parseInt(button.getAttribute('data-id'));
                const item = dataDummy.find(item => item.id === id);

                if (item) {
                    document.getElementById('modal-material-id').value = item.id;
                    document.getElementById('modal-material-name').innerText = `No. Surat: ${item.no_surat}`;
                    document.getElementById('modal-keterangan').value = item.keterangan;
                }
            }
        });

        // Event listener for Save button in the modal
        document.getElementById('save-keterangan-btn').addEventListener('click', function() {
            const materialId = parseInt(document.getElementById('modal-material-id').value);
            const newKeterangan = document.getElementById('modal-keterangan').value;

            // Find the item and update the data
            const itemToUpdate = dataDummy.find(item => item.id === materialId);
            if (itemToUpdate) {
                itemToUpdate.keterangan = newKeterangan;
            }

            // Close the modal
            const myModalEl = document.getElementById('previewModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();

            // Show success notification
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Keterangan berhasil diperbarui.',
                showConfirmButton: false,
                timer: 1500
            });
            renderTable(); // Re-render table to reflect changes
        });

        // Initial render
        renderTable();
    });
</script>
@endpush