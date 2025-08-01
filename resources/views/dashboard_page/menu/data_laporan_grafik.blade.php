@extends('dashboard_page.main')
@section('title', 'Laman Grafik Transaksi')
@section('content')

{{-- Main Report Title --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card p-3 shadow-sm position-relative" style="
            background: linear-gradient(to right, #0F2027 0%, #000000 100%); /* Deep blue to black gradient */
            color: white; /* White text for contrast */
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            min-height: 100px; /* Ensure enough space for the button */
        ">
            <h3 class="mb-1 fw-bold text-white">Laporan Grafik Penyaluran Material</h3>
            <p class="mb-0 opacity-8" style="font-size: 0.9em;">Visualisasi data transaksi material untuk analisis performa cabang.</p>

            {{-- Global Export PDF Button - Moved Inside Card --}}
            <div class="position-absolute bottom-0 end-0 p-3">
                <button id="exportPdfBtn" class="btn btn-sm btn-danger">Export Grafik ke PDF</button>
            </div>
        </div>
    </div>
</div>

{{-- Welcome Section: Pihak Pertama (Cabang Anda) --}}
<div class="col-12 mb-3">
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
                <h4 class="mb-1 text-white fw-bold">Grafik Penyaluran Material Cabang Anda</h4>
                <p class="mb-2 opacity-8">Analisis tren penyaluran material dari **Cabang Anda** oleh <strong style="color: #FFD700;">Nama User</strong>.</p>
                <span class="badge bg-white text-primary text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8em;">Pihak Pertama</span>
            </div>

            <div class="text-center position-relative me-md-4">
                <i class="fas fa-chart-line text-white opacity-8" style="font-size: 4em;"></i>
                <i class="fas fa-warehouse text-white opacity-5 position-absolute" style="font-size: 2em; top: 10px; right: 0;"></i>
            </div>
        </div>
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

{{-- Chart Section: Penyaluran Pihak Pertama --}}
<div class="col-12 mb-4">
    <div class="card shadow">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
            <h6>Grafik Penyaluran Material Cabang Anda</h6>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                {{-- Date Range Picker for Chart 1 --}}
                <div class="d-flex align-items-center gap-1">
                    <label for="start-date-chart-1" class="text-xs mb-0 me-1">Dari</label>
                    <input type="date" id="start-date-chart-1" class="form-control form-control-sm" style="height: 38px; width: 140px; min-width: 120px;">
                    <label for="end-date-chart-1" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                    <input type="date" id="end-date-chart-1" class="form-control form-control-sm" style="height: 38px; width: 140px; min-width: 120px;">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="chart-penyaluran-1" class="chart-canvas" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

---

{{-- Welcome Section: Pihak Kedua (SPBE/BPT Tujuan) --}}
<div class="col-12 mb-3">
    <div class="card p-3" style="
        background: linear-gradient(to right, #0F2027 0%, #203A43 50%, #2C5364 100%); /* Warna yang sama dengan Pihak Pertama */
        color: white;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
    ">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h4 class="mb-1 text-white fw-bold">Grafik Penyaluran Material Cabang Lain</h4>
                <p class="mb-2 opacity-8">Visualisasi transaksi penyaluran material ke cabang lain.</p>
                <span class="badge bg-white text-primary text-uppercase px-3 py-2 rounded-xl shadow-sm" style="font-size: 0.8em;">Pihak Kedua</span>
            </div>

            <div class="text-center position-relative me-md-4">
                <i class="fas fa-chart-area text-white opacity-8" style="font-size: 4em;"></i>
                <i class="fas fa-truck-loading text-white opacity-5 position-absolute" style="font-size: 2em; top: 10px; right: 0;"></i>
            </div>
        </div>
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

{{-- Chart Section: Penyaluran Pihak Kedua --}}
<div class="col-12 mb-4">
    <div class="card shadow">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
            <h6>Grafik Penyaluran Material Cabang Lain</h6>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center">
                {{-- Date Range Picker for Chart 2 --}}
                <div class="d-flex align-items-center gap-1">
                    <label for="start-date-chart-2" class="text-xs mb-0 me-1">Dari</label>
                    <input type="date" id="start-date-chart-2" class="form-control form-control-sm" style="height: 38px; width: 140px; min-width: 120px;">
                    <label for="end-date-chart-2" class="text-xs mb-0 ms-2 me-1">Sampai</label>
                    <input type="date" id="end-date-chart-2" class="form-control form-control-sm" style="height: 38px; width: 140px; min-width: 120px;">
                </div>

                {{-- Dropdown Filter Cabang Lain --}}
                <div class="dropdown">
                    <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="dropdownCabangLain" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                        Pilih Cabang
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownCabangLain">
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Semua Cabang">Semua Cabang</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Surabaya">Cabang Surabaya</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Jakarta">Cabang Jakarta</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Bandung">Cabang Bandung</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Medan">Cabang Medan</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Makassar">Cabang Makassar</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Semarang">Cabang Semarang</a></li>
                        <li><a class="dropdown-item" href="#" data-filter-cabang-chart="Cabang Yogyakarta">Cabang Yogyakarta</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="chart-penyaluran-2" class="chart-canvas" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

---

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    // Define window.jsPDF to make it accessible globally
    window.jsPDF = window.jspdf.jsPDF;

    // Dummy data (can be replaced with actual fetched data)
    const materialData1 = [
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', penerimaan: 1000, penyaluran: 800, stok: 200, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', penerimaan: 500, penyaluran: 350, stok: 150, tanggal: '2025-07-27' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', penerimaan: 1000, penyaluran: 500, stok: 500, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', penerimaan: 300, penyaluran: 150, stok: 150, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', penerimaan: 400, penyaluran: 200, stok: 200, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', penerimaan: 250, penyaluran: 100, stok: 150, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', penerimaan: 80, penyaluran: 30, stok: 50, tanggal: '2025-07-20' },
        { nama: 'Manometer', kode: 'MAN-001', penerimaan: 50, penyaluran: 10, stok: 40, tanggal: '2025-07-19' },
        { nama: 'Flow Meter', kode: 'FLM-002', penerimaan: 30, penyaluran: 10, stok: 20, tanggal: '2025-07-18' },
        // New dummy data for Pihak Pertama (Cabang Anda) - extending dates
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', penerimaan: 900, penyaluran: 750, stok: 150, tanggal: '2025-07-15' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', penerimaan: 400, penyaluran: 300, stok: 100, tanggal: '2025-07-16' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', penerimaan: 350, penyaluran: 180, stok: 170, tanggal: '2025-07-17' },
        { nama: 'Seal Karet', kode: 'SEAL-01', penerimaan: 1200, penyaluran: 600, stok: 600, tanggal: '2025-07-18' },
        { nama: 'Regulator', kode: 'REG-005', penerimaan: 280, penyaluran: 140, stok: 140, tanggal: '2025-07-19' },
        { nama: 'Selang Gas', kode: 'SLG-010', penerimaan: 450, penyaluran: 220, stok: 230, tanggal: '2025-07-20' },
        { nama: 'Kompor Portable', kode: 'KPR-015', penerimaan: 170, penyaluran: 80, stok: 90, tanggal: '2025-07-21' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', penerimaan: 300, penyaluran: 120, stok: 180, tanggal: '2025-07-22' },
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', penerimaan: 1100, penyaluran: 900, stok: 200, tanggal: '2025-08-01' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', penerimaan: 550, penyaluran: 400, stok: 150, tanggal: '2025-08-02' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', penerimaan: 420, penyaluran: 210, stok: 210, tanggal: '2025-08-03' },
    ];

    const materialData2 = [
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Surabaya', penerimaan: 500, penyaluran: 450, stok: 50, tanggal: '2025-07-28' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', cabang: 'Cabang Jakarta', penerimaan: 300, penyaluran: 200, stok: 100, tanggal: '2025-07-27' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', cabang: 'Cabang Bandung', penerimaan: 200, penyaluran: 100, stok: 100, tanggal: '2025-07-26' },
        { nama: 'Seal Karet', kode: 'SEAL-01', cabang: 'Cabang Surabaya', penerimaan: 800, penyaluran: 400, stok: 400, tanggal: '2025-07-25' },
        { nama: 'Regulator', kode: 'REG-005', cabang: 'Cabang Jakarta', penerimaan: 150, penyaluran: 70, stok: 80, tanggal: '2025-07-24' },
        { nama: 'Selang Gas', kode: 'SLG-010', cabang: 'Cabang Bandung', penerimaan: 250, penyaluran: 150, stok: 100, tanggal: '2025-07-23' },
        { nama: 'Kompor Portable', kode: 'KPR-015', cabang: 'Cabang Medan', penerimaan: 100, penyaluran: 50, stok: 50, tanggal: '2025-07-22' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', cabang: 'Cabang Makassar', penerimaan: 200, penyaluran: 80, stok: 120, tanggal: '2025-07-21' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', cabang: 'Cabang Surabaya', penerimaan: 60, penyaluran: 20, stok: 40, tanggal: '2025-07-20' },
        { nama: 'Manometer', kode: 'MAN-001', cabang: 'Cabang Jakarta', penerimaan: 40, penyaluran: 5, stok: 35, tanggal: '2025-07-19' },
        { nama: 'Flow Meter', kode: 'FLM-002', cabang: 'Cabang Bandung', penerimaan: 20, penyaluran: 5, stok: 15, tanggal: '2025-07-18' },
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Medan', penerimaan: 300, penyaluran: 280, stok: 20, tanggal: '2025-07-17' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', cabang: 'Cabang Makassar', penerimaan: 150, penyaluran: 75, stok: 75, tanggal: '2025-07-16' },
        { nama: 'Seal Karet', kode: 'SEAL-01', cabang: 'Cabang Jakarta', penerimaan: 600, penyaluran: 300, stok: 300, tanggal: '2025-07-15' },
        { nama: 'Regulator', kode: 'REG-005', cabang: 'Cabang Surabaya', penerimaan: 100, penyaluran: 40, stok: 60, tanggal: '2025-07-14' },
        { nama: 'Selang Gas', kode: 'SLG-010', cabang: 'Cabang Medan', penerimaan: 200, penyaluran: 100, stok: 100, tanggal: '2025-07-13' },
        // New dummy data for Pihak Kedua (Cabang Lain) - adding more branches and dates
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Semarang', penerimaan: 400, penyaluran: 380, stok: 20, tanggal: '2025-07-29' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', cabang: 'Cabang Yogyakarta', penerimaan: 250, penyaluran: 180, stok: 70, tanggal: '2025-07-30' },
        { nama: 'Tabung 3 kg', kode: 'TBG3-001', cabang: 'Cabang Surabaya', penerimaan: 220, penyaluran: 110, stok: 110, tanggal: '2025-07-31' },
        { nama: 'Seal Karet', kode: 'SEAL-01', cabang: 'Cabang Jakarta', penerimaan: 700, penyaluran: 350, stok: 350, tanggal: '2025-08-01' },
        { nama: 'Regulator', kode: 'REG-005', cabang: 'Cabang Bandung', penerimaan: 130, penyaluran: 60, stok: 70, tanggal: '2025-08-01' },
        { nama: 'Selang Gas', kode: 'SLG-010', cabang: 'Cabang Medan', penerimaan: 300, penyaluran: 170, stok: 130, tanggal: '2025-07-29' },
        { nama: 'Kompor Portable', kode: 'KPR-015', cabang: 'Cabang Makassar', penerimaan: 90, penyaluran: 45, stok: 45, tanggal: '2025-07-30' },
        { nama: 'Gas 5.5 kg', kode: 'LPG5.5-001', cabang: 'Cabang Semarang', penerimaan: 180, penyaluran: 70, stok: 110, tanggal: '2025-07-31' },
        { nama: 'Tabung 5.5 kg', kode: 'TBG5.5-001', cabang: 'Cabang Yogyakarta', penerimaan: 70, penyaluran: 25, stok: 45, tanggal: '2025-08-01' },
        { nama: 'Manometer', kode: 'MAN-001', cabang: 'Cabang Surabaya', penerimaan: 60, penyaluran: 15, stok: 45, tanggal: '2025-08-02' },
        { nama: 'Flow Meter', kode: 'FLM-002', cabang: 'Cabang Jakarta', penerimaan: 25, penyaluran: 8, stok: 17, tanggal: '2025-08-03' },
        { nama: 'Gas LPG 3 kg', kode: 'LPG3-001', cabang: 'Cabang Bandung', penerimaan: 350, penyaluran: 300, stok: 50, tanggal: '2025-08-04' },
        { nama: 'Gas LPG 12 kg', kode: 'LPG12-001', cabang: 'Cabang Medan', penerimaan: 200, penyaluran: 150, stok: 50, tanggal: '2025-08-05' },
    ];

    let currentSelectedBranch2 = "Semua Cabang"; // Default for chart 2 branch filter
    let startDateChart1 = ''; // State for start date filter for chart 1
    let endDateChart1 = ''; // State for end date filter for chart 1
    let startDateChart2 = ''; // State for start date filter for chart 2
    let endDateChart2 = ''; // State for end date filter for chart 2

    // Helper to get dates for "this week" (Monday to Sunday)
    function getThisWeekDates() {
        const today = new Date();
        const dayOfWeek = today.getDay(); // Sunday - 0, Monday - 1, etc.
        const diffToMonday = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Adjust for Sunday
        const monday = new Date(today.setDate(diffToMonday));
        monday.setHours(0, 0, 0, 0);

        const dates = [];
        for (let i = 0; i < 7; i++) {
            const date = new Date(monday);
            date.setDate(monday.getDate() + i);
            dates.push(date.toISOString().slice(0, 10)); // YYYY-MM-DD
        }
        return dates;
    }

    const thisWeekDates = getThisWeekDates();
    const daysOfWeek = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    // Function to generate a sequence of dates between two dates
    function getDatesBetween(startDate, endDate) {
        const dates = [];
        let currentDate = new Date(startDate);
        const end = new Date(endDate);
        while (currentDate <= end) {
            dates.push(currentDate.toISOString().slice(0, 10));
            currentDate.setDate(currentDate.getDate() + 1);
        }
        return dates;
    }

    // --- Chart 1: Pihak Pertama ---
    let chartPenyaluran1;
    function renderChart1() {
        const ctx = document.getElementById('chart-penyaluran-1').getContext('2d');
        
        let filteredData = materialData1;
        let chartLabels = daysOfWeek;
        let chartTitleSuffix = 'Minggu Ini';

        if (startDateChart1 && endDateChart1) {
            const start = new Date(startDateChart1);
            const end = new Date(endDateChart1);
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate >= start && itemDate <= end;
            });
            const datesInRange = getDatesBetween(startDateChart1, endDateChart1);
            chartLabels = datesInRange.map(date => {
                const d = new Date(date);
                return `${d.getDate()}/${d.getMonth() + 1}`; // e.g., 28/7
            });
            chartTitleSuffix = `${startDateChart1} s/d ${endDateChart1}`;
        } else {
            // Default to current week data if no date range is set
            filteredData = filteredData.filter(item => thisWeekDates.includes(item.tanggal));
        }

        const dataForChart1 = {};
        // Initialize dataForChart1 with 0 for all dates in the selected range/week
        const currentLabelsDates = (startDateChart1 && endDateChart1) ? getDatesBetween(startDateChart1, endDateChart1) : thisWeekDates;
        currentLabelsDates.forEach(date => {
            dataForChart1[date] = 0;
        });

        filteredData.forEach(item => {
            if (dataForChart1.hasOwnProperty(item.tanggal)) { // Check if the date is in our current range/week
                dataForChart1[item.tanggal] += item.penyaluran;
            }
        });

        const finalChartLabels = (startDateChart1 && endDateChart1) ? currentLabelsDates.map(date => {
            const d = new Date(date);
            return `${d.getDate()}/${d.getMonth() + 1}`;
        }) : daysOfWeek;

        const finalPenyaluranValues = currentLabelsDates.map(date => dataForChart1[date] || 0);

        if (chartPenyaluran1) {
            chartPenyaluran1.destroy();
        }

        chartPenyaluran1 = new Chart(ctx, {
            type: 'line', // Changed to line chart as requested
            data: {
                labels: finalChartLabels,
                datasets: [{
                    label: 'Jumlah Penyaluran (pcs)',
                    data: finalPenyaluranValues,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2, // Appropriate for line chart
                    fill: false, // Don't fill area under the line
                    tension: 0.3 // Smooth line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Total Penyaluran per Hari (Cabang Anda - ${chartTitleSuffix})`,
                        font: { size: 16 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Penyaluran (pcs)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hari/Tanggal'
                        }
                    }
                }
            }
        });
    }

    // --- Chart 2: Pihak Kedua ---
    let chartPenyaluran2;
    function renderChart2() {
        const ctx = document.getElementById('chart-penyaluran-2').getContext('2d');
        
        let filteredData = materialData2;
        let chartLabels = daysOfWeek;
        let chartTitleSuffix = 'Minggu Ini';

        // Apply branch filter
        if (currentSelectedBranch2 !== "Semua Cabang") {
            filteredData = filteredData.filter(item => item.cabang === currentSelectedBranch2);
        }

        // Apply date range filter
        if (startDateChart2 && endDateChart2) {
            const start = new Date(startDateChart2);
            const end = new Date(endDateChart2);
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate >= start && itemDate <= end;
            });
            const datesInRange = getDatesBetween(startDateChart2, endDateChart2);
            chartLabels = datesInRange.map(date => {
                const d = new Date(date);
                return `${d.getDate()}/${d.getMonth() + 1}`; // e.g., 28/7
            });
            chartTitleSuffix = `${startDateChart2} s/d ${endDateChart2}`;
        } else {
             // If no date range is selected, default to current week's data
            filteredData = filteredData.filter(item => thisWeekDates.includes(item.tanggal));
        }

        // Aggregate data by date
        const dataForChart2 = {};
        // Initialize dataForChart2 with 0 for all dates in the selected range/week
        const currentLabelsDates = (startDateChart2 && endDateChart2) ? getDatesBetween(startDateChart2, endDateChart2) : thisWeekDates;
        currentLabelsDates.forEach(date => {
            dataForChart2[date] = 0;
        });

        filteredData.forEach(item => {
            if (dataForChart2.hasOwnProperty(item.tanggal)) { // Check if the date is in our current range/week
                dataForChart2[item.tanggal] += item.penyaluran;
            }
        });

        const finalChartLabels = (startDateChart2 && endDateChart2) ? currentLabelsDates.map(date => {
            const d = new Date(date);
            return `${d.getDate()}/${d.getMonth() + 1}`;
        }) : daysOfWeek;

        const finalPenyaluranValues = currentLabelsDates.map(date => dataForChart2[date] || 0);


        if (chartPenyaluran2) {
            chartPenyaluran2.destroy();
        }

        chartPenyaluran2 = new Chart(ctx, {
            type: 'line', // Always a line chart as requested
            data: {
                labels: finalChartLabels,
                datasets: [{
                    label: 'Jumlah Penyaluran (pcs)',
                    data: finalPenyaluranValues,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    fill: false, // Don't fill area under the line
                    tension: 0.3 // Smooth line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Total Penyaluran per Hari (${currentSelectedBranch2} - ${chartTitleSuffix})`,
                        font: { size: 16 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Penyaluran (pcs)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hari/Tanggal'
                        }
                    }
                }
            }
        });
    }

    // --- PDF Export Functionality ---
    document.getElementById('exportPdfBtn').addEventListener('click', async function() {
        const doc = new jsPDF();
        let yPos = 10;
        const margin = 10;
        const pageWidth = doc.internal.pageSize.getWidth();
        
        doc.setFontSize(22);
        doc.text("Laporan Grafik Penyaluran Material", pageWidth / 2, yPos, { align: 'center' });
        yPos += 20;

        // Add Chart 1
        const canvas1 = document.getElementById('chart-penyaluran-1');
        const imgData1 = canvas1.toDataURL('image/png');
        const imgProps1 = doc.getImageProperties(imgData1);
        const pdfWidth1 = pageWidth - (2 * margin);
        const pdfHeight1 = (imgProps1.height * pdfWidth1) / imgProps1.width;

        doc.setFontSize(16);
        doc.text(chartPenyaluran1.options.plugins.title.text, margin, yPos); // Use chart title
        yPos += 10;
        doc.addImage(imgData1, 'PNG', margin, yPos, pdfWidth1, pdfHeight1);
        yPos += pdfHeight1 + 20; // Space after chart

        // Check if new page is needed for Chart 2
        if (yPos + 50 > doc.internal.pageSize.getHeight()) { // 50 is approximate space for title + chart
            doc.addPage();
            yPos = 10;
        }

        // Add Chart 2
        const canvas2 = document.getElementById('chart-penyaluran-2');
        const imgData2 = canvas2.toDataURL('image/png');
        const imgProps2 = doc.getImageProperties(imgData2);
        const pdfWidth2 = pageWidth - (2 * margin);
        const pdfHeight2 = (imgProps2.height * pdfWidth2) / imgProps2.width;

        doc.setFontSize(16);
        doc.text(chartPenyaluran2.options.plugins.title.text, margin, yPos); // Use chart title
        yPos += 10;
        doc.addImage(imgData2, 'PNG', margin, yPos, pdfWidth2, pdfHeight2);

        doc.save('grafik_penyaluran_material.pdf');
    });

    // --- Event Listeners and Initial Renders ---
    document.addEventListener('DOMContentLoaded', function() {
        // Get today's date
        const today = new Date();
        // Calculate the start of the current week (Monday)
        const dayOfWeek = today.getDay(); // Sunday - 0, Monday - 1, etc.
        const diffToMonday = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); 
        const monday = new Date(today.setDate(diffToMonday));
        monday.setHours(0, 0, 0, 0); // Set to start of the day

        // Calculate the end of the current week (Sunday)
        const sunday = new Date(monday);
        sunday.setDate(monday.getDate() + 6);
        sunday.setHours(23, 59, 59, 999); // Set to end of the day

        // Set default dates for Chart 1 to current week
        startDateChart1 = monday.toISOString().slice(0, 10);
        endDateChart1 = sunday.toISOString().slice(0, 10);
        document.getElementById('start-date-chart-1').value = startDateChart1;
        document.getElementById('end-date-chart-1').value = endDateChart1;

        // Set default dates for Chart 2 to current week
        startDateChart2 = monday.toISOString().slice(0, 10);
        endDateChart2 = sunday.toISOString().slice(0, 10);
        document.getElementById('start-date-chart-2').value = startDateChart2;
        document.getElementById('end-date-chart-2').value = endDateChart2;

        // Render initial charts
        renderChart1();
        renderChart2();

        // Event Listeners for Chart 1 date range filters
        document.getElementById('start-date-chart-1').addEventListener('change', function () {
            startDateChart1 = this.value;
            renderChart1();
        });

        document.getElementById('end-date-chart-1').addEventListener('change', function () {
            endDateChart1 = this.value;
            renderChart1();
        });

        // Event Listeners for Chart 2 date range filters
        document.getElementById('start-date-chart-2').addEventListener('change', function () {
            startDateChart2 = this.value;
            renderChart2();
        });

        document.getElementById('end-date-chart-2').addEventListener('change', function () {
            endDateChart2 = this.value;
            renderChart2();
        });

        // Event Listener for Chart 2 Branch Filter
        document.querySelectorAll('[data-filter-cabang-chart]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                currentSelectedBranch2 = this.getAttribute('data-filter-cabang-chart');
                document.getElementById('dropdownCabangLain').textContent = this.textContent;
                renderChart2(); // Re-render chart 2 with new filter
            });
        });
    });
</script>
@endpush

@endsection