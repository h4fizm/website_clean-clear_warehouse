@extends('dashboard_page.main')
@section('title', 'Data Material - ' . $facility->nama_plant)

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Daftar Stok Material - {{ $facility->nama_plant }}</h3>
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
                            @forelse($items as $item)
                            <tr>
                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                <td>
                                    <span class="badge bg-gradient-info text-white text-xs">{{ $item['nama_material'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-gradient-secondary text-white text-xs">{{ $item['kode_material'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-secondary text-white text-xs">{{ number_format($item['stok_awal'], 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-primary text-white text-xs">{{ number_format($item['penerimaan_total'], 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-info text-white text-xs">{{ number_format($item['penyaluran_total'], 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-warning text-white text-xs">{{ number_format($item['sales_total'], 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-success text-white text-xs">{{ number_format($item['stok_akhir'], 0, ',', '.') }} pcs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary text-white text-xs">
                                        {{ $item['latest_transaction_date'] ? \Carbon\Carbon::parse($item['latest_transaction_date'])->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning edit-btn" data-item-id="{{ $item['item_id'] }}" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success transaksi-btn" data-item-id="{{ $item['item_id'] }}" data-nama="{{ $item['nama_material'] }}" data-kode="{{ $item['kode_material'] }}" data-stok="{{ $item['stok_akhir'] }}" title="Transaksi">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <form action="{{ route('materials.destroy', $item['item_id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <p class="text-muted mb-0">Belum ada data material di {{ $facility->nama_plant }}.</p>
                                </td>
                            </tr>
                            @endforelse
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
    // Function to format date as 'hari, tanggal bulan tahun'
    function formatDateIndonesia(dateString) {
        if (!dateString) return '-';
        
        // Handle different date formats that might come from the server
        let date;
        
        // If dateString is already in ISO format (YYYY-MM-DD) or includes time
        if (typeof dateString === 'string') {
            // Remove timezone offset by creating a new Date object and setting it to UTC
            if (dateString.includes(' ')) {
                // If it includes time (like "2025-09-10 12:30:00")
                // Parse as local time then adjust for timezone
                date = new Date(dateString);
            } else if (dateString.includes('/')) {
                // If it's in format like "09/10/2025"
                date = new Date(dateString);
            } else if (dateString.length === 10) {
                // If it's in format like "2025-09-10" (YYYY-MM-DD)
                // Parse as local time by adding 'T00:00:00' to avoid timezone issues
                date = new Date(dateString + 'T00:00:00');
            } else {
                // For any other format, try to parse directly
                date = new Date(dateString);
            }
        } else {
            // If dateString is already a Date object
            date = dateString;
        }
        
        // Check if the date is valid
        if (isNaN(date.getTime())) {
            // If the date is invalid, try to parse differently
            date = new Date(dateString.replace(/\s+/, 'T')); // Try ISO format
            if (isNaN(date.getTime())) {
                return dateString; // Return original if still can't parse
            }
        }
        
        // Array for day names in Indonesian
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        // Array for month names in Indonesian
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        // Get the day, date, month, and year
        const dayName = days[date.getDay()];
        const dayNumber = date.getDate();
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();
        
        // Return formatted date: 'hari, tanggal bulan tahun'
        return `${dayName}, ${dayNumber} ${monthName} ${year}`;
    }
    
    // Function to check if a date is outdated (older than 30 days) and add a visual indicator
    function formatDateWithStaleness(dateString) {
        const formattedDate = formatDateIndonesia(dateString);
        if (!dateString || dateString === '-') return formattedDate;
        
        const date = new Date(dateString);
        const today = new Date();
        const diffTime = Math.abs(today - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // If the date is more than 30 days old, add a warning indicator
        if (diffDays > 30) {
            return `<span title="Data lebih dari ${diffDays} hari yang lalu" class="text-warning">${formattedDate} <i class="fas fa-exclamation-triangle" style="font-size: 0.7rem;"></i></span>`;
        }
        
        return `<span title="Data mutakhir">${formattedDate}</span>`;
    }

    $(document).ready(function() {
        // Initialize DataTables for non-server-side table
        const table = $('#table-material').DataTable({
            responsive: true,
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
            order: [[7, 'desc']], // Default order by last activity date
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Edit functionality
        $('#table-material').on('click', '.edit-btn', function() {
            const btn = $(this);
            const itemId = btn.data('item-id');

            // Get item data from the row
            const row = btn.closest('tr');
            const namaMaterial = row.find('td:eq(1) .badge').text().trim();
            const kodeMaterial = row.find('td:eq(2) .badge').text().trim();
            const stokAwal = row.find('td:eq(3) .badge').text().replace(' pcs', '').replace(/\./g, '');

            // Fill the form with current data
            $('#edit-nama_material').val(namaMaterial);
            $('#edit-kode_material').val(kodeMaterial);
            $('#edit-stok_awal').val(stokAwal);

            // Set form action to update URL
            $('#editMaterialForm').attr('action', '/materials/' + itemId);

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
                        Penghapusan ini akan menghapus seluruh data material ini dari fasilitas {{ $facility->nama_plant }}, termasuk semua riwayat transaksi dan stoknya secara permanen dari database. Tindakan ini <strong>tidak dapat dikembalikan.</strong>
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
        $('#table-material').on('click', '.transaksi-btn', function() {
            try {
                const btn = $(this);
                const itemId = btn.data('item-id');
                const namaMaterial = btn.data('nama');
                const kodeMaterial = btn.data('kode');
                const stokAkhir = btn.data('stok');

                console.log('Transaction data:', { itemId, namaMaterial, kodeMaterial, stokAkhir }); // Debug log

                // Fill modal with item info
                document.getElementById('modal-nama-material').textContent = namaMaterial;
                document.getElementById('modal-kode-material').textContent = kodeMaterial;
                document.getElementById('modal-stok-akhir').textContent = stokAkhir + ' pcs';

                // Set form data
                document.getElementById('modal-item-id').value = itemId;
                document.getElementById('modal-kode-material-hidden').value = kodeMaterial;

                // Set default date
                const today = new Date();
                document.getElementById('tanggal-transaksi').value = today.toISOString().slice(0, 10);

                // Reset form dan set default transaction type
                document.getElementById('transaksiMaterialForm').reset();
                document.getElementById('modal-item-id').value = itemId;
                document.getElementById('modal-kode-material-hidden').value = kodeMaterial;
                document.getElementById('tanggal-transaksi').value = today.toISOString().slice(0, 10);
                document.getElementById('jenis-penyaluran').checked = true;

                // Initialize form UI
                if (typeof window.updateFormUI === 'function') {
                    window.updateFormUI('penyaluran');
                }

                // Show modal
                const transaksiModal = new bootstrap.Modal(document.getElementById('transaksiMaterialModal'));
                transaksiModal.show();
            } catch (error) {
                console.error('Error in transaksi button handler:', error);
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
                        // Reload page to refresh data
                        window.location.reload();
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