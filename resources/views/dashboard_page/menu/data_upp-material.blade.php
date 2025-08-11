@extends('dashboard_page.main')
@section('title', 'UPP Material') {{-- Updated title for UPP Material page --}}
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h3>Tabel Data UPP Material</h3> {{-- Updated header title --}}
                    <h6>Daftar Material yang ingin dilakukan pemusnahan</h6> {{-- Updated sub-title to be more general for materials --}}
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto">
                    {{-- Filter Range Tanggal --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="start-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                        <input type="date" id="start-date" class="form-control form-control-sm" style="width: 150px;">
                        <label for="end-date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                        <input type="date" id="end-date" class="form-control form-control-sm" style="width: 150px;">
                    </div>
                    {{-- Search --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari Nama, Kode, BPT, Sales Area/Region....." style="width: 250px; height: 55px;"> {{-- Adjusted placeholder and height --}}
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-spbe-bpt">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material & Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sales Area/Region</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Pengajuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
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
                        <ul class="pagination pagination-sm mb-0" id="pagination-spbe-bpt">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

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
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Function to generate a random code with a fixed number of digits
    function generateRandomCode(length) {
        let result = '';
        const characters = '0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    // List of Sales Area/Regions
    const saRegions = ['SA Jambi', 'SA Bengkulu', 'SA Lampung'];

    // Helper function to format date to "Hari, DD Bulan YYYY"
    const hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function formatTanggal(tanggalString) {
        const date = new Date(tanggalString + 'T00:00:00');
        const hari = hariIndonesia[date.getDay()];
        const tanggal = date.getDate();
        const bulan = bulanIndonesia[date.getMonth()];
        const tahun = date.getFullYear();
        return `${hari}, ${tanggal} ${bulan} ${tahun}`;
    }

    // Helper function to parse date string from dummy data to a Date object
    function parseDateString(dateStr) {
        if (!dateStr || dateStr === 'N/A') return null;
        const parts = dateStr.split(', ')[1].split(' ');
        const months = { 'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'Mei': 4, 'Jun': 5, 'Jul': 6, 'Agu': 7, 'Sep': 8, 'Okt': 9, 'Nov': 10, 'Des': 11 };
        return new Date(parts[2], months[parts[1]], parts[0]);
    }

    // --- REVISED dataDummy with 'keterangan' field ---
    const dataDummy = [
        { id: 1, nama: 'Gas LPG 3 Kg', kode: 'LPG3001', jenis: 'LPG', stok: 150, nama_bpt: 'BPT Jakarta Timur A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-06-23', status: 'pending', keterangan: 'Keterangan default untuk Gas LPG 3 Kg.' },
        { id: 2, nama: 'Bright Gas 12 Kg', kode: 'BG1202', jenis: 'Bright Gas', stok: 90, nama_bpt: 'BPT Jakarta Timur B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-06-28', status: 'diterima', keterangan: 'Keterangan default untuk Bright Gas 12 Kg.' },
        { id: 3, nama: 'Pelumas Fastron', kode: 'PFAS03', jenis: 'Pelumas', stok: 0, nama_bpt: 'BPT Bekasi A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-06-25', status: 'clear', keterangan: 'Keterangan default untuk Pelumas Fastron.' },
        { id: 4, nama: 'Aspal Curah', kode: 'ASPC04', jenis: 'Aspal', stok: 110, nama_bpt: 'BPT Bekasi B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-06-20', status: 'pending', keterangan: 'Keterangan default untuk Aspal Curah.' },
        { id: 5, nama: 'Avtur', kode: 'AVTR05', jenis: 'Bahan Bakar', stok: 0, nama_bpt: 'BPT Bandung A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-05', status: 'diterima', keterangan: 'Keterangan default untuk Avtur.' },
        { id: 6, nama: 'Pertalite', kode: 'PRTL06', jenis: 'Bahan Bakar', stok: 95, nama_bpt: 'BPT Bandung B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-01', status: 'belum terdaftar', keterangan: '' },
        { id: 7, nama: 'Pertamina Dex', kode: 'PDEX07', jenis: 'Bahan Bakar', stok: 170, nama_bpt: 'BPT Surabaya A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-04', status: 'clear', keterangan: 'Keterangan default untuk Pertamina Dex.' },
        { id: 8, nama: 'Minyak Tanah', kode: 'MINT08', jenis: 'Bahan Bakar', stok: 140, nama_bpt: 'BPT Surabaya B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-09', status: 'pending', keterangan: 'Keterangan default untuk Minyak Tanah.' },
        { id: 9, nama: 'Asphalt Pen 60/70', kode: 'AP60709', jenis: 'Aspal', stok: 160, nama_bpt: 'BPT Malang A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-10', status: 'diterima', keterangan: 'Keterangan default untuk Asphalt Pen 60/70.' },
        { id: 10, nama: 'Bitumen', kode: 'BITU10', jenis: 'Aspal', stok: 130, nama_bpt: 'BPT Malang B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-12', status: 'belum terdaftar', keterangan: '' },
        { id: 11, nama: 'Gas LPG 3 Kg (Extra)', kode: 'LPG311', jenis: 'LPG', stok: 200, nama_bpt: 'BPT Tangerang A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-15', status: 'diterima', keterangan: 'Keterangan default untuk Gas LPG 3 Kg (Extra).' },
        { id: 12, nama: 'Elpiji Industri', kode: 'IND012', jenis: 'Industri', stok: 80, nama_bpt: 'BPT Tangerang B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-18', status: 'pending', keterangan: 'Keterangan default untuk Elpiji Industri.' },
        { id: 13, nama: 'Pelumas Meditran', kode: 'PMED13', jenis: 'Pelumas', stok: 190, nama_bpt: 'BPT Bogor A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-23', status: 'clear', keterangan: 'Keterangan default untuk Pelumas Meditran.' },
        { id: 14, nama: 'Dexlite', kode: 'DEXL14', jenis: 'Bahan Bakar', stok: 70, nama_bpt: 'BPT Bogor B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-07-29', status: 'diterima', keterangan: 'Keterangan default untuk Dexlite.' },
        { id: 15, nama: 'Solar Industri', kode: 'SLRI15', jenis: 'Bahan Bakar', stok: 100, nama_bpt: 'BPT Cirebon A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)], tanggal_pengajuan: '2025-08-01', status: 'pending', keterangan: 'Keterangan default untuk Solar Industri.' },
    ];
    // --- END REVISED dataDummy ---

    let searchQuery = '';
    let currentPage = 1;
    let startDate = null;
    let endDate = null;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function filterData() {
        return dataDummy.filter(item => {
            const matchSearch = searchQuery ?
                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.kode.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.nama_bpt.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.cabang.toLowerCase().includes(searchQuery.toLowerCase())) : true;
            
            const itemDate = item.tanggal_pengajuan !== 'N/A' ? new Date(item.tanggal_pengajuan) : null;
            const matchDate = (!startDate || (itemDate && itemDate >= startDate)) && (!endDate || (itemDate && itemDate <= endDate));

            return matchSearch && matchDate;
        });
    }

    function renderTable() {
        const tbody = document.querySelector('#table-spbe-bpt tbody');
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
                const iconHtml = `<span class="badge bg-gradient-warning rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-cube text-white" style="font-size: 0.75rem;"></i></span>`;

                const stockDisplay = item.stok === 0 ?
                    '<span class="text-danger text-xs font-weight-bold">Stok material kosong</span>' :
                    `<span class="text-center text-xs text-secondary font-weight-bold mb-0">${item.stok} pcs</span>`;
                
                // Logic for status badge and color
                let statusColor;
                let statusText;
                let isClickable = true;
                switch(item.status) {
                    case 'pending':
                        statusColor = 'bg-gradient-warning';
                        statusText = 'Pending';
                        break;
                    case 'diterima':
                        statusColor = 'bg-gradient-success';
                        statusText = 'Diterima';
                        break;
                    case 'clear':
                        statusColor = 'bg-info';
                        statusText = 'Clear';
                        break;
                    default:
                        statusColor = 'bg-gradient-primary';
                        statusText = 'Belum Terdaftar';
                        isClickable = false;
                        break;
                }

                const cursorStyle = isClickable ? 'cursor: pointer;' : 'cursor: default;';
                const statusBadge = `<span class="badge ${statusColor} text-white text-xs font-weight-bold status-badge" style="${cursorStyle}" data-id="${item.id}" data-is-clickable="${isClickable}">${statusText}</span>`;

                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                ${iconHtml}
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm font-weight-bolder">${item.nama}</h6>
                                    <p class="text-xs text-secondary mb-0">Kode: ${item.kode}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.nama_bpt}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                        </td>
                        <td class="text-center">
                            ${stockDisplay}
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary mb-0">${formatTanggal(item.tanggal_pengajuan)}</p>
                        </td>
                        <td class="text-center">
                            ${statusBadge}
                        </td>
                        <td class="align-middle text-center d-flex gap-2 justify-content-center">
                            <span class="badge bg-gradient-info text-white text-xs preview-keterangan-btn" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#previewModal" data-id="${item.id}">
                                <i class="fas fa-eye me-1"></i> Preview
                            </span>
                            <span class="badge bg-gradient-danger text-white text-xs confirm-pemusnahan-btn" style="cursor:pointer;" 
                                data-id="${item.id}" 
                                data-material-nama="${item.nama}" 
                                data-nama-bpt="${item.nama_bpt}"
                                data-nama-cabang="${item.cabang}" 
                                data-stok="${item.stok}">Lakukan Pemusnahan</span>
                        </td>
                    </tr>
                `;
            });

            // Event listener for Status badge
            document.querySelectorAll('.status-badge').forEach(badge => {
                badge.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const isClickable = this.getAttribute('data-is-clickable') === 'true';

                    if (!isClickable) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Status Tidak Dapat Diubah',
                            text: 'Status ini tidak dapat diubah karena belum ada pengajuan pemusnahan.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    const currentStatus = dataDummy.find(item => item.id === id).status;
                    const statusOptions = {
                        'pending': 'Pending',
                        'diterima': 'Diterima',
                        'clear': 'Clear'
                    };
                    const statusBadgeClass = {
                        'pending': 'bg-gradient-warning',
                        'diterima': 'bg-gradient-success',
                        'clear': 'bg-info'
                    };

                    Swal.fire({
                        title: 'Ubah Status',
                        html: `Pilih status baru untuk material ini:`,
                        icon: 'question',
                        input: 'radio',
                        inputOptions: statusOptions,
                        inputValue: currentStatus,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan Perubahan',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        preConfirm: (newStatus) => {
                            if (!newStatus) {
                                Swal.showValidationMessage('Anda harus memilih salah satu status');
                                return false;
                            }
                            return newStatus;
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const itemIndex = dataDummy.findIndex(item => item.id === id);
                            if (itemIndex > -1) {
                                dataDummy[itemIndex].status = result.value;
                            }
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Status berhasil diubah menjadi ${statusOptions[result.value]}.`,
                                icon: 'success'
                            });
                            renderTable();
                        }
                    });
                });
            });

            // Event listener for Pemusnahan button
            document.querySelectorAll('.confirm-pemusnahan-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const materialNama = this.getAttribute('data-material-nama');
                    const namaBPT = this.getAttribute('data-nama-bpt');
                    const namaCabang = this.getAttribute('data-nama-cabang');
                    const stokMaterial = parseInt(this.getAttribute('data-stok'));

                    Swal.fire({
                        title: 'Konfirmasi Pemusnahan',
                        html: `Apakah Anda yakin ingin melakukan pemusnahan material <strong>${materialNama}</strong><br>dari BPT <strong>${namaBPT}</strong> di **Sales Area/Region** <strong>${namaCabang}</strong>?<br>Stok saat ini: <strong>${stokMaterial} pcs</strong>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Pemusnahan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/keterangan-pemusnahan?material_id=${id}&material_nama=${encodeURIComponent(materialNama)}&nama_bpt=${encodeURIComponent(namaBPT)}&nama_cabang=${encodeURIComponent(namaCabang)}&stok_material=${stokMaterial}`;
                        }
                    });
                });
            });

            // New Event listener for Preview button
            document.querySelectorAll('.preview-keterangan-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const item = dataDummy.find(item => item.id === id);

                    if (item) {
                        document.getElementById('modal-material-id').value = item.id;
                        document.getElementById('modal-material-name').innerText = item.nama;
                        document.getElementById('modal-keterangan').value = item.keterangan;
                    }
                });
            });
        }

        renderPagination(data.length);
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-spbe-bpt');
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

    document.addEventListener('DOMContentLoaded', function() {
        // No dropdown filters, so no need for their text initialization
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

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
        });
    });

    // Event listeners for search and date filters
    document.getElementById('search-input').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable();
    });

    document.getElementById('start-date').addEventListener('change', function () {
        startDate = this.value ? new Date(this.value) : null;
        currentPage = 1;
        renderTable();
    });

    document.getElementById('end-date').addEventListener('change', function () {
        endDate = this.value ? new Date(this.value) : null;
        currentPage = 1;
        renderTable();
    });

    // Initial render
    renderTable();
</script>
@endpush
@endsection