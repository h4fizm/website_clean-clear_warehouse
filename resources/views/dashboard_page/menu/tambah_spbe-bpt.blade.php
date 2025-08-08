@extends('dashboard_page.main')
@section('title', 'Tambah SPBE/BPT')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header pb-0 p-3">
                <h3 class="mb-0">Form Tambah Data SPBE/BPT</h3>
            </div>
            <div class="card-body p-3">
                <form id="addSpbeBptForm" class="row g-3">
                    {{-- Semua elemen form diubah menjadi col-12 agar menjadi satu kolom --}}
                    <div class="col-12">
                        <label for="namaSpbeBpt" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control" id="namaSpbeBpt" required>
                    </div>
                    <div class="col-12">
                        <label for="kodePlant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control" id="kodePlant" required>
                    </div>
                    <div class="col-12">
                        <label for="jenisSpbeBpt" class="form-label">Jenis</label>
                        <div class="d-flex align-items-center mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisSpbeBpt" id="jenis-spbe" value="SPBE" required>
                                <label class="form-check-label" for="jenis-spbe">SPBE</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisSpbeBpt" id="jenis-bpt" value="BPT">
                                <label class="form-check-label" for="jenis-bpt">BPT</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="saRegion" class="form-label">SA Region</label>
                        <select class="form-select" id="saRegion" required>
                            <option value="">Pilih SA Region</option>
                            <option value="SA Jambi">SA Jambi</option>
                            <option value="SA Bengkulu">SA Bengkulu</option>
                            <option value="SA Lampung">SA Lampung</option>
                            <option value="SA Sumsel">SA Sumsel</option>
                            <option value="SA Babel">SA Babel</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="namaProvinsi" class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control" id="namaProvinsi" required>
                    </div>
                    <div class="col-12">
                        <label for="namaKabupaten" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control" id="namaKabupaten" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
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
        const form = document.getElementById('addSpbeBptForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form values
            const nama = document.getElementById('namaSpbeBpt').value;
            const kode = document.getElementById('kodePlant').value;
            const jenis = document.querySelector('input[name="jenisSpbeBpt"]:checked').value;
            const saRegion = document.getElementById('saRegion').value;
            const provinsi = document.getElementById('namaProvinsi').value; // Ambil nilai provinsi
            const kabupaten = document.getElementById('namaKabupaten').value;

            // Simple validation
            if (!nama || !kode || !jenis || !saRegion || !provinsi || !kabupaten) { // Tambah validasi provinsi
                Swal.fire('Gagal!', 'Harap lengkapi semua kolom dengan benar.', 'error');
                return;
            }

            // Simulate data submission
            console.log('Data yang akan disimpan:', {
                nama,
                kode,
                jenis,
                saRegion,
                provinsi, // Masukkan provinsi ke log
                kabupaten
            });

            Swal.fire({
                title: 'Berhasil!',
                text: 'Data SPBE/BPT baru berhasil disimpan.',
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