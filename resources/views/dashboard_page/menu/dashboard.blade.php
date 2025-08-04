@extends('dashboard_page.main')
@section('title', 'Laman Dashboard Utama')
@section('content')

{{-- Welcome Section - Opsi 1: Icon di Atas (Mobile) / Icon di Samping (Desktop) --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative" style=" {{-- Increased padding to p-4 --}}
        background-color: white;
        color: #344767;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0"> {{-- Removed default padding --}}
            {{-- Logo for mobile (order-md-2 to push it after text on desktop) --}}
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                     alt="Branch Icon"
                     style="height: 60px; width: auto; opacity: 0.9;">
            </div>

            {{-- Text Content (order-md-1 to put it first on desktop) --}}
            <div class="w-100 order-md-1 text-center text-md-start"> {{-- Added text-center for mobile --}}
                <h4 class="mb-1 fw-bold" id="welcome-title">
                    Selamat Datang, Nama User
                </h4>
                <p class="mb-2 opacity-8" id="welcome-text">
                    Lihat dan kelola data stok material serta riwayat transaksi untuk tiap Region/SA.
                </p>
                <span class="badge bg-primary text-white text-uppercase px-3 py-2 rounded-xl shadow-sm"
                      style="font-size: 0.8rem;">Nama Role</span>
            </div>
        </div>

        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            background-size: 60px 60px;
            opacity: 0.2;
            pointer-events: none;
        "></div>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="row">
    @php
        $cards = [
            ['title' => 'Total User', 'value' => '1,234', 'icon' => 'ni-single-02', 'bg' => 'primary'],
            ['title' => 'Total Cabang', 'value' => '24', 'icon' => 'ni-building', 'bg' => 'info'],
            ['title' => 'Transaksi Bulan Ini', 'value' => '12,567', 'icon' => 'ni-send', 'bg' => 'success'],
            ['title' => 'UPP Material Bulan ini', 'value' => '189', 'icon' => 'fas fa-sync-alt', 'bg' => 'danger'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-9">
                            <div class="numbers">
                                <p class="text-xs text-uppercase font-weight-bold mb-1 text-wrap" style="min-height: 38px;">
                                    {{ \Illuminate\Support\Str::limit($card['title'], 40) }}
                                </p>
                                <h5 class="font-weight-bolder mb-0">{{ $card['value'] }}</h5>
                            </div>
                        </div>
                        <div class="col-3 text-end d-flex align-items-center">
                            <div class="icon icon-shape bg-gradient-{{ $card['bg'] }} shadow-{{ $card['bg'] }} text-center rounded-circle">
                                <i class="ni {{ $card['icon'] }} text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabel & Grafik --}}
<div class="row mt-4">
    {{-- Tabel Daftar Cabang --}}
    <div class="col-md-6">
        <div class="card" style="height: 420px;">
            <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Data Material - Nama Cabang</h6>
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" id="search-input-cabang" class="form-control" placeholder="Search..." aria-label="Search">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table align-items-center mb-0" id="table-cabang-custom">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 40px;">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">Kode Material</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Custom Pagination --}}
                <div class="mt-2 d-flex justify-content-center">
                    <nav>
                        <ul id="custom-pagination" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="col-md-6">
        <div class="card" style="height: 420px;">
            <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
                <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Grafik Data Transaksi Bulan Ini Tiap Cabang</h6>

                {{-- Dropdown Pilih Cabang (UNTUK GRAFIK) --}}
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;">
                        Pilih Cabang
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#" data-value="Cabang 1">Cabang 1</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Cabang 2">Cabang 2</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Cabang 3">Cabang 3</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Cabang 4">Cabang 4</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Cabang 5">Cabang 5</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-3 d-flex justify-content-center align-items-center">
                <canvas id="grafik-transaksi" style="max-height: 330px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Function to generate a random code with a fixed number of digits
        function generateRandomMaterialCode(length) {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        // --- Data Dummy untuk Tabel Material ---
        const allMaterialData = [
            { id: 1, nama_material: 'Material A (Bahan Baku)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1500 },
            { id: 2, nama_material: 'Material B (Produk Jadi)', kode_material: generateRandomMaterialCode(5), stok_fisik: 800 },
            { id: 3, nama_material: 'Material C (Spare Part)', kode_material: generateRandomMaterialCode(5), stok_fisik: 250 },
            { id: 4, nama_material: 'Material D (Packaging)', kode_material: generateRandomMaterialCode(5), stok_fisik: 3000 },
            { id: 5, nama_material: 'Material E (Kimia Industri)', kode_material: generateRandomMaterialCode(5), stok_fisik: 120 },
            { id: 6, nama_material: 'Material F (Elektronik)', kode_material: generateRandomMaterialCode(5), stok_fisik: 75 },
            { id: 7, nama_material: 'Material G (Peralatan)', kode_material: generateRandomMaterialCode(5), stok_fisik: 30 },
            { id: 8, nama_material: 'Material H (Kertas Roll Besar)', kode_material: generateRandomMaterialCode(5), stok_fisik: 450 },
            { id: 9, nama_material: 'Material I (Pelumas)', kode_material: generateRandomMaterialCode(5), stok_fisik: 180 },
            { id: 10, nama_material: 'Material J (Seal Karet)', kode_material: generateRandomMaterialCode(5), stok_fisik: 900 },
            { id: 11, nama_material: 'Material K (Pipa PVC)', kode_material: generateRandomMaterialCode(5), stok_fisik: 600 },
            { id: 12, nama_material: 'Material L (Kabel Listrik)', kode_material: generateRandomMaterialCode(5), stok_fisik: 2200 },
            { id: 13, nama_material: 'Material M (Cat Industri)', kode_material: generateRandomMaterialCode(5), stok_fisik: 90 },
            { id: 14, nama_material: 'Material N (Baut & Mur)', kode_material: generateRandomMaterialCode(5), stok_fisik: 5000 },
            { id: 15, nama_material: 'Material O (Alat Ukur)', kode_material: generateRandomMaterialCode(5), stok_fisik: 15 },
            { id: 16, nama_material: 'Material P (Sensor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 40 },
            { id: 17, nama_material: 'Material Q (Filter Udara)', kode_material: generateRandomMaterialCode(5), stok_fisik: 100 },
            { id: 18, nama_material: 'Material R (Resistor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 300 },
            { id: 19, nama_material: 'Material S (Transistor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 200 },
            { id: 20, nama_material: 'Material T (Relay)', kode_material: generateRandomMaterialCode(5), stok_fisik: 60 },
            { id: 21, nama_material: 'Material U (Konektor)', kode_material: generateRandomMaterialCode(5), stok_fisik: 700 },
            { id: 22, nama_material: 'Material V (Pelat Logam)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1000 },
            { id: 23, nama_material: 'Material W (Gasket)', kode_material: generateRandomMaterialCode(5), stok_fisik: 400 },
            { id: 24, nama_material: 'Material X (Klem Pipa)', kode_material: generateRandomMaterialCode(5), stok_fisik: 1200 },
            { id: 25, nama_material: 'Material Y (Pompa Kecil)', kode_material: generateRandomMaterialCode(5), stok_fisik: 25 }
        ];

        // --- Custom Pagination & Search untuk Tabel Material ---
        const itemsPerPage = 5; // Jumlah baris per halaman
        const tableBody = document.querySelector('#table-cabang-custom tbody'); // This ID is still 'table-cabang-custom'
        const paginationContainer = document.getElementById('custom-pagination');
        let currentFilteredData = [];
        let currentPage = 0;

        function renderTable(data, page) {
            tableBody.innerHTML = '';
            const start = page * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = data.slice(start, end);

            if (paginatedItems.length === 0) {
                // Adjusted colspan to 4 for the new table structure
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Tidak ada data material ditemukan.</td></tr>';
            } else {
                paginatedItems.forEach((material, index) => {
                    const rowIndex = start + index + 1;
                    const row = `
                        <tr>
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                            </td>
                            <td class="w-30">
                                <div class="d-flex px-2 py-1 align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary shadow-secondary text-center rounded">
                                        <i class="fas fa-box text-white opacity-10"></i> {{-- Changed icon to fa-box for material --}}
                                    </div>
                                    <div class="ms-1">
                                        <h6 class="text-sm mb-0">${material.nama_material}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <h6 class="text-sm mb-0">${material.kode_material}</h6>
                                </div>
                            </td>
                            <td class="text-center">
                                <h6 class="text-sm mb-0">${material.stok_fisik.toLocaleString('id-ID')} pcs</h6>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            }
            updatePaginationButtons(page, Math.ceil(data.length / itemsPerPage));
        }

        function updatePaginationButtons(activePage, totalPages) {
            paginationContainer.innerHTML = '';

            const firstPageButton = document.createElement('li');
            firstPageButton.classList.add('page-item');
            if (activePage === 0 || totalPages === 0) firstPageButton.classList.add('disabled');
            firstPageButton.innerHTML = `<a class="page-link" href="javascript:;" data-page="0">&laquo;</a>`;
            if (activePage > 0 && totalPages > 0) {
                firstPageButton.querySelector('a').addEventListener('click', () => {
                    currentPage = 0;
                    renderTable(currentFilteredData, currentPage);
                });
            }
            paginationContainer.appendChild(firstPageButton);

            const maxVisible = 5;
            let startPage = Math.max(0, activePage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible);

            if (endPage - startPage < maxVisible && startPage > 0) {
                startPage = Math.max(0, endPage - maxVisible);
            }

            for (let i = startPage; i < endPage; i++) {
                const pageItem = document.createElement('li');
                pageItem.classList.add('page-item');
                if (i === activePage) pageItem.classList.add('active');
                pageItem.innerHTML = `<a class="page-link" href="javascript:;" data-page="${i}">${i + 1}</a>`;
                pageItem.querySelector('a').addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = i;
                    renderTable(currentFilteredData, currentPage);
                });
                paginationContainer.appendChild(pageItem);
            }

            const lastPageButton = document.createElement('li');
            lastPageButton.classList.add('page-item');
            if (activePage === totalPages - 1 || totalPages === 0) lastPageButton.classList.add('disabled');
            lastPageButton.innerHTML = `<a class="page-link" href="javascript:;" data-page="${totalPages - 1}">&raquo;</a>`;
            if (activePage < totalPages - 1 && totalPages > 0) {
                lastPageButton.querySelector('a').addEventListener('click', () => {
                    currentPage = totalPages - 1;
                    renderTable(currentFilteredData, currentPage);
                });
            }
            paginationContainer.appendChild(lastPageButton);
        }

        // Event listener untuk input search
        document.getElementById('search-input-cabang').addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            currentFilteredData = allMaterialData.filter(material => // Changed from allCabangData to allMaterialData
                material.nama_material.toLowerCase().includes(searchTerm) ||
                material.kode_material.toLowerCase().includes(searchTerm) // Added search by kode_material
            );
            currentPage = 0;
            renderTable(currentFilteredData, currentPage);
        });

        // Inisialisasi awal tabel dan paginasi
        currentFilteredData = allMaterialData; // Changed from allCabangData to allMaterialData
        renderTable(currentFilteredData, currentPage);


        // --- Inisialisasi Chart.js untuk Grafik Transaksi ---
        const ctxGrafik = document.getElementById('grafik-transaksi').getContext('2d');

        // Data dummy untuk 7 hari terakhir dari tanggal saat ini (menggunakan objek Date aktual)
        const labelsGrafik = [];
        const dataPointsGrafik = [];
        // Use the actual current date for dynamic date generation
        const todayActual = new Date(); 
        // For consistent dummy data in the context of this request, 
        // I will keep the fixed date `2025-07-28` for dummy generation, 
        // but note that `new Date()` would be more appropriate for a live system.
        const dummyFixedToday = new Date('2025-07-28T00:00:00'); 
        
        for (let i = 6; i >= 0; i--) {
            const date = new Date(dummyFixedToday);
            date.setDate(date.getDate() - i);
            const day = date.toLocaleString('id-ID', { weekday: 'short' });
            const formattedDate = date.toLocaleString('id-ID', { day: '2-digit', month: 'short' }).replace('.', '');
            labelsGrafik.push(`${day}, ${formattedDate}`);
            dataPointsGrafik.push(Math.floor(Math.random() * (180 - 100 + 1)) + 100);
        }

        const grafikTransaksi = new Chart(ctxGrafik, {
            type: 'line',
            data: {
                labels: labelsGrafik,
                datasets: [{
                    label: 'Total Stok',
                    data: dataPointsGrafik,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Hari, Tanggal Bulan',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total Stok',
                            font: {
                                weight: 'bold'
                            }
                        },
                        beginAtZero: true,
                        ticks: {
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });

        // --- Dropdown functionality untuk Grafik ---
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const selectedCabang = this.dataset.value;
                document.getElementById('dropdownMenuButton').innerText = this.innerText;
                console.log('Selected Cabang for Chart:', selectedCabang || 'Semua Cabang');
                // Logika untuk memperbarui data grafik di sini (jika data dinamis)
            });
        });
    });
</script>
@endpush
@endsection