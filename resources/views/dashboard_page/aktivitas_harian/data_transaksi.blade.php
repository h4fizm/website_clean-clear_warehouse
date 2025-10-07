@extends('dashboard_page.main')
@section('title', 'Aktivitas Log Harian Transaksi')
@section('content')

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold d-inline-block" id="summary-title">
                    Aktivitas Log Harian Transaksi
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Laporan detail semua aktivitas penerimaan, penyaluran, dan sales material.
                </p>
            </div>
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                     alt="Pertamina Patra Niaga Logo"
                     class="welcome-card-icon"
                     style="height: 60px;">
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h4>Tabel Aktivitas Transaksi Harian</h4>
                    <h6>Data riwayat penerimaan, penyaluran, dan sales material.</h6>
                </div>
                <span id="openExportModalBtn" class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </span>
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <div class="d-flex flex-wrap gap-3 mb-3 px-3 align-items-center justify-content-between">
                    {{-- Kolom Kiri: Search --}}
                    <div class="d-flex flex-wrap gap-3">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari Material, Kode, Lokasi, User..." style="width: 300px; height: 38px;">
                    </div>
                    
                    {{-- Kolom Kanan: Filter Tanggal & Tombol Cari --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="start_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                        <input type="date" id="startDate" class="form-control form-control-sm" style="width: 150px; height: 38px;">
                        
                        <label for="end_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                        <input type="date" id="endDate" class="form-control form-control-sm" style="width: 150px; height: 38px;">
                        
                        <select id="jenisTransaksi" class="form-control form-control-sm" style="width: 150px; height: 38px;">
                            <option value="">Semua Transaksi</option>
                            <option value="penyaluran">Penyaluran</option>
                            <option value="penerimaan">Penerimaan</option>
                            <option value="sales">Sales</option>
                        </select>

                        <button id="filterBtn" class="btn btn-primary btn-sm mb-0">Cari</button>
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-aktivitas-transaksi">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Material & Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Asal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tujuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jumlah Transaksi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Surat Persetujuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. BA Serah Terima</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aktivitas Asal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aktivitas Tujuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">User PJ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal export excel --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Data ke Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-sm text-secondary">Pilih rentang tanggal untuk data yang ingin Anda export.</p>
                <div class="mb-3">
                    <label for="exportStartDate" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="exportStartDate">
                </div>
                <div class="mb-3">
                    <label for="exportEndDate" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="exportEndDate">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmExportBtn">
                    <i class="fas fa-file-excel me-2"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- DataTables Configuration --}}
<script>
    $(document).ready(function() {
        const table = $('#table-aktivitas-transaksi').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('api.aktivitas.transaksi') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr, error, code) {
                    console.log('DataTable Error:', xhr.responseText);
                    console.log('Error details:', error, code);
                },
                data: function(d) {
                    d.search = $('#searchInput').val();
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                    d.jenis_transaksi = $('#jenisTransaksi').val();
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'id', 
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                {
                    data: 'item',
                    name: 'item.nama_material',
                    render: function(data, type, row) {
                        if (!row.item) {
                            return `<div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm font-weight-bolder">-</h6>
                                        <p class="text-xs text-secondary mb-0">Kode: -</p>
                                    </div>`;
                        }
                        return `<div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm font-weight-bolder">${row.item.nama_material || '-'}</h6>
                                    <p class="text-xs text-secondary mb-0">Kode: ${row.item.kode_material || '-'}</p>
                                </div>`;
                    }
                },
                { 
                    data: 'facility_from', 
                    name: 'facilityFrom.name',
                    render: function(data, type, row) {
                        return `<p class="text-xs font-weight-bold mb-0">${row.facility_from || row.region_from || 'N/A'}</p>`;
                    }
                },
                { 
                    data: 'tujuan', 
                    name: 'tujuan',
                    render: function(data, type, row) {
                        return `<p class="text-xs font-weight-bold mb-0">${row.facility_to || row.region_to || row.tujuan_sales || 'N/A'}</p>`;
                    }
                },
                { 
                    data: 'stok_awal_asal', 
                    name: 'stok_awal_asal',
                    render: function(data, type, row) {
                        return `<span class="badge bg-secondary text-white text-xs">${(row.stok_awal_asal || 0).toLocaleString('id-ID')} pcs</span>`;
                    }
                },
                { 
                    data: 'jumlah', 
                    name: 'jumlah',
                    render: function(data, type, row) {
                        return `<span class="badge bg-gradient-warning text-white text-xs">${row.jumlah.toLocaleString('id-ID')} pcs</span>`;
                    }
                },
                { 
                    data: 'stok_akhir_asal', 
                    name: 'stok_akhir_asal',
                    render: function(data, type, row) {
                        return `<span class="badge bg-info text-white text-xs">${(row.stok_akhir_asal || 0).toLocaleString('id-ID')} pcs</span>`;
                    }
                },
                { 
                    data: 'no_surat_persetujuan', 
                    name: 'no_surat_persetujuan',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary mb-0">${row.no_surat_persetujuan || '-'}</p>`;
                    }
                },
                { 
                    data: 'no_ba_serah_terima', 
                    name: 'no_ba_serah_terima',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary mb-0">${row.no_ba_serah_terima || '-'}</p>`;
                    }
                },
                { 
                    data: 'jenis_transaksi', 
                    name: 'jenis_transaksi',
                    render: function(data, type, row) {
                        let activityAsal = null;
                        
                        if (row.jenis_transaksi == 'sales') {
                            activityAsal = {text: 'Penyaluran', color: 'bg-gradient-danger', icon: 'fa-arrow-up'};
                        } else {
                            activityAsal = {text: 'Penyaluran', color: 'bg-gradient-danger', icon: 'fa-arrow-up'};
                        }
                        
                        return activityAsal ? `<span class="badge ${activityAsal['color']} text-white text-xs">
                                                    <i class="fas ${activityAsal['icon']} me-1"></i>
                                                    ${activityAsal['text']}
                                                </span>` : `<span class="text-muted text-xs">-</span>`;
                    }
                },
                { 
                    data: 'jenis_transaksi', 
                    name: 'jenis_transaksi',
                    render: function(data, type, row) {
                        let activityTujuan = null;
                        
                        if (row.jenis_transaksi == 'sales') {
                            activityTujuan = {text: 'Transaksi Sales', color: 'bg-gradient-warning', icon: 'fa-dollar-sign'};
                        } else {
                            activityTujuan = {text: 'Penerimaan', color: 'bg-gradient-success', icon: 'fa-arrow-down'};
                        }
                        
                        return activityTujuan ? `<span class="badge ${activityTujuan['color']} text-white text-xs">
                                                    <i class="fas ${activityTujuan['icon']} me-1"></i>
                                                    ${activityTujuan['text']}
                                                </span>` : `<span class="text-muted text-xs">-</span>`;
                    }
                },
                {
                    data: 'user',
                    name: 'user.name',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary mb-0">${(row.user && row.user.name) ? row.user.name : 'N/A'}</p>`;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row) {
                        if (!row.created_at) return '<p class="text-xs text-secondary mb-0">-</p>';
                        return `<p class="text-xs text-secondary mb-0">${moment(row.created_at).locale('id').format('dddd, D MMMM YYYY')}</p>`;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            dom: 'Bfrtip',
            order: [[12, 'desc']] // Default order by transaction date
        });

        // Handle search input
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Handle date filter
        $('#filterBtn').on('click', function() {
            table.draw();
        });
    });
</script>

{{-- modal export excel --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi modal Bootstrap
        const exportModalElement = document.getElementById('exportExcelModal');
        const exportModal = new bootstrap.Modal(exportModalElement);

        // Cari semua tombol yang relevan
        const openExportModalBtn = document.getElementById('openExportModalBtn');
        const confirmExportBtn = document.getElementById('confirmExportBtn');

        // Event listener untuk membuka modal
        if (openExportModalBtn) {
            openExportModalBtn.addEventListener('click', function() {
                exportModal.show();
            });
        }

        // Event listener untuk tombol "Export" di dalam modal
        if (confirmExportBtn) {
            confirmExportBtn.addEventListener('click', function() {
                // 1. Ambil nilai filter dari modal
                const startDate = document.getElementById('exportStartDate').value;
                const endDate = document.getElementById('exportEndDate').value;
                
                // 2. Ambil nilai filter pencarian utama dari halaman
                const searchValue = document.getElementById('searchInput').value;
                const jenisTransaksi = document.getElementById('jenisTransaksi').value;

                // 3. Siapkan URL dasar dari route Laravel
                const baseUrl = "{{ route('aktivitas.transaksi.export') }}";

                // 4. Buat URLSearchParams untuk menambahkan semua parameter filter
                const params = new URLSearchParams();
                if (startDate) {
                    params.append('start_date', startDate);
                }
                if (endDate) {
                    params.append('end_date', endDate);
                }
                if (searchValue) {
                    params.append('search', searchValue);
                }
                if (jenisTransaksi) {
                    params.append('jenis_transaksi', jenisTransaksi);
                }

                // 5. Gabungkan URL dasar dengan parameter
                const exportUrl = `${baseUrl}?${params.toString()}`;

                // 6. Sembunyikan modal
                exportModal.hide();

                // 7. Arahkan browser ke URL export untuk memulai download
                window.location.href = exportUrl;
            });
        }
    });
</script>
@endpush

@endsection