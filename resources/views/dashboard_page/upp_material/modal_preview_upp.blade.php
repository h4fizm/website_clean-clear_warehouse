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
        <button type="button" class="btn btn-danger" id="confirmPemusnahanBtn" data-no-surat="{{ $upp['no_surat'] }}">
            <i class="fas fa-trash-alt me-1"></i> Lakukan Pemusnahan
        </button>
    </div>
@endif