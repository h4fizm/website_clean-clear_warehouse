@extends('dashboard_page.main')
@section('title', 'Data Material - ' . $facility->name)

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                
                <form method="GET" action="{{ route('materials.index', $facility) }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Daftar Stok Material - {{ $facility->name }}</h3>
                    </div>
                    
                    <div class="row mb-3 align-items-start">
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Kode Material..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-md-end">
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" name="start_date" id="startDate" 
                                       class="form-control form-control-sm date-input" 
                                       style="max-width: 160px;"
                                       value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" name="end_date" id="endDate" 
                                       class="form-control form-control-sm date-input" 
                                       style="max-width: 160px;"
                                       value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                            <div class="align-self-end">
                                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0 pt-0 pb-5">
                {{-- Notifikasi --}}
                <div class="px-4 pt-2">
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
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-material">
                        {{-- ... Thead ... --}}
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
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $item->nama_material }}</p></td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $item->kode_material }}</p></td>
                                    <td class="text-center"><span class="badge bg-gradient-secondary text-white text-xs">{{ number_format($item->stok_awal) }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-gradient-primary text-white text-xs">{{ number_format($item->penerimaan_total) }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-gradient-info text-white text-xs">{{ number_format($item->penyaluran_total) }} pcs</span></td>
                                    <td class="text-center"><span class="badge bg-gradient-success text-white text-xs">{{ number_format($item->stok_akhir) }} pcs</span></td>
                                    <td class="text-center">
                                        <p class="text-xs text-secondary font-weight-bold mb-0">
                                            @php $tanggal = $item->latest_transaction_date ?? $item->updated_at; @endphp
                                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-success text-white me-1" title="Kirim Material" disabled><i class="fas fa-paper-plane"></i></button>
                                        <button type="button" class="btn btn-sm btn-info text-white me-1" title="Edit Data" data-bs-toggle="modal" data-bs-target="#editMaterialModal-{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('materials.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data material untuk SPBE/BPT ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Container untuk Tombol Kembali dan Pagination --}}
                <div class="mt-4 px-3 d-flex justify-content-between align-items-center">
                   @if ($items->hasPages())
                        @php $items->appends(request()->query()); @endphp
                        {{-- ... kode pagination kustom Anda ... --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loop untuk membuat Modal Edit untuk setiap item --}}
@foreach ($items as $item)
<div class="modal fade" id="editMaterialModal-{{ $item->id }}" tabindex="-1" aria-labelledby="editMaterialModalLabel-{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMaterialModalLabel-{{ $item->id }}">Edit Data Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('materials.update', $item) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama_material-{{ $item->id }}" class="form-label">Nama Material</label>
            <input type="text" class="form-control @if($errors->has('nama_material') && session('error_item_id') == $item->id) is-invalid @endif" id="nama_material-{{ $item->id }}" name="nama_material" value="{{ old('nama_material', $item->nama_material) }}" required>
            @if($errors->has('nama_material') && session('error_item_id') == $item->id) <div class="invalid-feedback">{{ $errors->first('nama_material') }}</div> @endif
          </div>
          <div class="mb-3">
            <label for="kode_material-{{ $item->id }}" class="form-label">Kode Material</label>
            <input type="text" class="form-control @if($errors->has('kode_material') && session('error_item_id') == $item->id) is-invalid @endif" id="kode_material-{{ $item->id }}" name="kode_material" value="{{ old('kode_material', $item->kode_material) }}" required>
            @if($errors->has('kode_material') && session('error_item_id') == $item->id) <div class="invalid-feedback">{{ $errors->first('kode_material') }}</div> @endif
          </div>
          <div class="mb-3">
            <label for="stok_awal-{{ $item->id }}" class="form-label">Stok Awal</label>
            <input type="number" class="form-control @if($errors->has('stok_awal') && session('error_item_id') == $item->id) is-invalid @endif" id="stok_awal-{{ $item->id }}" name="stok_awal" value="{{ old('stok_awal', $item->stok_awal) }}" min="0" required>
            @if($errors->has('stok_awal') && session('error_item_id') == $item->id) <div class="invalid-feedback">{{ $errors->first('stok_awal') }}</div> @endif
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
@endsection

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
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); 
                
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

