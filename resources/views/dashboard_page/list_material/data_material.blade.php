@extends('dashboard_page.main')
@section('title', 'Data Material - ' . $facility->name)

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Daftar Stok Material - {{ $facility->name }}</h3>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-secondary">Gunakan fitur pencarian dan filter bawaan tabel di bawah ini untuk mencari data atau mengatur urutan.</p>
                </div>
            </div>

            <div class="card-body px-4 pt-4 pb-5">
                {{-- Notifikasi --}}
                <div class="mb-4">
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

                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="table-material">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Awal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penerimaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Penyaluran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Sales</th>
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
        @method('PATCH')
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="edit-nama_material" name="nama_material" value="" placeholder=" " required>
            <label for="edit-nama_material">Nama Material</label>
            <div class="invalid-feedback" id="edit-nama_material-error"></div>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="edit-kode_material" name="kode_material" value="" placeholder=" " required>
            <label for="edit-kode_material">Kode Material</label>
            <div class="invalid-feedback" id="edit-kode_material-error"></div>
          </div>
          <div class="form-floating mb-3">
            <input type="number" class="form-control" id="edit-stok_awal" name="stok_awal" value="" placeholder=" " min="0" required>
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

{{-- Modal Transaksi --}}
<div class="modal fade" id="transaksiMaterialModal" tabindex="-1" aria-labelledby="transaksiMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transaksiMaterialModalLabel">Proses Transaksi Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card p-3 mb-3 bg-light border">
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">NAMA MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-nama-material"></p>
                    {{-- Tambahkan baris untuk Kode Material --}}
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">KODE MATERIAL</p>
                    <p class="mb-2 text-sm font-weight-bold" id="modal-kode-material"></p>
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">STOK SAAT INI DI LOKASI INI</p>
                    <p class="mb-0 text-sm font-weight-bold" id="modal-stok-akhir"></p>
                </div>

                <form id="transaksiMaterialForm" onsubmit="return false;">
                    @csrf
                    <input type="hidden" id="modal-item-id" name="item_id">
                    <input type="hidden" id="modal-kode-material-hidden" name="kode_material">

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

                    <div class="mb-3">
                        <label id="asal-label" class="form-label">Asal Transaksi</label>
                        <div id="asal-container"></div>
                    </div>

                    <div class="mb-3">
                        <label id="tujuan-label" class="form-label">Tujuan Transaksi</label>
                        <div id="tujuan-container"></div>
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
                <button type="button" class="btn btn-success" id="submitTransaksi">
                    <i class="fas fa-check me-2"></i> Konfirmasi Transaksi
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

{{-- DataTables Configuration --}}
<script>
    $(document).ready(function() {
        const table = $('#table-material').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('api.facility.materials', $facility->id) }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr, error, code) {
                    console.log('DataTable Error:', xhr.responseText);
                    console.log('Error details:', error, code);
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'id', 
                    searchable: false,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { 
                    data: 'nama_material', 
                    name: 'nama_material',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-info text-white text-xs">' + data + '</span>';
                    }
                },
                { 
                    data: 'kode_material', 
                    name: 'kode_material',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-secondary text-white text-xs">' + data + '</span>';
                    }
                },
                { 
                    data: 'stok_awal', 
                    name: 'stok_awal',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-secondary text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                { 
                    data: 'penerimaan_total', 
                    name: 'penerimaan_total',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-primary text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                { 
                    data: 'penyaluran_total', 
                    name: 'penyaluran_total',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-info text-white text-xs">' + (data || 0).toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                { 
                    data: 'sales_total', 
                    name: 'sales_total',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-warning text-white text-xs">' + data.toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                { 
                    data: 'stok_akhir', 
                    name: 'stok_akhir',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-success text-white text-xs">' + data.toLocaleString('id-ID') + ' pcs</span>';
                    }
                },
                { 
                    data: 'created_at', 
                    name: 'updated_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data ? '<span class="badge bg-secondary text-white text-xs">' + data + '</span>' : '-';
                    }
                },
                { 
                    data: 'actions', 
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
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
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 d-flex justify-content-center"p><"col-sm-12"i>>',
            order: [[8, 'desc']], // Default order by last activity date
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Edit functionality
        $('#table-material').on('click', '.edit-btn, .edit-icon', function() {
            const row = $(this).closest('tr');
            const rowData = table.row(row).data();

            // Fill the form with current data
            $('#edit-nama_material').val(rowData.nama_material);
            $('#edit-kode_material').val(rowData.kode_material);
            $('#edit-stok_awal').val(rowData.stok_awal);

            // Set form action to update URL
            $('#editMaterialForm').attr('action', '/materials/' + rowData.id);

            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
            editModal.show();
        });

        // Delete functionality
        $('#table-material').on('click', '.delete-btn, .btn-danger', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            // SweetAlert for confirmation
            Swal.fire({
                title: '⚠️ Peringatan Penting: Hapus Data Permanen!',
                html: `
                    <p class="text-start">
                        Penghapusan ini akan menghapus seluruh data material ini dari fasilitas {{ $facility->name }}, termasuk semua riwayat transaksi dan stoknya secara permanen dari database. Tindakan ini <strong>tidak dapat dikembalikan.</strong>
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
        $('#table-material').on('click', '.transaksi-btn, .transaksi-icon', function() {
            try {
                const rowData = table.row($(this).closest('tr')).data();
                console.log('Transaction row data:', rowData); // Debug log

                // Fill modal with item info
                document.getElementById('modal-nama-material').textContent = rowData.nama_material;
                document.getElementById('modal-kode-material').textContent = rowData.kode_material;
                document.getElementById('modal-stok-akhir').textContent = rowData.stok_akhir.toLocaleString('id-ID') + ' pcs';

                // Set form data
                document.getElementById('modal-item-id').value = rowData.id;
                document.getElementById('modal-kode-material-hidden').value = rowData.kode_material;

                // Set default date
                const today = new Date();
                document.getElementById('tanggal-transaksi').value = today.toISOString().slice(0, 10);

                // Reset form dan set default transaction type
                document.getElementById('transaksiMaterialForm').reset();
                document.getElementById('modal-item-id').value = rowData.id;
                document.getElementById('modal-kode-material-hidden').value = rowData.kode_material;
                document.getElementById('tanggal-transaksi').value = today.toISOString().slice(0, 10);
                document.getElementById('jenis-penyaluran').checked = true;

                // Initialize form UI - ensure global variables are available
                if (typeof locations !== 'undefined' && typeof currentFacility !== 'undefined') {
                    window.updateFormUI('penyaluran');
                } else {
                    console.error('Global variables not initialized yet');
                    // Wait for DOM to be ready and then initialize
                    setTimeout(() => {
                        if (typeof window.updateFormUI === 'function') {
                            window.updateFormUI('penyaluran');
                        }
                    }, 100);
                }

                // Show modal and ensure form is properly initialized
                const transaksiModal = new bootstrap.Modal(document.getElementById('transaksiMaterialModal'));
                transaksiModal.show();

                // Ensure form is initialized after modal is shown
                setTimeout(() => {
                    window.updateFormUI('penyaluran');
                }, 200);
            } catch (error) {
                console.error('Error in transaksi button handler:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat membuka modal transaksi'
                });
            }
        });
        
        // Convert text buttons to icons
        table.on('draw', function() {
            // Convert edit buttons to pencil icon with yellow color
            $('.edit-btn, .btn-primary').each(function() {
                if ($(this).text().trim() === 'Edit' || $(this).text().trim() === 'edit' || $(this).text().trim() === 'Ubah') {
                    $(this).html('<i class="fas fa-pencil-alt"></i>');
                    $(this).addClass('edit-icon');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-warning'); // Yellow color
                    $(this).attr('title', 'Edit');
                }
            });
            
            // Convert transaction buttons to exchange icon with green color
            $('.transaksi-btn, .btn-success').each(function() {
                if ($(this).text().trim() === 'Transaksi' || $(this).text().trim() === 'transaksi' || $(this).text().trim() === 'Proses') {
                    $(this).html('<i class="fas fa-exchange-alt"></i>');
                    $(this).addClass('transaksi-icon');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-success'); // Green color
                    $(this).attr('title', 'Proses Transaksi');
                }
            });
            
            // Convert delete buttons to trash icon with red color
            $('.btn-danger').not('.delete-btn').each(function() {
                if ($(this).text().trim() === 'Hapus' || $(this).text().trim() === 'hapus' || $(this).text().trim() === 'Delete') {
                    $(this).html('<i class="fas fa-trash"></i>');
                    $(this).addClass('delete-btn');
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-danger'); // Red color
                    $(this).attr('title', 'Hapus');
                }
            });
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
                'X-HTTP-Method-Override': 'PATCH',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the DataTable
                $('#table-material').DataTable().ajax.reload();
                
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

{{-- Global Variables and Functions for Transaction Modal --}}
<script>
    // Global variables
    let locations, currentFacility, transaksiModal, form;

    // Fungsi untuk membuat input teks non-editable (readonly)
    const readonlyInputHTML = (loc, nameAttr) => `
        <input type="text" class="form-control" value="${loc.name}" readonly>
        <input type="hidden" name="${nameAttr}" value="${loc.id}">
    `;

    // Fungsi untuk membuat input teks dengan fitur pencarian (autocomplete)
    function createSearchInputHTML(nameAttr) {
        return `
            <div class="position-relative w-100">
                <input type="text" class="form-control facility-search" placeholder="Cari Lokasi..." autocomplete="off">
                <input type="hidden" name="${nameAttr}">
                <div class="list-group position-absolute w-100 shadow-sm facility-suggestions"
                        style="z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></div>
            </div>
        `;
    }

    // Fungsi untuk membuat dropdown tujuan sales
    function createSalesDropdownHTML(nameAttr) {
        return `
            <select class="form-select" name="${nameAttr}">
                <option value="" selected disabled>-- Pilih Tujuan Sales --</option>
                <option value="Vendor UPP">Vendor UPP</option>
                <option value="Sales Agen">Sales Agen</option>
                <option value="Sales BPT">Sales BPT</option>
                <option value="Sales SPBE">Sales SPBE</option>
            </select>
        `;
    }

    // Fungsi untuk menginisialisasi fitur pencarian
    function initSearchbar(container, availableLocations, nameAttr) {
        const searchInput = container.querySelector(".facility-search");
        const hiddenInput = container.querySelector(`input[name="${nameAttr}"]`);
        const suggestionsBox = container.querySelector(".facility-suggestions");

        if (!searchInput) return;

        searchInput.addEventListener("input", function() {
            const query = this.value.toLowerCase();
            suggestionsBox.innerHTML = "";
            hiddenInput.value = ""; // Reset hidden input saat mulai mengetik

            if (!query) {
                suggestionsBox.style.display = "none";
                return;
            }
            const results = availableLocations.filter(loc => loc.name.toLowerCase().includes(query));
            if (results.length > 0) {
                results.forEach(loc => {
                    const item = document.createElement("button");
                    item.type = "button";
                    item.className = "list-group-item list-group-item-action";
                    item.textContent = loc.name;
                    item.dataset.id = loc.id;
                    item.addEventListener("click", function() {
                        searchInput.value = loc.name;
                        hiddenInput.value = loc.id;
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
            if (!container.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    }

    // Make function globally accessible
    window.updateFormUI = function(type) {
        // Check if required elements and variables exist
        if (!locations || !currentFacility) {
            console.error('Required global variables not initialized');
            return;
        }

        const asalContainer = document.getElementById('asal-container');
        const tujuanContainer = document.getElementById('tujuan-container');
        const asalLabel = document.getElementById('asal-label');
        const tujuanLabel = document.getElementById('tujuan-label');

        if (!asalContainer || !tujuanContainer || !asalLabel || !tujuanLabel) {
            console.error('Required DOM elements not found');
            return;
        }

        const selectedType = type || document.querySelector('input[name="jenis_transaksi"]:checked')?.value;

        if (!selectedType) {
            console.error('No transaction type selected');
            return;
        }

        // Filter lokasi lain yang tidak sama dengan lokasi saat ini
        const otherLocations = locations.filter(loc => loc.id != currentFacility.id);
        console.log('Available locations for selection:', otherLocations); // Debug log
        console.log('Current facility:', currentFacility); // Debug log

        // Hapus input yang ada di dalam container
        asalContainer.innerHTML = '';
        tujuanContainer.innerHTML = '';

        // Tentukan form yang akan dimuat berdasarkan jenis transaksi
        if (selectedType === 'penyaluran') {
            asalLabel.textContent = "Asal Penyaluran";
            tujuanLabel.textContent = "Tujuan Penyaluran";
            asalContainer.innerHTML = readonlyInputHTML(currentFacility, "asal_id");
            tujuanContainer.innerHTML = createSearchInputHTML("tujuan_id");
            initSearchbar(tujuanContainer, otherLocations, "tujuan_id");
        } else if (selectedType === 'penerimaan') {
            asalLabel.textContent = "Asal Penerimaan";
            tujuanLabel.textContent = "Tujuan Penerimaan";
            asalContainer.innerHTML = createSearchInputHTML("asal_id");
            tujuanContainer.innerHTML = readonlyInputHTML(currentFacility, "tujuan_id");
            initSearchbar(asalContainer, otherLocations, "asal_id");
        } else if (selectedType === 'sales') {
            asalLabel.textContent = "Asal Transaksi";
            tujuanLabel.textContent = "Tujuan Sales";
            asalContainer.innerHTML = readonlyInputHTML(currentFacility, "asal_id");
            tujuanContainer.innerHTML = createSalesDropdownHTML("tujuan_sales");
        }

        console.log(`Form UI updated for transaction type: ${selectedType}`); // Debug log
    }
</script>

{{-- Script Transaksi --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize global variables
        locations = @json($locations);
        currentFacility = @json($facility);
        transaksiModal = document.getElementById('transaksiMaterialModal');
        form = document.getElementById('transaksiMaterialForm');

        console.log('Global variables initialized:', { locations, currentFacility }); // Debug log

        // Event listener saat radio button jenis transaksi berubah
        document.querySelectorAll('input[name="jenis_transaksi"]').forEach(radio => {
            radio.addEventListener('change', function() {
                console.log('Transaction type changed to:', this.value);
                window.updateFormUI();
            });
        });

        // Initialize form with default transaction type
        setTimeout(() => {
            window.updateFormUI('penyaluran');
        }, 100);

        // Event listener untuk tombol submit
        document.getElementById('submitTransaksi').addEventListener('click', function() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const jenisTransaksi = document.querySelector('input[name="jenis_transaksi"]:checked').value;
            data.jenis_transaksi = jenisTransaksi;

            // Validasi di sisi klien
            let isValid = true;
            if (jenisTransaksi === 'penyaluran' && !data.tujuan_id) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih Tujuan Penyaluran!' });
                isValid = false;
            }
            if (jenisTransaksi === 'penerimaan' && !data.asal_id) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih Asal Penerimaan!' });
                isValid = false;
            }
            if (jenisTransaksi === 'sales' && !data.tujuan_sales) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih Tujuan Sales!' });
                isValid = false;
            }
            if (!data.jumlah || data.jumlah <= 0) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Jumlah harus lebih dari 0!' });
                isValid = false;
            }
            if (!data.tanggal_transaksi) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tanggal transaksi harus diisi!' });
                isValid = false;
            }

            if (!isValid) return;

            fetch('{{ route("materials.transaction") }}', {
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
                    return response.json().then(errorData => {
                        let errorMessage = 'Terjadi kesalahan pada server. Mohon coba lagi.';
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        } else if (response.status === 422 && errorData.errors) {
                            const validationErrors = Object.values(errorData.errors).flat().join('<br>');
                            errorMessage = `<strong>Gagal Validasi :</strong><br>${validationErrors}`;
                        }
                        return Promise.reject(new Error(errorMessage));
                    });
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        bootstrap.Modal.getInstance(transaksiModal).hide();
                        // Refresh the DataTable
                        $('#table-material').DataTable().ajax.reload();
                    });
                } else if (result.errors) {
                    let errorMessages = Object.values(result.errors).map(error => `<li>${error[0]}</li>`).join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Validasi',
                        html: `<ul class="text-start mb-0 ps-3">${errorMessages}</ul>`
                    });
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Terjadi Kesalahan',
                    html: error.message
                });
            });
        });
    });
</script>

@endpush

<style>
/* Center pagination */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 1rem;
    text-align: center;
}

.dataTables_wrapper .dataTables_paginate .pagination {
    justify-content: center;
    margin: 0;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.25rem 0.5rem;
    margin: 0 0.1rem;
    border-radius: 4px;
    font-size: 0.875rem;
    display: inline-block;
    border: 1px solid transparent;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #007bff !important;
    color: white !important;
    border: 1px solid #007bff !important;
}

/* Ensure pagination buttons are styled properly */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    color: #333;
    border: 1px solid #ddd;
}

/* Action button styling for icons */
.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    margin: 0 0.1rem;
}

.action-buttons i {
    font-size: 0.875rem;
}

/* Align action icons with other column data */
.dataTables_wrapper .dataTables_scrollBody .table td:last-child,
.dataTables_wrapper .dataTables_scrollBody .table th:last-child {
    text-align: center;
    vertical-align: middle;
}

/* Style action buttons to match other data */
.table .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 2.4rem;
    padding: 0.25rem;
    min-width: 3rem;
    margin: 0 0.05rem;
    line-height: 1;
    font-size: 0.875rem;
}

/* Ensure icons are properly centered and aligned */
.table .btn i {
    font-size: 0.875rem;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
}
</style>