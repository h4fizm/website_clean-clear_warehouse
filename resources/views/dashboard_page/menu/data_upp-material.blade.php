@extends('dashboard_page.main')
@section('title', 'UPP Material')

@section('content')

{{-- Tabel Data UPP Material --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="me-md-auto mb-2 mb-md-0">
                    <h4 class="mb-0">Tabel Data UPP Material</h4>
                    <p class="mt-3 text-xs font-italic text-secondary">
                        Klik badge status pada kolom "Status" untuk mengubah status.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('upp-material.create') }}" class="px-3 py-2 bg-primary text-white rounded d-flex align-items-center justify-content-center" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;">
                        <i class="fas fa-plus me-2"></i>Tambah UPP
                    </a>
                    <button type="button" id="openExportModalBtn" class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center" style="cursor: pointer; font-size: 0.875rem; font-weight: bold; border: none; outline: none;">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </button>
                </div>
            </div>
            
            {{-- Form Pencarian dan Filter --}}
            <div class="px-4 py-2">
                <form method="GET" action="{{ route('upp-material.index') }}">
                    <div class="row mb-3 align-items-end">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="searchInput" 
                                    class="form-control" 
                                    placeholder="Cari No. Surat..." 
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-8 d-flex flex-wrap justify-content-md-end">
                            <div class="d-flex align-items-center me-2">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" name="start_date" id="startDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="d-flex align-items-center me-2">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" name="end_date" id="endDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ request('end_date') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-3" style="margin-top: 15px;">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Tabel utama --}}
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Surat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahapan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Buat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Update Terakhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upps as $upp)
                            <tr>
                                <td class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($upps->currentPage() - 1) * $upps->perPage() }}</p>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <p class="mb-0 text-sm font-weight-bolder text-primary">{{ $upp->no_surat_persetujuan }}</p>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary mb-0">{{ $upp->tahapan }}</p>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusText = strtolower($upp->status) === 'done' ? 'Done' : 'Proses';
                                        $statusColor = strtolower($upp->status) === 'done' ? 'bg-gradient-success' : 'bg-gradient-warning';
                                    @endphp
                                    <span class="badge {{ $statusColor }} text-white text-xs font-weight-bold change-status-btn"
                                        data-no-surat="{{ $upp->no_surat_persetujuan }}"
                                        data-status-sekarang="{{ $upp->status }}"
                                        style="cursor: pointer;">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <p class="text-xs text-secondary font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($upp->tgl_buat)->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <p class="text-xs text-secondary font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($upp->tgl_update)->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    {{-- HANYA TAMPILKAN TOMBOL PREVIEW --}}
                                    <button type="button" class="btn btn-sm btn-info text-white preview-btn" data-no-surat="{{ $upp->no_surat_persetujuan }}" style="font-size: 0.75rem;">
                                        <i class="fas fa-eye me-1"></i> Preview
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Data Kosong</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION YANG DIPERBAIKI --}}
                @if ($upps->hasPages())
                <div class="mt-4 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $upps->lastPage();
                                $current = $upps->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $upps->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url(1) }}">&laquo;</a>
                            </li>
                            <li class="page-item {{ $upps->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->previousPageUrl() }}">&lsaquo;</a>
                            </li>
                            @php $wasGap = false; @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    @if (!$wasGap)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor
                            <li class="page-item {{ $upps->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $upps->nextPageUrl() }}">&rsaquo;</a>
                            </li>
                            <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $upps->appends(request()->except('page'))->url($total) }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Pop-up untuk Preview --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Detail Pengajuan UPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-content-placeholder">
                    {{-- Konten akan dimuat di sini oleh AJAX --}}
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export Excel BARU --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Data UPP ke Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-sm text-secondary">Pilih rentang tanggal **pengajuan** untuk data UPP yang ingin Anda export.</p>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const modalContentPlaceholder = document.getElementById('modal-content-placeholder');
        const exportModal = new bootstrap.Modal(document.getElementById('exportExcelModal'));

        // Event listener untuk tombol Preview (tetap sama)
        document.querySelectorAll('.preview-btn').forEach(button => {
            button.addEventListener('click', function() {
                const noSurat = this.getAttribute('data-no-surat');
                
                modalContentPlaceholder.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                `;
                
                previewModal.show();

                fetch(`/upp-material/preview/${noSurat}`)
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        }
                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.includes("application/json")) {
                            return response.json().then(data => {
                                throw new Error(data.error || 'Terjadi kesalahan tidak terduga.');
                            });
                        } else {
                            throw new Error('Data tidak ditemukan.');
                        }
                    })
                    .then(htmlContent => {
                        modalContentPlaceholder.innerHTML = htmlContent;
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        modalContentPlaceholder.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                Gagal memuat data: ${error.message}
                            </div>
                        `;
                    });
            });
        });
        
        // Event listener BARU untuk badge Status
        document.querySelectorAll('.change-status-btn').forEach(badge => {
            badge.addEventListener('click', function() {
                const noSurat = this.getAttribute('data-no-surat');
                const statusSekarang = this.getAttribute('data-status-sekarang');
                const nextStatus = statusSekarang.toLowerCase() === 'proses' ? 'done' : 'proses';
                const nextStatusText = nextStatus.toUpperCase();

                Swal.fire({
                    title: `Ubah Status`,
                    text: `Pilih status baru untuk pengajuan ${noSurat}.`,
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Ubah ke Done',
                    denyButtonText: 'Ubah ke Proses',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#28a745',
                    denyButtonColor: '#ffc107',
                    showLoaderOnConfirm: true,
                    preConfirm: (result) => {
                        // Tidak ada preConfirm di sini, karena tombol akan langsung mengirim
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    let newStatus = '';
                    if (result.isConfirmed) {
                        newStatus = 'done';
                    } else if (result.isDenied) {
                        newStatus = 'proses';
                    } else {
                        return; // Jika pengguna membatalkan
                    }

                    // Kirim permintaan perubahan status ke server
                    fetch(`/upp-material/change-status/${noSurat}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload(); // Reload halaman untuk melihat perubahan
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: `Gagal: ${error.message}`,
                            icon: 'error'
                        });
                    });
                });
            });
        });
        
        // ==========================================
        // SCRIPT BARU UNTUK EKSPOR EXCEL
        // ==========================================
        const openExportModalBtn = document.getElementById('openExportModalBtn');
        const confirmExportBtn = document.getElementById('confirmExportBtn');

        if (openExportModalBtn) {
            openExportModalBtn.addEventListener('click', function() {
                exportModal.show();
            });
        }
        
        if (confirmExportBtn) {
            confirmExportBtn.addEventListener('click', function() {
                const startDate = document.getElementById('exportStartDate').value;
                const endDate = document.getElementById('exportEndDate').value;

                if (!startDate || !endDate) {
                    Swal.fire('Peringatan', 'Silakan pilih rentang tanggal pengajuan terlebih dahulu.', 'warning');
                    return;
                }

                const baseUrl = "{{ route('upp-material.export') }}";
                const params = new URLSearchParams();
                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);

                const exportUrl = `${baseUrl}?${params.toString()}`;

                exportModal.hide();
                window.location.href = exportUrl;
            });
        }
    });
</script>
@endpush