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
                                        {{-- INI PERUBAHANNYA --}}
                                        <button type="button" class="btn btn-sm btn-success text-white me-1 transaksi-btn" 
                                            title="Transaksi Material"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#transaksiMaterialModal"
                                            data-item-id="{{ $item->id }}"
                                            data-nama-material="{{ $item->nama_material }}"
                                            data-kode-material="{{ $item->kode_material }}"
                                            data-stok-akhir="{{ $item->stok_akhir }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        {{-- AKHIR PERUBAHAN --}}
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
                        {{ $items->links('vendor.pagination.bootstrap-5-simple') }}
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
                    <p class="mb-1 text-xs text-secondary font-weight-bolder opacity-7">STOK SAAT INI DI LOKASI INI</p>
                    <p class="mb-0 text-sm font-weight-bold" id="modal-stok-akhir"></p>
                </div>

                <form id="transaksiMaterialForm" onsubmit="return false;">
                    @csrf
                    <input type="hidden" id="modal-item-id" name="item_id">

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="jenis-penyaluran" value="penyaluran" checked>
                                <label class="form-check-label" for="jenis-penyaluran">Penyaluran (Kirim dari Sini)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="jenis-penerimaan" value="penerimaan">
                                <label class="form-check-label" for="jenis-penerimaan">Penerimaan (Terima di Sini)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="asal-select" class="form-label">Asal Transaksi</label>
                        <select class="form-select" id="asal-select" name="asal_id" required></select>
                    </div>

                    <div class="mb-3">
                        <label for="tujuan-select" class="form-label">Tujuan Transaksi</label>
                        <select class="form-select" id="tujuan-select" name="tujuan_id" required></select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal-transaksi" class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="tanggal-transaksi" name="tanggal_transaksi" required>
                    </div>
                    
                    {{-- Input No Surat & BA (tidak berubah) --}}
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
                    // Teks disesuaikan dengan aksi di controller
                    text: "Seluruh data transaksi material ini akan dihapus dan stok awal akan di-reset menjadi 0.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, reset!',
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const locations = @json($locations);
        const currentFacility = @json($facility);
        const transaksiModal = document.getElementById('transaksiMaterialModal');
        
        const asalSelect = document.getElementById('asal-select');
        const tujuanSelect = document.getElementById('tujuan-select');
        const radioButtons = document.querySelectorAll('input[name="jenis_transaksi"]');

        // [LOGIKA BARU] Fungsi utama untuk mengatur ulang form berdasarkan pilihan radio
        function updateFormUI() {
            const selectedType = document.querySelector('input[name="jenis_transaksi"]:checked').value;
            
            // Buat daftar lokasi lain (semua lokasi kecuali lokasi saat ini)
            const otherLocations = locations.filter(loc => loc.id != currentFacility.id);

            if (selectedType === 'penyaluran') {
                // ASAL: Terkunci ke lokasi saat ini
                asalSelect.innerHTML = `<option value="${currentFacility.id}" selected>${currentFacility.name}</option>`;
                asalSelect.disabled = true;

                // TUJUAN: Bisa memilih dari lokasi lain
                tujuanSelect.innerHTML = '<option value="" selected disabled>-- Pilih Lokasi Tujuan --</option>';
                otherLocations.forEach(loc => {
                    tujuanSelect.add(new Option(loc.name, loc.id));
                });
                tujuanSelect.disabled = false;

            } else { // Jika 'penerimaan'
                // ASAL: Bisa memilih dari lokasi lain
                asalSelect.innerHTML = '<option value="" selected disabled>-- Pilih Lokasi Asal --</option>';
                otherLocations.forEach(loc => {
                    asalSelect.add(new Option(loc.name, loc.id));
                });
                asalSelect.disabled = false;

                // TUJUAN: Terkunci ke lokasi saat ini
                tujuanSelect.innerHTML = `<option value="${currentFacility.id}" selected>${currentFacility.name}</option>`;
                tujuanSelect.disabled = true;
            }
        }

        // Saat modal pertama kali dibuka
        transaksiModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('transaksiMaterialForm').reset();
            
            // Set default radio ke 'penyaluran'
            document.getElementById('jenis-penyaluran').checked = true;
            
            // Panggil fungsi utama untuk mengatur form ke kondisi awal
            updateFormUI();

            // Isi info material dan tanggal
            document.getElementById('modal-item-id').value = button.getAttribute('data-item-id');
            document.getElementById('modal-nama-material').textContent = button.getAttribute('data-nama-material');
            document.getElementById('modal-stok-akhir').textContent = `${parseInt(button.getAttribute('data-stok-akhir')).toLocaleString('id-ID')} pcs`;
            document.getElementById('tanggal-transaksi').value = new Date().toISOString().slice(0, 10);
        });

        // Tambahkan event listener ke setiap radio button
        radioButtons.forEach(radio => {
            radio.addEventListener('change', updateFormUI);
        });

        // Logika Fetch untuk submit (tidak berubah)
        document.getElementById('submitTransaksi').addEventListener('click', function() {
            // Aktifkan kembali field yang disabled agar nilainya terkirim
            asalSelect.disabled = false;
            tujuanSelect.disabled = false;

            const form = document.getElementById('transaksiMaterialForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Setelah mengambil data, kembalikan ke state disabled jika perlu (agar UI konsisten)
            updateFormUI(); 

            fetch('{{ route("materials.transaction") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 2500, showConfirmButton: false })
                    .then(() => {
                        bootstrap.Modal.getInstance(transaksiModal).hide();
                        window.location.reload();
                    });
                } else if (result.errors) {
                    let errorMessages = Object.values(result.errors).map(error => `<li>${error[0]}</li>`).join('');
                    Swal.fire({ icon: 'error', title: 'Gagal Validasi', html: `<ul class="text-start mb-0 ps-3">${errorMessages}</ul>` });
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Oops... Terjadi Kesalahan', text: error.message });
            });
        });
    });
</script>
@endpush
