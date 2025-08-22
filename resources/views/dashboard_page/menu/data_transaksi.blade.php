@extends('dashboard_page.main')
@section('title', 'Laman Transaksi')
@section('content')

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body p-0">
            <div class="row align-items-center">
                <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                    <h4 class="mb-1 fw-bold" id="summary-title">
                        Ringkasan Data Transaksi SPBE/BPT
                    </h4>
                    <p class="mb-2 opacity-8" id="summary-text">
                        Lihat dan kelola data stok dan transaksi SPBE/BPT untuk region :
                        <strong class="text-primary"><span id="dynamic-branch-name">{{ $selectedSalesArea }}</span></strong>.
                    </p>
                </div>
                <div class="col-md-5 text-center text-md-end">
                    <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                         alt="Ikon Perusahaan"
                         style="max-width: 100%; height: auto; max-height: 80px;">
                </div>
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

                {{-- BARIS 1: Judul dan Search Bar (Sudah Responsif) --}}
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
                    <div class="mb-3 mb-md-0">
                        <h4 class="mb-0">Tabel Stok SPBE/BPT - {{ $selectedSalesArea }}</h4>
                    </div>
                    <div>
                        <form action="{{ route('transaksi.index') }}" method="GET" id="search-form" class="w-100 w-md-auto" style="max-width: 320px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="hidden" name="sales_area" value="{{ $selectedSalesArea }}">
                                <input type="text" name="search" id="search-input" class="form-control" placeholder="Cari Nama atau Kode Plant..." value="{{ $search ?? '' }}">
                            </div>
                        </form>
                    </div>
                </div>
            
                {{-- BARIS 2: Tombol Pilihan Region --}}
                <div class="row">
                    <div class="col-12">
                        <p class="text-sm text-secondary mb-2">
                            *Pilih salah satu tombol di bawah ini untuk melihat data SPBE/BPT berdasarkan Sales Region : *
                        </p>

                        {{-- TAMPILAN DESKTOP: Tombol seperti semula, muncul di layar medium ke atas --}}
                        <div class="d-none d-md-block">
                            <div class="btn-group d-flex flex-wrap branch-buttons" role="group" aria-label="Branch selection">
                                @foreach ($regions as $region)
                                    <a href="{{ route('transaksi.index', ['sales_area' => $region->name_region]) }}"
                                       class="btn btn-sm btn-branch-custom {{ $selectedSalesArea == $region->name_region ? 'btn-primary' : 'btn-outline-primary' }}">
                                        {{ $region->name_region }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- TAMPILAN MOBILE: Dropdown, muncul di layar kecil (di bawah medium) --}}
                        <div class="d-block d-md-none mb-3">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="regionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Pilih Region: {{ $selectedSalesArea }}
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="regionDropdown">
                                    @foreach ($regions as $region)
                                    <li>
                                        <a class="dropdown-item {{ $selectedSalesArea == $region->name_region ? 'active' : '' }}" href="{{ route('transaksi.index', ['sales_area' => $region->name_region]) }}">
                                            {{ $region->name_region }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-5">
                
                {{-- LOKASI ALERTS --}}
                <div class="px-4 pt-3">
                    @if(session('success'))
                        <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Sukses!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                            <span class="alert-text"><strong>Gagal!</strong> {{ session('error') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger text-white" role="alert">
                        <strong class="d-block">Gagal! Terdapat beberapa kesalahan:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- ISI TABEL --}}
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
                            @forelse ($facilities as $facility)
                                <tr>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ ($facilities->currentPage() - 1) * $facilities->perPage() + $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <a href="{{ route('materials.index', $facility) }}" class="mb-0 text-sm font-weight-bolder text-decoration-underline text-primary">
                                                {{ $facility->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $facility->kode_plant }}</p></td>
                                    <td><p class="text-xs text-secondary font-weight-bold mb-0">{{ $facility->province }}</p></td>
                                    <td><p class="text-xs text-secondary font-weight-bold mb-0">{{ $facility->regency }}</p></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info text-white me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editSpbeBptModal-{{ $facility->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('transaksi.destroy', $facility->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION --}}
                @if ($facilities->hasPages())
                    @php $facilities->appends(request()->query()); @endphp
                    <div class="mt-4 px-3 d-flex justify-content-center">
                       <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @php
                                    $total = $facilities->lastPage();
                                    $current = $facilities->currentPage();
                                    $window = 1;
                                @endphp
                                <li class="page-item {{ $facilities->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $facilities->url(1) }}">&laquo;</a>
                                </li>
                                <li class="page-item {{ $facilities->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $facilities->previousPageUrl() }}">&lsaquo;</a>
                                </li>
                                @php $wasGap = false; @endphp
                                @for ($i = 1; $i <= $total; $i++)
                                    @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                        <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $facilities->url($i) }}">{{ $i }}</a>
                                        </li>
                                        @php $wasGap = false; @endphp
                                    @else
                                        @if (!$wasGap)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @php $wasGap = true; @endphp
                                        @endif
                                    @endif
                                @endfor
                                <li class="page-item {{ $facilities->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $facilities->nextPageUrl() }}">&rsaquo;</a>
                                </li>
                                <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $facilities->url($total) }}">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
@foreach ($facilities as $facility)
<div class="modal fade" id="editSpbeBptModal-{{ $facility->id }}" tabindex="-1" aria-labelledby="editSpbeBptModalLabel-{{ $facility->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpbeBptModalLabel-{{ $facility->id }}">Edit Data SPBE/BPT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaksi.update', $facility->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    @php $error_id = session('error_facility_id'); @endphp
                    <div class="mb-3">
                        <label for="edit-name-{{$facility->id}}" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control @if($errors->has('name') && $error_id == $facility->id) is-invalid @endif" id="edit-name-{{$facility->id}}" name="name" value="{{ old('name', $facility->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode_plant-{{$facility->id}}" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control @if($errors->has('kode_plant') && $error_id == $facility->id) is-invalid @endif" id="edit-kode_plant-{{$facility->id}}" name="kode_plant" value="{{ old('kode_plant', $facility->kode_plant) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-province-{{$facility->id}}" class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control @if($errors->has('province') && $error_id == $facility->id) is-invalid @endif" id="edit-province-{{$facility->id}}" name="province" value="{{ old('province', $facility->province) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-regency-{{$facility->id}}" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control @if($errors->has('regency') && $error_id == $facility->id) is-invalid @endif" id="edit-regency-{{$facility->id}}" name="regency" value="{{ old('regency', $facility->regency) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


@push('scripts')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk membuka kembali modal jika ada error validasi --}}
@if ($errors->any() && session('error_facility_id'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var errorModalId = 'editSpbeBptModal-{{ session('error_facility_id') }}';
        var errorModal = new bootstrap.Modal(document.getElementById(errorModalId));
        errorModal.show();
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // == SCRIPT UNTUK TOMBOL DELETE DENGAN SWEETALERT ==
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush


@push('styles')
{{-- Style ini mengembalikan seperti style awal Anda, tanpa horizontal scroll --}}
<style>
    .welcome-card { background-color: white; color: #344767; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); overflow: hidden; position: relative; padding: 1.5rem !important; }
    .welcome-card-icon { height: 60px; width: auto; opacity: 0.9; }
    
    @media (min-width: 768px) {
        .branch-selection-text-desktop { margin-bottom: 0.5rem; white-space: nowrap; }
        .btn-branch-custom { padding: 0.4rem 0.6rem; font-size: 0.78rem; }
    }
    @media (max-width: 767.98px) {
        .welcome-card { padding: 1rem !important; }
        #table-branch-name { text-align: center !important; font-size: 1.25rem !important; margin-bottom: 1rem !important; }
        .branch-buttons { justify-content: center !important; gap: 0.25rem; margin-bottom: 1rem; }
        .btn-branch-custom { padding: 0.3rem 0.6rem; font-size: 0.75rem; flex-grow: 1; min-width: unset; }
        .card-header { padding: 1rem !important; }
        #table-material-1 thead th { font-size: 0.65rem !important; }
        #table-material-1 tbody td { font-size: 0.75rem !important; }
    }
</style>
@endpush
@endsection