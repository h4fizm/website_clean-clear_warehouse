@extends('dashboard_page.main')
@section('title', 'Data Material - ' . request()->query('spbe_bpt_nama', 'Nama SPBE/BPT'))
@section('content')

{{-- Define initial URL parameters for Blade and JavaScript access --}}
<?php
    // Mengambil nilai spbe_bpt_nama dari URL dengan nilai default yang konsisten
    $spbeBptName = request()->query('spbe_bpt_nama', 'SPBE Sukamaju');
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                {{-- Row for Title and Add Button --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Daftar Stok Material - {{ $spbeBptName }}</h3>
                    {{-- Tombol Tambah Material untuk sementara disembunyikan --}}
                    <button class="btn btn-success d-none align-items-center justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#addMaterialModal"
                            title="Tambah Data Material">
                        <i class="fas fa-plus me-2"></i> Tambah Material
                    </button>
                </div>
                
                {{-- Row for Search Input --}}
                <div class="d-flex justify-content-end align-items-center mt-3">
                    <div class="input-group input-group-sm search-input-desktop-aligned" style="max-width: 250px;">
                        <input type="text" id="search-input-material" class="form-control" placeholder="Cari Nama atau Kode Material...">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi Terakhir</th>
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
                    <nav aria-label="Page navigation material">
                        <ul class="pagination pagination-sm mb-0" id="pagination-material">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for adding new material --}}
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{-- Menambahkan kelas h4 untuk memperbesar font --}}
                <h5 class="modal-title h4" id="addMaterialModalLabel">Daftar Stok Material - {{ $spbeBptName }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm">
                    <div class="mb-3">
                        <label for="addNamaMaterial" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="addNamaMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="addKodeMaterial" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="addKodeMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="addSpbeBpt" class="form-label">Nama SPBE/BPT</label>
                        <select class="form-select" id="addSpbeBpt" required>
                            <option value="SPBE Sukamaju">SPBE Sukamaju</option>
                            <option value="BPT Sejahtera">BPT Sejahtera</option>
                            <option value="SPBE Mandiri">SPBE Mandiri</option>
                            <option value="BPT Jaya Abadi">BPT Jaya Abadi</option>
                            <option value="SPBE Maju Bersama">SPBE Maju Bersama</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="addTipe" id="add-tipe-spbe" value="SPBE">
                                <label class="form-check-label" for="add-tipe-spbe">SPBE</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="addTipe" id="add-tipe-bpt" value="BPT">
                                <label class="form-check-label" for="add-tipe-bpt">BPT</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="addTotalStok" class="form-label">Total Stok</label>
                        <input type="number" class="form-control" id="addTotalStok" min="0" value="0" required>
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

{{-- Modal for editing material --}}
<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMaterialModalLabel">Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMaterialForm">
                    <input type="hidden" id="editMaterialId">
                    <div class="mb-3">
                        <label for="editNamaMaterial" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="editNamaMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="editKodeMaterial" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="editKodeMaterial" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSpbeBpt" class="form-label">Nama SPBE/BPT</label>
                        <select class="form-select" id="editSpbeBpt" required>
                             {{-- Options will be populated by JavaScript --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editTotalStok" class="form-label">Total Stok</label>
                        <input type="number" class="form-control" id="editTotalStok" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="editMaterialForm">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Kirim (Send) action --}}
<div class="modal fade" id="kirimMaterialModal" tabindex="-1" aria-labelledby="kirimMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kirimMaterialModalLabel">Kirim Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Card Informasi SPBE/BPT --}}
                <div class="card shadow p-3 mb-3">
                    <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Informasi SPBE/BPT</h6>
                    <div class="d-flex flex-column">
                        <p class="text-sm font-weight-bold mb-1">Nama SPBE / BPT : <span id="kirimNamaSpbeBpt" class="text-secondary font-weight-normal"></span></p>
                        <p class="text-sm font-weight-bold mb-1">Kode Plant : <span id="kirimKodePlant" class="text-secondary font-weight-normal"></span></p>
                        <p class="text-sm font-weight-bold mb-1">Region/SA : <span id="kirimRegionSa" class="text-secondary font-weight-normal"></span></p>
                        <p class="text-sm font-weight-bold mb-1">Kabupaten : <span id="kirimKabupaten" class="text-secondary font-weight-normal"></span></p>
                        <p class="text-sm font-weight-bold mb-1">Stok Saat Ini : <span id="kirimStok" class="text-success font-weight-bold"></span></p>
                    </div>
                </div>

                <form id="kirimMaterialForm">
                    <input type="hidden" id="kirimMaterialId">
                    <div class="mb-3">
                        <label for="asalTransaksi" class="form-label">Asal Transaksi</label>
                        <input type="text" class="form-control" id="asalTransaksiText" disabled>
                        <input type="hidden" id="asalTransaksi" name="asalTransaksi">
                    </div>
                    <div class="mb-3">
                        <label for="tujuanTransaksi" class="form-label">Tujuan Transaksi</label>
                        <select class="form-select" id="tujuanTransaksi" required>
                            <option value="">Pilih Tujuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenisPenambahan" value="penambahan" checked>
                                <label class="form-check-label" for="jenisPenambahan">Penambahan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenisPengurangan" value="pengurangan">
                                <label class="form-check-label" for="jenisPengurangan">Pengurangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlahStok" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlahStok" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="kirimMaterialForm">Kirim</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const selectedSpbeBptName = decodeURIComponent(urlParams.get('spbe_bpt_nama') || 'SPBE Sukamaju');
    const saRegion = decodeURIComponent(urlParams.get('sa_region') || 'SA Jambi');
    
    // Dummy data with all the requested fields
    const dataMaterialDummy = [
        { id: 1, nama: 'Gas LPG 3kg', kode: 'LPG3-001', stok_awal: 250, penerimaan: 1000, penyaluran: 800, total_stok: 450, tanggal: '2025-07-28', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 2, nama: 'Gas LPG 12kg', kode: 'LPG12-001', stok_awal: 180, penerimaan: 500, penyaluran: 350, total_stok: 330, tanggal: '2025-07-27', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 3, nama: 'Tabung 3kg', kode: 'TBG3-001', stok_awal: 75, penerimaan: 120, penyaluran: 100, total_stok: 95, tanggal: '2025-07-26', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 4, nama: 'Seal Karet', kode: 'SEAL-01', stok_awal: 300, penerimaan: 50, penyaluran: 200, total_stok: 150, tanggal: '2025-07-25', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 5, nama: 'Regulator', kode: 'REG-005', stok_awal: 120, penerimaan: 40, penyaluran: 60, total_stok: 100, tanggal: '2025-07-24', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 6, nama: 'Selang Gas', kode: 'SLG-010', stok_awal: 90, penerimaan: 30, penyaluran: 50, total_stok: 70, tanggal: '2025-07-23', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 7, nama: 'Kompor Portable', kode: 'KPR-015', stok_awal: 50, penerimaan: 10, penyaluran: 25, total_stok: 35, tanggal: '2025-07-22', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 8, nama: 'Gas 5.5kg', kode: 'LPG5.5-001', stok_awal: 250, penerimaan: 100, penyaluran: 150, total_stok: 200, tanggal: '2025-07-21', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 9, nama: 'Tabung 5.5kg', kode: 'TBG5.5-001', stok_awal: 180, penerimaan: 20, penyaluran: 30, total_stok: 170, tanggal: '2025-07-20', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 10, nama: 'Manometer', kode: 'MAN-001', stok_awal: 100, penerimaan: 50, penyaluran: 40, total_stok: 110, tanggal: '2025-07-19', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 11, nama: 'Konektor Gas', kode: 'KNG-001', stok_awal: 500, penerimaan: 100, penyaluran: 200, total_stok: 400, tanggal: '2025-07-18', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 12, nama: 'Flow Meter', kode: 'FLM-002', stok_awal: 0, penerimaan: 0, penyaluran: 0, total_stok: 0, tanggal: '2025-07-17', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 13, nama: 'Konektor Gas', kode: 'KNG-002', stok_awal: 500, penerimaan: 50, penyaluran: 150, total_stok: 400, tanggal: '2025-07-16', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 14, nama: 'Manometer', kode: 'MAN-002', stok_awal: 100, penerimaan: 20, penyaluran: 30, total_stok: 90, tanggal: '2025-07-15', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 15, nama: 'Gas 3kg', kode: 'LPG3-002', stok_awal: 50, penerimaan: 100, penyaluran: 120, total_stok: 30, tanggal: '2025-07-14', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
    ];
    
    // List of all unique SPBE/BPT names for the 'Kirim Material' modal dropdown
    const allLocations = [
        'P.Layang (Pusat)',
        ...new Set(dataMaterialDummy.map(item => item.spbe_bpt_nama))
    ].sort();

    // Data dummy untuk informasi SPBE/BPT
    const spbeBptInfoDummy = [
        { nama: 'SPBE Sukamaju', kode_plant: 'SPBE0101', region_sa: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { nama: 'BPT Sejahtera', kode_plant: 'BPT0202', region_sa: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { nama: 'BPT Jaya Abadi', kode_plant: 'BPT0303', region_sa: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { nama: 'SPBE Maju Bersama', kode_plant: 'SPBE0404', region_sa: 'SA Lampung', kabupaten: 'Pringsewu' },
        { nama: 'SPBE Mandiri', kode_plant: 'SPBE0505', region_sa: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { nama: 'P.Layang (Pusat)', kode_plant: 'PL0001', region_sa: 'Pusat', kabupaten: 'Palembang' },
    ];

    let searchMaterialQuery = '';
    let currentMaterialPage = 0;
    const itemsPerMaterialPage = 10;
    const maxMaterialPagesToShow = 5;

    function filterMaterialData() {
        const filteredData = dataMaterialDummy.filter(item => {
            const matchSpbeBpt = item.spbe_bpt_nama === selectedSpbeBptName;
            const matchSearch = searchMaterialQuery ?
                                (item.nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.kode.toLowerCase().includes(searchMaterialQuery.toLowerCase()))
                                : true;
            return matchSpbeBpt && matchSearch;
        });
        
        return filteredData;
    }

    function renderMaterialTable() {
        const tbody = document.querySelector('#table-material tbody');
        const noData = document.getElementById('no-data-material');
        const data = filterMaterialData();
        const start = currentMaterialPage * itemsPerMaterialPage;
        const end = start + itemsPerMaterialPage;
        const paginated = data.slice(start, end);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            noData.style.display = 'block';
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4">Tidak ada data material untuk ${selectedSpbeBptName}.</td></tr>`;
        } else {
            noData.style.display = 'none';
            const rowsHtml = paginated.map((item, index) => {
                const rowIndex = start + index + 1;
                // Fungsi helper untuk menentukan warna badge
                function getBadgeColor(stock) {
                    if (stock === 0) return 'danger';
                    if (stock <= 50) return 'warning';
                    return 'success';
                }
                const totalStokColor = getBadgeColor(item.total_stok);

                return `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.nama}</p>
                        </td>
                        <td>
                            <p class="text-xs text-secondary mb-0">${item.kode}</p>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-info text-white text-xs">${item.stok_awal}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-success text-white text-xs">+${item.penerimaan}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-danger text-white text-xs">-${item.penyaluran}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-${totalStokColor} text-white text-xs">${item.total_stok}</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary mb-0">${item.tanggal}</p>
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btn-icon btn-rounded btn-success kirim-material-btn p-1" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal" data-id="${item.id}" data-nama="${item.nama}" data-spbe-bpt="${item.spbe_bpt_nama}" title="Kirim Material">
                                <i class="fas fa-paper-plane text-white"></i>
                            </button>
                            <button class="btn btn-icon btn-rounded btn-info edit-material-btn p-1 ms-1" data-id="${item.id}" title="Edit Data">
                                <i class="fas fa-edit text-white"></i>
                            </button>
                            <button class="btn btn-icon btn-rounded btn-danger delete-material-btn p-1 ms-1" data-id="${item.id}" title="Hapus Data">
                                <i class="fas fa-trash-alt text-white"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            tbody.innerHTML = rowsHtml;
        }
        renderMaterialPagination(data.length);
        attachActionListeners();
    }

    function attachActionListeners() {
        document.querySelectorAll('.edit-material-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const material = dataMaterialDummy.find(item => item.id === id);
                if (material) {
                    document.getElementById('editMaterialId').value = material.id;
                    document.getElementById('editNamaMaterial').value = material.nama;
                    document.getElementById('editKodeMaterial').value = material.kode;
                    document.getElementById('editTotalStok').value = material.total_stok;
                    
                    // Populate the dropdown with all locations and select the current one
                    const editSpbeBptSelect = document.getElementById('editSpbeBpt');
                    editSpbeBptSelect.innerHTML = ''; // Clear existing options
                    allLocations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location;
                        option.textContent = location;
                        if (location === material.spbe_bpt_nama) {
                            option.selected = true;
                        }
                        editSpbeBptSelect.appendChild(option);
                    });
                    
                    const editMaterialModal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
                    editMaterialModal.show();
                }
            });
        });

        document.querySelectorAll('.delete-material-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data material ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
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
                                'Data material telah berhasil dihapus.',
                                'success'
                            );
                        }
                    }
                });
            });
        });

        // Kirim button listener
        document.querySelectorAll('.kirim-material-btn').forEach(button => {
            button.addEventListener('click', function() {
                const materialId = parseInt(this.getAttribute('data-id'));
                const spbeBptName = this.getAttribute('data-spbe-bpt');
                
                // Find the material and SPBE/BPT info
                const material = dataMaterialDummy.find(item => item.id === materialId);
                const spbeBptInfo = spbeBptInfoDummy.find(info => info.nama === spbeBptName);
                
                if (material && spbeBptInfo) {
                    document.getElementById('kirimMaterialModalLabel').textContent = `Kirim Material "${material.nama}"`;
                    
                    // Populate the Information Card
                    document.getElementById('kirimNamaSpbeBpt').textContent = spbeBptInfo.nama;
                    document.getElementById('kirimKodePlant').textContent = spbeBptInfo.kode_plant;
                    document.getElementById('kirimRegionSa').textContent = spbeBptInfo.region_sa;
                    document.getElementById('kirimKabupaten').textContent = spbeBptInfo.kabupaten;
                    document.getElementById('kirimStok').textContent = `${material.total_stok} unit`;
                    
                    // Populate Asal Transaksi input and hidden value
                    document.getElementById('asalTransaksiText').value = spbeBptName;
                    document.getElementById('asalTransaksi').value = spbeBptName;

                    // Populate Tujuan Transaksi dropdown with all locations
                    const tujuanTransaksi = document.getElementById('tujuanTransaksi');
                    tujuanTransaksi.innerHTML = `<option value="">Pilih Tujuan</option>`;
                    allLocations.forEach(location => {
                        if (location !== spbeBptName) {
                            const option = document.createElement('option');
                            option.value = location;
                            option.textContent = location;
                            tujuanTransaksi.appendChild(option);
                        }
                    });
                }
                
                // Set a default for jenisTransaksi and jumlahStok
                document.getElementById('jenisPenambahan').checked = true;
                document.getElementById('jumlahStok').value = '';
            });
        });
    }

    function renderMaterialPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerMaterialPage);
        const ul = document.getElementById('pagination-material');
        ul.innerHTML = '';

        function createButton(label, page, disabled = false, active = false) {
            const li = document.createElement('li');
            li.classList.add('page-item');
            if (disabled) li.classList.add('disabled');
            if (active) li.classList.add('active');
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.addEventListener('click', e => {
                    e.preventDefault();
                    currentMaterialPage = page;
                    renderMaterialTable();
                });
            }
            return li;
        }

        const startPage = Math.max(0, currentMaterialPage - Math.floor(maxMaterialPagesToShow / 2));
        const endPage = Math.min(totalPages, startPage + maxMaterialPagesToShow);

        ul.appendChild(createButton('«', 0, currentMaterialPage === 0));
        ul.appendChild(createButton('‹', currentMaterialPage - 1, currentMaterialPage === 0));

        for (let i = startPage; i < endPage; i++) {
            ul.appendChild(createButton(i + 1, i, false, i === currentMaterialPage));
        }

        ul.appendChild(createButton('›', currentMaterialPage + 1, currentMaterialPage === totalPages - 1 || totalPages === 0));
        ul.appendChild(createButton('»', totalPages - 1, currentMaterialPage === totalPages - 1 || totalPages === 0));
    }
    
    document.getElementById('editMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = parseInt(document.getElementById('editMaterialId').value);
        const material = dataMaterialDummy.find(item => item.id === id);
        if (material) {
            material.nama = document.getElementById('editNamaMaterial').value;
            material.kode = document.getElementById('editKodeMaterial').value;
            material.spbe_bpt_nama = document.getElementById('editSpbeBpt').value;
            material.total_stok = parseInt(document.getElementById('editTotalStok').value);
            
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editMaterialModal'));
            editModal.hide();
            renderMaterialTable();
            Swal.fire('Berhasil Diperbarui!', 'Data material berhasil diperbarui.', 'success');
        } else {
            Swal.fire('Gagal!', 'Data material tidak ditemukan.', 'error');
        }
    });

    document.getElementById('addMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const nama = document.getElementById('addNamaMaterial').value;
        const kode = document.getElementById('addKodeMaterial').value;
        const spbeBpt = document.getElementById('addSpbeBpt').value;
        const tipe = document.querySelector('input[name="addTipe"]:checked').value;
        const stok = parseInt(document.getElementById('addTotalStok').value);

        if (nama && kode && spbeBpt && tipe && !isNaN(stok) && stok >= 0) {
            const newData = {
                id: dataMaterialDummy.length > 0 ? Math.max(...dataMaterialDummy.map(d => d.id)) + 1 : 1,
                nama: nama,
                kode: kode,
                stok_awal: stok,
                penerimaan: 0,
                penyaluran: 0,
                total_stok: stok,
                tanggal: new Date().toISOString().split('T')[0],
                spbe_bpt_nama: spbeBpt,
                tipe: tipe,
                sa_region: saRegion, // Use the SA region from the URL
                kabupaten: 'Dummy Kabupaten'
            };
            dataMaterialDummy.push(newData);
            this.reset();
            const addModal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
            addModal.hide();
            renderMaterialTable();
            Swal.fire('Berhasil Ditambahkan!', 'Data material baru berhasil ditambahkan.', 'success');
        } else {
            Swal.fire('Gagal!', 'Harap lengkapi semua kolom dengan benar.', 'error');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('search-input-material').addEventListener('input', function () {
            searchMaterialQuery = this.value;
            currentMaterialPage = 0;
            renderMaterialTable();
        });

        document.getElementById('kirimMaterialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const asal = document.getElementById('asalTransaksi').value;
            const tujuan = document.getElementById('tujuanTransaksi').value;
            const jenis = document.querySelector('input[name="jenisTransaksi"]:checked').value;
            const jumlah = document.getElementById('jumlahStok').value;

            // Simple validation
            if (!asal || !tujuan || !jumlah) {
                Swal.fire('Gagal!', 'Harap lengkapi semua form.', 'error');
                return;
            }

            if (asal === tujuan) {
                Swal.fire('Gagal!', 'Asal dan tujuan tidak boleh sama.', 'error');
                return;
            }

            console.log(`Kirim material: Asal: ${asal}, Tujuan: ${tujuan}, Jenis: ${jenis}, Jumlah: ${jumlah}`);

            // Reset form and close modal
            this.reset();
            const kirimModal = bootstrap.Modal.getInstance(document.getElementById('kirimMaterialModal'));
            kirimModal.hide();
            Swal.fire('Berhasil Dikirim!', 'Transaksi material telah berhasil dicatat.', 'success');
        });

        renderMaterialTable();
    });
</script>
@endpush

{{-- CSS untuk halaman transaksi (Tidak Diubah) --}}
<style>
    /* General styles for welcome card */
    .welcome-card {
        background-color: white;
        color: #344767;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        padding: 1.5rem !important; /* Adjusted padding to p-4 equivalent */
    }

    .welcome-card-icon {
        height: 60px;
        width: auto;
        opacity: 0.9;
    }

    .welcome-card-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        background-size: 60px 60px;
        opacity: 0.2;
        pointer-events: none;
    }

    /* Desktop styles for filters and search */
    @media (min-width: 768px) {
        /* Apply these styles from 'md' breakpoint and up */
        .desktop-filter-row-top {
            justify-content: space-between !important;
            /* Distribute items with space between */
            align-items: flex-end;
            /* Align items to the bottom */
            margin-bottom: 0.5rem;
            /* Add some space below this row */
        }

        .branch-selection-text-desktop {
            margin-bottom: 0.5rem;
            /* Standard margin for text above buttons */
            white-space: nowrap;
            /* Prevent text wrapping */
        }

        .btn-branch-custom {
            padding: 0.4rem 0.6rem;
            /* Slightly smaller padding for buttons */
            font-size: 0.78rem;
            /* Slightly smaller font size */
        }

        .date-filter-desktop-container {
            /* This container holds "Dari" date input and "Sampai" date input */
            /* We need to measure its total width */
            display: flex;
            /* Ensure it's a flex container */
            align-items: center;
            /* Align items vertically */
            gap: 0.5rem;
            /* Standard gap */
            flex-wrap: nowrap;
            /* Keep all elements in one line */
            width: auto;
            /* Allow content to define width */
            flex-shrink: 0;
            margin-left: auto;
            /* Push to the right */
        }

        .date-input {
            width: 120px;
            /* Specific width for date inputs on desktop */
            height: 40px;
            /* Adjusted height for date inputs */
            min-width: unset;
            /* Remove min-width inherited from mobile */
        }

        .date-range-picker label {
            /* Reusing existing class, targetting labels inside it */
            white-space: nowrap;
            /* Prevent label wrapping */
            flex-shrink: 0;
            /* Prevent label from shrinking */
        }

        /* Search input specific styles for desktop alignment */
        .search-input-desktop-aligned {
            height: 40px;
            /* Adjusted height for desktop search input */
            max-width: 310px;
            /* Set a max-width to control its overall size */
            /* Instead of fixed width, we will dynamically set it via JS or use a calculated max-width */
            /* Using 'auto' and 'margin-left: auto' with its parent's 'justify-content: flex-end' for positioning */
        }

        .search-input-desktop-aligned .form-control,
        .search-input-aligned .input-group-text {
            height: 40px;
            /* Match the new height */
        }

        /* Order of columns for desktop */
        .order-md-1 {
            order: 1;
        }

        .order-md-2 {
            order: 2;
        }

        .order-md-3 {
            order: 3;
        }

        /* Ensuring columns adjust correctly for flex layout */
        .desktop-filter-row-top>div {
            flex-grow: 0;
            flex-shrink: 0;
        }

        .col-12.mt-3.order-3.order-md-3 {
            /* The row containing only the search input */
            display: flex;
            /* Make it a flex container */
            justify-content: flex-end;
            /* Push content (search bar) to the right */
            width: 100%;
            /* Ensure it takes full width of the row */
        }
    }

    /* Mobile specific styles (max-width 767.98px for Bootstrap's 'md' breakpoint) */
    @media (max-width: 767.98px) {
        /* --- Welcome Section Title Adjustment for Mobile --- */
        .welcome-card {
            padding: 1rem !important; /* Reduce padding for mobile */
        }

        .welcome-card .card-body {
            flex-direction: column; /* Stack items vertically */
            align-items: center; /* Center items horizontally */
        }

        .welcome-card .card-body > div {
            width: 100%; /* Take full width */
            text-align: center; /* Center text within these divs */
        }

        .welcome-card-icon {
            margin-bottom: 0.5rem; /* Space below icon on mobile */
            margin-top: 0.5rem; /* Space above icon on mobile */
        }

        #summary-title, #summary-text {
            text-align: center !important;
        }
        #summary-title {
            font-size: 1.25rem !important;
        }
        #summary-text {
            font-size: 0.8rem !important;
        }
        /* End Welcome Section */


        /* --- Table Branch Name Title Adjustment for Mobile --- */
        #table-branch-name {
            text-align: center !important;
            font-size: 1.25rem !important; /* Adjust as needed, e.g., 1rem or 0.9rem */
            margin-bottom: 1rem !important; /* Add some space below the title */
        }


        .export-excel-btn {
            height: 38px !important;
            /* Make button smaller */
            font-size: 0.8rem;
            /* Smaller font size */
            padding: 0.5rem 1rem;
            /* Adjust padding */
            margin-top: 1rem;
            /* Adjust padding */
        }

        .export-excel-btn .fas {
            margin-right: 0.5rem;
            /* Adjust icon spacing */
        }

        .branch-selection-text {
            text-align: center !important;
            /* Center the text */
            margin-bottom: 0.5rem !important;
            /* Reduce bottom margin */
        }

        .branch-buttons {
            justify-content: center !important;
            /* Center buttons */
            gap: 0.25rem;
            /* Reduce gap between buttons */
            margin-bottom: 1rem;
            /* Add margin below buttons for mobile */
        }

        .btn-branch-custom {
            padding: 0.3rem 0.6rem;
            /* Smaller padding for buttons */
            font-size: 0.75rem;
            /* Smaller font size for buttons */
            flex-grow: 1;
            /* Allow buttons to grow in mobile to fill space */
            min-width: unset;
            /* Remove min-width to allow more flexibility */
        }

        /* Adjust button width for smaller screens if they are too wide */
        .branch-buttons button {
            flex: 1 1 auto;
            /* Allow buttons to wrap and occupy available space */
            margin: 2px;
            /* Small margin for visual separation */
        }

        .date-range-picker {
            flex-direction: column;
            /* Stack date pickers vertically */
            align-items: center;
            /* Center items in column */
            width: 100%;
            /* Full width */
            gap: 0.5rem !important;
            /* Gap between stacked items */
        }

        .date-filter-desktop-container {
            flex-direction: column !important;
            width: 100% !important;
            align-items: center !important;
            margin-top: 0.5rem !important;
            margin-left: 0 !important;
        }

        .date-input {
            width: 100% !important;
            height: 38px !important;
        }

        .date-range-picker label {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }

        /* --- Specific changes for Search Input Group in Mobile --- */
        .search-input-group {
            width: 100% !important;
            height: 38px !important;
            margin-top: 0.5rem;
        }

        .search-input-group .form-control {
            height: 38px !important;
            font-size: 0.85rem !important;
            padding-right: 0.5rem;
        }

        .search-input-group .input-group-text {
            height: 38px !important;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem !important;
        }
        /* --- End of Mobile Specific changes for Search Input Group --- */

        .card-header {
            padding: 1rem !important;
        }

        #table-material-1 thead th {
            font-size: 0.65rem !important;
        }

        #table-material-1 tbody td {
            font-size: 0.75rem !important;
        }

        #table-material-1 tbody td,
        #table-material-1 thead th {
            padding: 0.5rem 0.5rem !important;
        }

        .order-1 { order: 1; }
        .order-2 { order: 2; }
        .order-3 { order: 3; }
    }
</style>
@endsection