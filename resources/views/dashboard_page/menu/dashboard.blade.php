@extends('dashboard_page.main')
@section('title', 'Laman Dashboard Utama')
@section('content')

{{-- Welcome Section  --}}
<div class="col-12 mb-6">
    <div class="card p-3" style="
        background: linear-gradient(to right, #0F2027 0%, #203A43 50%, #2C5364 100%); /* Gradien Biru Tua */
        color: white; /* Text color for contrast */
        border-radius: 1rem; /* Slightly more rounded corners */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Soft shadow */
        overflow: hidden; /* To contain pseudo-elements if added */
        position: relative;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h4 class="mb-1 text-white fw-bold">Selamat Datang, <strong style="color: #FFD700;">Nama User</strong>!</h4>
                {{-- Kalimat p diubah sesuai permintaan --}}
                <p class="mb-2 opacity-8">Informasi Data Stok Material dan Transaksi pada Cabang - Nama Cabang.</p>
                <span class="badge bg-white text-primary text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8em;">Nama Role</span>
            </div>
            
            <div class="text-center position-relative me-md-4">
                {{-- Icon yang lebih menonjol dan relevan --}}
                <i class="fas fa-hand-sparkles text-white opacity-8" style="font-size: 4em;"></i> 
                {{-- Tambahkan ikon kecil tambahan atau efek visual --}}
                <i class="fas fa-warehouse text-white opacity-5 position-absolute" style="font-size: 2em; top: 10px; right: 0;"></i>
            </div>
        </div>
        {{-- Optional: Overlay background pattern --}}
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 20v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zm0 20v-4H4v4H0v2h4v4h2v-4h4v-2H6zM36 4V0h-2v4h-4v2h4v4h2V6h4V4zm0 10V10h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 4V0H4v4H0v2h4v4h2V6h4V4z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
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
            ['title' => 'Total Transaksi Bulan Ini', 'value' => '12,567', 'icon' => 'ni-send', 'bg' => 'success'],
            ['title' => 'Total Recycle Bulan Ini', 'value' => '189', 'icon' => 'fas fa-sync-alt', 'bg' => 'danger'],
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
                    <h6 class="text-uppercase fw-bold mb-0" style="font-size: 14px;">Daftar Cabang</h6>
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
                                {{-- Kolom No. terpisah --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 40px;">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Cabang</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center ps-2">Total User</th>
                                {{-- KOLOM BARU: Aksi --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
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
                    {{-- ambil 5 data saja --}}
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

        // --- Data Dummy untuk Tabel Cabang ---
        const allCabangData = [
            @for ($i = 1; $i <= 30; $i++)
                { id: {{ $i }}, nama: 'Cabang {{ $i }}', total_user: {{ rand(50, 500) }} },
            @endfor
        ];

        // --- Custom Pagination & Search untuk Tabel Cabang ---
        const itemsPerPage = 5; // Jumlah baris per halaman
        const tableBody = document.querySelector('#table-cabang-custom tbody');
        const paginationContainer = document.getElementById('custom-pagination');
        let currentFilteredData = []; // Data yang difilter saat ini
        let currentPage = 0; // Halaman aktif

        function renderTable(data, page) {
            tableBody.innerHTML = ''; // Kosongkan tabel
            const start = page * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedItems = data.slice(start, end);

            if (paginatedItems.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Tidak ada data cabang ditemukan.</td></tr>'; // colspan 4 karena sekarang ada 4 kolom
            } else {
                paginatedItems.forEach((cabang, index) => {
                    const rowIndex = start + index + 1; // Nomor urut baris
                    const row = `
                        <tr>
                            {{-- Kolom No. --}}
                            <td class="text-center">
                                <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                            </td>
                            {{-- Kolom Nama Cabang dengan Icon --}}
                            <td class="w-30">
                                <div class="d-flex px-2 py-1 align-items-center">
                                    {{-- Icon Kotak Rounded --}}
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary shadow-secondary text-center rounded">
                                        <i class="fas fa-warehouse text-white opacity-10"></i>
                                    </div>
                                    <div class="ms-1">
                                        <h6 class="text-sm mb-0">${cabang.nama}</h6>
                                    </div>
                                </div>
                            </td>
                            {{-- Kolom Total User --}}
                            <td>
                                <div class="text-center">
                                    <h6 class="text-sm mb-0">${cabang.total_user.toLocaleString('id-ID')}</h6>
                                </div>
                            </td>
                            {{-- KOLOM BARU: Aksi dengan Badge Buttons --}}
                            <td class="text-center">
                               <a href="{{ url('/spbe-bpt') }}" class="badge badge-sm bg-gradient-info me-1" style="cursor:pointer;">
                                    Daftar SPBE & BPT
                                </a>
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

            // Tombol "First Page" (<<)
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

            // Tombol angka halaman (maksimal 5 terlihat)
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

            // Tombol "Last Page" (>>)
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
            currentFilteredData = allCabangData.filter(cabang =>
                cabang.nama.toLowerCase().includes(searchTerm)
            );
            currentPage = 0; // Reset ke halaman pertama setelah pencarian
            renderTable(currentFilteredData, currentPage);
        });

        // Inisialisasi awal tabel dan paginasi
        currentFilteredData = allCabangData;
        renderTable(currentFilteredData, currentPage);


        // --- Inisialisasi Chart.js untuk Grafik Transaksi ---
        const ctxGrafik = document.getElementById('grafik-transaksi').getContext('2d');

        // Data dummy untuk 7 hari terakhir dari tanggal saat ini (28 Juli 2025)
        const labelsGrafik = [];
        const dataPointsGrafik = [];
        const today = new Date('2025-07-28T00:00:00'); // Tetapkan tanggal "hari ini" ke 28 Juli 2025
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
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

