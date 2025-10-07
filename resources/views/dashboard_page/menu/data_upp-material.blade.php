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
                <div class="row mb-3 align-items-end">
                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" 
                                class="form-control" 
                                placeholder="Cari No. Surat...">
                        </div>
                    </div>
                    <div class="col-12 col-md-8 d-flex flex-wrap justify-content-md-end">
                        <div class="d-flex align-items-center me-2">
                            <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                            <input type="date" id="startDate" 
                                class="form-control form-control-sm date-input" 
                                style="max-width: 160px;">
                        </div>
                        <div class="d-flex align-items-center me-2">
                            <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                            <input type="date" id="endDate" 
                                class="form-control form-control-sm date-input" 
                                style="max-width: 160px;">
                        </div>
                        <button id="filterBtn" class="btn btn-primary btn-sm px-3" style="margin-top: 15px;">Filter</button>
                    </div>
                </div>
            </div>
            
            {{-- Tabel utama --}}
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-upp-material">
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
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

{{-- DataTables Configuration --}}
<script>
    $(document).ready(function() {
        const table = $('#table-upp-material').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('api.upp.materials') }}",
                type: "GET",
                data: function(d) {
                    d.search = $('#searchInput').val();
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'no_surat_persetujuan', 
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { 
                    data: 'no_surat_persetujuan', 
                    name: 'no_surat_persetujuan',
                    render: function(data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <p class="mb-0 text-sm font-weight-bolder text-primary">${data}</p>
                                </div>`;
                    }
                },
                { 
                    data: 'tahapan', 
                    name: 'tahapan',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary mb-0">${data || '-'}</p>`;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    orderable: false,
                    render: function(data, type, row) {
                        const statusText = data.toLowerCase() === 'done' ? 'Done' : 'Proses';
                        const statusColor = data.toLowerCase() === 'done' ? 'bg-gradient-success' : 'bg-gradient-warning';
                        return `<span class="badge ${statusColor} text-white text-xs font-weight-bold change-status-btn"
                                        data-no-surat="${row.no_surat_persetujuan}"
                                        data-status-sekarang="${data}"
                                        style="cursor: pointer;">
                                    ${statusText}
                                </span>`;
                    }
                },
                { 
                    data: 'tgl_buat', 
                    name: 'tgl_buat',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary font-weight-bold mb-0">
                                    ${moment(data, 'YYYY-MM-DD').locale('id').format('dddd, D MMMM YYYY')}
                                </p>`;
                    }
                },
                { 
                    data: 'tgl_update', 
                    name: 'tgl_update',
                    render: function(data, type, row) {
                        return `<p class="text-xs text-secondary font-weight-bold mb-0">
                                    ${moment(data, 'YYYY-MM-DD').locale('id').format('dddd, D MMMM YYYY')}
                                </p>`;
                    }
                },
                { 
                    data: 'actions', 
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<button type="button" class="btn btn-sm btn-info text-white preview-btn me-1" 
                                        data-no-surat="${row.no_surat_persetujuan}" 
                                        style="font-size: 0.75rem;" 
                                        title="Preview">
                                        <i class="fas fa-eye me-1"></i> Preview
                                    </button>
                                    <form action="/upp-material/destroy/${row.no_surat_persetujuan}" method="POST" class="d-inline delete-upp-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white" style="font-size: 0.75rem;" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>`;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            dom: 'Bfrtip',
            order: [[4, 'desc']] // Default order by creation date
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const modalContentPlaceholder = document.getElementById('modal-content-placeholder');
        const exportModal = new bootstrap.Modal(document.getElementById('exportExcelModal'));

        // Event listener untuk tombol Preview
        $('#table-upp-material').on('click', '.preview-btn', function() {
            const noSurat = $(this).data('no-surat');
            
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
        
        // Event listener untuk badge Status
        $('#table-upp-material').on('click', '.change-status-btn', function() {
            const noSurat = $(this).data('no-surat');
            const statusSekarang = $(this).data('status-sekarang');

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
                        // Refresh the DataTable
                        $('#table-upp-material').DataTable().ajax.reload();
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

        // SweetAlert konfirmasi hapus UPP
        $('#table-upp-material').on('submit', '.delete-upp-form', function (event) {
            event.preventDefault();

            Swal.fire({
                title: '⚠️ Peringatan Penting: Hapus Data UPP!',
                html: `
                    <p class="text-start">
                        Penghapusan ini akan menghapus seluruh data pengajuan UPP ini, termasuk semua riwayat transaksi dan materialnya secara permanen dari database. Tindakan ini <strong>tidak dapat dikembalikan.</strong>
                    </p>
                    <p class="text-start mb-0">
                        <strong>Apakah Anda yakin ingin melanjutkan?</strong>
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    const form = $(this);
                    const actionUrl = form.attr('action');
                    
                    fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: new URLSearchParams({
                            '_method': 'DELETE',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            // Refresh the DataTable
                            $('#table-upp-material').DataTable().ajax.reload();
                            
                            Swal.fire('Berhasil!', 'Data UPP berhasil dihapus.', 'success');
                        } else {
                            throw new Error('Terjadi kesalahan saat menghapus data.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal!', 'Gagal menghapus data UPP.', 'error');
                    });
                }
            });
        });
    });
</script>
@endpush