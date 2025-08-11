@extends('dashboard_page.main')
@section('title', 'Daftar Data P.Layang (Pusat)')
@section('content')

{{-- Welcome Section (Title for P.Layang) --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                    alt="Branch Icon"
                    class="welcome-card-icon">
            </div>
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="summary-title">
                    Ringkasan Data P.Layang (Pusat)
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Lihat dan kelola data stok material dari Pusat Layang.
                </p>
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

{{-- Tabel Data P.Layang (Pusat) --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
           <div class="card-header pb-0">
                {{-- Row for Table Title and Export Button --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                        <h4 class="mb-0" id="table-branch-name">Tabel Data Stok Material - P.Layang (Pusat)</h4>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="button" class="btn btn-success d-flex align-items-center justify-content-center w-100 w-md-auto export-excel-btn">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </button>
                    </div>
                </div>

                {{-- New row for search and date filters --}}
                <div class="row mb-3 align-items-center">
                    {{-- Search Bar --}}
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari material..." onkeyup="filterData()">
                        </div>
                    </div>
                    {{-- Date Range Filter --}}
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-start justify-content-md-end date-range-picker">
                        <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7">Dari:</label>
                        <input type="date" id="startDate" class="form-control me-2 date-input" onchange="filterData()">
                        <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7">Sampai:</label>
                        <input type="date" id="endDate" class="form-control date-input" onchange="filterData()">
                    </div>
                </div>
           </div>
           <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material-1">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi Terakhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data-material-1" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation material 1">
                        <ul class="pagination pagination-sm mb-0" id="pagination-material-1">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>
           </div>
        </div>
    </div>
</div>

{{-- Modal for Send Material Data --}}
<div class="modal fade" id="kirimMaterialModal" tabindex="-1" aria-labelledby="kirimMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kirimMaterialModalLabel">Kirim Material <span id="modal-nama-material"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card p-3 mb-3 bg-light">
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">NAMA MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-nama-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">KODE MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-kode-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">STOK AKHIR</p>
                    <p class="mb-0 text-sm font-weight-bold" id="modal-total-stok-display"></p>
                </div>
                
                <form id="kirimMaterialForm">
                    <input type="hidden" id="kirim-material-id">

                    <div class="mb-3">
                        <label for="asal-transaksi" class="form-label">Asal Transaksi</label>
                        <input type="text" class="form-control" id="asal-transaksi" value="P.Layang" readonly>
                    </div>

                    {{-- Searchable input for Tujuan Transaksi --}}
                    <div class="mb-3">
                        <label for="tujuan-transaksi-search" class="form-label">Tujuan Transaksi</label>
                        <input type="text" class="form-control" id="tujuan-transaksi-search" placeholder="Cari tujuan..." required>
                        <ul id="tujuan-transaksi-list" class="list-group mt-1" style="max-height: 150px; overflow-y: auto; display: none;">
                            {{-- List items will be populated by JavaScript --}}
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penerimaan" value="penerimaan" checked>
                                <label class="form-check-label" for="jenis-penerimaan">
                                    Penerimaan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penyaluran" value="penyaluran">
                                <label class="form-check-label" for="jenis-penyaluran">
                                    Penyaluran
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah-stok" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlah-stok" required>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="submitKirim">Kirim</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Editing Material Data --}}
<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMaterialModalLabel">Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMaterialForm">
                    <input type="hidden" id="edit-material-id">
                    <div class="mb-3">
                        <label for="edit-nama-material" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="edit-nama-material" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode-material" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="edit-kode-material" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-total-stok" class="form-label">Stok Akhir</label>
                        <input type="number" class="form-control" id="edit-total-stok" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveMaterialChanges">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const tableData = [
        { id: 1, nama_material: 'Material A (Kertas)', kode_material: 'KT-01', stok_awal: 500, penerimaan: 1200, penyaluran: 1000, total_stok: 700, tanggal: '2025-08-05' },
        { id: 2, nama_material: 'Material B (Tinta Cetak)', kode_material: 'TINT-02', stok_awal: 100, penerimaan: 250, penyaluran: 200, total_stok: 150, tanggal: '2025-08-04' },
        { id: 3, nama_material: 'Material C (Seal Karet)', kode_material: 'SK-03', stok_awal: 200, penerimaan: 400, penyaluran: 350, total_stok: 250, tanggal: '2025-08-03' },
        { id: 4, nama_material: 'Material D (Pelumas)', kode_material: 'PLM-04', stok_awal: 60, penerimaan: 100, penyaluran: 80, total_stok: 80, tanggal: '2025-08-02' },
        { id: 5, nama_material: 'Material E (Spare Part)', kode_material: 'SP-05', stok_awal: 30, penerimaan: 50, penyaluran: 20, total_stok: 60, tanggal: '2025-08-01' },
        { id: 6, nama_material: 'Material F (Kabel)', kode_material: 'KBL-06', stok_awal: 1500, penerimaan: 300, penyaluran: 200, total_stok: 1600, tanggal: '2025-07-31' },
        { id: 7, nama_material: 'Material G (Baut)', kode_material: 'BT-07', stok_awal: 2000, penerimaan: 500, penyaluran: 750, total_stok: 1750, tanggal: '2025-07-30' },
        { id: 8, nama_material: 'Material H (Cat)', kode_material: 'CAT-08', stok_awal: 80, penerimaan: 50, penyaluran: 30, total_stok: 100, tanggal: '2025-07-29' },
        { id: 9, nama_material: 'Material I (Gasket)', kode_material: 'GSK-09', stok_awal: 120, penerimaan: 40, penyaluran: 60, total_stok: 100, tanggal: '2025-07-28' },
        { id: 10, nama_material: 'Material J (Filter)', kode_material: 'FLTR-10', stok_awal: 400, penerimaan: 150, penyaluran: 200, total_stok: 350, tanggal: '2025-07-27' },
        { id: 11, nama_material: 'Material K (Pipa)', kode_material: 'PIP-11', stok_awal: 100, penerimaan: 50, penyaluran: 30, total_stok: 120, tanggal: '2025-07-26' },
        { id: 12, nama_material: 'Material L (Kawat)', kode_material: 'KWT-12', stok_awal: 200, penerimaan: 100, penyaluran: 50, total_stok: 250, tanggal: '2025-07-25' },
    ];

    const perPage = 10;
    let currentPage = 1;
    let filteredData = [...tableData];
    // Object to store the last entered quantity for each material ID
    const lastTransactionQuantities = {};
    // Object to store the last entered destination for each material ID
    const lastTransactionDestination = {};

    function formatTanggal(tgl) {
        const d = new Date(tgl);
        return d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }
    
    function filterData() {
        const searchQuery = document.getElementById('searchInput').value.toLowerCase();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let tempFilteredData = [...tableData];

        if (searchQuery) {
            tempFilteredData = tempFilteredData.filter(item => {
                return item.nama_material.toLowerCase().includes(searchQuery) ||
                        item.kode_material.toLowerCase().includes(searchQuery);
            });
        }
        
        if (startDate || endDate) {
            tempFilteredData = tempFilteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                const start = startDate ? new Date(startDate) : null;
                const end = endDate ? new Date(endDate) : null;

                if (start) start.setHours(0, 0, 0, 0);
                if (end) end.setHours(23, 59, 59, 999);
                
                return (!start || itemDate >= start) && (!end || itemDate <= end);
            });
        }
        
        filteredData = tempFilteredData;
        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const tbody = document.querySelector('#table-material-1 tbody');
        const noData = document.querySelector('#no-data-material-1');
        
        const start = (currentPage - 1) * perPage;
        const dataPage = filteredData.slice(start, start + perPage);

        tbody.innerHTML = '';
        if (dataPage.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            dataPage.forEach((item, index) => {
                const rowIndex = start + index + 1;
                tbody.innerHTML += `
                    <tr>
                        <td class="text-center"><p class="text-xs font-weight-bold mb-0">${rowIndex}</p></td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="mb-0 text-sm font-weight-bolder text-primary">${item.nama_material}</p>
                            </div>
                        </td>
                        <td><p class="text-xs text-secondary mb-0">${item.kode_material}</p></td>
                        <td class="text-center"><span class="badge bg-gradient-secondary text-white text-xs">${item.stok_awal} pcs</span></td>
                        <td class="text-center"><span class="badge bg-gradient-primary text-white text-xs">${item.penerimaan} pcs</span></td>
                        <td class="text-center"><span class="badge bg-gradient-info text-white text-xs">${item.penyaluran} pcs</span></td>
                        <td class="text-center"><span class="badge bg-gradient-success text-white text-xs">${item.total_stok} pcs</span></td>
                        <td class="text-center"><p class="text-xs text-secondary font-weight-bold mb-0">${formatTanggal(item.tanggal)}</p></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-success text-white me-1 kirim-btn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#editMaterialModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger text-white delete-btn" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            attachActionListeners();
        }
        renderPagination(filteredData.length);
    }

    function attachActionListeners() {
        // Event listener for "Kirim" button
        document.querySelectorAll('.kirim-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const material = tableData.find(item => item.id == id);
                if (material) {
                    document.getElementById('kirim-material-id').value = material.id;
                    document.getElementById('modal-nama-material-display').textContent = material.nama_material;
                    document.getElementById('modal-kode-material-display').textContent = material.kode_material;
                    document.getElementById('modal-total-stok-display').textContent = `${material.total_stok} pcs`;
                    
                    // Populate jumlah-stok with the last saved value
                    const lastValue = lastTransactionQuantities[id];
                    document.getElementById('jumlah-stok').value = lastValue || '';
                    
                    // Populate tujuan-transaksi-search with the last saved value
                    const lastDestination = lastTransactionDestination[id];
                    document.getElementById('tujuan-transaksi-search').value = lastDestination || '';

                    document.getElementById('tujuan-transaksi-list').style.display = 'none';
                    // Reset radio button to default (Penerimaan)
                    document.getElementById('jenis-penerimaan').checked = true;
                }
            });
        });

        // Event listener for "Edit" button
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const material = tableData.find(item => item.id == id);
                if (material) {
                    document.getElementById('edit-material-id').value = material.id;
                    document.getElementById('edit-nama-material').value = material.nama_material;
                    document.getElementById('edit-kode-material').value = material.kode_material;
                    document.getElementById('edit-total-stok').value = material.total_stok;
                }
            });
        });

        // Event listener for "Hapus" button
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'Berhasil Dihapus!',
                            'Data material telah berhasil dihapus.',
                            'success'
                        );
                    }
                });
            });
        });
    }

    function renderPagination(totalItems) {
        const pagination = document.getElementById('pagination-material-1');
        const totalPages = Math.ceil(totalItems / perPage);
        pagination.innerHTML = '';

        function createButton(label, page, disabled = false, active = false) {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = page;
                    renderTable();
                });
            }
            return li;
        }

        if (totalPages > 1) {
            pagination.appendChild(createButton('«', 1, currentPage === 1));
            pagination.appendChild(createButton('‹', currentPage - 1, currentPage === 1));

            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pagination.appendChild(createButton(i, i, false, i === currentPage));
            }

            pagination.appendChild(createButton('›', currentPage + 1, currentPage === totalPages));
            pagination.appendChild(createButton('»', totalPages, currentPage === totalPages));
        }
    }
    
    document.getElementById('submitKirim').addEventListener('click', function() {
        const id = document.getElementById('kirim-material-id').value;
        const tujuan = document.getElementById('tujuan-transaksi-search').value;
        const jenis = document.querySelector('input[name="jenisTransaksi"]:checked').value;
        const jumlah = parseInt(document.getElementById('jumlah-stok').value);

        // Validation
        if (!tujuan || isNaN(jumlah) || jumlah <= 0) {
            Swal.fire('Gagal!', 'Harap isi form dengan benar.', 'error');
            return;
        }

        // Find the material data to get the current stock
        const material = tableData.find(item => item.id == id);
        if (!material) {
            Swal.fire('Error!', 'Data material tidak ditemukan.', 'error');
            return;
        }

        let stokAkhir;
        if (jenis === 'penerimaan') {
            stokAkhir = material.total_stok + jumlah;
            material.penerimaan += jumlah; // Update penerimaan count
        } else if (jenis === 'penyaluran') {
            if (material.total_stok < jumlah) {
                Swal.fire('Gagal!', 'Stok tidak mencukupi untuk penyaluran.', 'warning');
                return;
            }
            stokAkhir = material.total_stok - jumlah;
            material.penyaluran += jumlah; // Update penyaluran count
        }

        // Save the last transaction quantity and destination
        lastTransactionQuantities[id] = jumlah;
        lastTransactionDestination[id] = tujuan;

        // Update the data in our mock array and re-render the table
        material.total_stok = stokAkhir;
        material.tanggal = new Date().toISOString().slice(0, 10);
        filterData(); // Re-render table to reflect changes

        const myModal = bootstrap.Modal.getInstance(document.getElementById('kirimMaterialModal'));
        myModal.hide();

        Swal.fire('Berhasil Dikirim!', `Stok akhir material saat ini adalah **${stokAkhir} pcs**.`, 'success');
    });

    document.getElementById('saveMaterialChanges').addEventListener('click', function() {
        const id = document.getElementById('edit-material-id').value;
        const nama = document.getElementById('edit-nama-material').value;
        const kode = document.getElementById('edit-kode-material').value;
        const stok = parseInt(document.getElementById('edit-total-stok').value);
    
        if (!nama || !kode || isNaN(stok)) {
            Swal.fire('Gagal!', 'Harap isi semua form edit dengan benar.', 'error');
            return;
        }
    
        const materialIndex = tableData.findIndex(item => item.id == id);
        if (materialIndex > -1) {
            tableData[materialIndex].nama_material = nama;
            tableData[materialIndex].kode_material = kode;
            tableData[materialIndex].total_stok = stok;
            filterData(); // Re-render table to reflect changes
        }
    
        const myModal = bootstrap.Modal.getInstance(document.getElementById('editMaterialModal'));
        myModal.hide();
    
        Swal.fire('Berhasil Disimpan!', 'Perubahan data material berhasil disimpan.', 'success');
    });


    // Dummy data for searchable tujuan transaksi
    const tujuanTransaksiData = ['SPBE Sukamaju', 'SPBE Makmur', 'SPBE Sentosa', 'SPBE Jaya', 'SPBE Maju Jaya'];
    const tujuanTransaksiInput = document.getElementById('tujuan-transaksi-search');
    const tujuanTransaksilist = document.getElementById('tujuan-transaksi-list');

    tujuanTransaksiInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        tujuanTransaksilist.innerHTML = '';
        tujuanTransaksilist.style.display = 'block';

        if (query.length > 0) {
            const filteredTujuan = tujuanTransaksiData.filter(tujuan =>
                tujuan.toLowerCase().includes(query)
            );

            if (filteredTujuan.length > 0) {
                filteredTujuan.forEach(tujuan => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.textContent = tujuan;
                    li.addEventListener('click', () => {
                        tujuanTransaksiInput.value = tujuan;
                        tujuanTransaksilist.style.display = 'none';
                    });
                    tujuanTransaksilist.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.textContent = 'Tidak ada hasil.';
                tujuanTransaksilist.appendChild(li);
            }
        } else {
            tujuanTransaksilist.style.display = 'none';
        }
    });

    // Hide list when clicking outside
    document.addEventListener('click', function(e) {
        if (!tujuanTransaksiInput.contains(e.target) && !tujuanTransaksilist.contains(e.target)) {
            tujuanTransaksilist.style.display = 'none';
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchInput').addEventListener('input', filterData);
        filterData();
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
            /* This class will ensure the search bar width matches the date filters */
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

        .branch-selection-text-desktop {
            display: none;
            /* Hide desktop specific text on mobile */
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
            /* Full width for date inputs */
            height: 38px !important;
            /* Smaller height for date inputs */
        }

        .date-range-picker label {
            margin-right: 0 !important;
            /* Remove right margin */
            margin-left: 0 !important;
            /* Remove left margin */
        }

        /* --- Specific changes for Search Input Group in Mobile --- */
        .search-input-group {
            width: 100% !important;
            /* Full width for the whole group */
            height: 38px !important;
            /* Smaller height for the whole group */
            margin-top: 0.5rem;
            /* Add some top margin when stacked below date pickers */
        }

        .search-input-group .form-control {
            height: 38px !important;
            /* Ensure input field matches group height */
            font-size: 0.85rem !important;
            /* Match font size */
            padding-right: 0.5rem;
            /* Adjust padding to make space for icon */
        }

        .search-input-group .input-group-text {
            height: 38px !important;
            /* Match height of input */
            padding: 0.4rem 0.8rem;
            /* Adjust padding */
            font-size: 0.85rem !important;
            /* Match font size */
        }

        /* --- End of Mobile Specific changes for Search Input Group --- */


        /* Adjust padding for card-header in mobile to give more space */
        .card-header {
            padding: 1rem !important;
        }

        /* Make table header text smaller in mobile */
        #table-material-1 thead th {
            font-size: 0.65rem !important;
            /* Smaller text for table headers */
        }

        /* Make table body text smaller in mobile */
        #table-material-1 tbody td {
            font-size: 0.75rem !important;
            /* Smaller text for table body */
        }

        /* Adjust padding for table cells */
        #table-material-1 tbody td,
        #table-material-1 thead th {
            padding: 0.5rem 0.5rem !important;
        }

        /* Ensure default order for mobile when using order-md-* */
        .order-1 {
            order: 1;
        }

        .order-2 {
            order: 2;
        }

        .order-3 {
            order: 3;
        }
    }
</style>
@endsection