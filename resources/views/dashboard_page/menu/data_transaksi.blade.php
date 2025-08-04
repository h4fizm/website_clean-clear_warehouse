@extends('dashboard_page.main')
@section('title', 'Laman Transaksi')
@section('content')

{{-- Define initialSalesArea for Blade and JavaScript access --}}
<?php
    $initialSalesArea = request()->query('sales_area', 'P.Layang');
?>

{{-- Welcome Section (Tidak Diubah) --}}
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
                    Ringkasan Data Transaksi Material
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Lihat dan kelola data stok material serta riwayat transaksi untuk cabang :
                    <strong class="text-primary"><span id="dynamic-branch-name">Cabang Anda</span></strong>.
                </p>
            </div>
        </div>
        <div class="welcome-card-background"></div>
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
                        <button type="button" class="btn btn-success d-flex align-items-center justify-content-center w-100 w-md-auto export-excel-btn">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </button>
                    </div>
                </div>
                {{-- Filters --}}
                <div class="row mt-3">
                    {{-- Row 1: Branch Selection Buttons (Left Side on Desktop, stacked on Mobile) and Date Range Picker (Right Side on Desktop) --}}
                    <div class="col-12 d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center desktop-filter-row-top">
                        {{-- Branch Selection Buttons (Left Side on Desktop, stacked on Mobile) --}}
                        <div class="col-12 col-md-auto mb-2 mb-md-0 d-flex flex-column order-1 order-md-1">
                            <p class="text-sm text-secondary mb-1 branch-selection-text-desktop">
                                *Pilih salah satu tombol di bawah ini untuk melihat data material berdasarkan lokasi cabang : *
                            </p>
                            <div class="btn-group d-flex flex-wrap branch-buttons" role="group" aria-label="Branch selection">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="P.Layang">P.Layang</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Jambi">SA Jambi</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Bengkulu">SA Bengkulu</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Lampung">SA Lampung</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Sumsel">SA Sumsel</button>
                                <button type="button" class="btn btn-outline-primary btn-sm btn-branch-custom" data-branch="SA Babel">SA Babel</button>
                            </div>
                        </div>

                        {{-- Date Range Picker (Right Side on Desktop) --}}
                        <div class="col-12 col-md-auto d-flex flex-wrap flex-md-nowrap gap-2 justify-content-start justify-content-md-end align-items-center mt-3 mt-md-0 order-2 order-md-2 date-filter-desktop-container">
                            <label for="start-date-material-1" class="text-xs mb-0 me-1">Dari</label>
                            <input type="date" id="start-date-material-1" class="form-control form-control-sm date-input">
                            <label for="end-date-material-1" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                            <input type="date" id="end-date-material-1" class="form-control form-control-sm date-input">
                        </div>
                    </div>

                    {{-- Row 2: Search Input (Full width on Desktop, below Date Range Picker) --}}
                    <div class="col-12 mt-3 order-3 order-md-3 d-flex justify-content-end">
                        <div class="input-group input-group-sm search-input-group search-input-desktop-aligned">
                            <input type="text" id="search-input-material-1" class="form-control" placeholder="Cari Nama atau Kode Material...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
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
    let selectedBranch = "{{ $initialSalesArea }}";

    // Dummy data for materials (This data is displayed on the "Laman Transaksi" table)
    // It does NOT need a 'cabang' property if the table itself isn't filtered by branch.
    // The 'selectedBranch' here only dictates where the link points to.
    const materialData1 = [
        { nama: 'Gas LPG 3kg', kode: 'LPG3-001', stok_awal: 250, penerimaan: 1000, penyaluran: 800, stok: 200, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12kg', kode: 'LPG12-001', stok_awal: 180, penerimaan: 500, penyaluran: 350, stok: 150, tanggal: '2025-07-27' },
        { nama: 'Tabung 3kg', kode: 'TBG3-001', stok_awal: 200, penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', stok_awal: 600, penerimaan: 1000, penyaluran: 500, stok: 500, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', stok_awal: 180, penerimaan: 300, penyaluran: 150, stok: 150, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', stok_awal: 250, penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', stok_awal: 80, penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5kg', kode: 'LPG5.5-001', stok_awal: 180, penerimaan: 250, penyaluran: 100, stok: 150, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5kg', kode: 'TBG5.5-001', stok_awal: 60, penerimaan: 80, penyaluran: 30, stok: 50, tanggal: '2025-07-20' },
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

    // Format date to short weekday, day, month, year
    function formatTanggal(tgl) {
        const d = new Date(tgl);
        return d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    // Update displayed branch names
    function updateBranchNames(branchName) {
        document.getElementById('dynamic-branch-name').textContent = branchName;
        document.getElementById('table-branch-name').textContent = `Tabel Stok Material Cabang ${branchName}`;
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

    // Filter material data based on search query (materialData1 is NOT filtered by branch here)
    function filterData1() {
        let filteredData = materialData1; // Always use the full materialData1
        if (searchQuery1) {
            filteredData = filteredData.filter(item =>
                item.nama.toLowerCase().includes(searchQuery1.toLowerCase()) ||
                item.kode.toLowerCase().includes(searchQuery1.toLowerCase())
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
                let baseUrl;
                // LOGIC PERBAIKAN ROUTING DI SINI
                if (selectedBranch === 'P.Layang') {
                    baseUrl = '{{ url('/pusat') }}';
                } else {
                    baseUrl = '{{ url('/spbe-bpt') }}';
                }
                const detailUrl = `${baseUrl}?sales_area=${encodeURIComponent(selectedBranch)}&id=${item.kode}&nama_material=${encodeURIComponent(item.nama)}`;

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

    // Render pagination buttons (Tidak Diubah)
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
                renderTable1(); // Re-render table to update links based on new selectedBranch
            });
        });

        // Initial render
        updateBranchNames(selectedBranch);
        renderBranchButtons();
        renderTable1();
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

        .branch-buttons .btn-sm {
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
            /* Ensure this specific container stacks in mobile */
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