@extends('dashboard_page.main')
@section('title', 'Tambah Material')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header pb-0 p-3">
                <h3 class="mb-0">Form Tambah Data Material</h3>
                <p class="text-muted mb-0"><small>Menambah data material ke P.Layang Pusat</small></p>
            </div>
            <div class="card-body p-3">
                {{-- Form yang sudah dimodifikasi untuk AJAX --}}
                <form id="addMaterialForm" action="{{ route('pusat.store') }}" method="POST" class="row g-3">
                    @csrf {{-- Token keamanan Laravel, wajib untuk form POST --}}

                    <div class="col-12">
                        <label for="namaMaterial" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="namaMaterial" name="nama_material" required>
                    </div>
                    <div class="col-12">
                        <label for="kodeMaterial" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="kodeMaterial" name="kode_material" required>
                    </div>
                    <div class="col-12">
                        <label for="totalStokMaterial" class="form-label">Total Stok Awal</label>
                        <input type="number" class="form-control" id="totalStokMaterial" name="total_stok" min="0" value="0" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Simpan Material</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addMaterialForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah form submit cara biasa

        const formData = new FormData(form);
        const url = form.getAttribute('action');

        // Kirim data menggunakan Fetch API
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            // Cek jika response tidak OK (misal: error validasi 422)
            if (!response.ok) {
                return response.json().then(data => {
                    // Lemparkan error agar ditangkap oleh .catch()
                    throw data; 
                });
            }
            // Jika response OK, lanjutkan ke .then() berikutnya
            return response.json(); 
        })
        .then(data => {
            // Ini blok untuk response SUKSES (status 2xx)
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Arahkan ke halaman index pusat setelah user menekan OK
                        window.location.href = data.redirect_url;
                    }
                });
            }
        })
        .catch(errorData => {
            // Ini blok untuk response GAGAL (misal: validasi gagal, server error)
            let errorMessages = '';
            if (errorData.errors) {
                // Format pesan validasi dari Laravel
                errorMessages = '<ul class="list-unstyled text-start">';
                for (const key in errorData.errors) {
                    errorData.errors[key].forEach(message => {
                        errorMessages += `<li>${message}</li>`;
                    });
                }
                errorMessages += '</ul>';
            } else {
                // Pesan error umum jika format tidak sesuai
                errorMessages = errorData.message || 'Terjadi kesalahan. Periksa kembali data Anda.';
            }

            Swal.fire({
                title: 'Gagal!',
                html: errorMessages, // Gunakan html agar bisa menampilkan list
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        });
    });
});
</script>
@endpush

{{-- Bagian style tidak perlu diubah --}}
<style>
    .card-header h5 {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #344767;
    }
    
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    @media (max-width: 767.98px) {
        .card-header h5 {
            text-align: center;
        }
        .form-check-inline {
            margin-right: 1.5rem;
        }
    }
</style>
@endsection