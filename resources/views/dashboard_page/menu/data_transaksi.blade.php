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

                {{-- BARIS 1: Judul Tabel --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                        <h4 class="mb-0" id="table-branch-name">Tabel Stok SPBE/BPT - {{ $selectedSalesArea }}</h4>
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
                                    <a href="javascript:void(0)"
                                       data-region="{{ $region->nama_regions }}"
                                       class="btn btn-sm btn-branch-custom {{ $selectedSalesArea == $region->nama_regions ? 'btn-primary' : 'btn-outline-primary' }}">
                                        {{ $region->nama_regions }}
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
                                        <a class="dropdown-item {{ $selectedSalesArea == $region->nama_regions ? 'active' : '' }}"
                                           href="javascript:void(0)" data-region="{{ $region->nama_regions }}">
                                            {{ $region->nama_regions }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="text-sm text-secondary">Gunakan fitur pencarian dan filter bawaan tabel di bawah ini untuk mencari data atau mengatur urutan.</p>
                </div>
            </div>

            <div class="card-body px-4 pt-4 pb-5">

                {{-- Area untuk menampilkan Bootstrap Alert dan Validasi Server-Side --}}
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

                <div class="table-responsive">
                    <table id="transaksi-table" class="table align-items-center mb-0" style="width:100%">
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
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DYNAMIC EDIT MODAL --}}
<div class="modal fade" id="editSpbeBptModal" tabindex="-1" aria-labelledby="editSpbeBptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpbeBptModalLabel">Edit Data SPBE/BPT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" action="" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" id="edit-plant-id" name="plant_id">
                    <div class="mb-3">
                        <label for="edit-nama_plant" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control" id="edit-nama_plant" name="nama_plant" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode_plant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control" id="edit-kode_plant" name="kode_plant" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-provinsi" class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control" id="edit-provinsi" name="provinsi" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kabupaten" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control" id="edit-kabupaten" name="kabupaten" required>
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


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

{{-- DataTables Configuration --}}
<script>
    $(document).ready(function() {
        // Get current sales area from URL or default
        const urlParams = new URLSearchParams(window.location.search);
        let currentSalesArea = urlParams.get('sales_area') || '{{ $selectedSalesArea }}';

        const table = $('#transaksi-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('api.transaksi.facilities') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: function(d) {
                    d.sales_area = currentSalesArea;
                    // Add cache-busting parameter
                    d._ = new Date().getTime();
                },
                error: function(xhr, error, code) {
                    console.log('DataTable Error - Status:', xhr.status);
                    console.log('DataTable Error - Response:', xhr.responseText);
                    console.log('Error details:', error, code);

                    if (xhr.status === 401 || xhr.status === 403) {
                        $('#transaksi-table').hide();
                        $('#transaksi-table').after('<div class="alert alert-warning">Authentication required. Please refresh the page and log in again.</div>');
                    } else {
                        $('#transaksi-table').after('<div class="alert alert-danger">Error loading data: ' + xhr.status + ' ' + error + '</div>');
                    }
                },
                dataSrc: function(json) {
                    console.log('API Data received:', json);
                    console.log('Total records:', json.recordsTotal);
                    console.log('Data rows:', json.data.length);
                    return json.data;
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
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        return '<a href="' + row.material_url + '" class="text-sm font-weight-bolder text-decoration-underline text-primary">' + data + '</a>';
                    }
                },
                {
                    data: 'kode_plant',
                    name: 'kode_plant',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-secondary text-white text-xs">' + data + '</span>';
                    }
                },
                {
                    data: 'province',
                    name: 'province',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-info text-white text-xs">' + data + '</span>';
                    }
                },
                {
                    data: 'regency',
                    name: 'regency',
                    render: function(data, type, row) {
                        return '<span class="badge bg-gradient-primary text-white text-xs">' + data + '</span>';
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data || '';
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
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
                },
                processing: "Sedang memuat..."
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 d-flex justify-content-center"p><"col-sm-12"i>>',
            order: [[1, 'asc']], // Default order by name
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
          });

        // Function to update table with new sales area
        function updateTableWithSalesArea(salesArea) {
            currentSalesArea = salesArea;

            // Update URL
            const newUrl = new URL(window.location);
            if (salesArea) {
                newUrl.searchParams.set('sales_area', salesArea);
            } else {
                newUrl.searchParams.delete('sales_area');
            }
            window.history.pushState({}, '', newUrl);

            // Update dynamic elements
            $('#dynamic-branch-name').text(salesArea);
            $('#table-branch-name').text('Tabel Stok SPBE/BPT - ' + salesArea);

            // Force reload table with new filter
            console.log('Force reloading DataTable for sales area:', salesArea);
            table.ajax.reload(null, false); // false = keep current page
        }

        // Function to force refresh DataTable (for debugging)
        window.forceRefreshTable = function() {
            console.log('Force refreshing DataTable...');
            table.ajax.reload();
        };

        // Handle region button clicks
        $('.btn-branch-custom').on('click', function(e) {
            e.preventDefault();
            const salesArea = $(this).data('region');

            // Update active button styling
            $('.btn-branch-custom').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            updateTableWithSalesArea(salesArea);
        });

        // Handle dropdown selection for mobile
        $('.dropdown-item').on('click', function(e) {
            e.preventDefault();
            const salesArea = $(this).data('region');

            // Update dropdown text
            $('#regionDropdown').text('Pilih Region: ' + salesArea);

            // Update active styling for dropdown items
            $('.dropdown-item').removeClass('active');
            $(this).addClass('active');

            updateTableWithSalesArea(salesArea);
        });

        // Function to open edit modal with data - global scope
        window.openEditModal = function(plantId) {
            console.log('=== OPEN EDIT MODAL ===');
            console.log('Plant ID:', plantId);

            // Find the button that triggered this function
            const button = $('#transaksi-table').find('[data-plant-id="' + plantId + '"]');
            console.log('Found button:', button.length > 0);

            if (button.length === 0) {
                console.error('Button not found for plant ID:', plantId);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Edit button not found. Please refresh the page.'
                });
                return;
            }

            // Get data from button attributes
            const plantName = button.data('plant-name');
            const plantCode = button.data('plant-code');
            const plantProvince = button.data('plant-province');
            const plantRegency = button.data('plant-regency');

            console.log('Plant data:', { plantName, plantCode, plantProvince, plantRegency });

            // Check modal element exists
            const modalEl = document.getElementById('editSpbeBptModal');
            if (!modalEl) {
                console.error('Modal element not found');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Modal not found. Please refresh the page.'
                });
                return;
            }

            // Fill modal fields
            $('#edit-plant-id').val(plantId);
            $('#edit-nama_plant').val(plantName || '');
            $('#edit-kode_plant').val(plantCode || '');
            $('#edit-provinsi').val(plantProvince || '');
            $('#edit-kabupaten').val(plantRegency || '');

            // Set form action
            const formAction = '/transaksi/' + plantId;
            $('#editForm').attr('action', formAction);
            console.log('Form action set to:', formAction);

            try {
                // Show modal
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                console.log('Modal shown successfully');
            } catch (error) {
                console.error('Error showing modal:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to open edit modal.'
                });
            }
        };

        // Delete functionality with SweetAlert2
        $(document).on('click', '.delete-btn, .btn-danger', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
            const form = $(this).closest('form');
            console.log('Delete form found:', form.length > 0);

            const url = form.attr('action');
            const method = form.find('input[name="_method"]').val();

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
                    console.log('Submitting delete AJAX request to:', url);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _method: method,
                            _token: form.find('input[name="_token"]').val()
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('Delete successful:', response);

                            // Reload table with null to reset pagination and get fresh data
                            table.ajax.reload(null, false); // false = keep current page

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data SPBE/BPT berhasil dihapus.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            console.error('Delete failed:', xhr);
                            console.error('Response text:', xhr.responseText);

                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Handle edit form submission
        $(document).on('submit', '#editForm', function(e) {
            e.preventDefault();
            console.log('=== EDIT FORM SUBMISSION ===');

            const form = $(this);
            const url = form.attr('action');
            const formData = form.serialize();

            console.log('Form action:', url);
            console.log('Form data:', formData);
            console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content'));

            if (!url || url === '') {
                console.error('Form action is empty');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Form action not set. Please refresh the page.'
                });
                return;
            }

            // Disable submit button to prevent double submission
            const submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

            console.log('Sending AJAX request...');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                beforeSend: function(xhr) {
                    console.log('AJAX beforeSend triggered');
                },
                success: function(response, status, xhr) {
                    console.log('=== AJAX SUCCESS ===');
                    console.log('Response:', response);
                    console.log('Status:', status);
                    console.log('XHR status:', xhr.status);

                    // Close modal properly
                    const modalEl = document.getElementById('editSpbeBptModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        // Force remove backdrop
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        }, 300);
                    }

                    // Reload table with null to reset pagination and get fresh data
                    console.log('Reloading DataTable...');
                    table.ajax.reload(null, false); // false = keep current page

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data SPBE/BPT berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr, status, error) {
                    console.log('=== AJAX ERROR ===');
                    console.error('XHR Status:', xhr.status);
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response text:', xhr.responseText);
                    console.error('Response headers:', xhr.getAllResponseHeaders());

                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    submitBtn.html('Simpan Perubahan');

                    let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                    let errorTitle = 'Gagal!';

                    if (xhr.status === 419) {
                        errorTitle = 'Session Expired';
                        errorMessage = 'Session Anda telah kedaluwarsa. Silakan refresh halaman.';
                    } else if (xhr.status === 401) {
                        errorTitle = 'Unauthorized';
                        errorMessage = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
                    } else if (xhr.status === 422) {
                        errorTitle = 'Validasi Gagal';
                        errorMessage = 'Terdapat kesalahan validasi.';
                        // Show validation errors
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            let errorList = '<ul>';
                            for (const field in errors) {
                                errorList += '<li>' + errors[field][0] + '</li>';
                            }
                            errorList += '</ul>';

                            Swal.fire({
                                icon: 'error',
                                title: errorTitle,
                                html: errorList
                            });
                            return;
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            errorMessage = errorData.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Server error: ' + xhr.status + ' ' + error;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        text: errorMessage
                    });
                },
                complete: function(xhr, status) {
                    console.log('=== AJAX COMPLETE ===');
                    console.log('Final status:', status);
                    // Re-enable submit button if not already done
                    submitBtn.prop('disabled', false);
                    submitBtn.html('Simpan Perubahan');
                }
            });
        });

        // Convert buttons to icons and add event listeners
        table.on('draw', function() {
            // Convert edit buttons to pencil icon and add click handler
            $('.btn-info').each(function() {
                if ($(this).find('i').length === 0) {
                    $(this).html('<i class="fas fa-edit"></i>');
                    $(this).addClass('edit-icon');
                    $(this).attr('title', 'Edit');
                }

                // Add click event for edit button
                $(this).off('click.edit').on('click.edit', function(e) {
                    e.preventDefault();
                    const plantId = $(this).data('plant-id');
                    console.log('Edit button clicked, plant ID:', plantId);

                    if (plantId && typeof window.openEditModal === 'function') {
                        window.openEditModal(plantId);
                    } else {
                        console.error('Cannot open edit modal - plant ID or function not found');
                    }
                });
            });

            // Convert delete buttons to trash icon
            $('.btn-danger').each(function() {
                if ($(this).find('i').length === 0) {
                    $(this).html('<i class="fas fa-trash-alt"></i>');
                    $(this).addClass('delete-icon');
                    $(this).attr('title', 'Hapus');
                }
            });
        });
    });
</script>

@endpush


@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

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

    /* Branch buttons styling */
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
        #transaksi-table thead th { font-size: 0.65rem !important; }
        #transaksi-table tbody td { font-size: 0.75rem !important; }
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

    /* Fix table overflow and responsive issues */
    .dataTables_wrapper {
        width: 100% !important;
        overflow-x: hidden;
    }

    #transaksi-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed;
    }

    #transaksi-table th,
    #transaksi-table td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            float: none !important;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .dataTables_wrapper .dataTables_info {
            float: none !important;
            text-align: center;
            margin-top: 0.5rem;
        }

        #transaksi-table th,
        #transaksi-table td {
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
        }
    }
</style>
@endpush
@endsection