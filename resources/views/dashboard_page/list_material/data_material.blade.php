@extends('dashboard_page.main')
@section('title', 'Data Material - ' . $facility->name)

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                
                {{-- Form untuk handle search & filter --}}
                <form method="GET" action="{{ route('materials.index', $facility) }}">
                    {{-- Baris untuk Judul dan Tombol --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Daftar Stok Material - {{ $facility->name }}</h3>
                    </div>
                    
                    {{-- Baris untuk Search & Filter Tanggal --}}
                    <div class="row mb-3 align-items-start">
                        {{-- Input Search --}}
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Kode Material..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>

                        {{-- [INI BAGIAN YANG DITAMBAHKAN] --}}
                        {{-- Filter Tanggal --}}
                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-md-end">
                            {{-- Start Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" name="start_date" id="startDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ $filters['start_date'] ?? '' }}">
                            </div>

                            {{-- End Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" name="end_date" id="endDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ $filters['end_date'] ?? '' }}">
                            </div>

                            {{-- Tombol Filter --}}
                             {{-- Button Filter (diturunkan sedikit) --}}
                            <div class="align-self-end">
                                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                            </div>
                        </div>
                        {{-- [AKHIR BAGIAN YANG DITAMBAHKAN] --}}
                    </div>
                </form>
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
                            {{-- Loop data asli dari controller menggunakan @forelse --}}
                            @forelse ($items as $item)
                                <tr>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $item->nama_material }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">{{ $item->kode_material }}</p>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-secondary text-white text-xs">{{ number_format($item->stok_awal) }} pcs</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-primary text-white text-xs">{{ number_format($item->penerimaan_total ?? 0) }} pcs</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-info text-white text-xs">{{ number_format($item->penyaluran_total ?? 0) }} pcs</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-success text-white text-xs">{{ number_format($item->stok_akhir) }} pcs</span>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs text-secondary font-weight-bold mb-0">
                                            @php
                                                $tanggal = $item->latest_transaction_date ?? $item->updated_at;
                                            @endphp
                                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        {{-- Tombol aksi sementara belum difungsikan --}}
                                        <button class="btn btn-sm btn-success text-white me-1" title="Kirim Material" disabled>
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info text-white me-1" title="Edit Data" disabled>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger text-white" title="Hapus Data" disabled>
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Tidak ada data material untuk SPBE/BPT ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            
                {{-- Menampilkan pagination dari Laravel --}}
                @if ($items->hasPages())
                    {{-- Baris ini penting agar filter pencarian tetap terbawa saat pindah halaman --}}
                    @php $items->appends(request()->query()); @endphp
                    <div class="mt-4 px-3 d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @php
                                    $total = $items->lastPage();
                                    $current = $items->currentPage();
                                    $window = 1;
                                @endphp
                                {{-- Tombol Halaman Pertama --}}
                                <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $items->url(1) }}">&laquo;</a>
                                </li>
                                {{-- Tombol Halaman Sebelumnya --}}
                                <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $items->previousPageUrl() }}">&lsaquo;</a>
                                </li>
                                
                                @php $wasGap = false; @endphp
                                @for ($i = 1; $i <= $total; $i++)
                                    @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                        <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a>
                                        </li>
                                        @php $wasGap = false; @endphp
                                    @else
                                        @if (!$wasGap)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @php $wasGap = true; @endphp
                                        @endif
                                    @endif
                                @endfor

                                {{-- Tombol Halaman Berikutnya --}}
                                <li class="page-item {{ $items->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $items->nextPageUrl() }}">&rsaquo;</a>
                                </li>
                                {{-- Tombol Halaman Terakhir --}}
                                <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $items->url($total) }}">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Semua modal tetap ada di sini, tapi fungsionalitasnya akan kita aktifkan nanti --}}

@endsection

@push('scripts')
{{-- Bagian JavaScript dikosongkan karena rendering tabel sudah dihandle oleh Blade --}}
{{-- Kita akan isi lagi nanti saat mengaktifkan fungsionalitas modal --}}
@endpush