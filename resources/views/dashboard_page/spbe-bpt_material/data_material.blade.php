@extends('dashboard_page.main')
@section('title', 'Data Material - ' . request()->query('spbe_bpt_nama', 'Nama SPBE/BPT'))
@section('content')

{{-- Define initial URL parameters for Blade and JavaScript access --}}
<?php
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE/BPT</th>
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
                                <input class="form-check-input" type="radio" name="editTipe" id="edit-tipe-spbe" value="SPBE">
                                <label class="form-check-label" for="edit-tipe-spbe">SPBE</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="editTipe" id="edit-tipe-bpt" value="BPT">
                                <label class="form-check-label" for="edit-tipe-bpt">BPT</label>
                            </div>
                        </div>
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
    const selectedSpbeBptName = urlParams.get('spbe_bpt_nama') || 'SPBE Sukamaju';
    const saRegion = urlParams.get('sa_region') || 'SA Jambi';

    const dataMaterialDummy = [
        { id: 1, nama: 'Gas LPG 3kg', kode: 'LPG3-001', stok: 150, spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 2, nama: 'Gas LPG 12kg', kode: 'LPG12-001', stok: 200, spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 3, nama: 'Tabung 3kg', kode: 'TBG3-001', stok: 75, spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 4, nama: 'Seal Karet', kode: 'SEAL-01', stok: 300, spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 5, nama: 'Regulator', kode: 'REG-005', stok: 120, spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 6, nama: 'Selang Gas', kode: 'SLG-010', stok: 90, spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 7, nama: 'Kompor Portable', kode: 'KPR-015', stok: 50, spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 8, nama: 'Gas 5.5kg', kode: 'LPG5.5-001', stok: 250, spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 9, nama: 'Tabung 5.5kg', kode: 'TBG5.5-001', stok: 180, spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 10, nama: 'Manometer', kode: 'MAN-001', stok: 100, spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 11, nama: 'Konektor Gas', kode: 'KNG-001', stok: 500, spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 12, nama: 'Flow Meter', kode: 'FLM-002', stok: 0, spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 13, nama: 'Konektor Gas', kode: 'KNG-002', stok: 500, spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 14, nama: 'Manometer', kode: 'MAN-002', stok: 100, spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 15, nama: 'Gas 3kg', kode: 'LPG3-002', stok: 50, spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
    ];
    
    const allLocations = [
        'P.Layang (Pusat)',
        ...new Set(dataMaterialDummy.map(item => item.spbe_bpt_nama))
    ].sort();

    let searchMaterialQuery = '';
    let currentMaterialPage = 0;
    const itemsPerMaterialPage = 10;
    const maxMaterialPagesToShow = 5;

    function filterMaterialData() {
        return dataMaterialDummy.filter(item => {
            const matchSpbeBpt = item.spbe_bpt_nama === selectedSpbeBptName;
            const matchSearch = searchMaterialQuery ?
                                (item.nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.kode.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                                item.spbe_bpt_nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()))
                                : true;
            return matchSpbeBpt && matchSearch;
        });
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
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Tidak ada data material untuk ${selectedSpbeBptName}.</td></tr>`;
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                const stockText = item.stok === 0 ?
                                  '<span class="text-danger text-xs font-weight-bold">Stok kosong</span>' :
                                  `<span class="text-xs font-weight-bold">${item.stok} pcs</span>`;

                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.nama}</p>
                        </td>
                        <td>
                            <p class="text-xs text-secondary mb-0">${item.kode}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.spbe_bpt_nama}</p>
                        </td>
                        <td class="text-center">
                            ${stockText}
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge bg-gradient-success text-white text-xs kirim-material-btn" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal" data-id="${item.id}" data-nama="${item.nama}" data-spbe-bpt="${item.spbe_bpt_nama}">Kirim</span>
                            <span class="badge bg-gradient-info text-white text-xs edit-material-btn ms-1" style="cursor:pointer;" data-id="${item.id}">Edit</span>
                            <span class="badge bg-gradient-danger text-white text-xs delete-material-btn ms-1" style="cursor:pointer;" data-id="${item.id}">Hapus</span>
                        </td>
                    </tr>
                `;
            });
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
                    document.getElementById('editTotalStok').value = material.stok;
                    document.getElementById('editSpbeBpt').value = material.spbe_bpt_nama;
                    if (material.tipe === 'SPBE') {
                        document.getElementById('edit-tipe-spbe').checked = true;
                    } else {
                        document.getElementById('edit-tipe-bpt').checked = true;
                    }
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
                const materialName = this.getAttribute('data-nama');
                const spbeBptName = this.getAttribute('data-spbe-bpt');
                
                document.getElementById('kirimMaterialModalLabel').textContent = `Kirim Material "${materialName}"`;
                
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
                
                // Set a default for jenisTransaksi and jumlahStok
                document.getElementById('jenisPenambahan').checked = true;
                document.getElementById('jumlahStok').value = '';
            });
        });

        // Handle Kirim Material form submission
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

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('search-input-material').addEventListener('input', function () {
            searchMaterialQuery = this.value;
            currentMaterialPage = 0;
            renderMaterialTable();
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
                    stok: stok,
                    spbe_bpt_nama: spbeBpt,
                    tipe: tipe,
                    sa_region: saRegion,
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

        document.getElementById('editMaterialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('editMaterialId').value);
            const material = dataMaterialDummy.find(item => item.id === id);
            if (material) {
                material.nama = document.getElementById('editNamaMaterial').value;
                material.kode = document.getElementById('editKodeMaterial').value;
                material.stok = parseInt(document.getElementById('editTotalStok').value);
                material.spbe_bpt_nama = document.getElementById('editSpbeBpt').value;
                material.tipe = document.querySelector('input[name="editTipe"]:checked').value;

                const editModal = bootstrap.Modal.getInstance(document.getElementById('editMaterialModal'));
                editModal.hide();
                renderMaterialTable();
                Swal.fire('Berhasil Diperbarui!', 'Data material berhasil diperbarui.', 'success');
            } else {
                Swal.fire('Gagal!', 'Data material tidak ditemukan.', 'error');
            }
        });

        renderMaterialTable();
    });
</script>
@endpush

<style>
    /* Styles to prevent button from expanding on click */
    .btn.btn-success:active,
    .btn.btn-success:focus {
        transform: none !important;
        outline: none !important;
    }

    /* Updated styles for better positioning */
    .card-header.pb-0 > .d-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .card-header.pb-0 > .d-flex:first-child {
        margin-bottom: 0;
    }
    .card-header h5 {
        margin-bottom: 0;
    }
    .card-header .btn {
        margin-left: 1rem;
    }
    .search-input-desktop-aligned {
        height: 38px;
        max-width: 250px;
    }
    .search-input-desktop-aligned .form-control {
        height: 100%;
    }
    .search-input-desktop-aligned .input-group-text {
        height: 100%;
    }
    
    /* Peningkatan ukuran font judul modal */
    .modal-header .h4 {
        font-size: 1.5rem; /* Atau ukuran lain yang Anda inginkan */
    }

    @media (max-width: 767.98px) {
        .card-header.pb-0 > .d-flex {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem;
        }
        .card-header.pb-0 > .d-flex:first-child {
            margin-bottom: 0.5rem;
        }
        .card-header h5 {
            text-align: center;
            width: 100%;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .card-header .btn {
            width: 100% !important;
            margin-left: 0;
        }
        .search-input-desktop-aligned {
            width: 100% !important;
            max-width: unset;
        }
        .search-input-desktop-aligned .form-control,
        .search-input-desktop-aligned .input-group-text {
            height: 38px;
        }
        .btn-sm {
            height: 38px;
            font-size: 0.8rem;
        }
        .card-header .d-flex {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
    }
</style>
@endsection