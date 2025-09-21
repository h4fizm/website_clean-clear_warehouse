@extends('dashboard_page.main')
@section('title', 'Form Pemusnahan UPP Material')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Form Pemusnahan UPP Material</h5>
            </div>
            <div class="card-body">
                <form id="pemusnahanForm" data-no-surat="{{ $upp['no_surat'] }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="noSurat" class="form-label">No. Surat</label>
                            <input type="text" class="form-control" id="noSurat"
                                name="no_surat_baru" value="{{ $upp['no_surat'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal"
                                name="tanggal_pengajuan" value="{{ \Carbon\Carbon::parse($upp['tgl_buat'])->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahapan" class="form-label">Tahapan</label>
                            <input type="text" class="form-control" id="tahapan"
                                name="tahapan" value="{{ $tahapan }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="penanggungjawab" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="penanggungjawab" name="penanggungjawab"
                                value="{{ $upp['penanggungjawab'] }}" required>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="tanggalPemusnahan" class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" class="form-control" id="tanggalPemusnahan" name="tanggal_pemusnahan" value="{{ $upp['tanggal_pemusnahan'] ?? '' }}" required>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <label class="form-label fw-bold">Daftar Material</label>
                            <a href="#" class="btn btn-sm btn-info" id="addMaterialBtn">
                                <i class="fas fa-plus me-1"></i> Tambah Material
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="selectedMaterialsTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Material & Kode</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Stok Saat Ini (Pcs)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah Diajukan (Pcs)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedMaterialsBody">
                                        @forelse ($upp['materials'] as $material)
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0 text-sm">{{ $material['nama_material'] }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Kode: {{ $material['kode_material'] }}</p>
                                                    <input type="hidden" name="materials[{{ $loop->index }}][item_id]" value="{{ $material['id'] }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" value="{{ $material['stok_akhir_pusat'] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm jumlah-diajukan" name="materials[{{ $loop->index }}][jumlah]" value="{{ $material['jumlah_diajukan'] }}" min="1" required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-id="{{ $material['id'] }}">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Tidak ada material yang diajukan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="aktivitasPemusnahan" class="form-label">Aktivitas Pemusnahan</label>
                        <textarea class="form-control" id="aktivitasPemusnahan" name="aktivitas_pemusnahan" rows="8">{{ $upp['aktivitas_pemusnahan'] ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan Pengajuan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="8">{{ $upp['keterangan'] ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('upp-material.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Perbarui Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Pilih Material --}}
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">Tambah Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="modalSearchInput" class="form-control" placeholder="Cari nama material...">
                </div>
                <div class="list-group" id="materialList">
                    {{-- Material list will be rendered here --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="addSelectedMaterialsBtn">Tambah Material Terpilih</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let keteranganEditor;
let aktivitasEditor;
let allMaterials = [];
let selectedMaterials = {};

document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi CKEditor 5
    ClassicEditor.create(document.querySelector('#keterangan'))
        .then(editor => {
            keteranganEditor = editor;
            @if(isset($upp['keterangan']))
                keteranganEditor.setData('{!! str_replace(["\r\n", "\r", "\n"], '', $upp['keterangan']) !!}');
            @endif
        })
        .catch(error => console.error('CKEditor Keterangan error:', error));

    ClassicEditor.create(document.querySelector('#aktivitasPemusnahan'))
        .then(editor => {
            aktivitasEditor = editor;
            @if(isset($upp['aktivitas_pemusnahan']))
                aktivitasEditor.setData('{!! str_replace(["\r\n", "\r", "\n"], '', $upp['aktivitas_pemusnahan']) !!}');
            @endif
        })
        .catch(error => console.error('CKEditor Aktivitas Pemusnahan error:', error));

    // Inisialisasi material yang sudah ada di tabel ke dalam objek JavaScript
    function initializeSelectedMaterials() {
        selectedMaterials = {};
        document.querySelectorAll('#selectedMaterialsBody tr').forEach(row => {
            const itemId = parseInt(row.querySelector('input[type="hidden"]').value);
            const itemName = row.querySelector('h6').textContent;
            const itemCode = row.querySelector('p').textContent.replace('Kode: ', '');
            const itemStock = parseInt(row.querySelector('td:nth-child(2) input').value);
            const itemJumlah = parseInt(row.querySelector('.jumlah-diajukan').value);
            selectedMaterials[itemId] = {
                id: itemId,
                nama: itemName,
                kode: itemCode,
                stok: itemStock,
                jumlah_diambil: itemJumlah
            };
        });
        attachRemoveListeners();
    }
    
    // Panggil fungsi inisialisasi saat pertama kali halaman dimuat
    initializeSelectedMaterials();

    // Tambah material baru ke form
    document.getElementById('addMaterialBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const materialModal = new bootstrap.Modal(document.getElementById('materialModal'));
        if (allMaterials.length === 0) {
            fetch('{{ route('upp-material.afkir') }}')
                .then(response => response.json())
                .then(data => {
                    allMaterials = data.map(item => ({
                        id: item.id,
                        nama: item.nama_material,
                        kode: item.kode_material,
                        stok: item.stok_akhir
                    }));
                    renderMaterialList('');
                })
                .catch(error => {
                    console.error('Error fetching material data:', error);
                    const materialList = document.getElementById('materialList');
                    materialList.innerHTML = `<p class="text-center text-danger mt-3">Gagal memuat data material. Silakan coba lagi.</p>`;
                });
        } else {
            renderMaterialList('');
        }
        materialModal.show();
    });

    function renderMaterialList(query = '') {
        const materialList = document.getElementById('materialList');
        materialList.innerHTML = '';
        const filteredMaterials = allMaterials.filter(item => 
            (item.nama.toLowerCase().includes(query.toLowerCase()) || item.kode.toLowerCase().includes(query.toLowerCase())) && !selectedMaterials[item.id]
        );
        if (filteredMaterials.length === 0) {
            materialList.innerHTML = '<p class="text-center text-muted mt-3">Tidak ada material yang ditemukan.</p>';
            return;
        }
        filteredMaterials.forEach(material => {
            const item = document.createElement('div');
            item.classList.add('list-group-item', 'list-group-item-action', 'd-flex', 'justify-content-between', 'align-items-center');
            item.innerHTML = `
                <div>
                    <h6 class="mb-1">${material.nama}</h6>
                    <small class="text-muted">Kode: ${material.kode} | Stok: ${material.stok} pcs</small>
                </div>
                <div>
                    <input class="form-check-input me-1" type="checkbox" value="${material.id}">
                </div>
            `;
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const checkbox = item.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
            });
            materialList.appendChild(item);
        });
    }

    document.getElementById('modalSearchInput').addEventListener('input', function() {
        renderMaterialList(this.value);
    });

    document.getElementById('addSelectedMaterialsBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('#materialList input[type="checkbox"]:checked');
        checkboxes.forEach(checkbox => {
            const id = parseInt(checkbox.value);
            const material = allMaterials.find(item => item.id === id);
            if (material) {
                selectedMaterials[id] = { ...material, jumlah_diambil: 1 };
            }
        });
        renderSelectedMaterials();
        const materialModal = bootstrap.Modal.getInstance(document.getElementById('materialModal'));
        materialModal.hide();
    });

    function renderSelectedMaterials() {
        const tbody = document.getElementById('selectedMaterialsBody');
        tbody.innerHTML = '';
        let index = 0;
        
        if (Object.keys(selectedMaterials).length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada material yang diajukan.</td></tr>`;
            return;
        }

        for (const id in selectedMaterials) {
            const item = selectedMaterials[id];
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <h6 class="mb-0 text-sm">${item.nama}</h6>
                    <p class="text-xs text-secondary mb-0">Kode: ${item.kode}</p>
                    <input type="hidden" name="materials[${index}][item_id]" value="${item.id}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" value="${item.stok}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm jumlah-diajukan" name="materials[${index}][jumlah]" value="${item.jumlah_diambil}" min="1" max="${item.stok}" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-id="${item.id}">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            index++;
        }
        // Pasang kembali event listener setelah tabel dirender ulang
        attachRemoveListeners();
    }
    
    // Fungsi baru untuk memasang event listener pada tombol hapus
    function attachRemoveListeners() {
        document.querySelectorAll('.remove-material-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                delete selectedMaterials[id];
                renderSelectedMaterials();
            });
        });
    }

    // --- Submit form untuk update data saja ---
    document.getElementById('pemusnahanForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const noSurat = document.getElementById('noSurat').value;
        const tanggalPengajuan = document.getElementById('tanggal').value;
        const tahapan = document.getElementById('tahapan').value;
        const penanggungjawab = document.getElementById('penanggungjawab').value;
        const tanggalPemusnahan = document.getElementById('tanggalPemusnahan').value;
        const aktivitasPemusnahan = aktivitasEditor.getData().trim();
        const keterangan = keteranganEditor.getData().trim();
        
        if (!noSurat || !tanggalPengajuan || !tahapan || !penanggungjawab || !tanggalPemusnahan || !aktivitasPemusnahan || !keterangan) {
            return Swal.fire('Error', 'Semua form data harus terisi.', 'error');
        }
        
        const materialsData = [];
        let isValid = true;
        
        const rows = document.querySelectorAll('#selectedMaterialsBody tr');
        if (rows.length === 0) {
            return Swal.fire('Error', 'Tidak ada material yang diajukan.', 'error');
        }
        
        rows.forEach(row => {
            const itemId = parseInt(row.querySelector('input[type="hidden"]').value);
            const inputJumlah = row.querySelector('.jumlah-diajukan');
            const jumlah = parseInt(inputJumlah.value);
            
            const stokSaatIni = parseInt(row.querySelector('td:nth-child(2) input').value);
            
            if (isNaN(jumlah) || jumlah <= 0 || jumlah > stokSaatIni) {
                const materialName = row.querySelector('h6').textContent;
                Swal.fire('Error', `Jumlah yang ingin dimusnahkan (${jumlah}) untuk ${materialName} tidak valid. Stok yang tersedia: ${stokSaatIni}.`, 'error');
                isValid = false;
                return;
            }
            materialsData.push({
                item_id: itemId,
                jumlah: jumlah
            });
        });

        if (!isValid) return;

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: 'Apakah Anda yakin ingin memperbarui pengajuan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Perbarui!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                const originalNoSurat = document.getElementById('pemusnahanForm').dataset.noSurat;
                const formData = {
                    no_surat_baru: noSurat,
                    tanggal_pengajuan: tanggalPengajuan,
                    tahapan: tahapan,
                    penanggungjawab: penanggungjawab,
                    tanggal_pemusnahan: tanggalPemusnahan,
                    aktivitas_pemusnahan: aktivitasPemusnahan,
                    keterangan: keterangan,
                    materials: materialsData,
                };

                fetch(`{{ url('/upp-material/update') }}/${originalNoSurat}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire('Gagal!', 'Terjadi kesalahan pada server. Mohon coba lagi.', 'error');
                });
            }
        });
    });
});
</script>
@endpush