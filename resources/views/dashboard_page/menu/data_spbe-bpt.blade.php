@extends('dashboard_page.main')
@section('title', 'Data SPBE & BPT - Nama Cabang')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h5>Tabel Data SPBE & BPT - Nama Cabang</h5>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center"> {{-- Added align-items-center here --}}
                    {{-- Dropdown Filter Cabang --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownCabang" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                            Semua Cabang
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownCabang">
                            <li><a class="dropdown-item" href="#" data-filter="cabang" data-value="">Semua Cabang</a></li> {{-- Added 'Semua Cabang' option --}}
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
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="">Semua Jenis</a></li> {{-- Added 'Semua Jenis' option --}}
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="SPBE">SPBE</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="jenis" data-value="BPT">BPT</a></li>
                        </ul>
                    </div>

                    {{-- Search --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari SPBE / BPT..." style="width: 200px; height: 55px;">

                    {{-- Add Data Button --}}
                    <button class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#addDataModal"
                            title="Tambah Data SPBE / BPT"
                            style="width: 38px; height: 38px; padding: 0;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-spbe-bpt">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE / BPT</th> {{-- Now includes code --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cabang</th>
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
                    <div class="mb-3">
                        <label for="kodeSpbeBpt" class="form-label">Kode SPBE / BPT</label>
                        <input type="text" class="form-control" id="kodeSpbeBpt" required placeholder="Contoh: SPBE001 / BPT001">
                    </div>
                    {{-- Manager field removed from here --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="addDataForm">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

{{-- Script Dummy Filter + Pagination --}}
@push('scripts')
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
        { id: 1, nama: 'SPBE Cakung', stok: 120, jenis: 'SPBE', cabang: 'Cabang 1', manager: 'Budi Santoso', kode: 'SPBE01' + generateRandomCode(2) },
        { id: 2, nama: 'BPT Jakarta Timur', stok: 90, jenis: 'BPT', cabang: 'Cabang 1', manager: 'Siti Aminah', kode: 'BPT02' + generateRandomCode(2) },
        { id: 3, nama: 'SPBE Bekasi', stok: 0, jenis: 'SPBE', cabang: 'Cabang 2', manager: '', kode: 'SPBE03' + generateRandomCode(2) }, // Stok 0 for testing
        { id: 4, nama: 'BPT Depok', stok: 110, jenis: 'BPT', cabang: 'Cabang 2', manager: 'Dewi Lestari', kode: 'BPT04' + generateRandomCode(2) },
        { id: 5, nama: 'SPBE Bandung', stok: 0, jenis: 'SPBE', cabang: 'Cabang 3', manager: '', kode: 'SPBE05' + generateRandomCode(2) }, // Stok 0 for testing
        { id: 6, nama: 'BPT Bandung', stok: 95, jenis: 'BPT', cabang: 'Cabang 3', manager: 'Rina Wijaya', kode: 'BPT06' + generateRandomCode(2) },
        { id: 7, nama: 'SPBE Surabaya', stok: 170, jenis: 'SPBE', cabang: 'Cabang 4', manager: 'Hadi Prasetyo', kode: 'SPBE07' + generateRandomCode(2) },
        { id: 8, nama: 'BPT Surabaya', stok: 140, jenis: 'BPT', cabang: 'Cabang 4', manager: '', kode: 'BPT08' + generateRandomCode(2) },
        { id: 9, nama: 'SPBE Malang', stok: 160, jenis: 'SPBE', cabang: 'Cabang 5', manager: 'Eko Nurcahyo', kode: 'SPBE09' + generateRandomCode(2) },
        { id: 10, nama: 'BPT Malang', stok: 130, jenis: 'BPT', cabang: 'Cabang 5', manager: 'Linda Kusumawati', kode: 'BPT10' + generateRandomCode(2) },
        { id: 11, nama: 'SPBE Bonus', stok: 200, jenis: 'SPBE', cabang: 'Cabang 5', manager: 'Fajar Indah', kode: 'SPBE11' + generateRandomCode(2) }
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
                                item.kode.toLowerCase().includes(searchQuery.toLowerCase()) || // Search by kode
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
                
                // Determine stock display text
                const stockText = item.stok === 0 ? 
                                  '<span class="text-danger font-weight-bold">Stok material kosong</span>' : 
                                  `${item.stok} pcs`;

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
                                    <p class="text-xs text-secondary mb-0">Kode: ${item.kode}</p> {{-- Kode moved under Nama --}}
                                </div>
                            </div>
                        </td>
                        <td> {{-- Display Cabang only --}}
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p>
                        </td>
                        <td class="text-center">
                            <a href="${materialUrl}" class="text-center text-xs text-secondary font-weight-bold mb-0 text-decoration-underline" style="cursor: pointer;">${stockText}</a> {{-- Conditional stock text --}}
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs" style="cursor:pointer;">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1" style="cursor:pointer;">Hapus</span>
                        </td>
                    </tr>
                `;
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

    // Set initial text for dropdown buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('dropdownCabang').textContent = 'Semua Cabang';
        document.getElementById('dropdownJenis').textContent = 'Semua Jenis';
    });


    document.querySelectorAll('[data-filter="cabang"]').forEach(el => {
        el.addEventListener('click', function () {
            selectedCabang = this.getAttribute('data-value');
            document.getElementById('dropdownCabang').textContent = this.textContent; // Update button text
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelectorAll('[data-filter="jenis"]').forEach(el => {
        el.addEventListener('click', function () {
            selectedJenis = this.getAttribute('data-value');
            document.getElementById('dropdownJenis').textContent = this.textContent; // Update button text
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
        e.preventDefault(); // Prevent default form submission

        const nama = document.getElementById('namaSpbeBpt').value;
        const jenis = document.getElementById('jenisSpbeBpt').value;
        const cabang = document.getElementById('cabangSpbeBpt').value;
        const kode = document.getElementById('kodeSpbeBpt').value; // Get the new kode input

        // Manager not taken from form, always empty or set default
        const manager = ''; // Manager will always be empty when added from this modal

        if (nama && jenis && cabang && kode) { // Validation: nama, jenis, cabang, AND kode are required
            const newData = {
                id: dataDummy.length > 0 ? Math.max(...dataDummy.map(d => d.id)) + 1 : 1, // Ensure unique ID
                nama: nama,
                stok: Math.floor(Math.random() * 200) + 50, // Dummy stok
                jenis: jenis,
                cabang: cabang,
                manager: manager, // Manager will be empty
                kode: kode // Use the manually entered kode
            };
            dataDummy.push(newData); // Add new data to our dummy array

            // Reset form and close modal
            this.reset();
            const addDataModal = bootstrap.Modal.getInstance(document.getElementById('addDataModal'));
            addDataModal.hide();

            // Re-render table to show new data
            renderTable();
            alert('Data berhasil ditambahkan!');
        } else {
            alert('Harap lengkapi Nama, Jenis, Cabang, dan Kode SPBE/BPT.'); // Updated alert message
        }
    });


    // Initial render
    renderTable();
</script>
@endpush
@endsection