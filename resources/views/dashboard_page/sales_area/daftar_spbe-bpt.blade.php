@extends('dashboard_page.main')

@section('title', 'Data SPBE & BPT - P.Layang Pusat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-start flex-wrap spbe-header">
                <div class="d-flex flex-column spbe-title-section">
                    <h3>Daftar Nama SPBE & BPT - P.Layang (Pusat)</h3>
                    <h6 class="text-secondary opacity-8 spbe-subtitle">Menampilkan daftar SPBE/BPT dari Setiap Region/SA, **kecuali** P.Layang itu sendiri.</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto spbe-search-section">
                    <input type="text" id="search-input" class="form-control form-control-sm search-input-spbe" placeholder="Cari Nama SPBE / BPT, Kode Plant, Kabupaten">
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0"> {{-- Removed .table-responsive-custom and no custom overflow here --}}
                    <table class="table align-items-center mb-0" id="table-spbe-bpt">
                        <thead>
                            <tr>
                                {{-- Widths are as per desktop, let Bootstrap's table-responsive handle overflow --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 5%;">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="width: 28%;">Nama SPBE / BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="width: 17%;">Kode Plant</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="width: 18%;">Region/Sales Area</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 17%;">Nama Kabupaten</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Aksi</th>
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

{{-- Update Data Modal --}}
<div class="modal fade" id="updateDataModal" tabindex="-1" aria-labelledby="updateDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateDataModalLabel">Update Data SPBE / BPT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateDataForm">
                    <input type="hidden" id="updateId">
                    <div class="mb-3">
                        <label for="updateNama" class="form-label">Nama SPBE / BPT</label>
                        <input type="text" class="form-control form-control-sm" id="updateNama" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateJenis" class="form-label">Jenis</label>
                        <select class="form-select form-control-sm" id="updateJenis" required>
                            <option value="SPBE">SPBE</option>
                            <option value="BPT">BPT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateKodePlant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control form-control-sm" id="updateKodePlant" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateSalesArea" class="form-label">Region/Sales Area</label>
                        <select class="form-select form-control-sm" id="updateSalesArea" required>
                            <option value="P.Layang">P.Layang</option>
                            <option value="SA Jambi">SA Jambi</option>
                            <option value="SA Bengkulu">SA Bengkulu</option>
                            <option value="SA Lampung">SA Lampung</option>
                            <option value="SA Sumsel">SA Sumsel</option>
                            <option value="SA Babel">SA Babel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateKabupaten" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control form-control-sm" id="updateKabupaten" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary btn-sm" form="updateDataForm">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Stok & Transaksi Modal --}}
<div class="modal fade" id="stockTransactionModal" tabindex="-1" aria-labelledby="stockTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100" id="stockTransactionModalLabel">Detail Stok & Transaksi untuk <span id="modal-spbe-bpt-name" class="fw-bold text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- SPBE/BPT Information --}}
                <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-3">INFORMASI SPBE/BPT</h6>
                <input type="hidden" id="transactionSpbeId">
                
                <div class="info-row d-flex mb-1 align-items-baseline">
                    <div class="info-label flex-shrink-0">
                        <strong class="text-secondary">Nama SPBE / BPT</strong>
                    </div>
                    <strong class="text-secondary info-colon me-2 flex-shrink-0">:</strong>
                    <div class="info-value flex-grow-1">
                        <span class="text-dark" id="modal-spbe-bpt-detail-name-body"></span>
                    </div>
                </div>
                
                <div class="info-row d-flex mb-1 align-items-baseline">
                    <div class="info-label flex-shrink-0">
                        <strong class="text-secondary">Kode Plant</strong>
                    </div>
                    <strong class="text-secondary info-colon me-2 flex-shrink-0">:</strong>
                    <div class="info-value flex-grow-1">
                        <span class="text-dark" id="modal-kode-plant"></span>
                    </div>
                </div>

                <div class="info-row d-flex mb-1 align-items-baseline">
                    <div class="info-label flex-shrink-0">
                        <strong class="text-secondary">Region/SA</strong>
                    </div>
                    <strong class="text-secondary info-colon me-2 flex-shrink-0">:</strong>
                    <div class="info-value flex-grow-1">
                        <span class="text-dark" id="modal-sales-area"></span>
                    </div>
                </div>

                <div class="info-row d-flex mb-3 align-items-baseline">
                    <div class="info-label flex-shrink-0">
                        <strong class="text-secondary">Kabupaten</strong>
                    </div>
                    <strong class="text-secondary info-colon me-2 flex-shrink-0">:</strong>
                    <div class="info-value flex-grow-1">
                        <span class="text-dark" id="modal-kabupaten"></span>
                    </div>
                </div>

                <hr class="my-3">

                {{-- Stock and Transaction Form --}}
                <div class="info-row d-flex mb-3 align-items-baseline">
                    <div class="info-label flex-shrink-0">
                        <strong class="text-secondary">Stok Saat Ini</strong>
                    </div>
                    <strong class="text-secondary info-colon me-2 flex-shrink-0">:</strong>
                    <div class="info-value flex-grow-1">
                        <span class="fs-5 text-success" id="modal-current-stock-display"></span>
                    </div>
                </div>

                <form id="stockTransactionForm">
                    <div class="mb-3">
                        <label for="transactionType" class="form-label">Jenis Transaksi</label>
                        <select class="form-select form-control-sm" id="transactionType" required>
                            <option value="">Pilih Jenis Transaksi</option>
                            <option value="Pengiriman">Pengiriman (Tambah Stok)</option>
                            <option value="Pengambilan">Pengambilan (Kurangi Stok)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="stockAmount" class="form-label">Jumlah Stok yang Digunakan</label>
                        <input type="number" class="form-control form-control-sm" id="stockAmount" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary btn-sm" form="stockTransactionForm">Proses Transaksi</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function generateRandomCode(length) {
        let result = '';
        const characters = '0123456789';
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }

    function generateRandomStock() {
        return Math.floor(Math.random() * (10000 - 1000 + 1)) + 1000;
    }

    const rawDataDummy = [
        { id: 1, nama: 'SPBE Jakarta Barat', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Budi Santoso', kode_plant: 'SPBE01' + generateRandomCode(2), kabupaten: 'Jakarta Barat', stock: generateRandomStock() },
        { id: 2, nama: 'BPT Jakarta Utara', jenis: 'BPT', cabang: 'P.Layang', manager: 'Siti Aminah', kode_plant: 'BPT02' + generateRandomCode(2), kabupaten: 'Jakarta Utara', stock: generateRandomStock() },
        { id: 3, nama: 'SPBE Jambi Kota', jenis: 'SPBE', cabang: 'SA Jambi', manager: '', kode_plant: 'SPBE03' + generateRandomCode(2), kabupaten: 'Kota Jambi', stock: generateRandomStock() },
        { id: 4, nama: 'BPT Muaro Jambi', jenis: 'BPT', cabang: 'SA Jambi', manager: 'Dewi Lestari', kode_plant: 'BPT04' + generateRandomCode(2), kabupaten: 'Muaro Jambi', stock: generateRandomStock() },
        { id: 5, nama: 'SPBE Bengkulu Selatan', jenis: 'SPBE', cabang: 'SA Bengkulu', kode_plant: 'SPBE05' + generateRandomCode(2), kabupaten: 'Bengkulu Selatan', stock: generateRandomStock() },
        { id: 6, nama: 'BPT Bengkulu Utara', jenis: 'BPT', cabang: 'SA Bengkulu', kode_plant: 'BPT06' + generateRandomCode(2), kabupaten: 'Bengkulu Utara', stock: generateRandomStock() },
        { id: 7, nama: 'SPBE Lampung Timur', jenis: 'SPBE', cabang: 'SA Lampung', kode_plant: 'SPBE07' + generateRandomCode(2), kabupaten: 'Lampung Timur', stock: generateRandomStock() },
        { id: 8, nama: 'BPT Lampung Barat', jenis: 'BPT', cabang: 'SA Lampung', kode_plant: 'BPT08' + generateRandomCode(2), kabupaten: 'Lampung Barat', stock: generateRandomStock() },
        { id: 9, nama: 'SPBE Palembang Kota', jenis: 'SPBE', cabang: 'SA Sumsel', kode_plant: 'SPBE09' + generateRandomCode(2), kabupaten: 'Kota Palembang', stock: generateRandomStock() },
        { id: 10, nama: 'BPT Ogan Ilir', jenis: 'BPT', cabang: 'SA Sumsel', kode_plant: 'BPT10' + generateRandomCode(2), kabupaten: 'Ogan Ilir', stock: generateRandomStock() },
        { id: 11, nama: 'SPBE Pangkalan Bun', jenis: 'SPBE', cabang: 'P.Layang', kode_plant: 'SPBE11' + generateRandomCode(2), kabupaten: 'Kotawaringin Barat', stock: generateRandomStock() },
        { id: 12, nama: 'BPT Sampit', jenis: 'BPT', cabang: 'P.Layang', kode_plant: 'BPT12' + generateRandomCode(2), kabupaten: 'Kotawaringin Timur', stock: generateRandomStock() },
        { id: 13, nama: 'SPBE Pekanbaru', jenis: 'SPBE', cabang: 'SA Jambi', kode_plant: 'SPBE13' + generateRandomCode(2), kabupaten: 'Pekanbaru', stock: generateRandomStock() },
        { id: 14, nama: 'BPT Padang', jenis: 'BPT', cabang: 'SA Bengkulu', kode_plant: 'BPT14' + generateRandomCode(2), kabupaten: 'Kota Padang', stock: generateRandomStock() },
        { id: 15, nama: 'SPBE Bandar Lampung', jenis: 'SPBE', cabang: 'SA Lampung', kode_plant: 'SPBE15' + generateRandomCode(2), kabupaten: 'Bandar Lampung', stock: generateRandomStock() },
        { id: 16, nama: 'BPT Prabumulih', jenis: 'BPT', cabang: 'SA Sumsel', kode_plant: 'BPT16' + generateRandomCode(2), kabupaten: 'Prabumulih', stock: generateRandomStock() },
        { id: 17, nama: 'SPBE Sungai Penuh', jenis: 'SPBE', cabang: 'SA Jambi', kode_plant: 'SPBE17' + generateRandomCode(2), kabupaten: 'Sungai Penuh', stock: generateRandomStock() },
        { id: 18, nama: 'BPT Manna', jenis: 'BPT', cabang: 'SA Bengkulu', kode_plant: 'BPT18' + generateRandomCode(2), kabupaten: 'Bengkulu Selatan', stock: generateRandomStock() },
        { id: 19, nama: 'SPBE Metro', jenis: 'SPBE', cabang: 'SA Lampung', kode_plant: 'SPBE19' + generateRandomCode(2), kabupaten: 'Metro', stock: generateRandomStock() },
        { id: 20, nama: 'BPT Lubuklinggau', jenis: 'BPT', cabang: 'SA Sumsel', kode_plant: 'BPT20' + generateRandomCode(2), kabupaten: 'Lubuklinggau', stock: generateRandomStock() },
        { id: 21, nama: 'SPBE Pangkalpinang', jenis: 'SPBE', cabang: 'SA Babel', kode_plant: 'SPBE21' + generateRandomCode(2), kabupaten: 'Pangkalpinang', stock: generateRandomStock() },
        { id: 22, nama: 'BPT Belitung', jenis: 'BPT', cabang: 'SA Babel', kode_plant: 'BPT22' + generateRandomCode(2), kabupaten: 'Belitung', stock: generateRandomStock() }
    ];

    // Filter data to exclude 'P.Layang' for this specific page
    const dataDisplay = rawDataDummy.filter(item => item.cabang !== 'P.Layang');

    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    // Filters data based on search query
    function filterData() {
        return dataDisplay.filter(item => 
            (searchQuery ? (
                item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.kode_plant.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.kabupaten.toLowerCase().includes(searchQuery.toLowerCase())
            ) : true)
        );
    }

    // Renders table rows based on filtered and paginated data
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
                const iconHtml = item.jenis === 'SPBE' ?
                    `<span class="badge bg-gradient-primary rounded-circle me-2 icon-sm"><i class="fas fa-warehouse text-white"></i></span>` :
                    `<span class="badge bg-gradient-info rounded-circle me-2 icon-sm"><i class="fas fa-building text-white"></i></span>`;

                const nameLinkHtml = `<a href="#" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary view-stock-btn" style="cursor: pointer;"
                                            data-id="${item.id}"
                                            data-nama="${item.nama}"
                                            data-kodeplant="${item.kode_plant}"
                                            data-salesarea="${item.cabang}"
                                            data-kabupaten="${item.kabupaten}"
                                            data-stock="${item.stock}">
                                            ${item.nama}
                                        </a>`;

                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                ${iconHtml}
                                <div class="d-flex flex-column justify-content-center">
                                    ${nameLinkHtml}
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.kode_plant}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                        </td>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${item.kabupaten}</p>
                        </td>
                        <td class="align-middle text-center action-buttons">
                            <span class="badge bg-gradient-info text-white text-xs edit-btn" style="cursor:pointer;"
                                data-id="${item.id}"
                                data-nama="${item.nama}"
                                data-jenis="${item.jenis}"
                                data-kodeplant="${item.kode_plant}"
                                data-salesarea="${item.cabang}"
                                data-kabupaten="${item.kabupaten}"
                            >Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1 delete-btn" style="cursor:pointer;" data-id="${item.id}">Hapus</span>
                        </td>
                    </tr>
                `;
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const itemData = rawDataDummy.find(item => item.id === id);
                    if (itemData) {
                        document.getElementById('updateId').value = itemData.id;
                        document.getElementById('updateNama').value = itemData.nama;
                        document.getElementById('updateJenis').value = itemData.jenis;
                        document.getElementById('updateKodePlant').value = itemData.kode_plant;
                        document.getElementById('updateSalesArea').value = itemData.cabang;
                        document.getElementById('updateKabupaten').value = itemData.kabupaten;

                        const updateModal = new bootstrap.Modal(document.getElementById('updateDataModal'));
                        updateModal.show();
                    }
                });
            });

            document.querySelectorAll('.view-stock-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = parseInt(this.getAttribute('data-id'));
                    const itemData = rawDataDummy.find(item => item.id === id);
                    if (itemData) {
                        document.getElementById('transactionSpbeId').value = itemData.id;
                        document.getElementById('modal-spbe-bpt-name').textContent = itemData.nama;
                        document.getElementById('modal-spbe-bpt-detail-name-body').textContent = itemData.nama;
                        document.getElementById('modal-kode-plant').textContent = itemData.kode_plant;
                        document.getElementById('modal-sales-area').textContent = itemData.cabang;
                        document.getElementById('modal-kabupaten').textContent = itemData.kabupaten;
                        document.getElementById('modal-current-stock-display').textContent = `${itemData.stock} unit`;
                        document.getElementById('stockAmount').value = '';
                        document.getElementById('transactionType').value = '';

                        const stockModal = new bootstrap.Modal(document.getElementById('stockTransactionModal'));
                        stockModal.show();
                    }
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const index = rawDataDummy.findIndex(item => item.id === id);
                            if (index !== -1) {
                                rawDataDummy.splice(index, 1);
                                renderTable();
                                Swal.fire('Dihapus!', 'Data telah berhasil dihapus.', 'success');
                            }
                        }
                    });
                });
            });
        }
        renderPagination(data.length);
    }

    // Renders pagination buttons
    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-spbe-bpt');
        ul.innerHTML = '';

        function createPaginationButton(label, page, disabled = false, active = false) {
            const li = document.createElement('li');
            li.classList.add('page-item');
            if (disabled) li.classList.add('disabled');
            if (active) li.classList.add('active');
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = page;
                    renderTable();
                });
            }
            return li;
        }

        ul.appendChild(createPaginationButton('«', 1, currentPage === 1));
        ul.appendChild(createPaginationButton('‹', currentPage - 1, currentPage === 1));

        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            ul.appendChild(createPaginationButton(i, i, false, i === currentPage));
        }

        ul.appendChild(createPaginationButton('›', currentPage + 1, currentPage === totalPages));
        ul.appendChild(createPaginationButton('»', totalPages, currentPage === totalPages));
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('search-input').addEventListener('input', function () {
            searchQuery = this.value;
            currentPage = 1;
            renderTable();
        });

        document.getElementById('updateDataForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('updateId').value);
            const updatedNama = document.getElementById('updateNama').value;
            const updatedJenis = document.getElementById('updateJenis').value;
            const updatedKodePlant = document.getElementById('updateKodePlant').value;
            const updatedSalesArea = document.getElementById('updateSalesArea').value;
            const updatedKabupaten = document.getElementById('updateKabupaten').value;

            const itemIndex = rawDataDummy.findIndex(item => item.id === id);
            if (itemIndex !== -1) {
                rawDataDummy[itemIndex].nama = updatedNama;
                rawDataDummy[itemIndex].jenis = updatedJenis;
                rawDataDummy[itemIndex].kode_plant = updatedKodePlant;
                rawDataDummy[itemIndex].cabang = updatedSalesArea;
                rawDataDummy[itemIndex].kabupaten = updatedKabupaten;

                Swal.fire('Berhasil!', 'Data telah berhasil diperbarui.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('updateDataModal')).hide();
                renderTable();
            } else {
                Swal.fire('Gagal!', 'Data tidak ditemukan.', 'error');
            }
        });

        document.getElementById('stockTransactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('transactionSpbeId').value);
            const transactionType = document.getElementById('transactionType').value;
            const stockAmount = parseInt(document.getElementById('stockAmount').value);

            if (!transactionType || isNaN(stockAmount) || stockAmount <= 0) {
                Swal.fire('Error!', 'Mohon lengkapi semua field dengan benar.', 'error');
                return;
            }

            const itemIndex = rawDataDummy.findIndex(item => item.id === id);
            if (itemIndex !== -1) {
                let currentStock = rawDataDummy[itemIndex].stock;
                let newStock = currentStock;
                let successMessage = '';
                let errorMessage = '';

                if (transactionType === 'Pengiriman') {
                    newStock = currentStock + stockAmount;
                    successMessage = `Stok berhasil dikirim. Stok ${rawDataDummy[itemIndex].nama} sekarang ${newStock} unit.`;
                } else if (transactionType === 'Pengambilan') {
                    if (currentStock >= stockAmount) {
                        newStock = currentStock - stockAmount;
                        successMessage = `Stok berhasil diambil. Stok ${rawDataDummy[itemIndex].nama} sekarang ${newStock} unit.`;
                    } else {
                        errorMessage = `Stok tidak mencukupi untuk pengambilan ${stockAmount} unit. Stok saat ini hanya ${currentStock} unit.`;
                    }
                }

                if (errorMessage) {
                    Swal.fire('Gagal!', errorMessage, 'error');
                } else {
                    rawDataDummy[itemIndex].stock = newStock;
                    Swal.fire('Berhasil!', successMessage, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('stockTransactionModal')).hide();
                    renderTable();
                }
            } else {
                Swal.fire('Gagal!', 'Data SPBE/BPT tidak ditemukan.', 'error');
            }
        });

        renderTable();
    });
</script>
@endpush

<style>
    /* Mobile specific styles (max-width 767.98px for Bootstrap's 'md' breakpoint) */
    @media (max-width: 767.98px) {
        /* Header section adjustments */
        .spbe-header {
            flex-direction: column; /* Stack title and search vertically */
            align-items: center !important; /* Center items */
            text-align: center; /* Center text within the section */
            padding: 1rem !important; /* Adjust padding for header */
        }

        .spbe-title-section {
            width: 100%; /* Take full width */
            margin-bottom: 1rem; /* Add spacing below title */
        }

        .spbe-title-section h3 {
            font-size: 1.25rem; /* Smaller font for main title */
        }

        .spbe-subtitle {
            font-size: 0.75rem !important; /* Smaller font for subtitle */
            line-height: 1.2; /* Adjust line height for readability */
        }

        .spbe-search-section {
            width: 100%; /* Take full width */
            margin-top: 0 !important; /* Remove desktop margin-top */
            margin-left: 0 !important; /* Remove desktop margin-left */
            justify-content: center !important; /* Center search input */
            margin-bottom: 0.75rem; /* Added margin below search input to separate from table */
        }

        .search-input-spbe {
            width: 100% !important; /* Full width for search input */
            height: 38px !important; /* Smaller height */
            min-width: unset !important; /* Remove min-width */
            padding: 0.5rem 0.75rem; /* Adjust padding */
            font-size: 0.85rem; /* Smaller font size */
        }

        /* Table adjustments - RETURNED TO ORIGINAL BEHAVIOR (let Bootstrap's table-responsive handle it) */
        /* Removed all custom widths, white-space, overflow, text-overflow for table cells */
        #table-spbe-bpt thead th,
        #table-spbe-bpt tbody td {
            font-size: 0.75rem !important; /* Keep text slightly smaller for readability in compressed view */
            padding: 0.5rem 0.5rem !important; /* Keep padding consistent */
            /* Removed white-space, overflow, text-overflow to allow default Bootstrap responsive behavior */
        }

        /* Ensure all columns are displayed (overriding previous `display: none;` rules) */
        .table-col-no,
        .table-col-nama,
        .table-col-kode,
        .table-col-region,
        .table-col-kab,
        .table-col-aksi {
            display: table-cell !important;
            /* Reset widths if they were set for specific mobile configurations */
            width: auto !important; /* Allow browser to determine width within table-responsive */
            text-align: inherit; /* Inherit alignment from parent or default */
        }

        /* Specific icon sizing */
        .icon-sm {
            width: 20px !important;
            height: 20px !important;
            font-size: 0.65rem !important;
            flex-shrink: 0;
        }

        /* Action buttons sizing */
        .action-buttons .badge {
            padding: 0.3rem 0.5rem;
            font-size: 0.7rem;
            margin-left: 0.2rem !important;
            margin-right: 0.2rem !important;
            margin-bottom: 0.2rem;
            white-space: nowrap;
        }

        /* Modal adjustments */
        .modal-dialog {
            margin: 0.5rem !important;
        }

        .modal-header .modal-title {
            font-size: 1rem !important;
            text-align: center !important;
            width: 100%;
        }

        .modal-body {
            padding: 1rem !important;
        }

        .modal-footer .btn {
            font-size: 0.8rem !important;
            padding: 0.4rem 0.8rem !important;
        }

        /* Modal info row alignment */
        .info-row {
            flex-direction: column;
            align-items: flex-start !important;
            margin-bottom: 0.5rem !important;
        }

        .info-label {
            width: 100% !important;
            margin-bottom: 0.2rem;
            font-size: 0.85rem;
        }

        .info-colon {
            display: none !important;
        }

        .info-value {
            width: 100%;
            text-align: left !important;
            margin-left: 0 !important;
            font-size: 0.9rem;
        }
        
        /* Modal form controls sizing */
        .modal-body .form-control,
        .modal-body .form-select {
            height: 38px !important;
            font-size: 0.85rem !important;
            padding: 0.5rem 0.75rem !important;
        }
    }
</style>
@endsection