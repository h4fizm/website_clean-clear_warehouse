@extends('dashboard_page.main')
@section('title', 'Data Material - Nama SPBE/BPT')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h5>Tabel Data Material - Nama SPBE/BPT</h5>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                    {{-- Search --}}
                    <input type="text" id="search-input-material" class="form-control form-control-sm" placeholder="Cari Nama atau Kode Material..." style="width: 250px; height: 55px;">

                    {{-- Add Material Button --}}
                    <button class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#addMaterialModal"
                            title="Tambah Data Material"
                            style="width: 38px; height: 38px; padding: 0;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Produk</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Produk</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-material" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0" id="pagination-material">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Add Material Modal --}}
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Tambah Data Material Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm">
                    <div class="mb-3">
                        <label for="namaMaterial" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="namaMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="kodeMaterialAuto" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="kodeMaterialAuto" readonly> {{-- Auto-generated and read-only --}}
                    </div>
                    <div class="mb-3">
                        <label for="totalStokMaterial" class="form-label">Total Stok</label> {{-- Form input baru --}}
                        <input type="number" class="form-control" id="totalStokMaterial" min="0" value="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="addMaterialForm">Simpan Material</button>
            </div>
        </div>
    </div>
</div>

{{-- Script for Material Page --}}
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

    // Dummy data for Material table
    const dataMaterialDummy = [
        { id: 1, nama: 'Material A', kode: 'M1001', stok: 150 },
        { id: 2, nama: 'Material B', kode: 'M1002', stok: 200 },
        { id: 3, nama: 'Material C', kode: 'M1003', stok: 75 },
        { id: 4, nama: 'Material D', kode: 'M1004', stok: 300 },
        { id: 5, nama: 'Material E', kode: 'M1005', stok: 120 },
        { id: 6, nama: 'Material F', kode: 'M1006', stok: 90 },
        { id: 7, nama: 'Material G', kode: 'M1007', stok: 50 },
        { id: 8, nama: 'Material H', kode: 'M1008', stok: 250 },
        { id: 9, nama: 'Material I', kode: 'M1009', stok: 180 },
        { id: 10, nama: 'Material J', kode: 'M1010', stok: 100 },
    ];

    let searchMaterialQuery = '';
    let currentMaterialPage = 1;
    const itemsPerMaterialPage = 10;
    const maxMaterialPagesToShow = 5;

    function filterMaterialData() {
        return dataMaterialDummy.filter(item => {
            const matchSearch = searchMaterialQuery ?
                                (item.nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.kode.toLowerCase().includes(searchMaterialQuery.toLowerCase()))
                                : true;
            return matchSearch;
        });
    }

    function renderMaterialTable() {
        const tbody = document.querySelector('#table-material tbody');
        const noData = document.getElementById('no-data-material');
        const data = filterMaterialData();
        const start = (currentMaterialPage - 1) * itemsPerMaterialPage;
        const paginated = data.slice(start, start + itemsPerMaterialPage);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                // Icon for material - using fa-cube as a generic product/material icon
                const iconHtml = `<span class="badge bg-gradient-warning rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-cube text-white" style="font-size: 0.75rem;"></i></span>`;

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
                            <p class="text-xs text-secondary mb-0">${item.kode}</p>
                        </td>
                        <td class="text-center">
                            <span class="text-xs font-weight-bold">${item.stok} pcs</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs" style="cursor:pointer;">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1" style="cursor:pointer;">Hapus</span>
                        </td>
                    </tr>
                `;
            });
        }

        renderMaterialPagination(data.length);
    }

    function renderMaterialPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerMaterialPage);
        const ul = document.getElementById('pagination-material');
        ul.innerHTML = '';

        // Add '<<' button
        const firstPageItem = document.createElement('li');
        firstPageItem.classList.add('page-item');
        if (currentMaterialPage === 1) firstPageItem.classList.add('disabled');
        firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">«</a>`;
        firstPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage !== 1) {
                currentMaterialPage = 1;
                renderMaterialTable();
            }
        });
        ul.appendChild(firstPageItem);

        // Add 'Previous' button
        const prevPageItem = document.createElement('li');
        prevPageItem.classList.add('page-item');
        if (currentMaterialPage === 1) prevPageItem.classList.add('disabled');
        prevPageItem.innerHTML = `<a class="page-link" href="#"><</a>`;
        prevPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage > 1) {
                currentMaterialPage--;
                renderMaterialTable();
            }
        });
        ul.appendChild(prevPageItem);

        let startPage = Math.max(1, currentMaterialPage - Math.floor(maxMaterialPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxMaterialPagesToShow - 1);

        if (endPage - startPage + 1 < maxMaterialPagesToShow) {
            startPage = Math.max(1, endPage - maxMaterialPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.classList.add('page-item');
            if (i === currentMaterialPage) li.classList.add('active');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', function(e) {
                e.preventDefault();
                currentMaterialPage = i;
                renderMaterialTable();
            });
            ul.appendChild(li);
        }

        // Add 'Next' button
        const nextPageItem = document.createElement('li');
        nextPageItem.classList.add('page-item');
        if (currentMaterialPage === totalPages) nextPageItem.classList.add('disabled');
        nextPageItem.innerHTML = `<a class="page-link" href="#">></a>`;
        nextPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage < totalPages) {
                currentMaterialPage++;
                renderMaterialTable();
            }
        });
        ul.appendChild(nextPageItem);

        // Add '>>' button
        const lastPageItem = document.createElement('li');
        lastPageItem.classList.add('page-item');
        if (currentMaterialPage === totalPages) lastPageItem.classList.add('disabled');
        lastPageItem.innerHTML = `<a class="page-link" href="#" aria-label="Last">»</a>`;
        lastPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage !== totalPages) {
                currentMaterialPage = totalPages;
                renderMaterialTable();
            }
        });
        ul.appendChild(lastPageItem);
    }

    // Event listener for search input on Material page
    document.getElementById('search-input-material').addEventListener('input', function () {
        searchMaterialQuery = this.value;
        currentMaterialPage = 1;
        renderMaterialTable();
    });

    // Populate Kode Material automatically when modal is shown
    document.getElementById('addMaterialModal').addEventListener('show.bs.modal', function () {
        document.getElementById('kodeMaterialAuto').value = 'MAT-' + generateRandomCode(4); // Example: MAT-1234
        document.getElementById('totalStokMaterial').value = '0'; // Reset total stok to 0
    });

    // Handle form submission for adding new Material data
    document.getElementById('addMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const nama = document.getElementById('namaMaterial').value;
        const kode = document.getElementById('kodeMaterialAuto').value;
        const stok = parseInt(document.getElementById('totalStokMaterial').value); // Ambil nilai stok dan konversi ke integer

        if (nama && kode && !isNaN(stok)) { // Pastikan stok juga valid
            const newData = {
                id: dataMaterialDummy.length + 1, // Simple ID generation for dummy data
                nama: nama,
                kode: kode,
                stok: stok // Tambahkan stok ke data baru
            };
            dataMaterialDummy.push(newData); // Add new data to our dummy array

            // Reset form and close modal
            this.reset();
            const addMaterialModal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
            addMaterialModal.hide();

            // Re-render table to show new data
            renderMaterialTable();
            alert('Data Material berhasil ditambahkan!');
        } else {
            alert('Harap lengkapi Nama Material, Kode Material, dan Total Stok dengan benar.');
        }
    });

    // Initial render for Material table
    renderMaterialTable();
</script>
@endpush
@endsection