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
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                        <h4 class="mb-0" id="table-branch-name">Tabel Data Stok Material - P.Layang (Pusat)</h4>
                    </div>
                    <div class="col-12 col-md-auto">
                        <span id="openExportModalBtn" class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </span>
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
                
                <div class="mb-3">
                    <p class="text-sm text-secondary">Gunakan fitur pencarian dan filter bawaan tabel di bawah ini untuk mencari data atau mengatur urutan.</p>
                </div>

            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table id="pusat-materials-table" class="table align-items-center mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Sales</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Pemusnahan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Akhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tgl. Transaksi Terakhir</th>
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

{{-- Modal for Send Material Data --}}
<div class="modal fade" id="kirimMaterialModal" tabindex="-1" aria-labelledby="kirimMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kirimMaterialModalLabel">Proses Transaksi Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Info Material --}}
                <div class="card p-3 mb-3 bg-light border">
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">NAMA MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-nama-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">KODE MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-kode-material-display"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">STOK SAAT INI DI P.LAYANG</p>
                    <p class="mb-0 text-sm font-weight-bold" id="modal-stok-akhir-display"></p>
                </div>

                {{-- Form Transaksi --}}
                <form id="kirimMaterialForm" onsubmit="return false;">
                    @csrf
                    <input type="hidden" id="item-id-pusat" name="item_id_pusat">
                    <input type="hidden" id="kode-material-selected" name="kode_material">

                    {{-- Pilihan Jenis Transaksi --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="jenis-penyaluran" value="penyaluran" checked>
                                <label class="form-check-label" for="jenis-penyaluran">Produk Transfer</label>
                            </div>
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="jenis-penerimaan" value="penerimaan">
                                <label class="form-check-label" for="jenis-penerimaan">Penerimaan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="jenis-sales" value="sales">
                                <label class="form-check-label" for="jenis-sales">Sales</label>
                            </div>
                        </div>
                    </div>

                    {{-- Form Dinamis --}}
                    <div class="mb-3">
                        <label id="asal-label" class="form-label">Asal Transaksi</label>
                        <div id="asal-container">
                            <input type="text" class="form-control" value="P.Layang (Pusat)" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label id="tujuan-label" class="form-label">Tujuan Transaksi</label>
                        <div id="tujuan-container">
                            <div class="position-relative w-100">
                                <input type="text" class="form-control" id="facility-search" placeholder="Cari SPBE/BPT...">
                                <input type="hidden" id="facility-id-hidden" name="facility_id_selected">
                                <div id="facility-suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal-transaksi" class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="tanggal-transaksi" name="tanggal_transaksi" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no-surat-persetujuan" class="form-label">No. Surat Persetujuan</label>
                            <input type="text" class="form-control" id="no-surat-persetujuan" name="no_surat_persetujuan" placeholder="(Opsional)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no-ba-serah-terima" class="form-label">No. BA Serah Terima</label>
                            <input type="text" class="form-control" id="no-ba-serah-terima" name="no_ba_serah_terima" placeholder="(Opsional)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah-stok" class="form-label">Jumlah (pcs)</label>
                        <input type="number" class="form-control" id="jumlah-stok" name="jumlah" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="submitKirim">
                    <i class="fas fa-check me-2"></i> Konfirmasi Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMaterialModalLabel">Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMaterialForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" 
                                id="edit-nama_material" name="nama_material" placeholder=" " 
                                value="">
                        <label for="edit-nama_material">Nama Material</label>
                        <div class="invalid-feedback" id="edit-nama_material-error"></div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" 
                                id="edit-kode_material" name="kode_material" placeholder=" " 
                                value="">
                        <label for="edit-kode_material">Kode Material</label>
                        <div class="invalid-feedback" id="edit-kode_material-error"></div>
                    </div>
                    
                    {{-- Field Kategori Material --}}
                    <div class="form-floating mb-3">
                        <select class="form-select"
                                id="edit-kategori_material" name="kategori_material" required>
                            <option value="Baru">Baru</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Afkir">Afkir</option>
                        </select>
                        <label for="edit-kategori_material">Kategori Material</label>
                        <div class="invalid-feedback" id="edit-kategori_material-error"></div>
                    </div>
                    
                    {{-- Field Stok Awal --}}
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" 
                                id="edit-stok_awal" name="stok_awal" placeholder=" " 
                                value="" min="0">
                        <label for="edit-stok_awal">Stok Awal</label>
                        <div class="invalid-feedback" id="edit-stok_awal-error"></div>
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

{{-- Modal untuk Export Excel --}}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

{{-- DataTables Configuration --}}
<script>
    $(document).ready(function() {
        const table = $('#pusat-materials-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('api.pusat.materials') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                error: function(xhr, error, code) {
                    console.log('DataTable Error - Status:', xhr.status);
                    console.log('DataTable Error - Response:', xhr.responseText);
                    console.log('Error details:', error, code);

                    if (xhr.status === 401 || xhr.status === 403) {
                        console.log('Authentication/Authorization error. Trying debug endpoint...');

                        // Try debug endpoint without authentication
                        $.ajax({
                            url: "/api/pusat-materials-debug",
                            type: "GET",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            data: {
                                draw: 1,
                                start: 0,
                                length: 10,
                                search: { value: '' }
                            },
                            success: function(debugData) {
                                console.log('Debug endpoint successful:', debugData);
                                alert('Debug API works! The issue is authentication/permissions. Check console for details.');
                            },
                            error: function(debugXhr, debugError) {
                                console.log('Debug endpoint also failed:', debugXhr.responseText);
                                alert('Both APIs failed. Check console for error details.');
                            }
                        });

                        $('#pusat-materials-table').hide();
                        $('#pusat-materials-table').after('<div class="alert alert-warning">Authentication required. Please refresh the page and log in again.</div>');
                    } else {
                        $('#pusat-materials-table').after('<div class="alert alert-danger">Error loading data: ' + xhr.status + ' ' + error + '</div>');
                    }
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'nama_material', name: 'nama_material' },
                { data: 'kode_material', name: 'kode_material' },
                {
                    data: 'stok_awal',
                    name: 'stok_awal',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-secondary text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'penerimaan_total',
                    name: 'penerimaan_total',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-primary text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'penyaluran_total',
                    name: 'penyaluran_total',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-info text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'sales_total',
                    name: 'sales_total',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-warning text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'pemusnahan_total',
                    name: 'pemusnahan_total',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-danger text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'stok_akhir',
                    name: 'stok_akhir',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-success text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        if (type === 'display' && data !== '-') {
                            return moment(data, 'DD-MM-YYYY HH:mm:ss').locale('id').format('DD MMMM YYYY, HH:mm');
                        }
                        return data;
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return data || '';
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json',
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data tersedia",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            order: [[9, 'desc']], // Default order by last activity date
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        
        // Edit functionality
        $('#pusat-materials-table').on('click', '.edit-btn', function() {
            const row = $(this).closest('tr');
            const rowData = table.row(row).data();
            
            // Fill the form with current data
            $('#edit-nama_material').val(rowData.nama_material);
            $('#edit-kode_material').val(rowData.kode_material);
            $('#edit-kategori_material').val(rowData.kategori_material);
            $('#edit-stok_awal').val(rowData.stok_awal);
            
            // Set form action to update URL
            $('#editMaterialForm').attr('action', '/pusat/' + rowData.id);
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
            editModal.show();
        });

        // Delete functionality
        $('#pusat-materials-table').on('click', '.btn-danger', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            // SweetAlert for confirmation
            Swal.fire({
                title: '⚠️ Peringatan Penting: Hapus Data Permanen!',
                html: `
                    <p class="text-start">
                        Penghapusan ini akan menghapus seluruh data material ini dari gudang pusat, termasuk semua riwayat transaksi dan stoknya secara permanen dari database. Tindakan ini <strong>tidak dapat dikembalikan.</strong>
                    </p>
                    <p class="text-start mb-0">
                        <strong>Apakah Anda sudah mengekspor atau mencadangkan data ini?</strong>
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Saya Sudah Backup!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Konfirmasi Terakhir',
                        text: "Apakah Anda benar-benar yakin ingin melanjutkan? Data ini akan dihapus secara permanen dari database.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus Sekarang!',
                        cancelButtonText: 'Kembali'
                    }).then((secondResult) => {
                        if (secondResult.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });

        // Process transaction functionality
        $('#pusat-materials-table').on('click', '.kirim-btn', function() {
            try {
                const rowData = table.row($(this).closest('tr')).data();
                console.log('Row data:', rowData); // Debug log

                // Fill modal with item info
                document.getElementById('modal-nama-material-display').textContent = rowData.nama_material;
                document.getElementById('modal-kode-material-display').textContent = rowData.kode_material;
                document.getElementById('modal-stok-akhir-display').textContent = rowData.stok_akhir + ' pcs';

                // Set form data
                document.getElementById('item-id-pusat').value = rowData.id;
                document.getElementById('kode-material-selected').value = rowData.kode_material;

                // Set default date
                const today = new Date();
                document.getElementById('tanggal-transaksi').value = today.toISOString().slice(0, 10);

                // Set default transaction type to "penyaluran" and initialize form
                document.getElementById('jenis-penyaluran').checked = true;

                // Check if updateFormUI function exists before calling
                if (typeof window.updateFormUI === 'function') {
                    window.updateFormUI('penyaluran');
                } else {
                    console.error('updateFormUI function not found');
                }

                // Show modal
                const kirimModal = new bootstrap.Modal(document.getElementById('kirimMaterialModal'));
                kirimModal.show();
            } catch (error) {
                console.error('Error in kirim button handler:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membuka modal transaksi'
                });
            }
        });
    });
</script>

{{-- Script for edit form submission --}}
<script>
    document.getElementById('editMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const actionUrl = this.getAttribute('action');
        
        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the DataTable
                $('#pusat-materials-table').DataTable().ajax.reload();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data material berhasil diperbarui!'
                });
                
                // Close modal
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editMaterialModal'));
                editModal.hide();
            } else if (data.errors) {
                // Show validation errors
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit-${field}-error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.parentElement.classList.add('is-invalid');
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan. Silakan coba lagi.'
            });
        });

        // Clear validation errors on input
        document.querySelectorAll('#editMaterialForm input, #editMaterialForm select').forEach(field => {
            field.addEventListener('input', function() {
                this.parentElement.classList.remove('is-invalid');
                const errorElement = document.getElementById(`${this.id}-error`);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            });
        });
    });
</script>

{{-- script transaksi --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const facilities = @json($facilities);
        const kirimModal = document.getElementById('kirimMaterialModal');
        const form = document.getElementById('kirimMaterialForm');

        const readonlyInputHTML = (locName) => `<input type="text" class="form-control" value="${locName}" readonly>`;
        const hiddenInputHTML = (nameAttr, value) => `<input type="hidden" name="${nameAttr}" value="${value}">`;

        function createFacilitySearchInputHTML() {
            return `
                <div class="position-relative w-100">
                    <input type="text" class="form-control" id="facility-search" placeholder="Cari SPBE/BPT..." autocomplete="off">
                    <input type="hidden" id="facility-id-hidden" name="facility_id_selected">
                    <div id="facility-suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></div>
                </div>
            `;
        }
        
        function createSalesDropdownHTML() {
            return `
                <select class="form-select" id="tujuan-sales-select" name="tujuan_sales">
                    <option value="" selected disabled>-- Pilih Tujuan Sales --</option>
                    <option value="Vendor UPP">Vendor UPP</option>
                    <option value="Sales Agen">Sales Agen</option>
                    <option value="Sales BPT">Sales BPT</option>
                    <option value="Sales SPBE">Sales SPBE</option>
                </select>
            `;
        }

        // Make function globally accessible
        window.updateFormUI = function(type) {
            const asalContainer = document.getElementById('asal-container');
            const tujuanContainer = document.getElementById('tujuan-container');
            const asalLabel = document.getElementById('asal-label');
            const tujuanLabel = document.getElementById('tujuan-label');

            asalLabel.textContent = "Asal Transaksi";
            tujuanLabel.textContent = "Tujuan Transaksi";

            if (type === 'penyaluran') {
                asalContainer.innerHTML = readonlyInputHTML('P.Layang (Pusat)');
                tujuanContainer.innerHTML = createFacilitySearchInputHTML();
                initFacilitySearchbar();

            } else if (type === 'penerimaan') {
                asalContainer.innerHTML = createFacilitySearchInputHTML();
                tujuanContainer.innerHTML = readonlyInputHTML('P.Layang (Pusat)');
                initFacilitySearchbar();

            } else if (type === 'sales') {
                asalContainer.innerHTML = readonlyInputHTML('P.Layang (Pusat)');
                tujuanContainer.innerHTML = createSalesDropdownHTML();
            }
        }

        function initFacilitySearchbar() {
            const searchInput = document.getElementById("facility-search");
            const hiddenInput = document.getElementById("facility-id-hidden");
            const suggestionsBox = document.getElementById("facility-suggestions");

            if (!searchInput) return;

            searchInput.addEventListener("input", function() {
                const query = this.value.toLowerCase();
                suggestionsBox.innerHTML = "";
                hiddenInput.value = ""; 

                if (!query) {
                    suggestionsBox.style.display = "none";
                    return;
                }

                const results = facilities.filter(facility => facility.name.toLowerCase().includes(query));

                if (results.length > 0) {
                    results.forEach(facility => {
                        const item = document.createElement("button");
                        item.type = "button";
                        item.className = "list-group-item list-group-item-action";
                        item.textContent = facility.name;
                        item.dataset.id = facility.id;

                        item.addEventListener("click", function() {
                            searchInput.value = facility.name;
                            hiddenInput.value = facility.id;
                            suggestionsBox.style.display = "none";
                        });

                        suggestionsBox.appendChild(item);
                    });
                    suggestionsBox.style.display = "block";
                } else {
                    suggestionsBox.style.display = "none";
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });
        }
        
        document.querySelectorAll('input[name="jenis_transaksi"]').forEach(radio => {
            radio.addEventListener('change', (event) => {
                updateFormUI(event.target.value);
            });
        });

        document.getElementById('submitKirim').addEventListener('click', function() {
            const jenisTransaksi = document.querySelector('input[name="jenis_transaksi"]:checked').value;
            let isValid = true;
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Validasi khusus untuk setiap jenis transaksi
            if (jenisTransaksi === 'sales') {
                if (!data.tujuan_sales) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih tujuan sales!' });
                    isValid = false;
                }
            } else if (jenisTransaksi === 'penyaluran' || jenisTransaksi === 'penerimaan') {
                if (!data.facility_id_selected) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih satu SPBE/BPT!' });
                    isValid = false;
                }
            }

            if (!isValid) return;

            fetch('{{ route('pusat.transfer') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan pada server. Mohon coba lagi.',
                        });
                        throw new Error('Server returned HTML instead of JSON');
                    });
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Close the modal first
                    const kirimModal = bootstrap.Modal.getInstance(document.getElementById('kirimMaterialModal'));
                    if (kirimModal) {
                        kirimModal.hide();
                    }

                    // Reset form
                    document.getElementById('kirimMaterialForm').reset();

                    // Refresh the DataTable immediately
                    console.log('Refreshing table after transaction...');
                    $('#pusat-materials-table').DataTable().ajax.reload(null, false); // false to keep current page

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message
                    });
                } else if (result.errors) {
                    let errorMessages = Object.values(result.errors).map(error => `<li>${error[0]}</li>`).join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Validasi',
                        html: `<ul class="text-start">${errorMessages}</ul>`
                    });
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                if (error.message !== 'Server returned HTML instead of JSON') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message
                    });
                }
            });
        });
    });
</script>

{{-- Script untuk Export Excel --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const exportModal = new bootstrap.Modal(document.getElementById('exportExcelModal'));
        const openExportBtn = document.getElementById('openExportModalBtn');
        const confirmExportBtn = document.getElementById('confirmExportBtn');

        openExportBtn.addEventListener('click', function () {
            exportModal.show();
        });

        confirmExportBtn.addEventListener('click', function () {
            const startDate = document.getElementById('exportStartDate').value;
            const endDate = document.getElementById('exportEndDate').value;

            if (!startDate || !endDate) {
                Swal.fire('Peringatan!', 'Silakan pilih rentang tanggal terlebih dahulu.', 'warning');
                return;
            }

            const url = '{{ route('pusat.export') }}' + `?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
            exportModal.hide();
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
        background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23000000" fill-opacity=".03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        background-size: 60px 60px;
        opacity: 0.2;
        pointer-events: none;
    }
      
    /* DataTables specific styles */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
    }
    
    .dataTables_wrapper .dataTables_length select {
        margin-right: 0.5em;
    }
</style>
@endsection