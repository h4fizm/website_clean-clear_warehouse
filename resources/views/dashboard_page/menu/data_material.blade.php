@extends('dashboard_page.main')
@section('title', 'Data Material - Nama SPBE/BPT') {{-- Title updated based on your last context --}}
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h5>Tabel Data Material - Nama Cabang</h5> {{-- Updated title in header --}}
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                    {{-- Search --}}
                    <input type="text" id="search-input-material" class="form-control form-control-sm" placeholder="Cari Nama, Kode atau SPBE/BPT..." style="width: 250px; height: 55px;"> {{-- Adjusted height to match buttons --}}

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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th> {{-- Changed from Nama Produk --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th> {{-- Changed from Kode Produk --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE/BPT</th> {{-- New column --}}
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
                        <label for="kodeMaterial" class="form-label">Kode Material</label> {{-- Changed ID to kodeMaterial --}}
                        <input type="text" class="form-control" id="kodeMaterial" required> {{-- Removed readonly, not auto-generated now --}}
                    </div>
                    <div class="mb-3">
                        <label for="spbeBptMaterial" class="form-label">Nama SPBE/BPT</label> {{-- New dropdown --}}
                        <select class="form-select" id="spbeBptMaterial" required>
                            <option value="">Pilih SPBE/BPT</option>
                            <option value="SPBE Cakung">SPBE Cakung</option>
                            <option value="BPT Jakarta Timur">BPT Jakarta Timur</option>
                            <option value="SPBE Bekasi">SPBE Bekasi</option>
                            <option value="BPT Depok">BPT Depok</option>
                            <option value="SPBE Bandung">SPBE Bandung</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="totalStokMaterial" class="form-label">Total Stok</label>
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
    // Function to generate a random code with a fixed number of digits (still useful for other parts, but Kode Material is now manual)
    function generateRandomCode(length) {
        let result = '';
        const characters = '0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    // Dummy data for Material table - Added spbe_bpt_nama
    const dataMaterialDummy = [
        { id: 1, nama: 'Material A', kode: 'M1001', stok: 150, spbe_bpt_nama: 'SPBE Cakung' },
        { id: 2, nama: 'Material B', kode: 'M1002', stok: 200, spbe_bpt_nama: 'BPT Jakarta Timur' },
        { id: 3, nama: 'Material C', kode: 'M1003', stok: 75, spbe_bpt_nama: 'SPBE Bekasi' },
        { id: 4, nama: 'Material D', kode: 'M1004', stok: 300, spbe_bpt_nama: 'BPT Depok' },
        { id: 5, nama: 'Material E', kode: 'M1005', stok: 120, spbe_bpt_nama: 'SPBE Bandung' },
        { id: 6, nama: 'Material F', kode: 'M1006', stok: 90, spbe_bpt_nama: 'SPBE Cakung' },
        { id: 7, nama: 'Material G', kode: 'M1007', stok: 50, spbe_bpt_nama: 'BPT Jakarta Timur' },
        { id: 8, nama: 'Material H', kode: 'M1008', stok: 250, spbe_bpt_nama: 'SPBE Bekasi' },
        { id: 9, nama: 'Material I', kode: 'M1009', stok: 180, spbe_bpt_nama: 'BPT Depok' },
        { id: 10, nama: 'Material J', kode: 'M1010', stok: 100, spbe_bpt_nama: 'SPBE Bandung' },
        { id: 11, nama: 'Material K', kode: 'M1011', stok: 500, spbe_bpt_nama: 'SPBE Cakung' },
        { id: 12, nama: 'Material L', kode: 'M1012', stok: 0, spbe_bpt_nama: 'BPT Jakarta Timur' }, // Example for 0 stock
    ];

    let searchMaterialQuery = '';
    let currentMaterialPage = 0; // Changed to 0-indexed
    const itemsPerMaterialPage = 10;
    const maxMaterialPagesToShow = 5;

    function filterMaterialData() {
        return dataMaterialDummy.filter(item => {
            const matchSearch = searchMaterialQuery ?
                                (item.nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.kode.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.spbe_bpt_nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()))
                                : true;
            return matchSearch;
        });
    }

    function renderMaterialTable() {
        const tbody = document.querySelector('#table-material tbody');
        const noData = document.getElementById('no-data-material');
        const data = filterMaterialData();
        const start = currentMaterialPage * itemsPerMaterialPage; // Adjusted for 0-indexed
        const end = start + itemsPerMaterialPage;
        const paginated = data.slice(start, end);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            // Updated colspan to 6 (No, Nama Material, Kode Material, Nama SPBE/BPT, Total Stok, Aksi)
            noData.style.display = 'block';
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Tidak ada data material ditemukan.</td></tr>';
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                // Icon for material - using fa-cube as a generic product/material icon
                const iconHtml = `<span class="badge bg-gradient-warning rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-cube text-white" style="font-size: 0.75rem;"></i></span>`;

                const stockText = item.stok === 0 ?
                                  '<span class="text-danger text-xs font-weight-bold">Stok kosong</span>' :
                                  `<span class="text-xs font-weight-bold">${item.stok} pcs</span>`;

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
                        <td> {{-- New column for Nama SPBE/BPT --}}
                            <p class="text-xs font-weight-bold mb-0">${item.spbe_bpt_nama}</p>
                        </td>
                        <td class="text-center">
                            ${stockText}
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-info text-white text-xs edit-material-btn" style="cursor:pointer;" data-id="${item.id}" data-nama="${item.nama}" data-kode="${item.kode}" data-stok="${item.stok}" data-spbe-bpt="${item.spbe_bpt_nama}">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs ms-1 delete-material-btn" style="cursor:pointer;" data-id="${item.id}">Hapus</span>
                        </td>
                    </tr>
                `;
            });

            // Attach event listeners for edit and delete buttons after rendering
            document.querySelectorAll('.edit-material-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const nama = this.getAttribute('data-nama');
                    const kode = this.getAttribute('data-kode');
                    const stok = parseInt(this.getAttribute('data-stok'));
                    const spbeBpt = this.getAttribute('data-spbe-bpt');

                    // Populate the modal fields
                    document.getElementById('editMaterialId').value = id;
                    document.getElementById('editNamaMaterial').value = nama;
                    document.getElementById('editKodeMaterial').value = kode;
                    document.getElementById('editTotalStokMaterial').value = stok;
                    document.getElementById('editSpbeBptMaterial').value = spbeBpt;

                    // Show the edit modal (you'll need to create this modal)
                    const editMaterialModal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
                    editMaterialModal.show();
                });
            });

            document.querySelectorAll('.delete-material-btn').forEach(button => {
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
                            const index = dataMaterialDummy.findIndex(item => item.id === id);
                            if (index !== -1) {
                                dataMaterialDummy.splice(index, 1);
                                renderMaterialTable();
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

        renderMaterialPagination(data.length);
    }

    function renderMaterialPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerMaterialPage);
        const ul = document.getElementById('pagination-material');
        ul.innerHTML = '';

        // Add '<<' button
        const firstPageItem = document.createElement('li');
        firstPageItem.classList.add('page-item');
        if (currentMaterialPage === 0 || totalPages === 0) firstPageItem.classList.add('disabled'); // Adjusted for 0-indexed
        firstPageItem.innerHTML = `<a class="page-link" href="#" aria-label="First">«</a>`;
        firstPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage !== 0) { // Adjusted for 0-indexed
                currentMaterialPage = 0; // Adjusted for 0-indexed
                renderMaterialTable();
            }
        });
        ul.appendChild(firstPageItem);

        // Add 'Previous' button
        const prevPageItem = document.createElement('li');
        prevPageItem.classList.add('page-item');
        if (currentMaterialPage === 0) prevPageItem.classList.add('disabled'); // Adjusted for 0-indexed
        prevPageItem.innerHTML = `<a class="page-link" href="#"><</a>`;
        prevPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage > 0) { // Adjusted for 0-indexed
                currentMaterialPage--;
                renderMaterialTable();
            }
        });
        ul.appendChild(prevPageItem);

        let startPage = Math.max(0, currentMaterialPage - Math.floor(maxMaterialPagesToShow / 2)); // Adjusted for 0-indexed
        let endPage = Math.min(totalPages, startPage + maxMaterialPagesToShow); // Adjusted for 0-indexed and exclusive end

        if (endPage - startPage < maxMaterialPagesToShow && startPage > 0) {
            startPage = Math.max(0, endPage - maxMaterialPagesToShow); // Adjusted for 0-indexed
        }

        for (let i = startPage; i < endPage; i++) { // Loop condition changed
            const li = document.createElement('li');
            li.classList.add('page-item');
            if (i === currentMaterialPage) li.classList.add('active');
            li.innerHTML = `<a class="page-link" href="#">${i + 1}</a>`;
            li.addEventListener('click', function(e) {
                e.preventDefault();
                currentMaterialPage = i; // Now correctly 0-indexed
                renderMaterialTable();
            });
            ul.appendChild(li);
        }

        // Add 'Next' button
        const nextPageItem = document.createElement('li');
        nextPageItem.classList.add('page-item');
        if (currentMaterialPage === totalPages - 1 || totalPages === 0) nextPageItem.classList.add('disabled'); // Adjusted for 0-indexed
        nextPageItem.innerHTML = `<a class="page-link" href="#">></a>`;
        nextPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage < totalPages - 1) { // Adjusted for 0-indexed
                currentMaterialPage++;
                renderMaterialTable();
            }
        });
        ul.appendChild(nextPageItem);

        // Add '>>' button
        const lastPageItem = document.createElement('li');
        lastPageItem.classList.add('page-item');
        if (currentMaterialPage === totalPages - 1 || totalPages === 0) lastPageItem.classList.add('disabled'); // Adjusted for 0-indexed
        lastPageItem.innerHTML = `<a class="page-link" href="#" aria-label="Last">»</a>`;
        lastPageItem.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentMaterialPage !== totalPages - 1) { // Adjusted for 0-indexed
                currentMaterialPage = totalPages - 1; // Adjusted for 0-indexed
                renderMaterialTable();
            }
        });
        ul.appendChild(lastPageItem);
    }

    // Event listener for search input on Material page
    document.getElementById('search-input-material').addEventListener('input', function () {
        searchMaterialQuery = this.value;
        currentMaterialPage = 0; // Reset to 0-indexed first page
        renderMaterialTable();
    });

    // Populate Kode Material automatically removed because it's fillable now.
    // document.getElementById('addMaterialModal').addEventListener('show.bs.modal', function () {
    //     document.getElementById('kodeMaterialAuto').value = 'MAT-' + generateRandomCode(4); 
    //     document.getElementById('totalStokMaterial').value = '0'; 
    // });

    // Handle form submission for adding new Material data
    document.getElementById('addMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const nama = document.getElementById('namaMaterial').value;
        // Changed ID from kodeMaterialAuto to kodeMaterial
        const kode = document.getElementById('kodeMaterial').value; 
        const spbeBpt = document.getElementById('spbeBptMaterial').value; // Get SPBE/BPT selection
        const stok = parseInt(document.getElementById('totalStokMaterial').value);

        if (nama && kode && spbeBpt && !isNaN(stok) && stok >= 0) { // Validate all fields
            const newData = {
                id: dataMaterialDummy.length > 0 ? Math.max(...dataMaterialDummy.map(d => d.id)) + 1 : 1, // Ensure unique ID
                nama: nama,
                kode: kode,
                stok: stok,
                spbe_bpt_nama: spbeBpt // Add SPBE/BPT name to new data
            };
            dataMaterialDummy.push(newData);

            // Reset form and close modal
            this.reset();
            const addMaterialModal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
            addMaterialModal.hide();

            // Re-render table to show new data
            renderMaterialTable();
            alert('Data Material berhasil ditambahkan!');
        } else {
            alert('Harap lengkapi semua kolom: Nama Material, Kode Material, Nama SPBE/BPT, dan Total Stok dengan benar.');
        }
    });

    // Initial render for Material table
    renderMaterialTable();
</script>
@endpush
@endsection