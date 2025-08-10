@extends('dashboard_page.main')
@section('title', 'Preview Dokumen UPP')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Preview Surat Keterangan Pemusnahan Material</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="materialName" class="form-label">Nama Material</label>
                            <input type="text" class="form-control" id="materialName" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="bptName" class="form-label">Nama BPT</label>
                            <input type="text" class="form-control" id="bptName" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cabangName" class="form-label">Sales Area/Region</label>
                            <input type="text" class="form-control" id="cabangName" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Pemusnahan</label>
                            <input type="date" class="form-control" id="tanggal" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pjUser" class="form-label">Penanggung Jawab Sales Area/Region</label>
                        <input type="text" class="form-control" id="pjUser" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan-preview" class="form-label">Keterangan Pemusnahan</label>
                        <div id="keterangan-preview" class="border p-2 rounded" style="min-height: 100px;"></div>
                    </div>

                    <div class="mb-3">
                        <label for="aktivitas-preview" class="form-label">Aktivitas Pemusnahan</label>
                        <div id="aktivitas-preview" class="border p-2 rounded" style="min-height: 100px;"></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ url('/aktivitas-upp') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dummy untuk contoh preview
        const dataPreview = {
            materialName: 'Gas LPG 3 Kg',
            bptName: 'BPT Jakarta Timur A',
            cabangName: 'SA Jambi',
            tanggal: '2025-08-10',
            pjUser: 'Adi Wijaya (Penanggung Jawab SA Jambi)',
            keterangan: '<p>Telah dilakukan pemusnahan material Gas LPG 3 Kg sejumlah 100 pcs karena kondisi tabung yang tidak layak pakai (rusak, berkarat, dan bocor).</p>',
            aktivitas: '<p>Proses pemusnahan dilakukan dengan cara mengeluarkan seluruh isi gas dari tabung di area terbuka, kemudian tabung-tabung tersebut dihancurkan agar tidak dapat digunakan kembali.</p>'
        };

        // Isi form dengan data preview
        document.getElementById('materialName').value = dataPreview.materialName;
        document.getElementById('bptName').value = dataPreview.bptName;
        document.getElementById('cabangName').value = dataPreview.cabangName;
        document.getElementById('tanggal').value = dataPreview.tanggal;
        document.getElementById('pjUser').value = dataPreview.pjUser;

        // Isi konten div dengan data HTML dari dummy
        document.getElementById('keterangan-preview').innerHTML = dataPreview.keterangan;
        document.getElementById('aktivitas-preview').innerHTML = dataPreview.aktivitas;
    });
</script>
@endpush
@endsection