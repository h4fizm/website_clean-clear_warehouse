@extends('dashboard_page.main')
@section('title', 'Daftar Pengguna')
@section('content')

{{-- PEMBUNGKUS UTAMA UNTUK TATA LETAK YANG BENAR --}}
<div class="row">

    {{-- Welcome Section --}}
    <div class="col-12 mb-3">
        <div class="card p-4 position-relative welcome-card">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
                <div class="w-100 order-md-1 text-center text-md-start">
                    <h4 class="mb-1 fw-bold" id="summary-title">Daftar Pengguna</h4>
                    <p class="mb-2 opacity-8" id="summary-text">Kelola data pengguna, hak akses, dan informasi akun.</p>
                </div>
                <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                    <img src="{{ asset('dashboard_template/assets/img/icon.png') }}" alt="Pertamina Patra Niaga Logo" class="welcome-card-icon" style="height: 60px;">
                </div>
            </div>
            <div class="welcome-card-background"></div>
        </div>
    </div>

    {{-- Kolom untuk Tabel Pengguna --}}
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h4>Tabel Data Pengguna</h4>
                    <h6>Daftar seluruh pengguna yang terdaftar dalam sistem.</h6>
                </div>
                @can('manage user')
                {{-- PERBAIKAN: Menyederhanakan kelas untuk menghilangkan bug --}}
                <button type="button" class="btn btn-primary ms-auto" style="height: 38px;" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                    <i class="fas fa-plus me-2"></i> Tambah Pengguna
                </button>
                @endcan
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <div class="px-4 pt-3">
                    @if(session('success'))
                        <div class="alert alert-success text-white" role="alert">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger text-white" role="alert">{{ session('error') }}</div>
                    @endif

                    {{-- Menampilkan daftar error validasi yang spesifik --}}
                    @if ($errors->any())
                    <div class="alert alert-danger text-white" role="alert">
                        <strong class="d-block">Gagal! Terdapat beberapa kesalahan:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Pengguna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Role</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-gradient-info text-white text-xs">{{ $user->getRoleNames()->first() ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @can('manage user')
                                        <button type="button" class="btn btn-sm btn-warning text-white" title="Edit" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white delete-btn" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Data Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- BLOK PAGINATION FINAL --}}
                @if ($users->hasPages())
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $users->lastPage();
                                $current = $users->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->url(1) }}">&laquo;</a>
                            </li>
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}">&lsaquo;</a>
                            </li>
                            @php $wasGap = false; @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    @if (!$wasGap)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor
                            <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}">&rsaquo;</a>
                            </li>
                            <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->url($total) }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK TAMBAH PENGGUNA --}}
<div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahUserModalLabel">Tambah Pengguna Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pengguna.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <div class="form-floating mb-3">
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" placeholder=" " value="{{ old('name') }}">
            <label for="name">Nama Pengguna</label>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" placeholder=" " value="{{ old('email') }}">
            <label for="email">Email</label>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder=" ">
            <label for="password">Password</label>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password_confirmation" 
                   name="password_confirmation" placeholder=" ">
            <label for="password_confirmation">Konfirmasi Password</label>
          </div>

          <div class="form-floating mb-3">
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
              <option value="">Pilih Role</option>
              @foreach ($roles as $role)
                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                  {{ $role->name }}
                </option>
              @endforeach
            </select>
            <label for="role">Role</label>
            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL UNTUK EDIT PENGGUNA --}}
@foreach ($users as $user)
<div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" 
     aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel-{{ $user->id }}">Edit Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pengguna.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">

          <div class="form-floating mb-3">
            <input type="text" class="form-control @if($errors->has('name') && session('error_user_id') == $user->id) is-invalid @endif" 
                   id="name-{{ $user->id }}" name="name" placeholder=" " 
                   value="{{ old('name', $user->name) }}">
            <label for="name-{{ $user->id }}">Nama Pengguna</label>
            @if($errors->has('name') && session('error_user_id') == $user->id) 
              <div class="invalid-feedback">{{ $errors->first('name') }}</div> 
            @endif
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control @if($errors->has('email') && session('error_user_id') == $user->id) is-invalid @endif" 
                   id="email-{{ $user->id }}" name="email" placeholder=" " 
                   value="{{ old('email', $user->email) }}">
            <label for="email-{{ $user->id }}">Email</label>
            @if($errors->has('email') && session('error_user_id') == $user->id) 
              <div class="invalid-feedback">{{ $errors->first('email') }}</div> 
            @endif
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control @if($errors->has('password') && session('error_user_id') == $user->id) is-invalid @endif" 
                   id="password-{{ $user->id }}" name="password" placeholder=" ">
            <label for="password-{{ $user->id }}">Password Baru (opsional)</label>
            @if($errors->has('password') && session('error_user_id') == $user->id) 
              <div class="invalid-feedback">{{ $errors->first('password') }}</div> 
            @endif
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password_confirmation-{{ $user->id }}" 
                   name="password_confirmation" placeholder=" ">
            <label for="password_confirmation-{{ $user->id }}">Konfirmasi Password</label>
          </div>

          <div class="form-floating mb-3">
            <select class="form-select @if($errors->has('role') && session('error_user_id') == $user->id) is-invalid @endif" 
                    id="role-{{ $user->id }}" name="role" required>
              @foreach ($roles as $role)
                <option value="{{ $role->name }}" 
                        {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                  {{ $role->name }}
                </option>
              @endforeach
            </select>
            <label for="role-{{ $user->id }}">Role</label>
            @if($errors->has('role') && session('error_user_id') == $user->id) 
              <div class="invalid-feedback">{{ $errors->first('role') }}</div> 
            @endif
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk membuka kembali modal jika ada error validasi --}}
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('error_user_id'))
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal-{{ session('error_user_id') }}'));
            editModal.show();
        @elseif (!old('_method'))
            var tambahModal = new bootstrap.Modal(document.getElementById('tambahUserModal'));
            tambahModal.show();
        @endif
    });
</script>
@endif

{{-- Script untuk konfirmasi hapus dengan SweetAlert --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); 
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pengguna ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); 
                    }
                });
            });
        });
    });
</script>
@endpush

@endsection