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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material & Kode</th> {{-- Now clearly Material --}}
                                {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis Material</th> --}} {{-- Removed Jenis Material column --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama BPT</th> {{-- Specific BPT for this material --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sales Area/Region</th> {{-- Changed "Cabang" to "Sales Area/Region" --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th> {{-- Changed "Total Stok" to "Stok Akhir" --}}
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

    // --- REVISED dataDummy for UPP Material to represent ACTUAL MATERIALS ---
    const dataDummy = [
        { id: 1, nama: 'Gas LPG 3 Kg', kode: 'LPG3001', jenis: 'LPG', stok: 150, nama_bpt: 'BPT Jakarta Timur A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 2, nama: 'Bright Gas 12 Kg', kode: 'BG1202', jenis: 'Bright Gas', stok: 90, nama_bpt: 'BPT Jakarta Timur B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 3, nama: 'Pelumas Fastron', kode: 'PFAS03', jenis: 'Pelumas', stok: 0, nama_bpt: 'BPT Bekasi A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] }, // Stok 0
        { id: 4, nama: 'Aspal Curah', kode: 'ASPC04', jenis: 'Aspal', stok: 110, nama_bpt: 'BPT Bekasi B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 5, nama: 'Avtur', kode: 'AVTR05', jenis: 'Bahan Bakar', stok: 0, nama_bpt: 'BPT Bandung A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] }, // Stok 0
        { id: 6, nama: 'Pertalite', kode: 'PRTL06', jenis: 'Bahan Bakar', stok: 95, nama_bpt: 'BPT Bandung B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 7, nama: 'Pertamina Dex', kode: 'PDEX07', jenis: 'Bahan Bakar', stok: 170, nama_bpt: 'BPT Surabaya A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 8, nama: 'Minyak Tanah', kode: 'MINT08', jenis: 'Bahan Bakar', stok: 140, nama_bpt: 'BPT Surabaya B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 9, nama: 'Asphalt Pen 60/70', kode: 'AP60709', jenis: 'Aspal', stok: 160, nama_bpt: 'BPT Malang A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 10, nama: 'Bitumen', kode: 'BITU10', jenis: 'Aspal', stok: 130, nama_bpt: 'BPT Malang B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 11, nama: 'Gas LPG 3 Kg (Extra)', kode: 'LPG311', jenis: 'LPG', stok: 200, nama_bpt: 'BPT Tangerang A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 12, nama: 'Elpiji Industri', kode: 'IND012', jenis: 'Industri', stok: 80, nama_bpt: 'BPT Tangerang B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 13, nama: 'Pelumas Meditran', kode: 'PMED13', jenis: 'Pelumas', stok: 190, nama_bpt: 'BPT Bogor A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 14, nama: 'Dexlite', kode: 'DEXL14', jenis: 'Bahan Bakar', stok: 70, nama_bpt: 'BPT Bogor B', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
        { id: 15, nama: 'Solar Industri', kode: 'SLRI15', jenis: 'Bahan Bakar', stok: 100, nama_bpt: 'BPT Cirebon A', cabang: saRegions[Math.floor(Math.random() * saRegions.length)] },
    ];
    // --- END REVISED dataDummy ---

    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function filterData() {
        return dataDummy.filter(item => {
            const matchSearch = searchQuery ?
                                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.kode.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                // item.jenis.toLowerCase().includes(searchQuery.toLowerCase()) || // Removed search by material type
                                item.nama_bpt.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.cabang.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            return matchSearch;
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
                            <p class="text-xs font-weight-bold mb-0">${item.nama_bpt}</p> {{-- Display BPT Name --}}
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.cabang}</p> {{-- Display Sales Area/Region --}}
                        </td>
                        <td class="text-center">
                            ${stockDisplay}
                        </td>
                        <td class="align-middle text-center">
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
    });

    document.getElementById('search-input').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable();
    });

    // Initial render
    renderTable();
</script>
@endpush
@endsection