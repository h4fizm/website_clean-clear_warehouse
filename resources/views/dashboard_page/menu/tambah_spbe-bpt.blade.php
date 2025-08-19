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
                
                {{-- Menampilkan error validasi umum --}}
                @if ($errors->any())
                    <div class="alert alert-danger text-white">
                        <strong>Gagal!</strong> Mohon periksa kembali isian Anda.
                    </div>
                @endif
                
                {{-- Form mengarah ke route 'transaksi.store' --}}
                <form action="{{ route('transaksi.store') }}" method="POST" class="row g-3">
                    @csrf
                    
                    <div class="col-12">
                        <label for="name" class="form-label">Nama SPBE/BPT</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="kode_plant" class="form-label">Kode Plant</label>
                        <input type="text" class="form-control @error('kode_plant') is-invalid @enderror" id="kode_plant" name="kode_plant" value="{{ old('kode_plant') }}" required>
                        @error('kode_plant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="type" class="form-label">Jenis</label>
                        <div class="d-flex align-items-center mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="jenis-spbe" value="SPBE" {{ old('type') == 'SPBE' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis-spbe">SPBE</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="jenis-bpt" value="BPT" {{ old('type') == 'BPT' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jenis-bpt">BPT</label>
                            </div>
                        </div>
                        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="name_region" class="form-label">SA Region</label>
                        <select class="form-select @error('name_region') is-invalid @enderror" id="name_region" name="name_region" required>
                            <option value="">Pilih SA Region</option>
                            {{-- Dropdown diisi secara dinamis --}}
                            @foreach ($regions as $region)
                                <option value="{{ $region->name_region }}" {{ old('name_region') == $region->name_region ? 'selected' : '' }}>
                                    {{ $region->name_region }}
                                </option>
                            @endforeach
                        </select>
                        @error('name_region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="province" class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}" required>
                        @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="regency" class="form-label">Nama Kabupaten</label>
                        <input type="text" class="form-control @error('regency') is-invalid @enderror" id="regency" name="regency" value="{{ old('regency') }}" required>
                        @error('regency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12 text-end">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Style dipindahkan ke sini agar rapi --}}
<style>
    .card-header h3 {
        font-weight: 600;
    }
    .form-label {
        font-weight: 500;
        color: #344767;
    }
</style>
@endpush