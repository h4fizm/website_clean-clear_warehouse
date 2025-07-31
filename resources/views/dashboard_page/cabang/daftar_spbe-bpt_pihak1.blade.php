@extends('dashboard_page.main')
@section('title', 'Data SPBE & BPT - Nama Material')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-start flex-wrap"> {{-- Changed align-items-center to align-items-start for vertical stack --}}
                <div class="d-flex flex-column"> {{-- Added a div to stack h3 and h6 vertically --}}
                    <h3>Tabel Data SPBE & BPT - Nama Material</h3>
                    <h6>Daftar Nama SPBE & BPT yang menyediakan Nama Material di Cabang ini</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto"> {{-- Added ms-auto to push to right --}}
                    {{-- Dropdown Filter Cabang --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownCabang" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                            Semua Cabang
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownCabang">
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="">Semua Cabang</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="Cabang 1">Cabang 1</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="Cabang 2">Cabang 2</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="Cabang 3">Cabang 3</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="Cabang 4">Cabang 4</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="Cabang 5">Cabang 5</a></li>
                        </ul>
                    </div>

                    {{-- Dropdown Filter Jenis --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="dropdownJenis" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                            Semua Jenis
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownJenis">
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="">Semua Jenis</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="SPBE">SPBE</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="BPT">BPT</a></li>
                        </ul>
                    </div>

                    {{-- Search --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari SPBE / BPT..." style="width: 200px; height: 55px;"> {{-- Adjusted height to match buttons --}}
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-spbe-bpt">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE / BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cabang & Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
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

{{-- Add Data Modal --}}
<div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDataModalLabel">Tambah Data SPBE / BPT Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDataForm">
                    <div class="mb-3">
                        <label for="namaSpbeBpt" class="form-label">Nama SPBE / BPT</label>
                        <input type="text" class="form-control" id="namaSpbeBpt" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenisSpbeBpt" class="form-label">Jenis</label>
                        <select class="form-select" id="jenisSpbeBpt" required>
                            <option value="">Pilih Jenis</option>
                            <option value="SPBE">SPBE</option>
                            <option value="BPT">BPT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cabangSpbeBpt" class="form-label">Cabang</label>
                        <select class="form-select" id="cabangSpbeBpt" required>
                            <option value="">Pilih Cabang</option>
                            <option value="Cabang 1">Cabang 1</option>
                            <option value="Cabang 2">Cabang 2</option>
                            <option value="Cabang 3">Cabang 3</option>
                            <option value="Cabang 4">Cabang 4</option>
                            <option value="Cabang 5">Cabang 5</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="addDataForm">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

{{-- Update Data Modal --}}
<div class="modal fade" id="updateDataModal" tabindex="-1" aria-labelledby="updateDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateDataModalLabel">Update Data Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateDataForm">
                    <input type="hidden" id="updateId">
                    <div class="mb-3">
                        <label for="updateStok" class="form-label">Total Stok</label>
                        <input type="number" class="form-control" id="updateStok" required min="0">
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


{{-- Script Dummy Filter + Pagination --}}
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

    const dataDummy = [
        { id: 1, nama: 'SPBE Cakung', stok: 120, jenis: 'SPBE', cabang: 'Cabang 1', manager: 'Budi Santoso', kode: generateRandomCode(5) },
        { id: 2, nama: 'BPT Jakarta Timur', stok: 90, jenis: 'BPT', cabang: 'Cabang 1', manager: 'Siti Aminah', kode: generateRandomCode(5) },
        { id: 3, nama: 'SPBE Bekasi', stok: 150, jenis: 'SPBE', cabang: 'Cabang 2', manager: '', kode: generateRandomCode(5) }, // Manager kosong
        { id: 4, nama: 'BPT Depok', stok: 110, jenis: 'BPT', cabang: 'Cabang 2', manager: 'Dewi Lestari', kode: generateRandomCode(5) },
        { id: 5, nama: 'SPBE Bandung', stok: 135, jenis: 'SPBE', cabang: 'Cabang 3', manager: '', kode: generateRandomCode(5) }, // Manager kosong
        { id: 6, nama: 'BPT Bandung', stok: 95, jenis: 'BPT', cabang: 'Cabang 3', manager: 'Rina Wijaya', kode: generateRandomCode(5) },
        { id: 7, nama: 'SPBE Surabaya', stok: 170, jenis: 'SPBE', cabang: 'Cabang 4', manager: 'Hadi Prasetyo', kode: generateRandomCode(5) },
        { id: 8, nama: 'BPT Surabaya', stok: 140, jenis: 'BPT', cabang: 'Cabang 4', manager: '', kode: generateRandomCode(5) }, // Manager kosong
        { id: 9, nama: 'SPBE Malang', stok: 160, jenis: 'SPBE', cabang: 'Cabang 5', manager: 'Eko Nurcahyo', kode: generateRandomCode(5) },
        { id: 10, nama: 'BPT Malang', stok: 130, jenis: 'BPT', cabang: 'Cabang 5', manager: 'Linda Kusumawati', kode: generateRandomCode(5) },
        { id: 11, nama: 'SPBE Bonus', stok: 200, jenis: 'SPBE', cabang: 'Cabang 5', manager: 'Fajar Indah', kode: generateRandomCode(5) }
    ];

    let selectedCabang = null;
    let selectedJenis = null;
    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5; // Maximum number of page links to display

    function filterData() {
        return dataDummy.filter(item => {
            const matchCabang = selectedCabang ? item.cabang === selectedCabang : true;
            const matchJenis = selectedJenis ? item.jenis === selectedJenis : true;
            const matchSearch = searchQuery ?
                                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kode.includes(searchQuery) ||
                                item.cabang.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            return matchCabang && matchJenis && matchSearch;
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

                const materialUrl = `/material?id=${item.id}&nama=${encodeURIComponent(item.nama)}`;

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
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                                <p class="text-xs text-secondary mb-0">Kode cabang : ${item.kode}</p>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="${materialUrl}" class="text-center text-xs text-secondary font-weight-bold mb-0 text-decoration-underline" style="cursor: pointer;">${item.stok} pcs</a>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs edit-btn" style="cursor:pointer;" data-id="${item.id}" data-stok="${item.stok}">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs delete-btn ms-1" style="cursor:pointer;" data-id="${item.id}">Hapus</span>
                        </td>
                    </tr>
                `;
            });

            // Attach event listeners for edit and delete buttons after rendering
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const stok = parseInt(this.getAttribute('data-stok'));
                    document.getElementById('updateId').value = id;
                    document.getElementById('updateStok').value = stok;
                    const updateModal = new bootstrap.Modal(document.getElementById('updateDataModal'));
                    updateModal.show();
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
                            const index = dataDummy.findIndex(item => item.id === id);
                            if (index !== -1) {
                                dataDummy.splice(index, 1);
                                renderTable();
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

    // Set initial text for dropdown buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('dropdownCabang').textContent = 'Semua Cabang';
        document.getElementById('dropdownJenis').textContent = 'Semua Jenis';
    });

    document.querySelectorAll('[data-filter="cabang"]').forEach(el => {
        el.addEventListener('click', function () {
            selectedCabang = this.getAttribute('data-value');
            document.getElementById('dropdownCabang').textContent = this.textContent;
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelectorAll('[data-filter="jenis"]').forEach(el => {
        el.addEventListener('click', function () {
            selectedJenis = this.getAttribute('data-value');
            document.getElementById('dropdownJenis').textContent = this.textContent;
            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('search-input').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable();
    });

    // Handle form submission for adding new data
    document.getElementById('addDataForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const nama = document.getElementById('namaSpbeBpt').value;
        const jenis = document.getElementById('jenisSpbeBpt').value;
        const cabang = document.getElementById('cabangSpbeBpt').value;

        const manager = '';

        if (nama && jenis && cabang) {
            const newData = {
                id: dataDummy.length > 0 ? Math.max(...dataDummy.map(d => d.id)) + 1 : 1, // Ensure unique ID
                nama: nama,
                stok: Math.floor(Math.random() * 200) + 50,
                jenis: jenis,
                cabang: cabang,
                manager: manager,
                kode: generateRandomCode(5)
            };
            dataDummy.push(newData);

            this.reset();
            const addDataModal = bootstrap.Modal.getInstance(document.getElementById('addDataModal'));
            addDataModal.hide();

            renderTable();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Harap lengkapi Nama, Jenis, dan Cabang.',
                confirmButtonText: 'Oke'
            });
        }
    });

    // Handle form submission for updating data
    document.getElementById('updateDataForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const idToUpdate = parseInt(document.getElementById('updateId').value);
        const newStok = parseInt(document.getElementById('updateStok').value);

        const itemIndex = dataDummy.findIndex(item => item.id === idToUpdate);
        if (itemIndex !== -1) {
            dataDummy[itemIndex].stok = newStok;

            const updateDataModal = bootstrap.Modal.getInstance(document.getElementById('updateDataModal'));
            updateDataModal.hide();

            renderTable();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Stok berhasil diperbarui!',
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data tidak ditemukan.',
                confirmButtonText: 'Oke'
            });
        }
    });

    // Initial render
    renderTable();
</script>
@endpush
@endsection