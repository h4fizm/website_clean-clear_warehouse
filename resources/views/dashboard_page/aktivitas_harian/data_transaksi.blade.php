@extends('dashboard_page.main')
@section('title', 'Aktivitas Log Harian Transaksi')
@section('content')

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="w-100 order-md-1 text-center text-md-start">
                <a href="{{ route('aktivitas.index') }}" class="text-secondary me-3 d-inline-block">
                    <i class="fas fa-arrow-left fa-2x"></i>
                </a>
                <h4 class="mb-1 fw-bold d-inline-block" id="summary-title">
                    Aktivitas Log Harian Transaksi
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Laporan detail semua aktivitas penerimaan dan penyaluran material.
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
                    <h6>Data riwayat penerimaan dan penyaluran material.</h6>
                </div>
                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center mt-2 mt-md-0">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </button>
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <form action="{{ route('aktivitas.transaksi') }}" method="GET">
                    <div class="d-flex flex-wrap gap-2 mb-3 px-3 align-items-center justify-content-between">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Material, Kode, Asal, Tujuan, User..." value="{{ $search ?? '' }}" style="width: 600px; height: 35px;">
                        
                        <div class="d-flex align-items-center gap-2">
                            <label for="start_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Dari:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate ?? '' }}" style="width: 150px; height: 35px;">
                            
                            <label for="end_date" class="form-label mb-0 text-xs text-secondary font-weight-bolder">Sampai:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate ?? '' }}" style="width: 150px; height: 35px;">

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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aktivitas</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">User PJ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                @php
                                    if (!$transaction->item) continue;
                                    $isPenerimaan = $transaction->jenis_transaksi === 'Penerimaan';
                                    $activityColor = $isPenerimaan ? 'bg-gradient-success' : 'bg-gradient-primary';
                                    $stokAkhir = $isPenerimaan 
                                        ? ($transaction->item->stok_awal + $transaction->jumlah) 
                                        : ($transaction->item->stok_awal - $transaction->jumlah);
                                @endphp
                                <tr>
                                    <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + $transactions->firstItem() - 1 }}</p></td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm font-weight-bolder">{{ $transaction->item->nama_material }}</h6>
                                            <p class="text-xs text-secondary mb-0">Kode: {{ $transaction->item->kode_material }}</p>
                                        </div>
                                    </td>
                                    
                                    {{-- =================== PERBAIKAN DI SINI =================== --}}
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $transaction->facilityFrom ? $transaction->facilityFrom->nama_facility : ($transaction->regionFrom->nama_region ?? 'N/A') }}</p></td> 
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $transaction->facilityTo ? $transaction->facilityTo->nama_facility : ($transaction->regionTo->nama_region ?? 'N/A') }}</p></td>
                                    {{-- ========================================================= --}}
                                    
                                    <td class="text-center"><span class="badge bg-secondary text-white text-xs">{{ $transaction->item->stok_awal }} pcs</span></td>
                                    <td class="text-center"><span class="badge {{ $activityColor }} text-white text-xs">{{ $transaction->jumlah }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-info text-white text-xs">{{ $stokAkhir }} pcs</span></td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $transaction->no_surat_persetujuan ?? '-' }}</p></td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $transaction->no_ba_serah_terima ?? '-' }}</p></td>
                                    <td class="text-center"><span class="badge {{ $activityColor }} text-white text-xs">{{ $transaction->jenis_transaksi }}</span></td>
                                    <td class="text-center"><p class="text-xs text-secondary mb-0">{{ $transaction->user->name ?? 'N/A' }}</p></td>
                                    <td class="text-center"><p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($transaction->created_at)->isoFormat('dddd, D MMMM YYYY') }}</p></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">Data tidak ditemukan</td>
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

@endsection