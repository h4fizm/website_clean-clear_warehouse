@extends('dashboard_page.main')
@section('title', 'Daftar Data P.Layang (Pusat)')
@section('content')

{{-- Welcome Section --}}
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
                    Ringkasan Data P.Layang (Pusat)
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Lihat dan kelola data stok material dari Pusat Layang.
                </p>
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

{{-- Tabel Data P.Layang (Pusat) --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                <form method="GET" action="{{ route('pusat.index') }}">
                    <div class="row mb-3 align-items-center">
                        <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                            <h4 class="mb-0" id="table-branch-name">Tabel Data Stok Material - P.Layang (Pusat)</h4>
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="button" class="btn btn-success d-flex align-items-center justify-content-center w-100 w-md-auto export-excel-btn" disabled>
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </button>
                        </div>
                    </div>

                    {{-- Area untuk menampilkan Bootstrap Alert dan Validasi Server-Side --}}
                    <div class="px-0 pt-2">
                        @if(session('success'))
                            <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
                            <strong class="d-block">Gagal! Terdapat beberapa kesalahan:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>
                    
                    <div class="row mb-3 align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari material..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-start justify-content-md-end date-range-picker">
                            <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                            <input type="date" name="start_date" id="startDate" class="form-control me-2 date-input" value="{{ $filters['start_date'] ?? '' }}">
                            <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                            <input type="date" name="end_date" id="endDate" class="form-control me-md-2 date-input" value="{{ $filters['end_date'] ?? '' }}">
                            <button type="submit" class="btn btn-primary btn-sm px-3 mt-2 mt-md-0">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
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
                            @forelse ($items as $item)
                            <tr>
                                <td class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</p>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <p class="mb-0 text-sm font-weight-bolder text-primary">{{ $item->nama_material }}</p>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary mb-0">{{ $item->kode_material }}</p>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-secondary text-white text-xs">{{ $item->stok_awal }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-primary text-white text-xs">{{ $item->penerimaan_total }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-info text-white text-xs">{{ $item->penyaluran_total }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-success text-white text-xs">{{ $item->stok_akhir }} pcs</span>
                                </td>
                                <td class="text-center">
                                     <p class="text-xs text-secondary font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($item->updated_at)->locale('id')->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success text-white me-1 kirim-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#editMaterialModal-{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('pusat.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white delete-btn" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Data Kosong</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($items->hasPages())
                <div class="mt-4 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $items->lastPage();
                                $current = $items->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->url(1) }}">&laquo;</a>
                            </li>
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
                            <li class="page-item {{ $items->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $items->nextPageUrl() }}">&rsaquo;</a>
                            </li>
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

{{-- Modal for Send Material Data --}}
<div class="modal fade" id="kirimMaterialModal" tabindex="-1" aria-labelledby="kirimMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kirimMaterialModalLabel">Kirim Material <span id="modal-nama-material"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card p-3 mb-3 bg-light">
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">NAMA MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-nama-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">KODE MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-kode-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">STOK AKHIR</p>
                    <p class="mb-0 text-sm font-weight-bold" id="modal-total-stok-display"></p>
                </div>
                <form id="kirimMaterialForm">
                    <input type="hidden" id="kirim-material-id">
                    <div class="mb-3">
                        <label for="asal-transaksi" class="form-label">Asal Transaksi</label>
                        <input type="text" class="form-control" id="asal-transaksi" value="P.Layang" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tujuan-transaksi-search" class="form-label">Tujuan Transaksi</label>
                        <input type="text" class="form-control" id="tujuan-transaksi-search" placeholder="Cari tujuan..." required>
                        <ul id="tujuan-transaksi-list" class="list-group mt-1" style="max-height: 150px; overflow-y: auto; display: none;"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="no-surat-persetujuan" class="form-label">No. Surat Persetujuan (Opsional)</label>
                        <input type="text" class="form-control" id="no-surat-persetujuan">
                    </div>
                    <div class="mb-3">
                        <label for="no-ba-serah-terima" class="form-label">No. BA Serah Terima (Opsional)</label>
                        <input type="text" class="form-control" id="no-ba-serah-terima">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penerimaan" value="penerimaan" checked>
                                <label class="form-check-label" for="jenis-penerimaan">Penerimaan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penyaluran" value="penyaluran">
                                <label class="form-check-label" for="jenis-penyaluran">Penyaluran</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah-stok" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlah-stok" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="submitKirim">Kirim</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit dibuat di dalam Loop --}}
@foreach ($items as $item)
<div class="modal fade" id="editMaterialModal-{{ $item->id }}" tabindex="-1" aria-labelledby="editMaterialModalLabel-{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMaterialModalLabel-{{ $item->id }}">Edit Data Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pusat.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input type="text" class="form-control @if($errors->has('nama_material') && session('error_item_id') == $item->id) is-invalid @endif" 
                   id="nama_material-{{ $item->id }}" name="nama_material" placeholder=" " 
                   value="{{ old('nama_material', $item->nama_material) }}">
            <label for="nama_material-{{ $item->id }}">Nama Material</label>
            @if($errors->has('nama_material') && session('error_item_id') == $item->id) 
              <div class="invalid-feedback">{{ $errors->first('nama_material') }}</div> 
            @endif
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control @if($errors->has('kode_material') && session('error_item_id') == $item->id) is-invalid @endif" 
                   id="kode_material-{{ $item->id }}" name="kode_material" placeholder=" " 
                   value="{{ old('kode_material', $item->kode_material) }}">
            <label for="kode_material-{{ $item->id }}">Kode Material</label>
            @if($errors->has('kode_material') && session('error_item_id') == $item->id) 
              <div class="invalid-feedback">{{ $errors->first('kode_material') }}</div> 
            @endif
          </div>
          <div class="form-floating mb-3">
            <input type="number" class="form-control @if($errors->has('stok_awal') && session('error_item_id') == $item->id) is-invalid @endif" 
                   id="stok_awal-{{ $item->id }}" name="stok_awal" placeholder=" " 
                   value="{{ old('stok_awal', $item->stok_awal) }}" min="0">
            <label for="stok_awal-{{ $item->id }}">Stok Awal</label>
            @if($errors->has('stok_awal') && session('error_item_id') == $item->id) 
              <div class="invalid-feedback">{{ $errors->first('stok_awal') }}</div> 
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk membuka kembali modal jika ada error validasi --}}
@if ($errors->any() && session('error_item_id'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = new bootstrap.Modal(document.getElementById('editMaterialModal-{{ session('error_item_id') }}'));
        editModal.show();
    });
</script>
@endif

{{-- Script konfirmasi hapus untuk form submit --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); 
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data material ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
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

{{-- CSS Lengkap --}}
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
    .date-input {
        width: auto;
        flex-grow: 1;
    }

    /* Mobile specific styles (max-width 767.98px for Bootstrap's 'md' breakpoint) */
    @media (max-width: 767.98px) {
        .date-range-picker {
            flex-direction: column;
            align-items: stretch !important;
        }
        .date-range-picker .form-control {
            margin-right: 0 !important;
            margin-bottom: 0.5rem;
        }
        .date-range-picker label {
            margin-bottom: 0.25rem;
            align-self: flex-start;
        }
    }
</style>
@endsection