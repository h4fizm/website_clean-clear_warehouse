@extends('dashboard_page.main')
@section('title', 'Daftar Data P.Layang (Pusat)')
@section('content')

{{-- Bagian Welcome & Filter tidak berubah --}}
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
                {{-- Form untuk Filter (Tidak Diubah) --}}
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
                    {{-- Tabel dan Isinya (Tidak Diubah) --}}
                    <table class="table align-items-center mb-0" id="table-material-1">
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
                                         {{-- Menggunakan updated_at karena tanggal transaksi terakhir tidak selalu ada --}}
                                        {{ \Carbon\Carbon::parse($item->updated_at)->locale('id')->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success text-white me-1 kirim-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#editMaterialModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger text-white delete-btn" data-id="{{ $item->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Data Kosong
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Kustom Anda (TIDAK DIUBAH) --}}
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

{{-- Modal Kirim Material (Tidak Diubah) --}}
<div class="modal fade" id="kirimMaterialModal" tabindex="-1" aria-labelledby="kirimMaterialModalLabel" aria-hidden="true">
    {{-- ... Konten Modal Kirim Anda ... --}}
</div>

{{-- =============================================== --}}
{{-- MODAL EDIT - SUDAH DIMODIFIKASI AGAR FUNGSIONAL --}}
{{-- =============================================== --}}
<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMaterialModalLabel">Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form action akan diisi oleh JS --}}
                <form id="editMaterialForm" method="POST"> 
                    @csrf
                    @method('PUT') {{-- Beritahu Laravel ini adalah request PUT --}}

                    <div class="mb-3">
                        <label for="edit-nama-material" class="form-label">Nama Material</label>
                        {{-- Tambahkan 'name' attribute --}}
                        <input type="text" class="form-control" id="edit-nama-material" name="nama_material" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode-material" class="form-label">Kode Material</label>
                        {{-- Tambahkan 'name' attribute --}}
                        <input type="text" class="form-control" id="edit-kode-material" name="kode_material" required>
                    </div>
                    <div class="mb-3">
                        {{-- Mengganti dari Stok Akhir ke Stok Awal agar logis --}}
                        <label for="edit-stok-awal" class="form-label">Stok Awal</label>
                        {{-- Tambahkan 'name' attribute --}}
                        <input type="number" class="form-control" id="edit-stok-awal" name="stok_awal" min="0" required>
                        <small class="text-muted">Stok akhir saat ini: <b id="current-stok-akhir"></b>. Mengubah stok awal akan menyesuaikan stok akhir.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveMaterialChanges">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- =============================================== --}}
{{-- SCRIPT JS - SUDAH DIMODIFIKASI AGAR FUNGSIONAL --}}
{{-- =============================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper function untuk menampilkan error validasi dari Laravel
    const displayValidationErrors = (errors) => {
        let errorMessages = '<ul class="list-unstyled text-start">';
        for (const key in errors) {
            errors[key].forEach(message => {
                errorMessages += `<li>${message}</li>`;
            });
        }
        errorMessages += '</ul>';
        Swal.fire({
            title: 'Gagal!',
            html: errorMessages,
            icon: 'error'
        });
    };
    
    // --- LOGIKA UNTUK EDIT MATERIAL ---
    const editModal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
    const editForm = document.getElementById('editMaterialForm');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const url = `/pusat/${id}/edit`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    editForm.querySelector('#edit-nama-material').value = data.nama_material;
                    editForm.querySelector('#edit-kode-material').value = data.kode_material;
                    editForm.querySelector('#edit-stok-awal').value = data.stok_awal;
                    document.getElementById('current-stok-akhir').textContent = data.stok_akhir + ' pcs';
                    editForm.action = `/pusat/${id}`;
                })
                .catch(error => {
                    Swal.fire('Error!', 'Tidak dapat mengambil data material.', 'error');
                });
        });
    });

    document.getElementById('saveMaterialChanges').addEventListener('click', function() {
        const formData = new FormData(editForm);
        const url = editForm.action;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) return response.json().then(data => { throw data; });
            return response.json();
        })
        .then(data => {
            if (data.success) {
                editModal.hide();
                Swal.fire('Berhasil!', data.message, 'success')
                    .then(() => location.reload());
            }
        })
        .catch(errorData => {
            if (errorData.errors) {
                displayValidationErrors(errorData.errors);
            } else {
                Swal.fire('Gagal!', errorData.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });

    // --- LOGIKA UNTUK HAPUS MATERIAL ---
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const url = `/pusat/${id}`;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data material ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(data => { throw data; });
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil Dihapus!', data.message, 'success')
                                .then(() => location.reload());
                        }
                    })
                    .catch(errorData => {
                        Swal.fire('Gagal!', errorData.message || 'Tidak dapat menghapus data.', 'error');
                    });
                }
            });
        });
    });

    // --- Sisa skrip Anda yang tidak diubah ---
    document.querySelectorAll('.kirim-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Tombol Kirim diklik untuk item ID: ' + this.dataset.id);
            // Logika untuk mengisi modal kirim bisa ditambahkan di sini
        });
    });

    const tujuanTransaksiData = ['SPBE Sukamaju', 'SPBE Makmur', 'SPBE Sentosa', 'SPBE Jaya', 'SPBE Maju Jaya'];
    const tujuanTransaksiInput = document.getElementById('tujuan-transaksi-search');
    const tujuanTransaksilist = document.getElementById('tujuan-transaksi-list');

    if(tujuanTransaksiInput) {
        // ... (sisa logika searchable input Anda tidak diubah)
    }
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