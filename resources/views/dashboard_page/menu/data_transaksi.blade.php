@extends('dashboard_page.main')
@section('title', 'Laman Transaksi')
@section('content')

{{-- Define initialSalesArea for Blade and JavaScript access --}}
<?php
    $initialSalesArea = request()->query('sales_area', 'SA Jambi');
?>

{{-- Welcome Section (Refactored Title) --}}
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
                    Ringkasan Data Transaksi SPBE/BPT
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Lihat dan kelola data stok dan transaksi SPBE/BPT untuk region :
                    <strong class="text-primary"><span id="dynamic-branch-name">SA Jambi</span></strong>.
                </p>
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

{{-- Tabel SPBE/BPT --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
           <div class="card-header pb-0">
                {{-- Row for Table Title and Search Filter --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                        <h4 class="mb-0" id="table-branch-name">Tabel Stok SPBE/BPT - Nama Cabang</h4>
                    </div>
                    <div class="col-12 col-md-4 d-flex justify-content-end">
                        <div class="input-group input-group-sm search-input-group">
                            <input type="text" id="search-input-material-1" class="form-control" placeholder="Cari Nama atau Kode Plant...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                {{-- Filters --}}
                <div class="row">
                    <div class="col-12 d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center">
                        <div class="col-12 col-md-auto mb-2 mb-md-0 d-flex flex-column order-1">
                            <p class="text-sm text-secondary mb-1 branch-selection-text-desktop">
                                *Pilih salah satu tombol di bawah ini untuk melihat data SPBE/BPT berdasarkan Sales Region : *
                            </p>
                            <div class="btn-group d-flex flex-wrap branch-buttons" role="group" aria-label="Branch selection">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Jambi">SA Jambi</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Bengkulu">SA Bengkulu</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Lampung">SA Lampung</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Sumsel">SA Sumsel</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Babel">SA Babel</button>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
           <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material-1">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE/BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Plant</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Provinsi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Kabupaten</th>
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

<div class="modal fade" id="editSpbeBptModal" tabindex="-1" aria-labelledby="editSpbeBptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpbeBptModalLabel">Edit Data SPBE/BPT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSpbeBptForm">
                    <input type="hidden" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control" id="edit-nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode-plant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control" id="edit-kode-plant" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="edit-tipe" id="tipe-spbe" value="SPBE">
                                <label class="form-check-label" for="tipe-spbe">SPBE</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="edit-tipe" id="tipe-bpt" value="BPT">
                                <label class="form-check-label" for="tipe-bpt">BPT</label>
                            </div>
                        </div>
                    </div>
                    {{-- Form field baru untuk Nama Provinsi --}}
                    <div class="mb-3">
                        <label for="edit-provinsi" class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control" id="edit-provinsi" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-sa-region" class="form-label">SA Region/Sales</label>
                        <select class="form-select" id="edit-sa-region" required>
                            <option value="SA Jambi">SA Jambi</option>
                            <option value="SA Bengkulu">SA Bengkulu</option>
                            <option value="SA Lampung">SA Lampung</option>
                            <option value="SA Sumsel">SA Sumsel</option>
                            <option value="SA Babel">SA Babel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kabupaten" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control" id="edit-kabupaten" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveSpbeBpt">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedBranch = "{{ $initialSalesArea }}";

    // Dummy data for SPBE/BPT with new columns
    const spbeBptData = [
        { id: 1, nama: 'SPBE Sukamaju', kode_plant: 'PL-001', tipe: 'SPBE', sa_region: 'SA Jambi', nama_provinsi: 'Jambi', nama_kabupaten: 'Muaro Jambi' },
        { id: 2, nama: 'BPT Sejahtera', kode_plant: 'PL-002', tipe: 'BPT', sa_region: 'SA Jambi', nama_provinsi: 'Jambi', nama_kabupaten: 'Tanjung Jabung Timur' },
        { id: 3, nama: 'SPBE Indah', kode_plant: 'PL-003', tipe: 'SPBE', sa_region: 'SA Jambi', nama_provinsi: 'Jambi', nama_kabupaten: 'Kota Jambi' },
        { id: 4, nama: 'BPT Makmur', kode_plant: 'PL-004', tipe: 'BPT', sa_region: 'SA Jambi', nama_provinsi: 'Jambi', nama_kabupaten: 'Batanghari' },
        { id: 5, nama: 'SPBE Bersama', kode_plant: 'PL-005', tipe: 'SPBE', sa_region: 'SA Jambi', nama_provinsi: 'Jambi', nama_kabupaten: 'Sarolangun' },
        { id: 6, nama: 'SPBE Mandiri', kode_plant: 'PL-006', tipe: 'SPBE', sa_region: 'SA Bengkulu', nama_provinsi: 'Bengkulu', nama_kabupaten: 'Bengkulu Utara' },
        { id: 7, nama: 'BPT Jaya Abadi', kode_plant: 'PL-007', tipe: 'BPT', sa_region: 'SA Bengkulu', nama_provinsi: 'Bengkulu', nama_kabupaten: 'Rejang Lebong' },
        { id: 8, nama: 'SPBE Barokah', kode_plant: 'PL-008', tipe: 'SPBE', sa_region: 'SA Bengkulu', nama_provinsi: 'Bengkulu', nama_kabupaten: 'Mukomuko' },
        { id: 9, nama: 'BPT Maju', kode_plant: 'PL-009', tipe: 'BPT', sa_region: 'SA Bengkulu', nama_provinsi: 'Bengkulu', nama_kabupaten: 'Seluma' },
        { id: 10, nama: 'SPBE Unggul', kode_plant: 'PL-010', tipe: 'SPBE', sa_region: 'SA Lampung', nama_provinsi: 'Lampung', nama_kabupaten: 'Lampung Selatan' },
        { id: 11, nama: 'BPT Sentosa', kode_plant: 'PL-011', tipe: 'BPT', sa_region: 'SA Lampung', nama_provinsi: 'Lampung', nama_kabupaten: 'Pringsewu' },
        { id: 12, nama: 'SPBE Damai', kode_plant: 'PL-012', tipe: 'SPBE', sa_region: 'SA Lampung', nama_provinsi: 'Lampung', nama_kabupaten: 'Kota Metro' },
        { id: 13, nama: 'BPT Mufakat', kode_plant: 'PL-013', tipe: 'BPT', sa_region: 'SA Sumsel', nama_provinsi: 'Sumatera Selatan', nama_kabupaten: 'Palembang' },
        { id: 14, nama: 'SPBE Sentral', kode_plant: 'PL-014', tipe: 'SPBE', sa_region: 'SA Sumsel', nama_provinsi: 'Sumatera Selatan', nama_kabupaten: 'Ogan Ilir' },
        { id: 15, nama: 'BPT Harmoni', kode_plant: 'PL-015', tipe: 'BPT', sa_region: 'SA Sumsel', nama_provinsi: 'Sumatera Selatan', nama_kabupaten: 'Lahat' },
        { id: 16, nama: 'SPBE Bahagia', kode_plant: 'PL-016', tipe: 'SPBE', sa_region: 'SA Babel', nama_provinsi: 'Bangka Belitung', nama_kabupaten: 'Bangka' },
        { id: 17, nama: 'BPT Lancar', kode_plant: 'PL-017', tipe: 'BPT', sa_region: 'SA Babel', nama_provinsi: 'Bangka Belitung', nama_kabupaten: 'Belitung' },
        { id: 18, nama: 'SPBE Maju', kode_plant: 'PL-018', tipe: 'SPBE', sa_region: 'SA Babel', nama_provinsi: 'Bangka Belitung', nama_kabupaten: 'Pangkal Pinang' },
    ];

    const perPage = 10;
    let currentPage1 = 1;
    let searchQuery1 = '';

    // Update displayed branch names
    function updateBranchNames(branchName) {
        document.getElementById('dynamic-branch-name').textContent = branchName;
        document.getElementById('table-branch-name').textContent = `Tabel Data SPBE/BPT - ${branchName}`;
    }

    // Render active state of branch buttons
    function renderBranchButtons() {
        const branchButtons = document.querySelectorAll('.btn-group button');
        branchButtons.forEach(button => {
            if (button.dataset.branch === selectedBranch) {
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-primary');
            } else {
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
            }
        });
    }

    // Filter data based on selected SA and search query
    function filterData1() {
        let filteredData = spbeBptData.filter(item => item.sa_region === selectedBranch);

        if (searchQuery1) {
            filteredData = filteredData.filter(item =>
                item.nama.toLowerCase().includes(searchQuery1.toLowerCase()) ||
                item.kode_plant.toLowerCase().includes(searchQuery1.toLowerCase()) ||
                item.nama_provinsi.toLowerCase().includes(searchQuery1.toLowerCase()) ||
                item.nama_kabupaten.toLowerCase().includes(searchQuery1.toLowerCase())
            );
        }
        return filteredData;
    }

    // Render table rows
    function renderTable1() {
        const tbody = document.querySelector('#table-material-1 tbody');
        const noData = document.querySelector('#no-data-material-1');
        const filtered = filterData1();
        const start = (currentPage1 - 1) * perPage;
        const dataPage = filtered.slice(start, start + perPage);

        tbody.innerHTML = '';
        if (dataPage.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            dataPage.forEach((item, index) => {
                const detailUrl = `{{ url('/material') }}?sa_region=${encodeURIComponent(item.sa_region)}&spbe_bpt_nama=${encodeURIComponent(item.nama)}`;
                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${start + index + 1}</p>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1 align-items-center">
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="${detailUrl}" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary" style="cursor: pointer;">
                                        ${item.nama}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <p class="text-xs text-secondary mb-0">${item.kode_plant}</p>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs text-secondary font-weight-bold mb-0">${item.nama_provinsi}</p>
                        </td>
                        <td>
                            <p class="text-xs text-secondary font-weight-bold mb-0">${item.nama_kabupaten}</p>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#editSpbeBptModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger text-white delete-btn" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            // Re-attach event listeners after rendering
            attachActionListeners();
        }
        renderPagination1(filtered.length);
    }

    // Attach event listeners for edit and delete buttons
    function attachActionListeners() {
        // Edit button listener
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const spbe = spbeBptData.find(item => item.id == id);
                if (spbe) {
                    document.getElementById('edit-id').value = spbe.id;
                    document.getElementById('edit-nama').value = spbe.nama;
                    document.getElementById('edit-kode-plant').value = spbe.kode_plant;
                    document.getElementById('edit-provinsi').value = spbe.nama_provinsi;
                    document.getElementById('edit-sa-region').value = spbe.sa_region;
                    document.getElementById('edit-kabupaten').value = spbe.nama_kabupaten;
                    if (spbe.tipe === 'SPBE') {
                        document.getElementById('tipe-spbe').checked = true;
                    } else {
                        document.getElementById('tipe-bpt').checked = true;
                    }
                }
            });
        });

        // Delete button listener
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
                            'Data SPBE/BPT telah berhasil dihapus.',
                            'success'
                        );
                    }
                });
            });
        });
    }

    // Render pagination buttons
    function renderPagination1(totalItems) {
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
                    currentPage1 = page;
                    renderTable1();
                });
            }
            return li;
        }

        if (totalPages > 1) {
            // First and Previous buttons (optional, simplified for brevity)
            pagination.appendChild(createButton('«', 1, currentPage1 === 1));
            pagination.appendChild(createButton('‹', currentPage1 - 1, currentPage1 === 1));

            // Page number buttons
            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage1 - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pagination.appendChild(createButton(i, i, false, i === currentPage1));
            }

            // Next and Last buttons (optional, simplified for brevity)
            pagination.appendChild(createButton('›', currentPage1 + 1, currentPage1 === totalPages));
            pagination.appendChild(createButton('»', totalPages, currentPage1 === totalPages));
        }
    }

    document.getElementById('saveSpbeBpt').addEventListener('click', function() {
        const id = document.getElementById('edit-id').value;
        const nama = document.getElementById('edit-nama').value;
        const kode_plant = document.getElementById('edit-kode-plant').value;
        const tipe = document.querySelector('input[name="edit-tipe"]:checked').value;
        const provinsi = document.getElementById('edit-provinsi').value;
        const sa_region = document.getElementById('edit-sa-region').value;
        const kabupaten = document.getElementById('edit-kabupaten').value;

        // Find and update the data in our mock array
        const spbeIndex = spbeBptData.findIndex(item => item.id == id);
        if (spbeIndex > -1) {
            spbeBptData[spbeIndex] = {
                id: parseInt(id),
                nama: nama,
                kode_plant: kode_plant,
                tipe: tipe,
                sa_region: sa_region,
                nama_provinsi: provinsi,
                nama_kabupaten: kabupaten
            };
        }

        const myModal = bootstrap.Modal.getInstance(document.getElementById('editSpbeBptModal'));
        myModal.hide();
        Swal.fire('Berhasil Disimpan!', 'Perubahan data SPBE/BPT berhasil disimpan.', 'success');
        
        // Re-render the table to reflect the changes
        renderTable1();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Search input event listener
        document.getElementById('search-input-material-1').addEventListener('input', function () {
            searchQuery1 = this.value;
            currentPage1 = 1;
            renderTable1();
        });

        // Branch selection buttons event listener
        const branchButtons = document.querySelectorAll('.btn-group button');
        branchButtons.forEach(button => {
            button.addEventListener('click', function() {
                selectedBranch = this.dataset.branch;
                updateBranchNames(selectedBranch);
                renderBranchButtons();
                currentPage1 = 1;
                renderTable1();
            });
        });

        // Initial render
        updateBranchNames(selectedBranch);
        renderBranchButtons();
        renderTable1();
    });
</script>
@endpush

{{-- CSS untuk halaman transaksi --}}
<style>
    /* General styles for welcome card */
    .welcome-card {
        background-color: white;
        color: #344767;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        padding: 1.5rem !important;
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
        .search-input-group {
            max-width: 250px;
        }

        .branch-selection-text-desktop {
            margin-bottom: 0.5rem;
            white-space: nowrap;
        }

        .btn-branch-custom {
            padding: 0.4rem 0.6rem;
            font-size: 0.78rem;
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
        .desktop-filter-row-top > div {
            flex-grow: 0;
            flex-shrink: 0;
        }
    }

    /* Mobile specific styles (max-width 767.98px for Bootstrap's 'md' breakpoint) */
    @media (max-width: 767.98px) {
        /* --- Welcome Section Title Adjustment for Mobile --- */
        .welcome-card {
            padding: 1rem !important;
        }
        .welcome-card .card-body {
            flex-direction: column;
            align-items: center;
        }
        .welcome-card .card-body > div {
            width: 100%;
            text-align: center;
        }
        .welcome-card-icon {
            margin-bottom: 0.5rem;
            margin-top: 0.5rem;
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
            font-size: 1.25rem !important;
            margin-bottom: 1rem !important;
        }

        .branch-selection-text {
            text-align: center !important;
            margin-bottom: 0.5rem !important;
        }
        .branch-selection-text-desktop {
            display: none;
        }
        .branch-buttons {
            justify-content: center !important;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }
        .btn-branch-custom {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
            flex-grow: 1;
            min-width: unset;
        }
        .branch-buttons button {
            flex: 1 1 auto;
            margin: 2px;
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