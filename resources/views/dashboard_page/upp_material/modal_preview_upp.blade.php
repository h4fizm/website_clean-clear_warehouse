{{-- resources/views/dashboard_page/upp_material/modal_preview_upp.blade.php --}}

@if(isset($upp))
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 text-sm"> {{-- Tambahkan text-sm di sini untuk mengecilkan teks secara global --}}
                <p class="mb-1"><small class="text-muted text-xxs">No. Surat:</small><br>
                    <strong>{{ $upp['no_surat'] }}</strong></p>
                <p class="mb-1"><small class="text-muted text-xxs">Tanggal Pengajuan:</small><br>
                    <strong>{{ \Carbon\Carbon::parse($upp['tgl_buat'])->translatedFormat('l, d F Y') }}</strong></p>
                <p class="mb-1"><small class="text-muted text-xxs">Status:</small><br>
                    @php
                        $statusText = strtolower($upp['materials'][0]->status) === 'done' ? 'Done' : 'Proses';
                        $statusColor = strtolower($upp['materials'][0]->status) === 'done' ? 'bg-gradient-success' : 'bg-gradient-warning';
                    @endphp
                    <span class="badge {{ $statusColor }} text-white font-weight-bold">{{ $statusText }}</span>
                </p>

                {{-- Keterangan dipindah ke sini --}}
                <p class="mb-1"><small class="text-muted text-xxs">Keterangan:</small><br>
                    <span class="d-block">{!! $upp['keterangan'] ?? '-' !!}</span></p> {{-- Gunakan span d-block agar paragraf dari CKEditor tetap di baris baru --}}
            </div>
        </div>
        <h6 class="text-sm">Daftar Material yang Diajukan:</h6> {{-- Kecilkan ukuran h6 --}}
        <div class="mb-4 table-responsive shadow-sm rounded"> {{-- Tambahkan shadow-sm dan rounded untuk efek timbul --}}
            <table class="table table-striped align-items-center mb-0 border rounded"> {{-- Gunakan table-striped dan table-bordered --}}
                <thead class="bg-light"> {{-- Warna header abu-abu cerah --}}
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder py-2 ps-3 border-bottom">Nama Material & Kode</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder py-2 text-center border-bottom">Jumlah Diajukan (Pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upp['materials'] as $transaction)
                    <tr>
                        <td class="py-2 ps-3 border-bottom"> {{-- Tambahkan border-bottom --}}
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm font-weight-bold">{{ $transaction->item->nama_material }}</h6>
                                <p class="text-xs text-muted mb-0">Kode: {{ $transaction->item->kode_material }}</p>
                            </div>
                        </td>
                        <td class="text-center py-2 border-bottom"> {{-- Tambahkan border-bottom --}}
                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->jumlah }} pcs</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-danger">
        Data UPP tidak ditemukan.
    </div>
@endif

{{-- Footer --}}
@if(strtolower($upp['materials'][0]->status) !== 'done')
    <div class="modal-footer d-flex justify-content-end border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        {{-- Tombol Lakukan Pemusnahan dengan desain konsisten --}}
        <a href="{{ route('upp-material.edit', ['no_surat' => $upp['no_surat']]) }}" class="btn btn-danger">
            <i class="fas fa-trash-alt me-1"></i> Lakukan Pemusnahan
        </a>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmPemusnahanBtn = document.getElementById('confirmPemusnahanBtn');

        confirmPemusnahanBtn.addEventListener('click', function() {
            const noSurat = this.getAttribute('data-no-surat');

            Swal.fire({
                title: 'Konfirmasi Pemusnahan',
                text: "Apakah Anda yakin ingin memusnahkan material untuk UPP ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lakukan Pemusnahan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lakukan permintaan POST/PUT ke endpoint pemusnahan
                    // Contoh menggunakan fetch API
                    fetch(`{{ url('/upp-material/update') }}/${noSurat}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            // Kirim data yang diperlukan untuk update/pemusnahan
                            _method: 'PUT',
                            status: 'done', // Contoh: ubah status
                            tahapan: 'pemusnahan',
                            tanggal_pemusnahan: new Date().toISOString().slice(0, 19).replace('T', ' '),
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                window.location.reload(); // Reload halaman setelah sukses
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal!', 'Gagal memuat data. Periksa koneksi atau coba lagi.', 'error');
                    });
                }
            });
        });
    });
</script>