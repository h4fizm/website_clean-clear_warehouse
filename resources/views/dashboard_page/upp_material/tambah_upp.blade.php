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
                <form id="pengajuanForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="noSurat" class="form-label">No. Surat</label>
                            <input type="text" class="form-control" id="noSurat" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahapan" class="form-label">Tahapan</label>
                            <input type="text" class="form-control" id="tahapan" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pjUser" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="pjUser" required>
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
                        <textarea class="form-control" id="keterangan" rows="8"></textarea>
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
    let selectedMaterials = {}; // Menyimpan material yang dipilih

    document.addEventListener('DOMContentLoaded', function() {
        // Data dummy material untuk modal
        const materialDummy = [
            { id: 1, nama: 'Gas LPG 3 Kg', kode: 'LPG3001', stok: 150 },
            { id: 2, nama: 'Bright Gas 12 Kg', kode: 'BG1202', stok: 90 },
            { id: 3, nama: 'Pelumas Fastron', kode: 'PFAS03', stok: 0 },
            { id: 4, nama: 'Aspal Curah', kode: 'ASPC04', stok: 110 },
            { id: 5, nama: 'Avtur', kode: 'AVTR05', stok: 0 },
            { id: 6, nama: 'Pertalite', kode: 'PRTL06', stok: 95 },
            { id: 7, nama: 'Pertamina Dex', kode: 'PDEX07', stok: 170 },
            { id: 8, nama: 'Minyak Tanah', kode: 'MINT08', stok: 140 },
            { id: 9, nama: 'Asphalt Pen 60/70', kode: 'AP60709', stok: 160 },
            { id: 10, nama: 'Bitumen', kode: 'BITU10', stok: 130 },
            { id: 11, nama: 'Gas LPG 3 Kg (Extra)', kode: 'LPG311', stok: 200 },
            { id: 12, nama: 'Elpiji Industri', kode: 'IND012', stok: 80 },
        ];
        
        // Inisialisasi CKEditor 5
        ClassicEditor
            .create( document.querySelector( '#keterangan' ) )
            .then( editor => {
                keteranganEditor = editor;
            })
            .catch( error => {
                console.error( 'Ada kesalahan saat menginisialisasi CKEditor:', error );
            });

        // Tampilkan daftar material di modal
        function renderMaterialList(query = '') {
            const materialList = document.getElementById('materialList');
            materialList.innerHTML = '';
            
            const filteredMaterials = materialDummy.filter(item => 
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
        
        // Render tabel material yang dipilih
        function renderSelectedMaterials() {
            const tbody = document.getElementById('selectedMaterialsBody');
            tbody.innerHTML = '';
            
            if (Object.keys(selectedMaterials).length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Silakan pilih material yang akan diajukan.</td></tr>';
                return;
            }
            
            Object.values(selectedMaterials).forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <h6 class="mb-0 text-sm">${item.nama}</h6>
                        <p class="text-xs text-secondary mb-0">Kode: ${item.kode}</p>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" value="${item.stok}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm jumlah-diambil" data-id="${item.id}" value="${item.jumlah_diambil || ''}" min="1" max="${item.stok}" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-id="${item.id}">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Tambahkan event listener untuk input jumlah
            document.querySelectorAll('.jumlah-diambil').forEach(input => {
                input.addEventListener('input', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    const value = parseInt(this.value);
                    if (value > 0 && value <= selectedMaterials[id].stok) {
                        selectedMaterials[id].jumlah_diambil = value;
                    }
                });
            });

            // Tambahkan event listener untuk tombol hapus
            document.querySelectorAll('.remove-material-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    delete selectedMaterials[id];
                    renderSelectedMaterials();
                });
            });
        }
        
        // Event listener untuk tombol "Tambah Material Terpilih"
        document.getElementById('addSelectedMaterialsBtn').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#materialList input[type="checkbox"]:checked');
            checkboxes.forEach(checkbox => {
                const id = parseInt(checkbox.value);
                const material = materialDummy.find(item => item.id === id);
                if (material && !selectedMaterials[id]) {
                    selectedMaterials[id] = { ...material, jumlah_diambil: '' };
                }
            });
            
            renderSelectedMaterials();
            const materialModal = bootstrap.Modal.getInstance(document.getElementById('materialModal'));
            materialModal.hide();
        });

        // Event listener untuk search di modal
        document.getElementById('modalSearchInput').addEventListener('input', function() {
            renderMaterialList(this.value);
        });

        // Event listener untuk saat modal dibuka
        document.getElementById('materialModal').addEventListener('show.bs.modal', function() {
            renderMaterialList(''); // Render ulang daftar setiap kali modal dibuka
        });

        // Handle form submission with Swal confirmation
        document.getElementById('pengajuanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi: pastikan ada material yang dipilih
            if (Object.keys(selectedMaterials).length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Material Belum Dipilih',
                    text: 'Silakan pilih setidaknya satu material untuk diajukan.',
                });
                return;
            }
            
            // Validasi: pastikan semua jumlah diisi dengan benar
            for (const id in selectedMaterials) {
                const item = selectedMaterials[id];
                if (!item.jumlah_diambil || item.jumlah_diambil <= 0 || item.jumlah_diambil > item.stok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: `Jumlah diambil untuk material ${item.nama} tidak valid.`,
                    });
                    return;
                }
            }
            
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
                        no_surat: document.getElementById('noSurat').value,
                        tanggal: document.getElementById('tanggal').value,
                        tahapan: document.getElementById('tahapan').value,
                        penanggung_jawab: document.getElementById('pjUser').value,
                        keterangan: keteranganEditor.getData(),
                        materials: Object.values(selectedMaterials).map(item => ({
                            id: item.id,
                            nama: item.nama,
                            kode: item.kode,
                            jumlah_diambil: item.jumlah_diambil
                        }))
                    };

                    console.log('Data yang akan dikirim:', formData);
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Pengajuan UPP berhasil ditambahkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ url('/upp-material') }}";
                    });
                }
            });
        });

        // Render tabel material saat halaman pertama kali dimuat
        renderSelectedMaterials();
    });
</script>
@endpush