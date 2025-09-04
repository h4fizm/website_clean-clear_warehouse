@extends('dashboard_page.main')
@section('title', 'Laman Dashboard Utama')
@section('content')

{{-- 1. Welcome Card Dinamis --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative" style="background-color: white; color: #344767; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); overflow: hidden;">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                     alt="Branch Icon"
                     style="height: 60px; width: auto; opacity: 0.9;">
            </div>
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="welcome-title">
                    Selamat Datang, {{ $user->name }}
                </h4>
                <p class="mb-2 opacity-8" id="welcome-text">
                    Lihat dan kelola data stok material serta riwayat transaksi untuk tiap Region/SA.
                </p>
                <span class="badge bg-primary text-white text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8rem;">
                    {{ $roleName }}
                </span>
            </div>
        </div>
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px; opacity: 0.2; pointer-events: none;"></div>
    </div>
</div>

{{-- Statistik Cards Dinamis --}}
<div class="row g-3">
    @foreach ($cards as $card)
        <div class="col-12 col-sm-6 col-md-4 col-lg">
            <a href="{{ $card['link'] }}" class="card h-100" style="text-decoration: none; color: inherit;">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs text-uppercase font-weight-bold mb-1 text-wrap" style="min-height: 28px;">
                                    {{ $card['title'] }}
                                </p>
                                <h5 class="font-weight-bolder mb-0">{{ $card['value'] }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end d-flex align-items-center justify-content-end">
                            <div class="icon icon-shape bg-gradient-{{ $card['bg'] }} shadow-{{ $card['bg'] }} text-center rounded-circle">
                                <i class="{{ $card['icon'] }} text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>


{{-- 2. Tabel Data Material dari Controller --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 p-3">
                <form action="{{ route('dashboard') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Data Material - Regional Sumbagsel</h6>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search_material" class="form-control" placeholder="Cari material..." value="{{ request('search_material') }}">
                            <button type="submit" class="btn btn-primary mb-0"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 40px;">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td class="text-center"><p class="text-xs font-weight-bold mb-0">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</p></td>
                                    <td class="w-30">
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary shadow-secondary text-center rounded">
                                                <i class="fas fa-box text-white opacity-10"></i>
                                            </div>
                                            <div class="ms-1">
                                                <h6 class="text-sm mb-0">{{ $item->nama_material }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><div class="text-center"><h6 class="text-sm mb-0">{{ $item->kode_material }}</h6></div></td>
                                    <td class="text-center">
                                        <h6 class="text-sm mb-0">{{ number_format($item->total_stok_awal) }} pcs</h6>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data material ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION KUSTOM DITERAPKAN DI SINI --}}
                <div class="mt-2 d-flex justify-content-center">
                    @if ($items->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            @php
                                $total = $items->lastPage();
                                $current = $items->currentPage();
                                $window = 1; 
                            @endphp
                            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->url(1) }}">&laquo;</a>
                            </li>
                            <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->previousPageUrl() }}">&lsaquo;</a>
                            </li>
                            @php $wasGap = false; @endphp
                            @for ($i = 1; $i <= $total; $i++)
                                @if ($i == 1 || $i == $total || abs($i - $current) <= $window)
                                    <li class="page-item {{ ($i == $current) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @php $wasGap = false; @endphp
                                @else
                                    @if (!$wasGap)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @php $wasGap = true; @endphp
                                    @endif
                                @endif
                            @endfor
                            <li class="page-item {{ $items->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $items->nextPageUrl() }}">&rsaquo;</a>
                            </li>
                            <li class="page-item {{ $current == $total ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $items->url($total) }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Tabel Stok Material --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">STOCK MATERIAL REGION</h6>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search-stock-material" class="form-control" placeholder="Cari Nama Material..." aria-label="Search Material">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <div id="material-suggestions" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; display: none;"></div>
                <p class="text-center text-dark mb-3 fw-bold fs-5" id="stock-title">Memuat data...</p>
            </div>
            <div class="card-body p-2" style="padding-top: 0 !important;">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="table-stock-material-custom">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 ps-2" style="width: 25%;">Material</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Gudang</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Baik</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Rusak</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Retur</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Musnah</th>
                                <th class="text-uppercase text-white text-xxs font-weight-bolder opacity-7 text-center">Layak Edar (Baik)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Anda yang lain dari controller (items) bisa diletakkan di sini jika masih perlu --}}

@endsection

@push('scripts')
{{-- SCRIPT DIPERBARUI TOTAL --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    const allMaterialNames = @json($materialList);
    const initialStockData = @json($initialStockData);
    const defaultMaterialName = @json($defaultMaterialName);

    const stockTableBody = document.querySelector('#table-stock-material-custom tbody');
    const stockSearchInput = document.getElementById('search-stock-material');
    const stockTitle = document.getElementById('stock-title');
    const materialSuggestionsContainer = document.getElementById('material-suggestions');

    function renderStockTable(data) {
        stockTableBody.innerHTML = '';
        const materialName = data?.stock?.[0]?.material_name;

        if (materialName) {
            stockTitle.innerText = `Stok ${materialName} - {{ now()->translatedFormat('F Y') }}`;
            stockSearchInput.value = materialName;
        } else {
            stockTitle.innerText = `Stok {{ now()->translatedFormat('F Y') }}`;
        }
        
        if (data && data.stock && data.stock.length > 0) {
            const stockData = data.stock;
            
            // Menggunakan forEach untuk render dinamis (desain awal)
            stockData.forEach((item, index) => {
                const rowHtml = `
                    <tr>
                        ${index === 0 ? `<td class="ps-2 text-wrap align-middle" rowspan="${stockData.length}">
                            <h6 class="text-sm font-weight-bold mb-0">${item.material_name}</h6>
                        </td>` : ''}
                        <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.gudang}</span></td>
                        <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.baik.toLocaleString('id-ID')}</span></td>
                        <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.rusak.toLocaleString('id-ID')}</span></td>
                        <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.retur.toLocaleString('id-ID')}</span></td>
                        <td class="text-secondary text-center text-xs"><span class="font-weight-bold">${item.musnah.toLocaleString('id-ID')}</span></td>
                        <td class="text-secondary text-center text-xs"><h6 class="text-sm font-weight-bolder mb-0">${item.layak_edar.toLocaleString('id-ID')}</h6></td>
                    </tr>
                `;
                stockTableBody.insertAdjacentHTML('beforeend', rowHtml);
            });

        } else {
             stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Pilih atau cari material untuk menampilkan data.</td></tr>';
        }

        if (materialName) {
            const capacity = data.capacity || 0;
            const capacityDisplay = capacity === 0 ? '-' : capacity.toLocaleString('id-ID');
            const capacityRowHtml = `
                <tr class="bg-gray-200">
                    <td colspan="2" class="p-2 align-middle">
                        <p class="text-sm font-weight-bold mb-0">Kapasitas Daya Tampung ${materialName} :</p>
                    </td>
                    <td colspan="5" class="p-2 text-end">
                        <form id="capacity-form" class="d-flex align-items-center justify-content-end" onsubmit="return false;">
                            <input type="text" id="capacity-input" class="form-control form-control-sm me-2 text-end" value="${capacityDisplay}" style="width: 150px;" disabled>
                            <button type="button" id="edit-capacity-btn" class="btn btn-sm btn-info me-2 text-white"><i class="fas fa-edit"></i> Edit</button>
                            <button type="submit" id="submit-capacity-btn" class="btn btn-sm btn-primary" style="display: none;"><i class="fas fa-save"></i> Submit</button>
                        </form>
                    </td>
                </tr>
            `;
            stockTableBody.insertAdjacentHTML('beforeend', capacityRowHtml);
            setupCapacityFormEvents(materialName);
        }
    }

    async function fetchStockData(materialName) {
        stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>';
        try {
            const response = await fetch(`/api/stock-data/${encodeURIComponent(materialName)}`);
            if (!response.ok) throw new Error('Gagal mengambil data.');
            const data = await response.json();
            renderStockTable(data);
        } catch (error) {
            console.error('Fetch error:', error);
            stockTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${error.message}</td></tr>`;
        }
    }
    
    function setupCapacityFormEvents(materialName) {
        const capacityInput = document.getElementById('capacity-input');
        const editCapacityBtn = document.getElementById('edit-capacity-btn');
        const submitCapacityBtn = document.getElementById('submit-capacity-btn');
        
        editCapacityBtn.addEventListener('click', function() {
            capacityInput.disabled = false;
            if (capacityInput.value === '-') {
                capacityInput.value = '';
            }
            capacityInput.focus();
            editCapacityBtn.style.display = 'none';
            submitCapacityBtn.style.display = 'inline-block';
        });

        submitCapacityBtn.addEventListener('click', async function() {
            submitCapacityBtn.disabled = true;
            submitCapacityBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                Swal.fire('Error Kritis!', 'CSRF Token tidak ditemukan. Pastikan layout utama Anda memiliki meta tag csrf-token.', 'error');
                submitCapacityBtn.disabled = false;
                submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
                return;
            }
            
            const rawValue = capacityInput.value.replace(/\./g, '');
            let newCapacity = (rawValue.trim() === '-' || rawValue.trim() === '') ? 0 : parseInt(rawValue, 10);

            if (isNaN(newCapacity) || newCapacity < 0) {
                Swal.fire('Error!', 'Kapasitas harus berupa angka positif atau tanda "-".', 'error');
                submitCapacityBtn.disabled = false;
                submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
                return;
            }

            try {
                const response = await fetch('/api/stock-capacity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify({ material_name: materialName, capacity: newCapacity })
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui kapasitas.');

                Swal.fire('Berhasil!', result.message, 'success');
                fetchStockData(materialName);

            } catch (error) {
                console.error('Submit error:', error);
                Swal.fire('Error!', error.message, 'error');
            } finally {
                submitCapacityBtn.disabled = false;
                submitCapacityBtn.innerHTML = '<i class="fas fa-save"></i> Submit';
            }
        });
    }

    function showSuggestions(searchTerm) {
        const filteredNames = allMaterialNames.filter(name =>
            name.toLowerCase().includes(searchTerm.toLowerCase())
        );
        materialSuggestionsContainer.innerHTML = '';

        if (searchTerm && filteredNames.length > 0) {
            materialSuggestionsContainer.style.display = 'block';
            filteredNames.forEach(name => {
                const item = document.createElement('a');
                item.href = '#';
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = name;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchStockData(name);
                    materialSuggestionsContainer.style.display = 'none';
                });
                materialSuggestionsContainer.appendChild(item);
            });
        } else {
            materialSuggestionsContainer.style.display = 'none';
        }
    }

    document.addEventListener('click', (e) => {
        if (!stockSearchInput.contains(e.target) && !materialSuggestionsContainer.contains(e.target)) {
            materialSuggestionsContainer.style.display = 'none';
        }
    });
    
    stockSearchInput.addEventListener('keyup', function() { showSuggestions(this.value); });

    if (defaultMaterialName && initialStockData.stock) {
        renderStockTable(initialStockData);
    } else {
        stockTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Belum ada material yang dapat ditampilkan.</td></tr>';
        stockTitle.innerText = `Stok {{ now()->translatedFormat('F Y') }}`;
    }
});
</script>
@endpush





