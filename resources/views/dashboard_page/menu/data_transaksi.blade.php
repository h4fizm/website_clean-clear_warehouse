@extends('dashboard_page.main')
@section('title', 'Laman Transaksi')
@section('content')

{{-- Define initialSalesArea using PHP to make it accessible to Blade and JavaScript --}}
<?php
    // Get the 'sales_area' query parameter from the URL, default to 'P.Layang' if not set
    $initialSalesArea = request()->query('sales_area', 'P.Layang');
?>

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-3" style="
        background-color: white; /* Putih Polos */
        color: #344767; /* Warna teks gelap agar kontras dengan latar putih */
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Soft shadow */
        overflow: hidden;
        position: relative;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h4 class="mb-1 fw-bold">Ringkasan Data Transaksi Material</h4>
                <p class="mb-2 opacity-8">Lihat dan kelola data stok material serta riwayat transaksi untuk cabang : <strong class="text-primary"><span id="dynamic-branch-name">Cabang Anda</span></strong>.</p>
            </div>

            <div class="text-center position-relative me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}" alt="Branch Icon" style="height: 60px; width: auto; opacity: 0.9;">
            </div>
        </div>
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            background-size: 60px 60px;
            opacity: 0.2;
            pointer-events: none;
        "></div>
    </div>
</div>

{{-- Tabel Material Cabang --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
           <div class="card-header pb-0">
                {{-- Row for Table Title and Export Button --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                        <h4 class="mb-0" id="table-branch-name">Tabel Stok Material Cabang Anda - Nama Cabang</h4>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="button" class="btn btn-success d-flex align-items-center justify-content-center w-100 w-md-auto" style="height: 45px;">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </button>
                    </div>
                </div>
                {{-- Row for Filters --}}
                <div class="row align-items-center">
                    {{-- Branch Selection Buttons (Left Side) --}}
                    <div class="col-12 col-md-auto mb-2 mb-md-0">
                        <p class="text-sm text-secondary mb-1">
                            *Pilih salah satu tombol di bawah ini untuk melihat data material berdasarkan lokasi cabang : *
                        </p>
                        <div class="btn-group d-flex flex-wrap" role="group" aria-label="Branch selection">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="P.Layang">P.Layang</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="Sales Area Jambi">SA Jambi</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="SA Bengkulu">SA Bengkulu</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="SA Lampung">SA Lampung</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="SA Sumsel">SA Sumsel</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-branch="SA Babel">SA Babel</button>
                        </div>
                    </div>

                    {{-- Search and Date Range Picker (Right Side) --}}
                    <div class="col-12 col-md d-flex flex-wrap flex-md-nowrap gap-2 justify-content-start justify-content-md-end align-items-center">
                        {{-- Date Range Picker --}}
                        <div class="d-flex align-items-center gap-1">
                            <label for="start-date-material-1" class="text-xs mb-0 me-1">Dari</label>
                            <input type="date" id="start-date-material-1" class="form-control form-control-sm" style="height: 60px; width: 140px; min-width: 120px;">
                            <label for="end-date-material-1" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                            <input type="date" id="end-date-material-1" class="form-control form-control-sm" style="height: 60px; width: 140px; min-width: 120px;">
                        </div>
                        {{-- Input Search --}}
                        <input type="text" id="search-input-material-1" class="form-control form-control-sm" placeholder="Cari Nama atau Kode Material..." style="width: 200px; min-width: 150px; height: 60px;">
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jml Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi Terakhir</th>
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

@push('scripts')
<script>
    // Pass the PHP variable to JavaScript
    let selectedBranch = "{{ $initialSalesArea }}"; // Now properly initialized from URL param or default

    const materialData1 = [
        // NOTE: Your materialData1 currently does NOT have a 'cabang' property for filtering.
        // If you want to filter this table by selectedBranch, each item needs a 'cabang' property.
        // For now, the branch selection only updates the displayed branch name in the header,
        // and routes to the correct SPBE/BPT list page for that selected branch.
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', stok_awal: 250, penerimaan: 1000, penyaluran: 800, stok: 200, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', stok_awal: 180, penerimaan: 500, penyaluran: 350, stok: 150, tanggal: '2025-07-27' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', stok_awal: 200, penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', stok_awal: 600, penerimaan: 1000, penyaluran: 500, stok: 500, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', stok_awal: 180, penerimaan: 300, penyaluran: 150, stok: 150, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', stok_awal: 250, penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', stok_awal: 80, penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', stok_awal: 180, penerimaan: 250, penyaluran: 100, stok: 150, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', stok_awal: 60, penerimaan: 80, penyaluran: 30, stok: 50, tanggal: '2025-07-20' },
        { nama: 'Manometer', kode: 'MAN-001', stok_awal: 45, penerimaan: 50, penyaluran: 10, stok: 40, tanggal: '2025-07-19' },
        { nama: 'Flow Meter', kode: 'FLM-002', stok_awal: 25, penerimaan: 30, penyaluran: 10, stok: 20, tanggal: '2025-07-18' },
        { nama: 'Konektor Gas', kode: 'KNG-001', stok_awal: 150, penerimaan: 200, penyaluran: 120, stok: 80, tanggal: '2025-07-17' },
        { nama: 'Tabung Bright Gas 5.5kg', kode: 'TBG5.5-BG', stok_awal: 70, penerimaan: 100, penyaluran: 60, stok: 40, tanggal: '2025-07-16' },
        { nama: 'Gas Elpiji 12kg Bright', kode: 'LPG12-BG', stok_awal: 300, penerimaan: 400, penyaluran: 250, stok: 150, tanggal: '2025-07-15' },
        { nama: 'Burner Kompor', kode: 'BRN-001', stok_awal: 100, penerimaan: 120, penyaluran: 70, stok: 50, tanggal: '2025-07-14' },
        { nama: 'Pipa Gas Fleksibel', kode: 'PGF-001', stok_awal: 90, penerimaan: 100, penyaluran: 50, stok: 50, tanggal: '2025-07-13' },
    ];

    const perPage = 10;
    let currentPage1 = 1;
    let searchQuery1 = '';
    // selectedBranch is already initialized above from PHP

    function formatTanggal(tgl) {
        const d = new Date(tgl);
        return d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    function updateBranchNames(branchName) {
        document.getElementById('dynamic-branch-name').textContent = branchName;
        document.getElementById('table-branch-name').textContent = `Tabel Stok Material Cabang ${branchName}`;
    }

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

    function filterData1() {
        let filteredData = materialData1;

        if (searchQuery1) {
            filteredData = filteredData.filter(item =>
                item.nama.toLowerCase().includes(searchQuery1.toLowerCase()) ||
                item.kode.toLowerCase().includes(searchQuery1.toLowerCase())
            );
        }
        return filteredData;
    }

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
                // Determine the base URL based on selectedBranch
                let baseUrl;
                if (selectedBranch === 'P.Layang') {
                    baseUrl = '{{ url('/pusat') }}'; // New URL for P.Layang
                } else {
                    baseUrl = '{{ url('/spbe-bpt') }}'; // Existing URL for other SAs
                }

                // Construct the detail URL including the sales_area parameter
                const detailUrl = `${baseUrl}?sales_area=${encodeURIComponent(selectedBranch)}&id=${item.kode}&nama_material=${encodeURIComponent(item.nama)}`;
                // Note: I used item.kode as a placeholder for id, adjust if your data structure has an actual unique ID for material items.
                // Also added nama_material for clarity on the receiving page.

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
                                <p class="text-xs text-secondary mb-0">${item.kode}</p>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-secondary text-white text-xs">${item.stok_awal} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-primary text-white text-xs">${item.penerimaan} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-info text-white text-xs">${item.penyaluran} pcs</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-success text-white text-xs">${item.stok} pcs</span>
                        </td>
                        <td class="text-center">
                            <p class="text-xs text-secondary font-weight-bold mb-0">${formatTanggal(item.tanggal)}</p>
                        </td>
                    </tr>
                `;
            });
        }
        renderPagination1(filtered.length);
    }

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

        pagination.appendChild(createButton('«', 1, currentPage1 === 1));
        pagination.appendChild(createButton('‹', currentPage1 - 1, currentPage1 === 1));

        for (let i = 1; i <= totalPages; i++) {
            pagination.appendChild(createButton(i, i, false, i === currentPage1));
        }

        pagination.appendChild(createButton('›', currentPage1 + 1, currentPage1 === totalPages));
        pagination.appendChild(createButton('»', totalPages, currentPage1 === totalPages));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event Listener for Table 1 Search
        document.getElementById('search-input-material-1').addEventListener('input', function () {
            searchQuery1 = this.value;
            currentPage1 = 1;
            renderTable1();
        });

        // Event listener for Branch Selection Buttons
        const branchButtons = document.querySelectorAll('.btn-group button');
        branchButtons.forEach(button => {
            button.addEventListener('click', function() {
                selectedBranch = this.dataset.branch;
                updateBranchNames(selectedBranch);
                renderBranchButtons(); // Update active button style

                // If materialData1 needs to be filtered by selectedBranch,
                // you would add filtering logic here or modify materialData1 structure.
                // For now, it just re-renders with the same data but updated branch name.
                currentPage1 = 1; // Reset to first page
                renderTable1(); // Re-render table to apply the new URL logic
            });
        });

        // Initial render for table 1 and set default branch name and active button
        updateBranchNames(selectedBranch); // Set initial branch name using the variable from PHP
        renderBranchButtons(); // Set active button based on initial selectedBranch
        renderTable1(); // Initial render to populate table and apply URL logic
    });
</script>
@endpush
@endsection