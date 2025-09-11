@extends('dashboard_page.main')
@section('title', 'Form Pengajuan UPP')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Form Pengajuan UPP Material</h5>
            </div>
            <div class="card-body">
                <form id="pengajuanForm" action="{{ route('upp-material.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="noSurat" class="form-label">No. Surat</label>
                            <input type="text" class="form-control" id="noSurat" name="noSurat" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahapan" class="form-label">Tahapan</label>
                            <input type="text" class="form-control" id="tahapan" name="tahapan" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pjUser" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="pjUser" name="pjUser" required>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <label class="form-label fw-bold">Daftar Material</label>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#materialModal">
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah Diambil (Pcs)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedMaterialsBody">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Silakan pilih material yang akan diajukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan Pengajuan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="8"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ url('/upp-material') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Tambah Pengajuan UPP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">Pilih Material</h5>
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
    let selectedMaterials = {};
    let allMaterials = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi CKEditor
        ClassicEditor
            .create(document.querySelector('#keterangan'))
            .then(editor => {
                keteranganEditor = editor;
            })
            .catch(error => {
                console.error('Ada kesalahan saat menginisialisasi CKEditor:', error);
            });

        // Ambil data material saat modal dibuka
        document.getElementById('materialModal').addEventListener('show.bs.modal', function() {
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
        });

        function renderMaterialList(query = '') {
            const materialList = document.getElementById('materialList');
            materialList.innerHTML = '';
            
            const filteredMaterials = allMaterials.filter(item => 
                item.nama.toLowerCase().includes(query.toLowerCase()) || 
                item.kode.toLowerCase().includes(query.toLowerCase())
            );

            if (filteredMaterials.length === 0) {
                materialList.innerHTML = '<p class="text-center text-muted mt-3">Tidak ada material yang ditemukan.</p>';
                return;
            }

            filteredMaterials.forEach(material => {
                const isSelected = selectedMaterials[material.id] !== undefined;
                const item = document.createElement('div');
                item.classList.add('list-group-item', 'list-group-item-action', 'd-flex', 'justify-content-between', 'align-items-center');
                item.style.cursor = 'pointer';
                if (isSelected) {
                    item.classList.add('bg-light');
                }
                item.innerHTML = `
                    <div>
                        <h6 class="mb-1">${material.nama}</h6>
                        <small class="text-muted">Kode: ${material.kode} | Stok: ${material.stok} pcs</small>
                    </div>
                    <div>
                        <input class="form-check-input me-1" type="checkbox" value="${material.id}" ${isSelected ? 'checked' : ''}>
                    </div>
                `;
                item.addEventListener('click', function(e) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                });
                materialList.appendChild(item);
            });
        }
        
        function renderSelectedMaterials() {
            const tbody = document.getElementById('selectedMaterialsBody');
            tbody.innerHTML = '';
            
            if (Object.keys(selectedMaterials).length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Silakan pilih material yang akan diajukan.</td></tr>';
                return;
            }
            
            Object.values(selectedMaterials).forEach(item => {
                const row = document.createElement('tr');
                const uniqueId = `jumlah-diambil-${item.id}`;
                row.innerHTML = `
                    <td>
                        <h6 class="mb-0 text-sm">${item.nama}</h6>
                        <p class="text-xs text-secondary mb-0">Kode: ${item.kode}</p>
                    </td>
                    <td>
                        <span>${item.stok} pcs</span>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm jumlah-diambil" id="${uniqueId}" data-id="${item.id}" value="${item.jumlah_diambil || ''}" min="1" max="${item.stok}" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-id="${item.id}">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                document.getElementById(uniqueId).addEventListener('input', function() {
                    let value = parseInt(this.value);
                    if (isNaN(value) || value < 1) {
                        value = 1;
                        this.value = 1;
                    } else if (value > item.stok) {
                        value = item.stok;
                        this.value = item.stok;
                    }
                    selectedMaterials[item.id].jumlah_diambil = value;
                });
            });
            
            document.querySelectorAll('.remove-material-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    delete selectedMaterials[id];
                    renderSelectedMaterials();
                });
            });
        }
        
        document.getElementById('addSelectedMaterialsBtn').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#materialList input[type="checkbox"]:checked');
            checkboxes.forEach(checkbox => {
                const id = parseInt(checkbox.value);
                const material = allMaterials.find(item => item.id === id);
                if (material && !selectedMaterials[id]) {
                    selectedMaterials[id] = { ...material, jumlah_diambil: '' };
                }
            });
            
            renderSelectedMaterials();
            const materialModal = bootstrap.Modal.getInstance(document.getElementById('materialModal'));
            materialModal.hide();
        });

        document.getElementById('modalSearchInput').addEventListener('input', function() {
            renderMaterialList(this.value);
        });

        document.getElementById('pengajuanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const noSurat = document.getElementById('noSurat').value;
            const tanggal = document.getElementById('tanggal').value;
            const tahapan = document.getElementById('tahapan').value;
            const pjUser = document.getElementById('pjUser').value;
            const keterangan = keteranganEditor.getData();

            if (!noSurat || !tanggal || !tahapan || !pjUser || keterangan.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Pastikan semua kolom form utama telah diisi.',
                });
                return;
            }

            if (Object.keys(selectedMaterials).length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Material Belum Dipilih',
                    text: 'Silakan pilih setidaknya satu material untuk diajukan.',
                });
                return;
            }
            
            const materialsToSubmit = [];
            let isValid = true;
            for (const id in selectedMaterials) {
                const item = selectedMaterials[id];
                const inputJumlah = document.getElementById(`jumlah-diambil-${id}`);
                const value = parseInt(inputJumlah.value);

                if (isNaN(value) || value <= 0 || value > item.stok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: `Jumlah diambil untuk material "${item.nama}" tidak valid. Harap isi dengan angka antara 1 sampai ${item.stok}.`,
                    });
                    isValid = false;
                    break;
                }
                materialsToSubmit.push({ id: item.id, jumlah_diambil: value });
            }
            
            if (!isValid) return;

            Swal.fire({
                title: 'Konfirmasi Pengajuan',
                text: "Apakah Anda yakin ingin mengajukan UPP ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tambah Pengajuan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = {
                        noSurat: noSurat,
                        tanggal: tanggal,
                        tahapan: tahapan,
                        pjUser: pjUser,
                        keterangan: keterangan,
                        materials: materialsToSubmit,
                    };
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route('upp-material.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Terjadi kesalahan saat menyimpan data.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan data.';
                            if (data.errors) {
                                errorMessage = Object.values(data.errors).flat().join('<br>');
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                html: errorMessage,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Terjadi Kesalahan',
                            text: error.message || 'Gagal terhubung ke server.',
                            icon: 'error'
                        });
                    });
                }
            });
        });

        // Set tanggal pengajuan default ke hari ini
        document.getElementById('tanggal').value = new Date().toISOString().slice(0, 10);
    });
</script>
@endpush