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
                <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    {{-- WADAH UNTUK ALERT BOOTSTRAP --}}
                    <div class="alert alert-danger d-none" id="validation-alert" role="alert">
                        Terdapat kesalahan pada data yang Anda masukkan. Silahkan periksa kembali.
                    </div>

                    @csrf
                    @method('PATCH')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Pengguna</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                    </div>

                    {{-- ================================================================= --}}
                    {{--     PERUBAHAN: KONDISI UNTUK MENAMPILKAN ATAU SEMBUNYIKAN ROLE     --}}
                    {{-- ================================================================= --}}
                    @role('Manager')
                        {{-- Jika user adalah Manager, tampilkan dropdown --}}
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    @else
                        {{-- Jika bukan Manager, kirim role saat ini secara tersembunyi --}}
                        <input type="hidden" name="role" value="{{ $user->getRoleNames()->first() }}">
                    @endrole
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Bagian script JavaScript tidak ada perubahan --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;

        try {
            const response = await fetch("{{ route('profile.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    displayErrors(result.errors);
                    const alertBox = document.getElementById('validation-alert');
                    if (alertBox) {
                        alertBox.classList.remove('d-none');
                    }
                    Swal.fire('Gagal!', 'Data yang Anda masukkan tidak valid. Silahkan periksa kembali form.', 'error');
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan pada server.');
                }
            } else {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Simpan Perubahan';
        }
    });

    function displayErrors(errors) {
        for (const field in errors) {
            const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field][0];
                }
            }
        }
    }

    function clearErrors() {
        const alertBox = document.getElementById('validation-alert');
        if (alertBox) {
            alertBox.classList.add('d-none');
        }
        document.querySelectorAll('.is-invalid').forEach(function(element) {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(function(element) {
            element.textContent = '';
        });
    }
});
</script>
@endpush
@endsection