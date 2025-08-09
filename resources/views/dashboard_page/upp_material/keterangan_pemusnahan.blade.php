@extends('dashboard_page.main')
@section('title', 'Form Keterangan Pemusnahan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Form Surat Keterangan Pemusnahan Material</h5>
            </div>
            <div class="card-body">
                <form id="pemusnahanForm">
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
                            <input type="date" class="form-control" id="tanggal" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pjUser" class="form-label">Penanggung Jawab Sales Area/Region</label>
                        <input type="text" class="form-control" id="pjUser" value="Adi Wijaya (Penanggung Jawab)" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan Pemusnahan</label>
                        <textarea class="form-control" id="keterangan" rows="8"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="aktivitas" class="form-label">Aktivitas Pemusnahan</label>
                        <textarea class="form-control" id="aktivitas" rows="8"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        {{-- Button Batal di kiri --}}
                        <a href="{{ url('/upp-material') }}" class="btn btn-secondary">Batal</a>
                        {{-- Button Submit di kanan --}}
                        <button type="submit" class="btn btn-danger">Submit Keterangan Pemusnahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- CKEditor 5 CDN (Classic build) --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let keteranganEditor;
    let aktivitasEditor;

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi CKEditor 5 untuk Keterangan
        ClassicEditor
            .create( document.querySelector( '#keterangan' ) )
            .then( editor => {
                keteranganEditor = editor;
                console.log( 'CKEditor untuk Keterangan siap.' );
            })
            .catch( error => {
                console.error( 'Ada kesalahan saat menginisialisasi CKEditor untuk Keterangan:', error );
            });

        // Inisialisasi CKEditor 5 untuk Aktivitas
        ClassicEditor
            .create( document.querySelector( '#aktivitas' ) )
            .then( editor => {
                aktivitasEditor = editor;
                console.log( 'CKEditor untuk Aktivitas siap.' );
            })
            .catch( error => {
                console.error( 'Ada kesalahan saat menginisialisasi CKEditor untuk Aktivitas:', error );
            });

        // Autofill Tanggal dengan tanggal hari ini
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        document.getElementById('tanggal').value = `${yyyy}-${mm}-${dd}`;

        // Ambil data dari query parameter URL
        const urlParams = new URLSearchParams(window.location.search);
        const materialId = urlParams.get('material_id'); 
        const materialNama = urlParams.get('material_nama'); 
        const namaBPT = urlParams.get('nama_bpt');
        const namaCabang = urlParams.get('nama_cabang'); // This variable holds the SA/Region name
        const stokMaterial = urlParams.get('stok_material'); 

        // Isi form autofill
        document.getElementById('materialName').value = materialNama ? decodeURIComponent(materialNama) : 'Data Tidak Ditemukan';
        document.getElementById('bptName').value = namaBPT ? decodeURIComponent(namaBPT) : 'Data Tidak Ditemukan';
        document.getElementById('cabangName').value = namaCabang ? decodeURIComponent(namaCabang) : 'Data Tidak Ditemukan';
        
        // Update label PJ to be consistent
        document.querySelector('label[for="pjUser"]').textContent = `Penanggung Jawab ${document.getElementById('cabangName').value}`;

        // Handle form submission with Swal confirmation
        document.getElementById('pemusnahanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Pemusnahan',
                text: "Apakah Anda yakin ingin menyelesaikan proses pemusnahan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Dapatkan konten dari CKEditor
                    const keteranganContent = keteranganEditor.getData();
                    const aktivitasContent = aktivitasEditor.getData();

                    // Log data to console (simulasi kirim ke backend)
                    console.log('Tanggal:', document.getElementById('tanggal').value);
                    console.log('Penanggung Jawab:', document.getElementById('pjUser').value);
                    console.log('Nama Material:', document.getElementById('materialName').value);
                    console.log('Nama BPT:', document.getElementById('bptName').value);
                    console.log('Sales Area/Region:', document.getElementById('cabangName').value);
                    console.log('Keterangan:', keteranganContent);
                    console.log('Aktivitas:', aktivitasContent);

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Keterangan pemusnahan berhasil disubmit.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ url('/upp-material') }}";
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection