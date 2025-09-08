@extends('dashboard_page.main')
@section('title', 'UPP Material')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h3>Tabel Data UPP Material</h3>
                    <h6>Daftar Usulan Pemusnahan Material</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto">
                    {{-- Tombol Tambah UPP --}}
                    <a href="{{ url('/upp-material/tambah') }}" class="px-3 py-2 bg-primary text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;" id="tambah-upp-btn">
                        <i class="fas fa-plus me-2"></i>Tambah UPP
                    </a>
                    {{-- Tombol Export Excel --}}
                    <span class="px-3 py-2 bg-success text-white rounded d-flex align-items-center justify-content-center mt-2 mt-md-0" style="cursor: pointer; font-size: 0.875rem; font-weight: bold;" id="export-excel-btn" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </span>
                </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2">
                <div class="d-flex justify-content-between align-items-center px-4 py-2 flex-wrap">
                    <div class="row mb-3 align-items-center w-100">
                        {{-- Input Search --}}
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" 
                                name="search"
                                class="form-control" 
                                placeholder="Cari No.Surat...">
                            </div>
                        </div>

                        {{-- Date Range + Filter Button --}}
                        <div class="col-12 col-md-8 d-flex flex-wrap align-items-center justify-content-md-end">
                            {{-- Start Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="startDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Dari:</label>
                                <input type="date" id="startDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;">
                            </div>

                            {{-- End Date --}}
                            <div class="d-flex align-items-center me-2 mb-3">
                                <label for="endDate" class="me-2 text-secondary text-xxs font-weight-bolder opacity-7 mb-0">Sampai:</label>
                                <input type="date" id="endDate" 
                                    class="form-control form-control-sm date-input" 
                                    style="max-width: 160px;">
                            </div>

                            {{-- Button Filter --}}
                            <div class="align-self-end">
                                <button id="filter-btn" class="btn btn-primary btn-sm px-3">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Table contents --}}
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-upp-material">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. Surat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahapan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Buat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Update Terakhir</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upps as $upp)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($upps->currentPage()-1)*$upps->perPage() }}</td>
                                <td>{{ $upp->no_surat_persetujuan }}</td>
                                <td>{{ $upp->tahapan }}</td>
                                <td class="text-center">
                                    @php
                                        $statusText = strtolower($upp->status) === 'done' ? 'Done' : 'Proses';
                                        $statusColor = strtolower($upp->status) === 'done' ? 'bg-gradient-success' : 'bg-gradient-warning';
                                    @endphp
                                    <span class="badge {{ $statusColor }} text-white text-xs font-weight-bold">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($upp->tgl_buat)->translatedFormat('l, d F Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($upp->tgl_update)->translatedFormat('l, d F Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('upp-material.preview', $upp->no_surat_persetujuan) }}" 
                                    class="badge bg-gradient-info text-white text-xs">
                                        <i class="fas fa-eye me-1"></i> Preview
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data Kosong</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                    <div id="no-data" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-4 mb-3 px-3 d-flex justify-content-center">
                    {{ $upps->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW BARU --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Detail UPP Material <span id="modal-upp-surat"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary text-xxs font-weight-bolder">Daftar Material:</h6>
                    <div class="table-responsive rounded shadow-sm">
                        <table class="table table-bordered table-striped align-items-center mb-0" style="min-width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Material</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Material</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stok Saat Ini</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stok Diambil</th>
                                </tr>
                            </thead>
                            <tbody id="material-list-table">
                                {{-- Material details will be rendered here --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan:</h6>
                    <p id="modal-keterangan" class="form-control-plaintext text-sm text-muted p-2 border rounded" style="background-color: #f8f9fa;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="lakukan-pemusnahan-btn">
                    <i class="fas fa-times me-2"></i> Lakukan Pemusnahan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Export Excel --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Data ke Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-sm text-secondary">Pilih rentang tanggal untuk data yang ingin Anda export.</p>
                <div class="mb-3">
                    <label for="exportStartDate" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="exportStartDate">
                </div>
                <div class="mb-3">
                    <label for="exportEndDate" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="exportEndDate">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmExportBtn">
                    <i class="fas fa-file-excel me-2"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush