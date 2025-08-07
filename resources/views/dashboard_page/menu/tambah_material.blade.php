@extends('dashboard_page.main')
@section('title', 'Tambah Material')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header pb-0 p-3">
                <h3 class="mb-0">Form Tambah Data Material</h3>
            </div>
            <div class="card-body p-3">
                <form id="addMaterialForm" class="row g-3">
                    <div class="col-12">
                        <label for="namaMaterial" class="form-label">Nama Material</label>
                        <input type="text" class="form-control" id="namaMaterial" required>
                    </div>
                    <div class="col-12">
                        <label for="kodeMaterial" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" id="kodeMaterial" required>
                    </div>
                    <div class="col-12">
                        <label for="spbeBptMaterial" class="form-label">Nama SPBE/BPT</label>
                        <select class="form-select" id="spbeBptMaterial" required>
                            <option value="">Pilih SPBE/BPT</option>
                            <option value="SPBE Sukamaju">SPBE Sukamaju</option>
                            <option value="BPT Sejahtera">BPT Sejahtera</option>
                            <option value="SPBE Mandiri">SPBE Mandiri</option>
                            <option value="BPT Jaya Abadi">BPT Jaya Abadi</option>
                            <option value="SPBE Maju Bersama">SPBE Maju Bersama</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="totalStokMaterial" class="form-label">Total Stok</label>
                        <input type="number" class="form-control" id="totalStokMaterial" min="0" value="0" required>
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
            e.preventDefault();

            // Get form values
            const nama = document.getElementById('namaMaterial').value;
            const kode = document.getElementById('kodeMaterial').value;
            const spbeBpt = document.getElementById('spbeBptMaterial').value;
            const stok = parseInt(document.getElementById('totalStokMaterial').value);

            // Simple validation
            if (!nama || !kode || !spbeBpt || isNaN(stok) || stok < 0) {
                Swal.fire('Gagal!', 'Harap lengkapi semua kolom dengan benar.', 'error');
                return;
            }

            // Simulate data submission
            console.log('Data material yang akan disimpan:', {
                nama,
                kode,
                spbeBpt,
                stok
            });

            Swal.fire({
                title: 'Berhasil!',
                text: 'Data material baru berhasil disimpan.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                form.reset();
            });
        });
    });
</script>
@endpush

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