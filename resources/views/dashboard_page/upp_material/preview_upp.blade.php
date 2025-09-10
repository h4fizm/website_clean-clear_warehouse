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
                <form id="pengajuanForm" data-no-surat="{{ $upp['no_surat'] }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="noSurat" class="form-label">No. Surat</label>
                            <input type="text" class="form-control" id="noSurat"
                                value="{{ $upp['no_surat'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal"
                                value="{{ \Carbon\Carbon::parse($upp['tgl_buat'])->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahapan" class="form-label">Tahapan</label>
                            <input type="text" class="form-control" id="tahapan"
                                value="{{ $tahapan ?? '-' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pjUser" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="pjUser"
                                value="{{ $pjUser ?? '-' }}" required>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="tanggalPemusnahan" class="form-label">Tanggal Pemusnahan</label>
                        <input type="date" class="form-control" id="tanggalPemusnahan" required>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <label class="form-label fw-bold">Daftar Material</label>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#materialModal">
                                <i class="fas fa-plus me-1"></i> Pilih Material
                            </button>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="selectedMaterialsTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Material & Kode</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Stok Saat Ini (Pcs)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah Dipilih (Pcs)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedMaterialsBody">
                                        {{-- Data akan di-render oleh JavaScript --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="aktivitasPemusnahan" class="form-label">Aktivitas Pemusnahan</label>
                        <textarea class="form-control" id="aktivitasPemusnahan" rows="8">{{ $upp['aktivitas_pemusnahan'] ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan Pengajuan</label>
                        <textarea class="form-control" id="keterangan" rows="8">{{ $upp['keterangan'] ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ url('/upp-material') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Selesai & Proses Pemusnahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">Pilih Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="modalSearchInput" class="form-control"
                        placeholder="Cari nama material...">
                </div>
                <div class="list-group" id="materialList">
                    {{-- Material list will be rendered here --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary"
                    id="addSelectedMaterialsBtn">Tambah Material Terpilih</button>
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

    // --- Fetch Materials ---
    async function fetchMaterials() {
        try {
            const response = await fetch("{{ route('upp-material.afkir') }}");
            if (!response.ok) throw new Error('Gagal mengambil data material.');
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal mengambil data material afkir.', 'error');
            return [];
        }
    }

    let allMaterials = [];

    function renderMaterialList(query = '') {
        const materialList = document.getElementById('materialList');
        materialList.innerHTML = '';

        const filtered = allMaterials.filter(item =>
            item.nama_material.toLowerCase().includes(query.toLowerCase()) ||
            item.kode_material.toLowerCase().includes(query.toLowerCase())
        );

        if (filtered.length === 0) {
            materialList.innerHTML =
                '<p class="text-center text-muted mt-3">Tidak ada material yang ditemukan.</p>';
            return;
        }

        filtered.forEach(material => {
            const isSelected = selectedMaterials[material.id] !== undefined;
            const item = document.createElement('div');
            item.className =
                'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            item.style.cursor = 'pointer';
            if (isSelected) item.classList.add('bg-light');

            item.innerHTML = `
                <div>
                    <h6 class="mb-1">${material.nama_material}</h6>
                    <small class="text-muted">Kode: ${material.kode_material} | Stok: ${material.stok_akhir} pcs</small>
                </div>
                <div>
                    <input class="form-check-input me-1" type="checkbox" value="${material.id}" ${isSelected ? 'checked' : ''}>
                </div>
            `;

            item.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                }
            });

            materialList.appendChild(item);
        });
    }

    function renderSelectedMaterials() {
        const tbody = document.getElementById('selectedMaterialsBody');
        tbody.innerHTML = '';

        if (Object.keys(selectedMaterials).length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="4" class="text-center text-muted">Silakan pilih material yang akan diajukan.</td></tr>';
            return;
        }

        Object.values(selectedMaterials).forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <h6 class="mb-0 text-sm">${item.nama_material}</h6>
                    <p class="text-xs text-secondary mb-0">Kode: ${item.kode_material}</p>
                </td>
                <td><input type="number" class="form-control form-control-sm" value="${item.stok_akhir}" readonly></td>
                <td><input type="number" class="form-control form-control-sm jumlah-dimusnahkan" data-id="${item.id}" value="${item.jumlah}" min="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-id="${item.id}">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        document.querySelectorAll('.jumlah-dimusnahkan').forEach(input => {
            input.addEventListener('input', function () {
                const id = parseInt(this.dataset.id);
                const value = parseInt(this.value);
                if (value > 0) {
                    selectedMaterials[id].jumlah = value;
                }
            });
        });

        document.querySelectorAll('.remove-material-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = parseInt(this.dataset.id);
                delete selectedMaterials[id];
                renderSelectedMaterials();
            });
        });
    }

    // --- Event Listeners ---
    document.getElementById('addSelectedMaterialsBtn').addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('#materialList input[type="checkbox"]:checked');
        const newlyAdded = [];
        checkboxes.forEach(cb => {
            const id = parseInt(cb.value);
            const material = allMaterials.find(item => item.id === id);
            if (material && !selectedMaterials[id]) {
                selectedMaterials[id] = { ...material, jumlah: 1 };
                newlyAdded.push(material.nama_material);
            }
        });
        if (newlyAdded.length > 0) {
            Swal.fire('Berhasil!', `Material berhasil ditambahkan: ${newlyAdded.join(', ')}`, 'success');
        }
        renderSelectedMaterials();
        bootstrap.Modal.getInstance(document.getElementById('materialModal')).hide();
    });

    document.getElementById('modalSearchInput').addEventListener('input', function () {
        renderMaterialList(this.value);
    });

    document.getElementById('materialModal').addEventListener('show.bs.modal', async () => {
        allMaterials = await fetchMaterials();
        renderMaterialList('');
    });

    document.getElementById('pengajuanForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const noSurat = document.getElementById('noSurat').value;
        const tanggalPengajuan = document.getElementById('tanggal').value;
        const tahapan = document.getElementById('tahapan').value;
        const pjUser = document.getElementById('pjUser').value;
        const tanggalPemusnahan = document.getElementById('tanggalPemusnahan').value;
        const aktivitasPemusnahan = aktivitasEditor.getData().trim();
        const keterangan = keteranganEditor.getData().trim();

        if (!noSurat || !tanggalPengajuan || !tahapan || !pjUser) {
            return Swal.fire('Error', 'Semua form data harus terisi.', 'error');
        }
        if (!tanggalPemusnahan) {
            return Swal.fire('Error', 'Silakan pilih tanggal pemusnahan.', 'error');
        }
        if (!aktivitasPemusnahan) {
            return Swal.fire('Error', 'Silakan isi deskripsi aktivitas pemusnahan.', 'error');
        }
        if (!keterangan) {
            return Swal.fire('Error', 'Silakan isi keterangan pengajuan.', 'error');
        }
        if (Object.keys(selectedMaterials).length === 0) {
            return Swal.fire('Error', 'Silakan pilih setidaknya satu material.', 'error');
        }

        // Pengecekan stok material dilakukan saat submit
        for (const id in selectedMaterials) {
            const item = selectedMaterials[id];
            const inputJumlah = document.querySelector(`.jumlah-dimusnahkan[data-id="${id}"]`);
            const jumlahDimusnahkan = parseInt(inputJumlah.value);
            
            if (jumlahDimusnahkan <= 0 || jumlahDimusnahkan > item.stok_akhir) {
                return Swal.fire('Error', `Jumlah yang ingin dimusnahkan (${jumlahDimusnahkan}) untuk ${item.nama_material} tidak valid. Stok yang tersedia: ${item.stok_akhir}.`, 'error');
            }
            // Update jumlah di object selectedMaterials
            selectedMaterials[id].jumlah = jumlahDimusnahkan;
        }

        Swal.fire({
            title: 'Konfirmasi Pemusnahan',
            text: 'Apakah Anda yakin ingin menyelesaikan proses pemusnahan UPP ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Proses Pemusnahan!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                const originalNoSurat = this.dataset.noSurat;
                const formData = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    no_surat_baru: noSurat,
                    tanggal_pengajuan: tanggalPengajuan,
                    tahapan: tahapan,
                    pj_user: pjUser,
                    tanggal_pemusnahan: tanggalPemusnahan,
                    aktivitas_pemusnahan: aktivitasPemusnahan,
                    keterangan: keterangan,
                    materials: Object.values(selectedMaterials).map(item => ({
                        item_id: item.id,
                        jumlah: item.jumlah
                    }))
                };

                fetch(`{{ url('/upp-material/update') }}/${originalNoSurat}`, {
                    method: 'POST',
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

    // --- Muat Data Awal ---
    const initialData = @json($upp['materials']);
    initialData.forEach(transaction => {
        selectedMaterials[transaction.item.id] = {
            id: transaction.item.id,
            nama_material: transaction.item.nama_material,
            kode_material: transaction.item.kode_material,
            stok_akhir: transaction.stok_awal_asal,
            jumlah: transaction.jumlah
        };
    });

    if (initialData.length > 0) {
        // Baris ini sudah benar, jadi tidak perlu diubah.
        // document.getElementById('tanggal').value = initialData[0].created_at.split(' ')[0];
        document.getElementById('noSurat').value = initialData[0].no_surat_persetujuan;
        document.getElementById('tahapan').value = initialData[0].tahapan;
        document.getElementById('pjUser').value = initialData[0].user.name;

        if (initialData[0].tanggal_pemusnahan) {
            document.getElementById('tanggalPemusnahan').value = initialData[0].tanggal_pemusnahan;
        }
    }

    renderSelectedMaterials();
});
</script>
@endpush