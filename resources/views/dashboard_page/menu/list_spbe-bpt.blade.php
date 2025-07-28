@extends('dashboard_page.main')

@section('content')
<div class="row mb-3">
    <div class="col-md-8 d-flex flex-wrap gap-2">
        {{-- Dropdown Filter Cabang --}}
        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownCabang" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Cabang
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownCabang">
                <li><a class="dropdown-item" href="#" data-value="Cabang 1">Cabang 1</a></li>
                <li><a class="dropdown-item" href="#" data-value="Cabang 2">Cabang 2</a></li>
                <li><a class="dropdown-item" href="#" data-value="Cabang 3">Cabang 3</a></li>
                <li><a class="dropdown-item" href="#" data-value="Cabang 4">Cabang 4</a></li>
                <li><a class="dropdown-item" href="#" data-value="Cabang 5">Cabang 5</a></li>
            </ul>
        </div>

        {{-- Dropdown Filter Jenis --}}
        <div class="dropdown">
            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="dropdownJenis" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Jenis
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownJenis">
                <li><a class="dropdown-item" href="#" data-value="SPBE">SPBE</a></li>
                <li><a class="dropdown-item" href="#" data-value="BPT">BPT</a></li>
            </ul>
        </div>

        {{-- Search --}}
        <input type="text" class="form-control form-control-sm" placeholder="Cari SPBE / BPT..." style="width: 200px;">
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 400px;">
            <div class="card-header pb-0">
                <h6>Daftar SPBE & BPT</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama SPBE / BPT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Stok</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Manager</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data Dummy --}}
                            @for ($i = 1; $i <= 10; $i++)
                                <tr>
                                    <td class="px-3">
                                        <p class="text-xs font-weight-bold mb-0">{{ $i }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">SPBE {{ $i }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">{{ rand(100, 500) }} tabung</p>
                                    </td>
                                    <td>
                                        <p class="text-xs mb-0">Manager {{ $i }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-gradient-info text-white text-xs" style="cursor:pointer;">Edit</span>
                                        <span class="badge bg-gradient-danger text-white text-xs ms-1" style="cursor:pointer;">Hapus</span>
                                    </td>
                                </tr>
                            @endfor

                            {{-- Jika tidak ada data --}}
                            @if (false)
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Data Kosong
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3">
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Berikutnya</a></li>
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- Script Dummy Filter + Pagination --}}
@push('scripts')
<script>
  const dataDummy = [
    { nama: 'SPBE Cakung', stok: 120, jenis: 'SPBE', cabang: 'Cabang 1' },
    { nama: 'BPT Jakarta Timur', stok: 90, jenis: 'BPT', cabang: 'Cabang 1' },
    { nama: 'SPBE Bekasi', stok: 150, jenis: 'SPBE', cabang: 'Cabang 2' },
    { nama: 'BPT Depok', stok: 110, jenis: 'BPT', cabang: 'Cabang 2' },
    { nama: 'SPBE Bandung', stok: 135, jenis: 'SPBE', cabang: 'Cabang 3' },
    { nama: 'BPT Bandung', stok: 95, jenis: 'BPT', cabang: 'Cabang 3' },
    { nama: 'SPBE Surabaya', stok: 170, jenis: 'SPBE', cabang: 'Cabang 4' },
    { nama: 'BPT Surabaya', stok: 140, jenis: 'BPT', cabang: 'Cabang 4' },
    { nama: 'SPBE Malang', stok: 160, jenis: 'SPBE', cabang: 'Cabang 5' },
    { nama: 'BPT Malang', stok: 130, jenis: 'BPT', cabang: 'Cabang 5' },
    { nama: 'SPBE Bonus', stok: 200, jenis: 'SPBE', cabang: 'Cabang 5' }
  ];

  let selectedCabang = null;
  let selectedJenis = null;
  let searchQuery = '';
  let currentPage = 1;
  const itemsPerPage = 10;

  function filterData() {
    return dataDummy.filter(item => {
      const matchCabang = selectedCabang ? item.cabang === selectedCabang : true;
      const matchJenis = selectedJenis ? item.jenis === selectedJenis : true;
      const matchSearch = searchQuery ? item.nama.toLowerCase().includes(searchQuery.toLowerCase()) : true;
      return matchCabang && matchJenis && matchSearch;
    });
  }

  function renderTable() {
    const tbody = document.querySelector('#table-spbe-bpt tbody');
    const noData = document.getElementById('no-data');
    const data = filterData();
    const start = (currentPage - 1) * itemsPerPage;
    const paginated = data.slice(start, start + itemsPerPage);

    tbody.innerHTML = '';
    if (paginated.length === 0) {
      noData.style.display = 'block';
    } else {
      noData.style.display = 'none';
      paginated.forEach((item, index) => {
        tbody.innerHTML += `
          <tr>
            <td class="text-center">${start + index + 1}</td>
            <td class="ps-3 text-sm">${item.nama}</td>
            <td class="text-center text-sm">${item.stok}</td>
          </tr>
        `;
      });
    }

    renderPagination(data.length);
  }

  function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const ul = document.getElementById('pagination-spbe-bpt');
    ul.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement('li');
      li.classList.add('page-item');
      if (i === currentPage) li.classList.add('active');
      li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener('click', function(e) {
        e.preventDefault();
        currentPage = i;
        renderTable();
      });
      ul.appendChild(li);
    }
  }

  document.querySelectorAll('[data-filter="cabang"]').forEach(el => {
    el.addEventListener('click', function () {
      selectedCabang = this.getAttribute('data-value');
      currentPage = 1;
      renderTable();
    });
  });

  document.querySelectorAll('[data-filter="jenis"]').forEach(el => {
    el.addEventListener('click', function () {
      selectedJenis = this.getAttribute('data-value');
      currentPage = 1;
      renderTable();
    });
  });

  document.getElementById('search-input').addEventListener('input', function () {
    searchQuery = this.value;
    currentPage = 1;
    renderTable();
  });

  // Inisialisasi awal
  renderTable();
</script>
@endpush
@endsection