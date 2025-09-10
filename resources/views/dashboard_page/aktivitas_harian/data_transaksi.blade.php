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
                <form action="{{ route('aktivitas.transaksi') }}" method="GET">
                    <div class="d-flex flex-wrap gap-3 mb-3 px-3 align-items-center justify-content-between">
                        {{-- Kolom Kiri: Search --}}
                        <div class="d-flex flex-wrap gap-3">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Material, Kode, Lokasi, User..." value="{{ $search ?? '' }}" style="width: 300px; height: 38px;">
                        </div>
                        
                        {{-- Kolom Kanan: Filter Tanggal & Tombol Cari --}}
                        <div class="d-flex align-items-center gap-2">
                            <label for="start_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate ?? '' }}" style="width: 150px; height: 38px;">
                            
                            <label for="end_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate ?? '' }}" style="width: 150px; height: 38px;">

                            <button type="submit" class="btn btn-primary btn-sm mb-0">Cari</button>
                        </div>
                    </div>
                </form>

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
                            @forelse ($transactions as $transaction)
                                @if (!$transaction->item) @continue @endif

                                @php
                                    $asal = 'N/A';
                                    $tujuan = 'N/A';
                                    $activityAsal = null;
                                    $activityTujuan = null;
                                    
                                    // CASE 1: Jika transaksi adalah SALES
                                    if ($transaction->jenis_transaksi == 'sales') {
                                        $asal = $transaction->facilityFrom->name ?? $transaction->regionFrom->name_region ?? 'N/A';
                                        $tujuan = $transaction->tujuan_sales;

                                        $activityAsal = ['text' => 'Penyaluran', 'color' => 'bg-gradient-danger', 'icon' => 'fa-arrow-up'];
                                        $activityTujuan = ['text' => 'Transaksi Sales', 'color' => 'bg-gradient-warning', 'icon' => 'fa-dollar-sign'];
                                    
                                    // CASE 2: Jika transaksi adalah TRANSFER (Penyaluran/Penerimaan)
                                    } else {
                                        $asal = $transaction->facilityFrom->name ?? $transaction->regionFrom->name_region ?? 'N/A';
                                        $tujuan = $transaction->facilityTo->name ?? $transaction->regionTo->name_region ?? 'N/A';

                                        $activityAsal = ['text' => 'Penyaluran', 'color' => 'bg-gradient-danger', 'icon' => 'fa-arrow-up'];
                                        $activityTujuan = ['text' => 'Penerimaan', 'color' => 'bg-gradient-success', 'icon' => 'fa-arrow-down'];
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $loop->iteration + $transactions->firstItem() - 1 }}
                                        </p>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm font-weight-bolder">{{ $transaction->item->nama_material }}</h6>
                                            <p class="text-xs text-secondary mb-0">Kode: {{ $transaction->item->kode_material }}</p>
                                        </div>
                                    </td>
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $asal }}</p></td>
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $tujuan }}</p></td>

                                    {{-- Kolom stok dari snapshot historis lokasi asal --}}
                                    <td class="text-center"><span class="badge bg-secondary text-white text-xs">{{ number_format($transaction->stok_awal_asal ?? 0) }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-gradient-warning text-white text-xs">{{ number_format($transaction->jumlah) }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-info text-white text-xs">{{ number_format($transaction->stok_akhir_asal ?? 0) }} pcs</span></td>

                                    <td><p class="text-xs text-secondary mb-0">{{ $transaction->no_surat_persetujuan ?? '-' }}</p></td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $transaction->no_ba_serah_terima ?? '-' }}</p></td>

                                    {{-- Kolom Aktivitas Asal --}}
                                    <td class="text-center">
                                        @if ($activityAsal)
                                            <span class="badge {{ $activityAsal['color'] }} text-white text-xs">
                                                <i class="fas {{ $activityAsal['icon'] }} me-1"></i>
                                                {{ $activityAsal['text'] }}
                                            </span>
                                        @else
                                            <span class="text-muted text-xs">-</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aktivitas Tujuan --}}
                                    <td class="text-center">
                                        @if ($activityTujuan)
                                            <span class="badge {{ $activityTujuan['color'] }} text-white text-xs">
                                                <i class="fas {{ $activityTujuan['icon'] }} me-1"></i>
                                                {{ $activityTujuan['text'] }}
                                            </span>
                                        @else
                                            <span class="text-muted text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center"><p class="text-xs text-secondary mb-0">{{ $transaction->user->name ?? 'N/A' }}</p></td>
                                    <td class="text-center"><p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($transaction->created_at)->isoFormat('dddd, D MMMM YYYY') }}</p></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginasi kustom Anda --}}
                @if ($transactions->hasPages())
                    <div class="mt-4 px-3 d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @php
                                    $total = $transactions->lastPage();
                                    $current = $transactions->currentPage();
                                    $window = 1; 
                                @endphp
                                <li class="page-item {{ $transactions->onFirstPage() ? 'disabled' : '' }}"><a class="page-link" href="{{ $transactions->url(1) }}">&laquo;</a></li>
                                <li class="page-item {{ $transactions->onFirstPage() ? 'disabled' : '' }}"><a class="page-link" href="{{ $transactions->previousPageUrl() }}">&lsaquo;</a></li>
                                @php $wasGap = false; @endphp
                                @for ($i = 1; $i <= $total; $i++)
                                    @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                        <li class="page-item {{ ($i == $current) ? 'active' : '' }}"><a class="page-link" href="{{ $transactions->url($i) }}">{{ $i }}</a></li>
                                        @php $wasGap = false; @endphp
                                    @else
                                        @if (!$wasGap)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @php $wasGap = true; @endphp
                                        @endif
                                    @endif
                                @endfor
                                <li class="page-item {{ $transactions->hasMorePages() ? '' : 'disabled' }}"><a class="page-link" href="{{ $transactions->nextPageUrl() }}">&rsaquo;</a></li>
                                <li class="page-item {{ $current == $total ? 'disabled' : '' }}"><a class="page-link" href="{{ $transactions->url($total) }}">&raquo;</a></li>
                            </ul>
                        </nav>
                    </div>
                @endif
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
{{-- modal export excel --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi modal Bootstrap
        const exportModalElement = document.getElementById('exportExcelModal');
        const exportModal = new bootstrap.Modal(exportModalElement);

        // Cari semua tombol yang relevan
        const openExportModalBtn = document.getElementById('openExportModalBtn');
        const confirmExportBtn = document.getElementById('confirmExportBtn');
        const searchInput = document.querySelector('input[name="search"]');

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
                const searchValue = searchInput ? searchInput.value : '';

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
                // Tambahkan filter untuk mengecualikan "pemusnahan"
                params.append('jenis_transaksi', 'penyaluran,penerimaan,sales');


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