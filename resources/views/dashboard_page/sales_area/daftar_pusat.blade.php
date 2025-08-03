@extends('dashboard_page.main')

@section('title', 'Data SPBE & BPT - P.Layang Pusat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-start flex-wrap">
                <div class="d-flex flex-column">
                    <h3>Daftar Nama SPBE & BPT - P.Layang (Pusat)</h3>
                    <h6 class="text-secondary opacity-8">Menampilkan daftar SPBE/BPT dari Setiap Region/SA, **kecuali** P.Layang itu sendiri.</h6>
                </div>
                {{-- Only Search remains, pushed to the right --}}
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto">
                    {{-- Search --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari Nama SPBE / BPT, Kode Plant, Kabupaten" style="width: 300px; height: 55px;">
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-spbe-bpt">
                        <thead>
                            <tr>
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
                    <div id="no-data" class="text-center text-muted py-4">
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

{{-- Update Data Modal (Existing) --}}
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
                        <input type="text" class="form-control" id="updateNama" required>
                    </div>
                    {{-- Tambahkan dropdown untuk Jenis SPBE/BPT --}}
                    <div class="mb-3">
                        <label for="updateJenis" class="form-label">Jenis</label>
                        <select class="form-select" id="updateJenis" required>
                            <option value="SPBE">SPBE</option>
                            <option value="BPT">BPT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateKodePlant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control" id="updateKodePlant" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateSalesArea" class="form-label">Region/Sales Area</label>
                        <select class="form-select" id="updateSalesArea" required>
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
                        <input type="text" class="form-control" id="updateKabupaten" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="updateDataForm">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Stok & Transaksi Modal (Desain Portrait dengan Alignment Rapi) --}}
<div class="modal fade" id="stockTransactionModal" tabindex="-1" aria-labelledby="stockTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100" id="stockTransactionModalLabel">Detail Stok & Transaksi untuk <span id="modal-spbe-bpt-name" class="fw-bold text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Bagian Informasi SPBE/BPT --}}
                <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-3">INFORMASI SPBE/BPT</h6>
                <input type="hidden" id="transactionSpbeId">
                
                {{-- Setiap baris informasi menggunakan flexbox untuk alignment yang tepat --}}
                <div class="d-flex mb-1 align-items-baseline">
                    <div class="flex-shrink-0" style="width: 140px;"> {{-- Lebar tetap untuk label --}}
                        <strong class="text-secondary">Nama SPBE / BPT</strong>
                    </div>
                    <strong class="text-secondary me-2 flex-shrink-0">:</strong> {{-- Titik dua sebagai elemen terpisah dengan margin kanan --}}
                    <div class="flex-grow-1"> {{-- Konten mengambil sisa ruang dan wrap --}}
                        <span class="text-dark" id="modal-spbe-bpt-detail-name-body"></span>
                    </div>
                </div>
                
                <div class="d-flex mb-1 align-items-baseline">
                    <div class="flex-shrink-0" style="width: 140px;">
                        <strong class="text-secondary">Kode Plant</strong>
                    </div>
                    <strong class="text-secondary me-2 flex-shrink-0">:</strong>
                    <div class="flex-grow-1">
                        <span class="text-dark" id="modal-kode-plant"></span>
                    </div>
                </div>

                <div class="d-flex mb-1 align-items-baseline">
                    <div class="flex-shrink-0" style="width: 140px;">
                        <strong class="text-secondary">Region/SA</strong>
                    </div>
                    <strong class="text-secondary me-2 flex-shrink-0">:</strong>
                    <div class="flex-grow-1">
                        <span class="text-dark" id="modal-sales-area"></span>
                    </div>
                </div>

                <div class="d-flex mb-3 align-items-baseline">
                    <div class="flex-shrink-0" style="width: 140px;">
                        <strong class="text-secondary">Kabupaten</strong>
                    </div>
                    <strong class="text-secondary me-2 flex-shrink-0">:</strong>
                    <div class="flex-grow-1">
                        <span class="text-dark" id="modal-kabupaten"></span>
                    </div>
                </div>

                <hr class="my-3"> {{-- Garis pemisah --}}

                {{-- Bagian Stok dan Form Transaksi --}}
                <div class="d-flex mb-3 align-items-baseline">
                    <div class="flex-shrink-0" style="width: 140px;">
                        <strong class="text-secondary">Stok Saat Ini</strong>
                    </div>
                    <strong class="text-secondary me-2 flex-shrink-0">:</strong>
                    <div class="flex-grow-1">
                        <span class="fs-5 text-success" id="modal-current-stock-display"></span>
                    </div>
                </div>

                <form id="stockTransactionForm">
                    <div class="mb-3">
                        <label for="transactionType" class="form-label">Jenis Transaksi</label>
                        <select class="form-select" id="transactionType" required>
                            <option value="">Pilih Jenis Transaksi</option>
                            <option value="Pengiriman">Pengiriman (Tambah Stok)</option>
                            <option value="Pengambilan">Pengambilan (Kurangi Stok)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="stockAmount" class="form-label">Jumlah Stok yang Digunakan</label>
                        <input type="number" class="form-control" id="stockAmount" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="stockTransactionForm">Proses Transaksi</button>
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

    // Function to generate a random stock amount between 1000 and 10000
    function generateRandomStock() {
        return Math.floor(Math.random() * (10000 - 1000 + 1)) + 1000;
    }

    // Dummy data including all Sales Areas
    // Catatan: Data P.Layang tetap ada di sini untuk keperluan edit/delete jika user memilihnya
    // Namun, data ini akan difilter agar tidak ditampilkan di tabel utama halaman 'Pusat'
    const rawDataDummy = [
        // Data P.Layang yang tidak akan ditampilkan di tabel utama (sesuai permintaan)
        { id: 1, nama: 'SPBE Jakarta Barat', jenis: 'SPBE', cabang: 'P.Layang', kode_plant: 'SPBE01' + generateRandomCode(2), kabupaten: 'Jakarta Barat', stock: generateRandomStock() },
        { id: 2, nama: 'BPT Jakarta Utara', jenis: 'BPT', cabang: 'P.Layang', kode_plant: 'BPT02' + generateRandomCode(2), kabupaten: 'Jakarta Utara', stock: generateRandomStock() },
        { id: 11, nama: 'SPBE Pangkalan Bun', jenis: 'SPBE', cabang: 'P.Layang', kode_plant: 'SPBE11' + generateRandomCode(2), kabupaten: 'Kotawaringin Barat', stock: generateRandomStock() },
        { id: 12, nama: 'BPT Sampit', jenis: 'BPT', cabang: 'P.Layang', kode_plant: 'BPT12' + generateRandomCode(2), kabupaten: 'Kotawaringin Timur', stock: generateRandomStock() },

        // Data dari Region/Sales Area lainnya yang akan ditampilkan di tabel
        { id: 3, nama: 'SPBE Jambi Kota', jenis: 'SPBE', cabang: 'SA Jambi', kode_plant: 'SPBE03' + generateRandomCode(2), kabupaten: 'Kota Jambi', stock: generateRandomStock() },
        { id: 4, nama: 'BPT Muaro Jambi', jenis: 'BPT', cabang: 'SA Jambi', kode_plant: 'BPT04' + generateRandomCode(2), kabupaten: 'Muaro Jambi', stock: generateRandomStock() },
        { id: 5, nama: 'SPBE Bengkulu Selatan', jenis: 'SPBE', cabang: 'SA Bengkulu', kode_plant: 'SPBE05' + generateRandomCode(2), kabupaten: 'Bengkulu Selatan', stock: generateRandomStock() },
        { id: 6, nama: 'BPT Bengkulu Utara', jenis: 'BPT', cabang: 'SA Bengkulu', kode_plant: 'BPT06' + generateRandomCode(2), kabupaten: 'Bengkulu Utara', stock: generateRandomStock() },
        { id: 7, nama: 'SPBE Lampung Timur', jenis: 'SPBE', cabang: 'SA Lampung', kode_plant: 'SPBE07' + generateRandomCode(2), kabupaten: 'Lampung Timur', stock: generateRandomStock() },
        { id: 8, nama: 'BPT Lampung Barat', jenis: 'BPT', cabang: 'SA Lampung', kode_plant: 'BPT08' + generateRandomCode(2), kabupaten: 'Lampung Barat', stock: generateRandomStock() },
        { id: 9, nama: 'SPBE Palembang Kota', jenis: 'SPBE', cabang: 'SA Sumsel', kode_plant: 'SPBE09' + generateRandomCode(2), kabupaten: 'Kota Palembang', stock: generateRandomStock() },
        { id: 10, nama: 'BPT Ogan Ilir', jenis: 'BPT', cabang: 'SA Sumsel', kode_plant: 'BPT10' + generateRandomCode(2), kabupaten: 'Ogan Ilir', stock: generateRandomStock() },
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

    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function filterData() {
        return rawDataDummy.filter(item => {
            // Filter out data where 'cabang' is 'P.Layang'
            const notPLayang = item.cabang !== 'P.Layang';

            const matchSearch = searchQuery ?
                                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kode_plant.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kabupaten.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            return notPLayang && matchSearch; // Only display if not P.Layang AND matches search query
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
                const iconHtml = item.jenis === 'SPBE' ?
                    `<span class="badge bg-gradient-primary rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-warehouse text-white" style="font-size: 0.75rem;"></i></span>` :
                    `<span class="badge bg-gradient-info rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-building text-white" style="font-size: 0.75rem;"></i></span>`;

                // Modified to open the new stockTransactionModal
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
                        <td> {{-- Kode Plant column --}}
                            <p class="text-xs font-weight-bold mb-0">${item.kode_plant}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                        </td>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${item.kabupaten}</p>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs edit-btn" style="cursor:pointer;"
                                data-id="${item.id}"
                                data-nama="${item.nama}"
                                data-jenis="${item.jenis}" {{-- Tambahkan data-jenis --}}
                                data-kodeplant="${item.kode_plant}"
                                data-salesarea="${item.cabang}"
                                data-kabupaten="${item.kabupaten}"
                            >Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1 delete-btn" style="cursor:pointer;" data-id="${item.id}">Hapus</span>
                        </td>
                    </tr>
                `;
            });

            // Update edit button click listener to populate new fields
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const nama = this.getAttribute('data-nama');
                    const jenis = this.getAttribute('data-jenis'); // Ambil jenis
                    const kodePlant = this.getAttribute('data-kodeplant');
                    const salesArea = this.getAttribute('data-salesarea');
                    const kabupaten = this.getAttribute('data-kabupaten');

                    document.getElementById('updateId').value = id;
                    document.getElementById('updateNama').value = nama;
                    document.getElementById('updateJenis').value = jenis; // Set nilai dropdown jenis
                    document.getElementById('updateKodePlant').value = kodePlant;
                    document.getElementById('updateSalesArea').value = salesArea;
                    document.getElementById('updateKabupaten').value = kabupaten;

                    const updateModal = new bootstrap.Modal(document.getElementById('updateDataModal'));
                    updateModal.show();
                });
            });

            // Listener for the new stock transaction modal
            document.querySelectorAll('.view-stock-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default link behavior
                    const id = parseInt(this.getAttribute('data-id'));
                    const nama = this.getAttribute('data-nama');
                    const kodePlant = this.getAttribute('data-kodeplant');
                    const salesArea = this.getAttribute('data-salesarea');
                    const kabupaten = this.getAttribute('data-kabupaten');
                    const stock = parseInt(this.getAttribute('data-stock')); // Get stock

                    // Populate the stock transaction modal with new IDs
                    document.getElementById('transactionSpbeId').value = id;
                    document.getElementById('modal-spbe-bpt-name').textContent = nama; // Judul modal
                    document.getElementById('modal-spbe-bpt-detail-name-body').textContent = nama; // Di dalam detail informasi
                    document.getElementById('modal-kode-plant').textContent = kodePlant;
                    document.getElementById('modal-sales-area').textContent = salesArea;
                    document.getElementById('modal-kabupaten').textContent = kabupaten;
                    document.getElementById('modal-current-stock-display').textContent = `${stock} unit`; // Pastikan menampilkan 'unit'
                    document.getElementById('stockAmount').value = ''; // Clear previous input
                    document.getElementById('transactionType').value = ''; // Clear previous selection

                    const stockModal = new bootstrap.Modal(document.getElementById('stockTransactionModal'));
                    stockModal.show();
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
                            // Find the index in the original rawDataDummy
                            const index = rawDataDummy.findIndex(item => item.id === id);
                            if (index !== -1) {
                                rawDataDummy.splice(index, 1); // Remove from raw data
                                renderTable(); // Re-render table after deletion
                                Swal.fire(
                                    'Dihapus!',
                                    'Data telah berhasil dihapus.',
                                    'success'
                                );
                            }
                        }
                    });
                });
            });
        }

        renderPagination(data.length);
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-spbe-bpt');
        ul.innerHTML = '';

        // Add '<<' button
        const firstPageItem = document.createElement('li');
        firstPageItem.classList.add('page-item');
        if (currentPage === 1) firstPageItem.classList.add('disabled');
        firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">«</a>`;
        firstPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage !== 1) {
                currentPage = 1;
                renderTable();
            }
        });
        ul.appendChild(firstPageItem);

        // Add 'Previous' button
        const prevPageItem = document.createElement('li');
        prevPageItem.classList.add('page-item');
        if (currentPage === 1) prevPageItem.classList.add('disabled');
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

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
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

        // Add 'Next' button
        const nextPageItem = document.createElement('li');
        nextPageItem.classList.add('page-item');
        if (currentPage === totalPages) nextPageItem.classList.add('disabled');
        nextPageItem.innerHTML = `<a class="page-link" href="#">></a>`;
        nextPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        ul.appendChild(nextPageItem);

        // Add '>>' button
        const lastPageItem = document.createElement('li');
        lastPageItem.classList.add('page-item');
        if (currentPage === totalPages) lastPageItem.classList.add('disabled');
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
        document.getElementById('search-input').addEventListener('input', function () {
            searchQuery = this.value;
            currentPage = 1;
            renderTable();
        });

        // Handle form submission for update modal
        document.getElementById('updateDataForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('updateId').value);
            const updatedNama = document.getElementById('updateNama').value;
            const updatedJenis = document.getElementById('updateJenis').value; // Ambil nilai jenis
            const updatedKodePlant = document.getElementById('updateKodePlant').value;
            const updatedSalesArea = document.getElementById('updateSalesArea').value;
            const updatedKabupaten = document.getElementById('updateKabupaten').value;

            // Find the item in the original rawDataDummy to update it
            const itemIndex = rawDataDummy.findIndex(item => item.id === id);
            if (itemIndex !== -1) {
                rawDataDummy[itemIndex].nama = updatedNama;
                rawDataDummy[itemIndex].jenis = updatedJenis; // Perbarui jenis
                rawDataDummy[itemIndex].kode_plant = updatedKodePlant;
                rawDataDummy[itemIndex].cabang = updatedSalesArea; // Update 'cabang' as it represents Sales Area
                rawDataDummy[itemIndex].kabupaten = updatedKabupaten;

                Swal.fire(
                    'Berhasil!',
                    'Data telah berhasil diperbarui.',
                    'success'
                );
                bootstrap.Modal.getInstance(document.getElementById('updateDataModal')).hide();
                renderTable(); // Re-render table after update
            } else {
                Swal.fire(
                    'Gagal!',
                    'Data tidak ditemukan.',
                    'error'
                );
            }
        });

        // Handle form submission for stock transaction modal
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
                    renderTable(); // Re-render table to update stock display if needed (though not directly displayed in this table)
                }
            } else {
                Swal.fire('Gagal!', 'Data SPBE/BPT tidak ditemukan.', 'error');
            }
        });

        // Initial render
        renderTable();
    });
</script>
@endpush
@endsection