<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0 d-flex align-items-center" href="#">
      <img src="{{ asset('dashboard_template/assets/img/favicon.png') }}" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-2 font-weight-bold">Clean & Clear Warehouse</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main" style="max-height: none !important; height: auto !important; overflow: hidden !important;">
    <ul class="navbar-nav">
      <!-- Dashboard Menu Item -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Dashboard</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ url('dashboard') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-tachometer-alt text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <!-- Section: Transaksi -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Transaksi</h6>
      </li>
      {{-- Penyaluran/Penerimaan --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi/penyaluran') ? 'active' : '' }}" href="{{ url('/transaksi') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-exchange-alt text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Penyaluran/Penerimaan</span>
        </a>
      </li>
      {{-- Tambah Data Cabang --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi/stok-keseluruhan') ? 'active' : '' }}" href="{{ url('/cabang') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-building text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tambah Data Cabang</span>
        </a>
      </li>
      {{-- Tambah Data SPBE & BPT --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi/stok-keseluruhan') ? 'active' : '' }}" href="{{ url('/spbe-bpt') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-boxes text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tambah Data SPBE & BPT</span>
        </a>
      </li>
      {{-- Tambah Material --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi/stok-keseluruhan') ? 'active' : '' }}" href="{{ url('/material') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-cube text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tambah Data Material</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger {{ request()->is('transaksi/recycle') ? 'active' : '' }}" href="{{ url('transaksi/recycle') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-sync-alt text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Recycle Material</span>
        </a>
      </li>
     

      <!-- Section: Analisis -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Laporan</h6>
      </li>
      {{-- Laporan Grafik --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('analisis/grafik') ? 'active' : '' }}" href="{{ url('analisis/grafik') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-chart-line text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Laporan Grafik</span>
        </a>
      </li>
      {{-- Aktivitas Harian --}}
      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi/aktivitas') ? 'active' : '' }}" href="{{ url('transaksi/aktivitas') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-calendar-check text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Aktivitas Harian</span>
        </a>
      </li>

      <!-- Section: Pengguna -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Pengguna</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->is('pengguna') ? 'active' : '' }}" href="{{ url('pengguna') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-users-cog text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Manajemen User</span>
        </a>
      </li>
    </ul>
  </div>
</aside>
