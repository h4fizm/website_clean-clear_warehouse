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
                {{-- Row for Title and Export Button --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Daftar Stok Material - {{ $spbeBptName }}</h3>
                    <button class="btn btn-success d-flex align-items-center justify-content-center"
                            id="export-excel-btn"
                            title="Export Data ke Excel">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </button>
                </div>
                
                {{-- Row for Search Input and Date Filters --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="search-input-material" class="form-control" placeholder="Cari Nama atau Kode Material...">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-start justify-content-md-end date-range-picker">
                        <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7">Dari:</label>
                        <input type="date" id="startDate" class="form-control me-2 date-input">
                        <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7">Sampai:</label>
                        <input type="date" id="endDate" class="form-control date-input">
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
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
                                <input class="form-check-input" type="radio" name="addTipe" id="add-tipe-spbe" value="SPBE" checked>
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
                        <label for="editSpbeBptSearch" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control" id="editSpbeBptSearch" placeholder="Cari SPBE/BPT..." required>
                        <ul id="editSpbeBptList" class="list-group mt-1" style="max-height: 150px; overflow-y: auto; display: none; position: absolute; z-index: 1000; width: 93%;">
                            {{-- List items will be populated by JavaScript --}}
                        </ul>
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
                    
                    {{-- Searchable input for Tujuan Transaksi --}}
                    <div class="mb-3">
                        <label for="tujuanTransaksiSearch" class="form-label">Tujuan Transaksi</label>
                        <input type="text" class="form-control" id="tujuanTransaksiSearch" placeholder="Cari tujuan..." required>
                        <ul id="tujuanTransaksiList" class="list-group mt-1" style="max-height: 150px; overflow-y: auto; display: none; position: absolute; z-index: 1000; width: 93%;">
                            {{-- List items will be populated by JavaScript --}}
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenisPenerimaan" value="penambahan" checked>
                                <label class="form-check-label" for="jenisPenerimaan">Penerimaan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenisPengurangan" value="pengurangan">
                                <label class="form-check-label" for="jenisPengurangan">Penyaluran</label>
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
        { id: 16, nama: 'Gas LPG 3kg', kode: 'LPG3-003', stok_awal: 250, penerimaan: 800, penyaluran: 600, total_stok: 450, tanggal: '2025-07-13', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 17, nama: 'Gas LPG 12kg', kode: 'LPG12-002', stok_awal: 180, penerimaan: 300, penyaluran: 250, total_stok: 230, tanggal: '2025-07-12', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 18, nama: 'Tabung 3kg', kode: 'TBG3-002', stok_awal: 75, penerimaan: 80, penyaluran: 90, total_stok: 65, tanggal: '2025-07-11', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 19, nama: 'Seal Karet', kode: 'SEAL-02', stok_awal: 300, penerimaan: 100, penyaluran: 150, total_stok: 250, tanggal: '2025-07-10', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 20, nama: 'Regulator', kode: 'REG-006', stok_awal: 120, penerimaan: 50, penyaluran: 70, total_stok: 100, tanggal: '2025-07-09', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 21, nama: 'Selang Gas', kode: 'SLG-011', stok_awal: 90, penerimaan: 20, penyaluran: 30, total_stok: 80, tanggal: '2025-07-08', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 22, nama: 'Kompor Portable', kode: 'KPR-016', stok_awal: 50, penerimaan: 5, penyaluran: 15, total_stok: 40, tanggal: '2025-07-07', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 23, nama: 'Gas 5.5kg', kode: 'LPG5.5-002', stok_awal: 250, penerimaan: 75, penyaluran: 50, total_stok: 275, tanggal: '2025-07-06', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 24, nama: 'Tabung 5.5kg', kode: 'TBG5.5-002', stok_awal: 180, penerimaan: 15, penyaluran: 25, total_stok: 170, tanggal: '2025-07-05', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 25, nama: 'Manometer', kode: 'MAN-003', stok_awal: 100, penerimaan: 25, penyaluran: 30, total_stok: 95, tanggal: '2025-07-04', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 26, nama: 'Konektor Gas', kode: 'KNG-003', stok_awal: 500, penerimaan: 70, penyaluran: 80, total_stok: 490, tanggal: '2025-07-03', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 27, nama: 'Flow Meter', kode: 'FLM-003', stok_awal: 0, penerimaan: 0, penyaluran: 0, total_stok: 0, tanggal: '2025-07-02', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 28, nama: 'Konektor Gas', kode: 'KNG-004', stok_awal: 500, penerimaan: 100, penyaluran: 200, total_stok: 400, tanggal: '2025-07-01', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 29, nama: 'Manometer', kode: 'MAN-004', stok_awal: 100, penerimaan: 50, penyaluran: 25, total_stok: 125, tanggal: '2025-06-30', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 30, nama: 'Gas 3kg', kode: 'LPG3-004', stok_awal: 50, penerimaan: 75, penyaluran: 40, total_stok: 85, tanggal: '2025-06-29', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 31, nama: 'Gas LPG 3kg', kode: 'LPG3-005', stok_awal: 250, penerimaan: 1000, penyaluran: 800, total_stok: 450, tanggal: '2025-06-28', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
        { id: 32, nama: 'Gas LPG 12kg', kode: 'LPG12-005', stok_awal: 180, penerimaan: 500, penyaluran: 350, total_stok: 330, tanggal: '2025-06-27', spbe_bpt_nama: 'SPBE Sukamaju', tipe: 'SPBE', sa_region: 'SA Jambi', kabupaten: 'Muaro Jambi' },
        { id: 33, nama: 'Tabung 3kg', kode: 'TBG3-005', stok_awal: 75, penerimaan: 120, penyaluran: 100, total_stok: 95, tanggal: '2025-06-26', spbe_bpt_nama: 'BPT Sejahtera', tipe: 'BPT', sa_region: 'SA Jambi', kabupaten: 'Tanjung Jabung Timur' },
        { id: 34, nama: 'Seal Karet', kode: 'SEAL-05', stok_awal: 300, penerimaan: 50, penyaluran: 200, total_stok: 150, tanggal: '2025-06-25', spbe_bpt_nama: 'SPBE Mandiri', tipe: 'SPBE', sa_region: 'SA Bengkulu', kabupaten: 'Bengkulu Utara' },
        { id: 35, nama: 'Regulator', kode: 'REG-007', stok_awal: 120, penerimaan: 40, penyaluran: 60, total_stok: 100, tanggal: '2025-06-24', spbe_bpt_nama: 'BPT Jaya Abadi', tipe: 'BPT', sa_region: 'SA Bengkulu', kabupaten: 'Rejang Lebong' },
        { id: 36, nama: 'Selang Gas', kode: 'SLG-015', stok_awal: 90, penerimaan: 30, penyaluran: 50, total_stok: 70, tanggal: '2025-06-23', spbe_bpt_nama: 'SPBE Maju Bersama', tipe: 'SPBE', sa_region: 'SA Lampung', kabupaten: 'Pringsewu' },
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
    let startDateFilter = '';
    let endDateFilter = '';
    let currentMaterialPage = 0;
    const itemsPerMaterialPage = 10;
    const maxMaterialPagesToShow = 5;

    // Objek untuk menyimpan nilai terakhir
    const lastTransactionQuantities = {};
    const lastTransactionDestination = {};

    function formatTanggal(tgl) {
        const d = new Date(tgl);
        const options = { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' };
        const formattedDate = d.toLocaleDateString('id-ID', options);
        return formattedDate.replace(/\./g, ''); // Menghilangkan titik dari singkatan hari
    }

    function filterMaterialData() {
        let filteredData = dataMaterialDummy.filter(item => item.spbe_bpt_nama === selectedSpbeBptName);

        if (searchMaterialQuery) {
            filteredData = filteredData.filter(item =>
                item.nama.toLowerCase().includes(searchMaterialQuery.toLowerCase()) ||
                item.kode.toLowerCase().includes(searchMaterialQuery.toLowerCase())
            );
        }

        if (startDateFilter || endDateFilter) {
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                const start = startDateFilter ? new Date(startDateFilter) : null;
                const end = endDateFilter ? new Date(endDateFilter) : null;

                if (start) start.setHours(0, 0, 0, 0);
                if (end) end.setHours(23, 59, 59, 999);
                
                return (!start || itemDate >= start) && (!end || itemDate <= end);
            });
        }
        
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
        } else {
            noData.style.display = 'none';
            const rowsHtml = paginated.map((item, index) => {
                const rowIndex = start + index + 1;
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
                            <p class="text-xs text-secondary mb-0">${formatTanggal(item.tanggal)}</p>
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btn-sm btn-success text-white me-1 kirim-material-btn" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal" data-id="${item.id}" data-spbe-bpt="${item.spbe_bpt_nama}" title="Kirim Material">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-sm btn-info text-white me-1 edit-material-btn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#editMaterialModal" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger text-white delete-material-btn" data-id="${item.id}" title="Hapus Data">
                                <i class="fas fa-trash-alt"></i>
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
                    
                    document.getElementById('editSpbeBptSearch').value = material.spbe_bpt_nama;
                    document.getElementById('editSpbeBptList').style.display = 'none';
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

        document.querySelectorAll('.kirim-material-btn').forEach(button => {
            button.addEventListener('click', function() {
                const materialId = parseInt(this.getAttribute('data-id'));
                const spbeBptName = this.getAttribute('data-spbe-bpt');
                
                const material = dataMaterialDummy.find(item => item.id === materialId);
                const spbeBptInfo = spbeBptInfoDummy.find(info => info.nama === spbeBptName);
                
                if (material && spbeBptInfo) {
                    document.getElementById('kirimMaterialId').value = material.id;
                    document.getElementById('kirimMaterialModalLabel').textContent = `Kirim Material "${material.nama}"`;
                    
                    document.getElementById('kirimNamaSpbeBpt').textContent = spbeBptInfo.nama;
                    document.getElementById('kirimKodePlant').textContent = spbeBptInfo.kode_plant;
                    document.getElementById('kirimRegionSa').textContent = spbeBptInfo.region_sa;
                    document.getElementById('kirimKabupaten').textContent = spbeBptInfo.kabupaten;
                    document.getElementById('kirimStok').textContent = `${material.total_stok} unit`;
                    
                    document.getElementById('asalTransaksiText').value = spbeBptName;
                    document.getElementById('asalTransaksi').value = spbeBptName;

                    const lastDestination = lastTransactionDestination[material.id];
                    const lastQuantity = lastTransactionQuantities[material.id];
                    
                    document.getElementById('tujuanTransaksiSearch').value = lastDestination || '';
                    document.getElementById('jumlahStok').value = lastQuantity || '';
                    document.getElementById('tujuanTransaksiList').style.display = 'none';

                    document.getElementById('jenisPenerimaan').checked = true;
                }
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

        if (totalPages > 1) {
            ul.appendChild(createButton('«', 0, currentMaterialPage === 0));
            ul.appendChild(createButton('‹', currentMaterialPage - 1, currentMaterialPage === 0));

            const startPage = Math.max(0, currentMaterialPage - Math.floor(maxMaterialPagesToShow / 2));
            const endPage = Math.min(totalPages, startPage + maxMaterialPagesToShow);

            for (let i = startPage; i < endPage; i++) {
                ul.appendChild(createButton(i + 1, i, false, i === currentMaterialPage));
            }

            ul.appendChild(createButton('›', currentMaterialPage + 1, currentMaterialPage === totalPages - 1 || totalPages === 0));
            ul.appendChild(createButton('»', totalPages - 1, currentMaterialPage === totalPages - 1 || totalPages === 0));
        }
    }
    
    document.getElementById('editMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = parseInt(document.getElementById('editMaterialId').value);
        const material = dataMaterialDummy.find(item => item.id === id);
        if (material) {
            const newSpbeBptName = document.getElementById('editSpbeBptSearch').value;
            const spbeBptInfo = spbeBptInfoDummy.find(info => info.nama === newSpbeBptName);

            if (!spbeBptInfo) {
                Swal.fire('Gagal!', 'Nama SPBE/BPT tidak valid.', 'error');
                return;
            }

            material.nama = document.getElementById('editNamaMaterial').value;
            material.kode = document.getElementById('editKodeMaterial').value;
            material.spbe_bpt_nama = newSpbeBptName;
            material.tipe = spbeBptInfo.nama.startsWith('SPBE') ? 'SPBE' : 'BPT';
            material.sa_region = spbeBptInfo.region_sa;
            material.kabupaten = spbeBptInfo.kabupaten;
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
            const spbeBptInfo = spbeBptInfoDummy.find(info => info.nama === spbeBpt);
            
            if (!spbeBptInfo) {
                Swal.fire('Gagal!', 'Nama SPBE/BPT tidak valid.', 'error');
                return;
            }
            
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
                sa_region: spbeBptInfo.region_sa,
                kabupaten: spbeBptInfo.kabupaten
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

    document.getElementById('kirimMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('kirimMaterialId').value;
        const asal = document.getElementById('asalTransaksi').value;
        const tujuan = document.getElementById('tujuanTransaksiSearch').value;
        const jenis = document.querySelector('input[name="jenisTransaksi"]:checked').value;
        const jumlah = parseInt(document.getElementById('jumlahStok').value);

        if (!tujuan || isNaN(jumlah) || jumlah <= 0) {
            Swal.fire('Gagal!', 'Harap isi form dengan benar.', 'error');
            return;
        }
        
        const isTujuanValid = allLocations.some(location => location === tujuan);
        if (!isTujuanValid) {
            Swal.fire('Gagal!', 'Tujuan transaksi tidak valid.', 'error');
            return;
        }

        const material = dataMaterialDummy.find(item => item.id == id);
        if (!material) {
            Swal.fire('Error!', 'Data material tidak ditemukan.', 'error');
            return;
        }

        let stokAkhir;
        if (jenis === 'penambahan') {
            stokAkhir = material.total_stok + jumlah;
            material.penerimaan += jumlah;
        } else if (jenis === 'pengurangan') {
            if (material.total_stok < jumlah) {
                Swal.fire('Gagal!', 'Stok tidak mencukupi untuk penyaluran.', 'warning');
                return;
            }
            stokAkhir = material.total_stok - jumlah;
            material.penyaluran += jumlah;
        }
        
        lastTransactionQuantities[id] = jumlah;
        lastTransactionDestination[id] = tujuan;
        
        material.total_stok = stokAkhir;
        material.tanggal = new Date().toISOString().split('T')[0];
        
        renderMaterialTable();

        const kirimModal = bootstrap.Modal.getInstance(document.getElementById('kirimMaterialModal'));
        kirimModal.hide();

        Swal.fire('Berhasil Dikirim!', `Stok **${material.nama}** di **${asal}** saat ini adalah **${stokAkhir} unit**.`, 'success');
    });

    const spbeBptEditData = [...allLocations];
    const editSpbeBptSearch = document.getElementById('editSpbeBptSearch');
    const editSpbeBptList = document.getElementById('editSpbeBptList');

    const tujuanTransaksiSearch = document.getElementById('tujuanTransaksiSearch');
    const tujuanTransaksiList = document.getElementById('tujuanTransaksiList');

    function filterAndRenderList(inputElement, listElement, dataArray, excludeValue) {
        inputElement.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            listElement.innerHTML = '';
            
            const filteredList = dataArray.filter(item =>
                item.toLowerCase().includes(query) && item !== excludeValue
            );

            if (query.length > 0 && filteredList.length > 0) {
                listElement.style.display = 'block';
                filteredList.forEach(item => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.textContent = item;
                    li.addEventListener('click', () => {
                        inputElement.value = item;
                        listElement.style.display = 'none';
                    });
                    listElement.appendChild(li);
                });
            } else if (query.length > 0 && filteredList.length === 0) {
                listElement.style.display = 'block';
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.textContent = 'Tidak ada hasil.';
                listElement.appendChild(li);
            } else {
                listElement.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('search-input-material').addEventListener('input', function () {
            searchMaterialQuery = this.value;
            currentMaterialPage = 0;
            renderMaterialTable();
        });

        document.getElementById('startDate').addEventListener('change', function() {
            startDateFilter = this.value;
            currentMaterialPage = 0;
            renderMaterialTable();
        });

        document.getElementById('endDate').addEventListener('change', function() {
            endDateFilter = this.value;
            currentMaterialPage = 0;
            renderMaterialTable();
        });

        renderMaterialTable();

        // Attach listeners for Edit Modal
        editSpbeBptSearch.addEventListener('input', function() {
            filterAndRenderList(editSpbeBptSearch, editSpbeBptList, allLocations, null);
        });

        // Attach listeners for Kirim Modal
        const kirimModal = document.getElementById('kirimMaterialModal');
        kirimModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const asalTransaksiValue = button.getAttribute('data-spbe-bpt');

            // Reset dan atur ulang pencarian tujuan setiap kali modal dibuka
            tujuanTransaksiSearch.value = '';
            tujuanTransaksiList.innerHTML = '';
            tujuanTransaksiList.style.display = 'none';

            // Panggil fungsi filter dengan nilai yang harus dikecualikan
            filterAndRenderList(tujuanTransaksiSearch, tujuanTransaksiList, allLocations, asalTransaksiValue);
        });

        // Hide list when clicking outside for both modals
        document.addEventListener('click', function(e) {
            // Check for edit modal
            if (!editSpbeBptSearch.contains(e.target) && !editSpbeBptList.contains(e.target)) {
                editSpbeBptList.style.display = 'none';
            }
            // Check for kirim modal
            if (!tujuanTransaksiSearch.contains(e.target) && !tujuanTransaksiList.contains(e.target)) {
                tujuanTransaksiList.style.display = 'none';
            }
        });
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
        .search-input-group {
            max-width: 250px;
        }

        .date-range-picker {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .date-input {
            width: 120px;
            height: 40px;
            min-width: unset;
        }

        .date-range-picker label {
            white-space: nowrap;
            flex-shrink: 0;
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
        
        .export-excel-btn {
            width: 100% !important;
            margin-top: 1rem;
        }

        .branch-selection-text {
            text-align: center !important;
            margin-bottom: 0.5rem !important;
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
        .input-group.input-group-sm {
            height: 38px !important;
            margin-top: 0.5rem;
        }
        .input-group.input-group-sm .form-control,
        .input-group.input-group-sm .input-group-text {
            height: 38px !important;
            font-size: 0.85rem !important;
            padding: 0.4rem 0.8rem !important;
        }
        
        .date-range-picker {
            flex-direction: column;
            align-items: center;
            width: 100%;
            gap: 0.5rem !important;
        }
        .date-input {
            width: 100% !important;
            height: 38px !important;
        }
        .date-range-picker label {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        

        /* --- End of Mobile Specific changes for Search Input Group and Date Picker--- */

        .card-header {
            padding: 1rem !important;
        }

        #table-material thead th {
            font-size: 0.65rem !important;
        }
        #table-material tbody td {
            font-size: 0.75rem !important;
        }
        #table-material tbody td,
        #table-material thead th {
            padding: 0.5rem 0.5rem !important;
        }
    }
</style>
@endsection