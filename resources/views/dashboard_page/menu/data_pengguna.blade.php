@extends('dashboard_page.main')
@section('title', 'Daftar Pengguna')
@section('content')

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

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h4>Tabel Data Pengguna</h4>
                    <h6>Daftar seluruh pengguna yang terdaftar dalam sistem.</h6>
                </div>
                @can('manage user')
                <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center ms-auto" style="height: 38px;">
                    <i class="fas fa-plus me-2"></i> Tambah Pengguna
                </a>
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
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                       {{-- THEAD TIDAK BERUBAH --}}
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Pengguna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Role</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        {{-- TBODY TIDAK BERUBAH --}}
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
                                        <a href="#" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger text-white" title="Hapus"><i class="fas fa-trash-alt"></i></a>
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

                {{-- ================================================================= --}}
                {{--         BLOK PAGINATION FINAL DENGAN SEMUA LOGIKA BARU            --}}
                {{-- ================================================================= --}}
                @if ($users->hasPages())
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $users->lastPage();
                                $current = $users->currentPage();
                                // Atur berapa nomor halaman di kiri dan kanan halaman aktif
                                $window = 1; 
                            @endphp

                            {{-- Tombol Pertama (<<) --}}
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->url(1) }}">&laquo;</a>
                            </li>

                            {{-- Tombol Previous (<) --}}
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}">&lsaquo;</a>
                            </li>

                            {{-- Looping Nomor Halaman & Elipsis --}}
                            @php
                                $wasGap = false;
                            @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    {{-- Jika ini halaman yang harus ditampilkan --}}
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    {{-- Jika ini adalah gap/jarak --}}
                                    @if (!$wasGap)
                                        {{-- Tampilkan elipsis hanya sekali per gap --}}
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor

                            {{-- Tombol Next (>) --}}
                            <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}">&rsaquo;</a>
                            </li>

                            {{-- Tombol Terakhir (>>) --}}
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
@endsection