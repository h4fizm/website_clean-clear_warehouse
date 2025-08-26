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
                <form method="GET" action="{{ route('pusat.index') }}">
                    <div class="row mb-3 align-items-center">
                        <div class="col-12 col-md-auto me-auto mb-2 mb-md-0">
                            <h4 class="mb-0" id="table-branch-name">Tabel Data Stok Material - P.Layang (Pusat)</h4>
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="button" class="btn btn-success d-flex align-items-center justify-content-center mt-2 mt-md-0">
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </button>
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
                    
                   <div class="row mb-3 align-items-start">
                        {{-- Input Search --}}
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" id="searchInput" 
                                    class="form-control" 
                                    placeholder="Cari material..." 
                                    value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Date Range + Filter Button --}}
                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-md-end">
                            {{-- Start Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" name="start_date" id="startDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ $filters['start_date'] ?? '' }}">
                            </div>

                            {{-- End Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" name="end_date" id="endDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;"
                                    value="{{ $filters['end_date'] ?? '' }}">
                            </div>

                            {{-- Button Filter (diturunkan sedikit) --}}
                            <div class="align-self-end">
                                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
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
                                {{-- DENGAN KODE BARU INI --}}
                                <td class="text-center">
                                    <p class="text-xs text-secondary font-weight-bold mb-0">
                                        {{-- Gunakan tanggal transaksi terakhir, jika tidak ada, gunakan tanggal update item --}}
                                        @php
                                            $tanggal = $item->latest_transaction_date ?? $item->updated_at;
                                        @endphp
                                        {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success text-white me-1 kirim-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#kirimMaterialModal">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#editMaterialModal-{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('pusat.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white delete-btn" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Data Kosong</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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
                    <input type="hidden" id="item-id-pusat">
                    <input type="hidden" id="kode-material-selected">

                    {{-- Pilihan Jenis Transaksi --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penyaluran" value="penyaluran" checked>
                                <label class="form-check-label" for="jenis-penyaluran">Penyaluran (Kirim dari P.Layang)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenisTransaksi" id="jenis-penerimaan" value="penerimaan">
                                <label class="form-check-label" for="jenis-penerimaan">Penerimaan (Ambil dari SPBE/BPT)</label>
                            </div>
                        </div>
                    </div>

                    {{-- Form Dinamis --}}
                    <div class="mb-3">
                        <label id="asal-label" class="form-label">Asal Transaksi</label>
                        <div id="asal-container">
                            {{-- Akan diisi oleh JavaScript --}}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label id="tujuan-label" class="form-label">Tujuan Transaksi</label>
                        <div id="tujuan-container">
                             {{-- Akan diisi oleh JavaScript --}}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal-transaksi" class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="tanggal-transaksi" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no-surat-persetujuan" class="form-label">No. Surat Persetujuan</label>
                            <input type="text" class="form-control" id="no-surat-persetujuan" placeholder="(Opsional)">
                        </div>
                        <div class="col-md-6 mb-3">
                             <label for="no-ba-serah-terima" class="form-label">No. BA Serah Terima</label>
                            <input type="text" class="form-control" id="no-ba-serah-terima" placeholder="(Opsional)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah-stok" class="form-label">Jumlah (pcs)</label>
                        <input type="number" class="form-control" id="jumlah-stok" min="1" required>
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

{{-- Modal Edit dibuat di dalam Loop --}}
@foreach ($items as $item)
<div class="modal fade" id="editMaterialModal-{{ $item->id }}" tabindex="-1" aria-labelledby="editMaterialModalLabel-{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMaterialModalLabel-{{ $item->id }}">Edit Data Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pusat.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input type="text" class="form-control @if($errors->has('nama_material') && session('error_item_id') == $item->id) is-invalid @endif" 
                   id="nama_material-{{ $item->id }}" name="nama_material" placeholder=" " 
                   value="{{ old('nama_material', $item->nama_material) }}">
            <label for="nama_material-{{ $item->id }}">Nama Material</label>
            @if($errors->has('nama_material') && session('error_item_id') == $item->id) 
              <div class="invalid-feedback">{{ $errors->first('nama_material') }}</div> 
            @endif
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control @if($errors->has('kode_material') && session('error_item_id') == $item->id) is-invalid @endif" 
                   id="kode_material-{{ $item->id }}" name="kode_material" placeholder=" " 
                   value="{{ old('kode_material', $item->kode_material) }}">
            <label for="kode_material-{{ $item->id }}">Kode Material</label>
            @if($errors->has('kode_material') && session('error_item_id') == $item->id) 
              <div class="invalid-feedback">{{ $errors->first('kode_material') }}</div> 
            @endif
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
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); 
                const form = this.closest('form');
                
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Ambil data facilities dari Controller
        const facilities = @json($facilities);

        // 🔹 Template Searchbar
        function createSearchInputHTML() {
            return `
                <div class="position-relative w-100">
                    <input type="text" class="form-control" id="facility-search" placeholder="Cari Facility...">
                    <input type="hidden" id="facility-id-hidden">
                    <div id="facility-suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 200px; overflow-y: auto; display: none;"></div>
                </div>
            `;
        }

        const readonlyInputHTML = `<input type="text" class="form-control" value="P.Layang (Pusat)" readonly>`;

        // 🔹 Fungsi untuk update form sesuai jenis transaksi
        function updateFormUI(type) {
            const asalContainer = document.getElementById('asal-container');
            const tujuanContainer = document.getElementById('tujuan-container');

            if (type === 'penyaluran') { 
                asalContainer.innerHTML = readonlyInputHTML;
                tujuanContainer.innerHTML = createSearchInputHTML();
                initSearchbar(); // aktifkan searchbar
            } else { 
                asalContainer.innerHTML = createSearchInputHTML();
                tujuanContainer.innerHTML = readonlyInputHTML;
                initSearchbar(); // aktifkan searchbar
            }
        }

        // 🔹 Fungsi Searchbar Autocomplete
        function initSearchbar() {
            const searchInput = document.getElementById("facility-search");
            const hiddenInput = document.getElementById("facility-id-hidden");
            const suggestionsBox = document.getElementById("facility-suggestions");

            if (!searchInput) return;

            searchInput.addEventListener("input", function() {
                const query = this.value.toLowerCase();
                suggestionsBox.innerHTML = "";

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
        }

        // 🔹 Radio button listener
        document.querySelectorAll('input[name="jenisTransaksi"]').forEach(radio => {
            radio.addEventListener('change', (event) => {
                updateFormUI(event.target.value);
            });
        });

        // 🔹 Saat klik tombol kirim
        document.querySelectorAll('.kirim-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                document.getElementById('item-id-pusat').value = this.getAttribute('data-id');
                document.getElementById('modal-nama-material-display').textContent = row.cells[1].innerText;
                document.getElementById('modal-kode-material-display').textContent = row.cells[2].innerText;
                document.getElementById('modal-stok-akhir-display').textContent = row.cells[6].innerText;
                document.getElementById('kode-material-selected').value = row.cells[2].innerText;
                document.getElementById('tanggal-transaksi').value = new Date().toISOString().slice(0, 10);

                document.getElementById('jenis-penyaluran').checked = true;
                updateFormUI('penyaluran');
            });
        });

        // 🔹 Submit button
        document.getElementById('submitKirim').addEventListener('click', function() {
            const selectedFacilityId = document.getElementById('facility-id-hidden')?.value;

            if (!selectedFacilityId) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Anda harus memilih satu SPBE/BPT!' });
                return;
            }

            const formData = {
                _token: document.querySelector('#kirimMaterialForm input[name="_token"]').value,
                item_id_pusat: document.getElementById('item-id-pusat').value,
                kode_material: document.getElementById('kode-material-selected').value,
                facility_id_selected: selectedFacilityId,
                jenis_transaksi: document.querySelector('input[name="jenisTransaksi"]:checked').value,
                jumlah: document.getElementById('jumlah-stok').value,
                tanggal_transaksi: document.getElementById('tanggal-transaksi').value,
                no_surat_persetujuan: document.getElementById('no-surat-persetujuan').value,
                no_ba_serah_terima: document.getElementById('no-ba-serah-terima').value,
            };

            fetch('{{ route('pusat.transfer') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message
                    }).then(() => window.location.reload());
                } else if (data.errors) {
                    let errorMessages = Object.values(data.errors).map(error => `<li>${error[0]}</li>`).join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Validasi',
                        html: `<ul class="text-start">${errorMessages}</ul>`
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message
                });
            });
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