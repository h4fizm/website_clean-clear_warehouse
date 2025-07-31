@extends('dashboard_page.main')
@section('title', 'Data Cabang - Nama Cabang')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h3>Tabel Data Cabang</h3>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                    {{-- Search --}}
                    <input type="text" id="search-input-cabang" class="form-control form-control-sm" placeholder="Cari Cabang..." style="width: 250px; height: 55px;"> {{-- Adjusted placeholder --}}

                    {{-- Add Cabang Button --}}
                    <button class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#addCabangModal"
                            title="Tambah Data Cabang"
                            style="width: 38px; height: 38px; padding: 0;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-cabang">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Cabang</th>
                                {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Cabang</th> --}} {{-- Removed Kode Cabang --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Manager</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-cabang" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0" id="pagination-cabang">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Add Cabang Modal --}}
<div class="modal fade" id="addCabangModal" tabindex="-1" aria-labelledby="addCabangModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCabangModalLabel">Tambah Data Cabang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCabangForm">
                    <div class="mb-3">
                        <label for="namaCabang" class="form-label">Nama Cabang</label>
                        <input type="text" class="form-control" id="namaCabang" required>
                    </div>
                    {{-- Removed Kode Cabang input field from here --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="addCabangForm">Simpan Cabang</button>
            </div>
        </div>
    </div>
</div>

{{-- Script for Cabang Page --}}
@push('scripts')
<script>
    // generateRandomCode is no longer strictly needed for branch codes, 
    // but keeping it for potential future use or other dummy data generation.
    function generateRandomCode(length) {
        let result = '';
        const characters = '0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    // Dummy data for Cabang table - 'kode' property removed
    const dataCabangDummy = [
        { id: 1, nama: 'Cabang 1', stok: 700, manager: 'Manager A' },
        { id: 2, nama: 'Cabang 2', stok: 550, manager: 'Manager B' },
        { id: 3, nama: 'Cabang 3', stok: 820, manager: '' },
        { id: 4, nama: 'Cabang 4', stok: 610, manager: 'Manager C' },
        { id: 5, nama: 'Cabang 5', stok: 930, manager: '' },
        { id: 6, nama: 'Cabang 6', stok: 400, manager: 'Manager D' },
        { id: 7, nama: 'Cabang 7', stok: 750, manager: '' },
        { id: 8, nama: 'Cabang 8', stok: 680, manager: 'Manager E' },
        { id: 9, nama: 'Cabang 9', stok: 500, manager: 'Manager F' },
        { id: 10, nama: 'Cabang 10', stok: 990, manager: '' },
        { id: 11, nama: 'Cabang 11', stok: 650, manager: 'Manager G' },
        { id: 12, nama: 'Cabang 12', stok: 720, manager: 'Manager H' },
        { id: 13, nama: 'Cabang 13', stok: 480, manager: '' },
        { id: 14, nama: 'Cabang 14', stok: 880, manager: 'Manager I' },
        { id: 15, nama: 'Cabang 15', stok: 590, manager: '' },
        { id: 16, nama: 'Cabang 16', stok: 710, manager: 'Manager J' },
        { id: 17, nama: 'Cabang 17', stok: 630, manager: '' },
        { id: 18, nama: 'Cabang 18', stok: 910, manager: 'Manager K' },
        { id: 19, nama: 'Cabang 19', stok: 530, manager: 'Manager L' },
        { id: 20, nama: 'Cabang 20', stok: 840, manager: '' },
        { id: 21, nama: 'Cabang 21', stok: 770, manager: 'Manager M' },
        { id: 22, nama: 'Cabang 22', stok: 600, manager: 'Manager N' },
        { id: 23, nama: 'Cabang 23', stok: 950, manager: '' },
        { id: 24, nama: 'Cabang 24', stok: 510, manager: 'Manager O' },
        { id: 25, nama: 'Cabang 25', stok: 800, manager: '' },
        { id: 26, nama: 'Cabang 26', stok: 730, manager: 'Manager P' },
        { id: 27, nama: 'Cabang 27', stok: 660, manager: '' },
        { id: 28, nama: 'Cabang 28', stok: 980, manager: 'Manager Q' },
        { id: 29, nama: 'Cabang 29', stok: 570, manager: 'Manager R' },
        { id: 30, nama: 'Cabang 30', stok: 890, manager: '' },
    ];

    let searchCabangQuery = '';
    let currentCabangPage = 1;
    const itemsPerCabangPage = 10;
    const maxCabangPagesToShow = 5;

    function filterCabangData() {
        return dataCabangDummy.filter(item => {
            const matchSearch = searchCabangQuery ?
                                item.nama.toLowerCase().includes(searchCabangQuery.toLowerCase())
                                : true;
            return matchSearch;
        });
    }

    function renderCabangTable() {
        const tbody = document.querySelector('#table-cabang tbody');
        const noData = document.getElementById('no-data-cabang');
        const data = filterCabangData();
        const start = (currentCabangPage - 1) * itemsPerCabangPage;
        const paginated = data.slice(start, start + itemsPerCabangPage);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            noData.style.display = 'block';
            // Updated colspan to 5 for the new column structure
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Data Kosong</td></tr>'; 
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                const iconHtml = `<span class="badge bg-gradient-success rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-city text-white" style="font-size: 0.75rem;"></i></span>`;

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
                        {{-- Removed Kode Cabang column --}}
                        <td>
                            <p class="text-xs text-secondary mb-0">${item.stok} pcs</p>
                        </td>
                        <td>
                            <p class="text-xs mb-0">${item.manager ? item.manager : 'N/A'}</p>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs" style="cursor:pointer;">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1" style="cursor:pointer;">Hapus</span>
                        </td>
                    </tr>
                `;
            });
        }

        renderCabangPagination(data.length);
    }

    function renderCabangPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerCabangPage);
        const ul = document.getElementById('pagination-cabang');
        ul.innerHTML = '';

        // Add '<<' button
        const firstPageItem = document.createElement('li');
        firstPageItem.classList.add('page-item');
        if (currentCabangPage === 1) firstPageItem.classList.add('disabled');
        firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">«</a>`;
        firstPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentCabangPage !== 1) {
                currentCabangPage = 1;
                renderCabangTable();
            }
        });
        ul.appendChild(firstPageItem);

        // Add 'Previous' button
        const prevPageItem = document.createElement('li');
        prevPageItem.classList.add('page-item');
        if (currentCabangPage === 1) prevPageItem.classList.add('disabled');
        prevPageItem.innerHTML = `<a class="page-link" href="#"><</a>`;
        prevPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentCabangPage > 1) {
                currentCabangPage--;
                renderCabangTable();
            }
        });
        ul.appendChild(prevPageItem);

        let startPage = Math.max(1, currentCabangPage - Math.floor(maxCabangPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxCabangPagesToShow - 1);

        if (endPage - startPage + 1 < maxCabangPagesToShow) {
            startPage = Math.max(1, endPage - maxCabangPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.classList.add('page-item');
            if (i === currentCabangPage) li.classList.add('active');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', function(e) {
                e.preventDefault();
                currentCabangPage = i;
                renderCabangTable();
            });
            ul.appendChild(li);
        }

        // Add 'Next' button
        const nextPageItem = document.createElement('li');
        nextPageItem.classList.add('page-item');
        if (currentCabangPage === totalPages) nextPageItem.classList.add('disabled');
        nextPageItem.innerHTML = `<a class="page-link" href="#">></a>`;
        nextPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentCabangPage < totalPages) {
                currentCabangPage++;
                renderCabangTable();
            }
        });
        ul.appendChild(nextPageItem);

        // Add '>>' button
        const lastPageItem = document.createElement('li');
        lastPageItem.classList.add('page-item');
        if (currentCabangPage === totalPages) lastPageItem.classList.add('disabled');
        lastPageItem.innerHTML = `<a class="page-link" href="#" aria-label="Last">»</a>`;
        lastPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentCabangPage !== totalPages) {
                currentCabangPage = totalPages;
                renderCabangTable();
            }
        });
        ul.appendChild(lastPageItem);
    }

    // Event listener for search input on Cabang page
    document.getElementById('search-input-cabang').addEventListener('input', function () {
        searchCabangQuery = this.value;
        currentCabangPage = 1;
        renderCabangTable();
    });

    // Removed the event listener for populating Kode Cabang as it's no longer used.
    // document.getElementById('addCabangModal').addEventListener('show.bs.modal', function () {
    //     document.getElementById('kodeCabangAuto').value = 'C' + generateRandomCode(4); 
    // });

    // Handle form submission for adding new Cabang data
    document.getElementById('addCabangForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const nama = document.getElementById('namaCabang').value;
        // Removed 'kode' constant as it's no longer sourced from the form

        if (nama) { // Validation updated to only check for 'nama'
            const newData = {
                id: dataCabangDummy.length + 1, // Simple ID generation for dummy data
                nama: nama,
                // Removed 'kode: kode,' from newData object
                stok: Math.floor(Math.random() * 1000) + 100, // Dummy stok for branch
                manager: '' // Manager is empty by default when adding new branch
            };
            dataCabangDummy.push(newData); // Add new data to our dummy array

            // Reset form and close modal
            this.reset();
            const addCabangModal = bootstrap.Modal.getInstance(document.getElementById('addCabangModal'));
            addCabangModal.hide();

            // Re-render table to show new data
            renderCabangTable();
            alert('Data Cabang berhasil ditambahkan!'); // Alert message adjusted
        } else {
            alert('Harap lengkapi Nama Cabang.'); // Alert message adjusted
        }
    });

    // Initial render for Cabang table
    renderCabangTable();
</script>
@endpush
@endsection