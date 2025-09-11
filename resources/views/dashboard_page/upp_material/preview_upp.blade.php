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
                                value="{{ $upp['no_surat'] }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal"
                                value="{{ \Carbon\Carbon::parse($upp['tgl_buat'])->format('Y-m-d') }}" readonly>
                        </div>
                    </div>

                   <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahapan" class="form-label">Tahapan</label>
                            <input type="text" class="form-control" id="tahapan"
                                value="{{ $tahapan }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pjUser" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="pjUser" name="pj_user"
                                value="{{ $pjUser }}" required>
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
                                        {{-- Perbaikan: Loop data material langsung dari controller --}}
                                        @forelse ($upp['materials'] as $material)
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0 text-sm">{{ $material['nama_material'] }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Kode: {{ $material['kode_material'] }}</p>
                                                </td>
                                                <td><input type="number" class="form-control form-control-sm" value="{{ $material['stok_akhir_pusat'] }}" readonly></td>
                                                <td><input type="number" class="form-control form-control-sm jumlah-dimusnahkan" data-id="{{ $material['id'] }}" value="{{ $material['jumlah_diajukan'] }}" min="1" required></td>
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
                        <button type="submit" class="btn btn-success">Selesai & Proses Pemusnahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Perbaikan: Menghapus modal yang tidak diperlukan karena tidak ada fungsi "Pilih Material" --}}
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let keteranganEditor;
let aktivitasEditor;

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

    // Perbaikan: Hapus logika "fetchMaterials" karena tidak diperlukan
    // Hapus juga event listeners terkait modal material

    // --- Submit Form dengan data yang sudah dimuat ---
    document.getElementById('pengajuanForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const noSurat = document.getElementById('noSurat').value;
        const tanggalPengajuan = document.getElementById('tanggal').value;
        const tahapan = document.getElementById('tahapan').value;
        const pjUser = document.getElementById('pjUser').value;
        const tanggalPemusnahan = document.getElementById('tanggalPemusnahan').value;
        const aktivitasPemusnahan = aktivitasEditor.getData().trim();
        const keterangan = keteranganEditor.getData().trim();
        
        // Pengecekan validasi form
        if (!noSurat || !tanggalPengajuan || !tahapan || !pjUser || !tanggalPemusnahan || !aktivitasPemusnahan || !keterangan) {
            return Swal.fire('Error', 'Semua form data harus terisi.', 'error');
        }
        
        const materialsData = [];
        const rows = document.querySelectorAll('#selectedMaterialsBody tr');
        if (rows.length === 0) {
             return Swal.fire('Error', 'Tidak ada material yang diajukan.', 'error');
        }
        
        // Pengecekan stok material saat submit
        let isValid = true;
        rows.forEach(row => {
            const inputJumlah = row.querySelector('.jumlah-dimusnahkan');
            const jumlahDimusnahkan = parseInt(inputJumlah.value);
            const id = parseInt(inputJumlah.dataset.id);
            const stokSaatIni = parseInt(row.querySelector('td:nth-child(2) input').value); // Ambil stok saat ini dari input
            
            if (jumlahDimusnahkan <= 0 || jumlahDimusnahkan > stokSaatIni) {
                const materialName = row.querySelector('h6').textContent;
                Swal.fire('Error', `Jumlah yang ingin dimusnahkan (${jumlahDimusnahkan}) untuk ${materialName} tidak valid. Stok yang tersedia: ${stokSaatIni}.`, 'error');
                isValid = false;
                return;
            }
            
            materialsData.push({
                item_id: id,
                jumlah: jumlahDimusnahkan
            });
        });

        if (!isValid) return;

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
                    materials: materialsData, // Kirim data material yang sudah diolah
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
});
</script>
@endpush