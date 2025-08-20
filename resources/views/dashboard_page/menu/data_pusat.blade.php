@extends('dashboard_page.main')
@section('title', 'Daftar Data P.Layang (Pusat)')
@section('content')

{{-- Welcome Section (Title for P.Layang) --}}
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
                {{-- Form untuk Filter --}}
                <form method="GET" action="{{ route('pusat.index') }}">
                    {{-- Row for Table Title and Export Button --}}
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

                    {{-- New row for search and date filters --}}
                    <div class="row mb-3 align-items-center">
                        {{-- Search Bar --}}
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari material..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>
                        {{-- Date Range Filter --}}
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
                                        {{-- Menggunakan updated_at dan format tanggal sesuai permintaan --}}
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

                {{-- Pagination --}}
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

                    {{-- Searchable input for Tujuan Transaksi --}}
                    <div class="mb-3">
                        <label for="tujuan-transaksi-search" class="form-label">Tujuan Transaksi</label>
                        <input type="text" class="form-control" id="tujuan-transaksi-search" placeholder="Cari tujuan..." required>
                        <ul id="tujuan-transaksi-list" class="list-group mt-1" style="max-height: 150px; overflow-y: auto; display: none;">
                            {{-- List items will be populated by JavaScript --}}
                        </ul>
                    </div>
                    
                    {{-- NEW: No. Surat Persetujuan --}}
                    <div class="mb-3">
                        <label for="no-surat-persetujuan" class="form-label">No. Surat Persetujuan (Opsional)</label>
                        <input type="text" class="form-control" id="no-surat-persetujuan">
                    </div>
                    
                    {{-- NEW: No. BA Serah Terima --}}
                    <div class="mb-3">
                        <label for="no-ba-serah-terima" class="form-label">No. BA Serah Terima (Opsional)</label>
                        <input type="text" class="form-control" id="no-ba-serah-terima">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penerimaan" value="penerimaan" checked>
                                <label class="form-check-label" for="jenis-penerimaan">
                                    Penerimaan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penyaluran" value="penyaluran">
                                <label class="form-check-label" for="jenis-penyaluran">
                                    Penyaluran
                                </label>
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

{{-- Modal for Editing Material Data --}}
<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMaterialModalLabel">Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMaterialForm">
                    <input type="hidden" id="edit-material-id">
                    <div class="mb-3">
                        <label for="edit-nama-material" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="edit-nama-material" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode-material" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="edit-kode-material" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-total-stok" class="form-label">Stok Akhir</label>
                        <input type="number" class="form-control" id="edit-total-stok" required>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk modal dan aksi tombol tetap ada, namun data diambil dari atribut data-*
    // Event listener untuk "Kirim" button (MODAL)
    document.querySelectorAll('.kirim-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Logika untuk mengisi data ke modal Kirim
            // Nanti diisi dengan AJAX call untuk mengambil data item terkini jika perlu
            console.log('Tombol Kirim diklik untuk item ID: ' + this.dataset.id);
        });
    });

    // Event listener untuk "Edit" button (MODAL)
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Logika untuk mengisi data ke modal Edit
            // Nanti diisi dengan AJAX call untuk mengambil data item dan mengisi form
            console.log('Tombol Edit diklik untuk item ID: ' + this.dataset.id);
        });
    });

    // Event listener untuk "Hapus" button
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Di sini nanti Anda akan menambahkan logika untuk mengirim request DELETE ke server
                    // Contoh:
                    // fetch(`/items/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                    // .then(...)

                    Swal.fire(
                        'Berhasil Dihapus!',
                        'Data material telah berhasil dihapus.',
                        'success'
                    );
                }
            });
        });
    });

    // Kode JS lain untuk fungsionalitas modal (seperti searchable input) bisa dipertahankan di sini
    // Dummy data for searchable tujuan transaksi
    const tujuanTransaksiData = ['SPBE Sukamaju', 'SPBE Makmur', 'SPBE Sentosa', 'SPBE Jaya', 'SPBE Maju Jaya'];
    const tujuanTransaksiInput = document.getElementById('tujuan-transaksi-search');
    const tujuanTransaksilist = document.getElementById('tujuan-transaksi-list');

    if(tujuanTransaksiInput) {
        tujuanTransaksiInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tujuanTransaksilist.innerHTML = '';
            tujuanTransaksilist.style.display = 'block';

            if (query.length > 0) {
                const filteredTujuan = tujuanTransaksiData.filter(tujuan =>
                    tujuan.toLowerCase().includes(query)
                );

                if (filteredTujuan.length > 0) {
                    filteredTujuan.forEach(tujuan => {
                        const li = document.createElement('li');
                        li.classList.add('list-group-item', 'list-group-item-action');
                        li.textContent = tujuan;
                        li.addEventListener('click', () => {
                            tujuanTransaksiInput.value = tujuan;
                            tujuanTransaksilist.style.display = 'none';
                        });
                        tujuanTransaksilist.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item');
                    li.textContent = 'Tidak ada hasil.';
                    tujuanTransaksilist.appendChild(li);
                }
            } else {
                tujuanTransaksilist.style.display = 'none';
            }
        });

        // Hide list when clicking outside
        document.addEventListener('click', function(e) {
            if (!tujuanTransaksiInput.contains(e.target) && !tujuanTransaksilist.contains(e.target)) {
                tujuanTransaksilist.style.display = 'none';
            }
        });
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