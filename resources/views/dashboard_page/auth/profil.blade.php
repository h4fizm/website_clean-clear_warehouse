@extends('dashboard_page.main')
@section('title', 'Profil Pengguna')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Profil Pengguna</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="nama" value="Muhammad Amir" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Pengguna</label>
                            <input type="email" class="form-control" id="email" value="amir.m@gmail.com" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                        <div class="col-md-6">
                            <label for="confirm-password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm-password" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" required>
                            <option value="Manager" selected>Manager</option>
                            <option value="Admin P.Layang">Admin P.Layang</option>
                            <option value="User P.Layang">User P.Layang</option>
                            <option value="User SA Jambi">User SA Jambi</option>
                            <option value="User SA Bengkulu">User SA Bengkulu</option>
                            <option value="User SA Lampung">User SA Lampung</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const role = document.getElementById('role').value;

            // Simple validation
            if (password && password !== confirmPassword) {
                Swal.fire('Gagal!', 'Password dan Konfirmasi Password tidak cocok.', 'error');
                return;
            }

            // In a real application, you would send this data to a server via AJAX
            console.log('Data yang dikirim:', { nama, email, password, role });

            Swal.fire({
                title: 'Berhasil!',
                text: 'Perubahan profil berhasil disimpan.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
    });
</script>
@endpush
@endsection