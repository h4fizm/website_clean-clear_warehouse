@extends('dashboard_page.main')
@section('title', 'Data SPBE & BPT - Nama Material')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-start flex-wrap">
                <div class="d-flex flex-column">
                    <h3>Tabel Data SPBE & BPT - Nama Material</h3>
                    {{-- Dynamically display selected Sales Area --}}
                    <h6 id="current-sales-area-display">Daftar Nama SPBE & BPT yang menyediakan Nama Material di Region/Sales Area - ... </h6>
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
                        <input type="text" class="form-control" id="updateNama" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateKodePlant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control" id="updateKodePlant" required>
                    </div>
                    <div class="mb-3">
                        <label for="updateSalesArea" class="form-label">Region/Sales Area</label>
                        <select class="form-select" id="updateSalesArea" required>
                            <option value="P.Layang">P.Layang</option>
                            <option value="Sales Area Jambi">Sales Area Jambi</option>
                            <option value="SA Bengkulu">SA Bengkulu</option>
                            <option value="SA Lampung">SA Lampung</option>
                            <option value="SA Sumsel">SA Sumsel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateKabupaten" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control" id="updateKabupaten" required>
                    </div>
                    {{-- Removed Total Stok field from modal HTML --}}
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

    // Dummy data reflecting the Sales Area names
    const rawDataDummy = [
        { id: 1, nama: 'SPBE Jakarta Barat', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Budi Santoso', kode_plant: 'SPBE01' + generateRandomCode(2), kabupaten: 'Jakarta Barat' },
        { id: 2, nama: 'BPT Jakarta Utara', jenis: 'BPT', cabang: 'P.Layang', manager: 'Siti Aminah', kode_plant: 'BPT02' + generateRandomCode(2), kabupaten: 'Jakarta Utara' },
        { id: 3, nama: 'SPBE Jakarta Selatan', jenis: 'SPBE', cabang: 'P.Layang', manager: '', kode_plant: 'SPBE03' + generateRandomCode(2), kabupaten: 'Jakarta Selatan' },
        { id: 4, nama: 'BPT Jakarta Pusat', jenis: 'BPT', cabang: 'P.Layang', manager: 'Dewi Lestari', kode_plant: 'BPT04' + generateRandomCode(2), kabupaten: 'Jakarta Pusat' },
        { id: 5, nama: 'SPBE Jakarta Timur', jenis: 'SPBE', cabang: 'P.Layang', manager: '', kode_plant: 'SPBE05' + generateRandomCode(2), kabupaten: 'Jakarta Timur' },
        { id: 6, nama: 'BPT Tangerang', jenis: 'BPT', cabang: 'P.Layang', manager: 'Rina Wijaya', kode_plant: 'BPT06' + generateRandomCode(2), kabupaten: 'Kab. Tangerang' },
        { id: 7, nama: 'SPBE Bekasi', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Hadi Prasetyo', kode_plant: 'SPBE07' + generateRandomCode(2), kabupaten: 'Kota Bekasi' },
        { id: 8, nama: 'BPT Depok', jenis: 'BPT', cabang: 'P.Layang', manager: '', kode_plant: 'BPT08' + generateRandomCode(2), kabupaten: 'Kota Depok' },
        { id: 9, nama: 'SPBE Bogor', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Eko Nurcahyo', kode_plant: 'SPBE09' + generateRandomCode(2), kabupaten: 'Kab. Bogor' },
        { id: 10, nama: 'BPT Cirebon', jenis: 'BPT', cabang: 'P.Layang', manager: 'Linda Kusumawati', kode_plant: 'BPT10' + generateRandomCode(2), kabupaten: 'Kota Cirebon' },
        { id: 11, nama: 'SPBE Karawang', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Fajar Indah', kode_plant: 'SPBE11' + generateRandomCode(2), kabupaten: 'Kab. Karawang' },
        { id: 12, nama: 'BPT Sukabumi', jenis: 'BPT', cabang: 'P.Layang', manager: 'Andi Jaya', kode_plant: 'BPT12' + generateRandomCode(2), kabupaten: 'Kab. Sukabumi' },
        { id: 13, nama: 'SPBE Purwakarta', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Candra Dewi', kode_plant: 'SPBE13' + generateRandomCode(2), kabupaten: 'Kab. Purwakarta' },
        { id: 14, nama: 'BPT Subang', jenis: 'BPT', cabang: 'P.Layang', manager: 'Dina Permata', kode_plant: 'BPT14' + generateRandomCode(2), kabupaten: 'Kab. Subang' },
        { id: 15, nama: 'SPBE Majalengka', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Eka Putra', kode_plant: 'SPBE15' + generateRandomCode(2), kabupaten: 'Kab. Majalengka' },
        { id: 16, nama: 'BPT Sumedang', jenis: 'BPT', cabang: 'P.Layang', manager: 'Fita Sari', kode_plant: 'BPT16' + generateRandomCode(2), kabupaten: 'Kab. Sumedang' },
        { id: 17, nama: 'SPBE Garut', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Gilang Ramadhan', kode_plant: 'SPBE17' + generateRandomCode(2), kabupaten: 'Kab. Garut' },
        { id: 18, nama: 'BPT Tasikmalaya', jenis: 'BPT', cabang: 'P.Layang', manager: 'Hani Fauziah', kode_plant: 'BPT18' + generateRandomCode(2), kabupaten: 'Kota Tasikmalaya' },
        { id: 19, nama: 'SPBE Indramayu', jenis: 'SPBE', cabang: 'P.Layang', manager: 'Imran Syah', kode_plant: 'SPBE19' + generateRandomCode(2), kabupaten: 'Kab. Indramayu' },
        { id: 20, nama: 'BPT Kuningan', jenis: 'BPT', cabang: 'P.Layang', manager: 'Juwita Nur', kode_plant: 'BPT20' + generateRandomCode(2), kabupaten: 'Kab. Kuningan' }
    ];

    // Get current Sales Area from URL (assuming it's passed as a query parameter, e.g., ?sales_area=P.Layang)
    const urlParams = new URLSearchParams(window.location.search);
    const initialSalesArea = urlParams.get('sales_area') || 'P.Layang'; // Default to P.Layang if not specified

    // Filter dataDummy based on initial Sales Area.
    const dataDummy = rawDataDummy.filter(item => item.cabang === initialSalesArea);


    let currentSalesArea = initialSalesArea; // The sales area this page is currently displaying
    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function filterData() {
        return dataDummy.filter(item => {
            const matchSearch = searchQuery ?
                                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kode_plant.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kabupaten.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            return matchSearch;
        });
    }

    function updatePageTitleAndSalesAreaDisplay() {
        // Update the h6 tag to show the current Sales Area
        const displayElement = document.getElementById('current-sales-area-display');
        if (displayElement) {
            displayElement.textContent = `Daftar Nama SPBE & BPT yang menyediakan Nama Material di Region/Sales Area - ${currentSalesArea}`;
        }
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

                const detailUrl = `/spbe-bpt/detail?id=${item.id}&nama=${encodeURIComponent(item.nama)}&jenis=${encodeURIComponent(item.jenis)}`;

                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                ${iconHtml}
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="${detailUrl}" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary" style="cursor: pointer;">
                                        ${item.nama}
                                    </a>
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
                                data-kodeplant="${item.kode_plant}"
                                data-salesarea="${item.cabang}"
                                data-kabupaten="${item.kabupaten}"
                                {{-- Removed data-stok as it's no longer editable via this modal --}}
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
                    const kodePlant = this.getAttribute('data-kodeplant');
                    const salesArea = this.getAttribute('data-salesarea');
                    const kabupaten = this.getAttribute('data-kabupaten');
                    // const stok = parseInt(this.getAttribute('data-stok')); // No longer needed here


                    document.getElementById('updateId').value = id;
                    document.getElementById('updateNama').value = nama;
                    document.getElementById('updateKodePlant').value = kodePlant;
                    document.getElementById('updateSalesArea').value = salesArea;
                    document.getElementById('updateKabupaten').value = kabupaten;
                    // document.getElementById('updateStok').value = stok; // No longer needed for setting modal value

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
        // Set the initial Sales Area display based on URL param
        updatePageTitleAndSalesAreaDisplay();

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
            const updatedKodePlant = document.getElementById('updateKodePlant').value;
            const updatedSalesArea = document.getElementById('updateSalesArea').value;
            const updatedKabupaten = document.getElementById('updateKabupaten').value;
            // const updatedStok = parseInt(document.getElementById('updateStok').value); // No longer reading from modal


            const itemIndex = dataDummy.findIndex(item => item.id === id);
            if (itemIndex !== -1) {
                dataDummy[itemIndex].nama = updatedNama;
                dataDummy[itemIndex].kode_plant = updatedKodePlant;
                dataDummy[itemIndex].cabang = updatedSalesArea; // Update 'cabang' as it represents Sales Area
                dataDummy[itemIndex].kabupaten = updatedKabupaten;
                // dataDummy[itemIndex].stok = updatedStok; // No longer updating stok from modal

                Swal.fire(
                    'Berhasil!',
                    'Data telah berhasil diperbarui.',
                    'success'
                );
                bootstrap.Modal.getInstance(document.getElementById('updateDataModal')).hide();
                renderTable(); // Re-render the table to reflect changes
            } else {
                Swal.fire(
                    'Gagal!',
                    'Data tidak ditemukan.',
                    'error'
                );
            }
        });

        // Initial render
        renderTable();
    });
</script>
@endpush
@endsection