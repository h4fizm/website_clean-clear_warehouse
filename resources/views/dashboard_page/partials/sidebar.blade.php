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

      {{-- ================= Menu Dashboard ================= --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Dashboard</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-tachometer-alt text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      {{-- ================= Menu Transaksi ================= --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Transaksi</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('pusat') ? 'active' : '' }}" href="{{ url('/pusat') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-warehouse text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Data P.Layang (Pusat)</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('transaksi') ? 'active' : '' }}" href="{{ url('/transaksi') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-exchange-alt text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Data Transaksi</span>
        </a>
      </li>

      <li class="nav-item">
        {{-- Menggunakan helper route() untuk URL dan routeIs() untuk status active --}}
        <a class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}" href="{{ route('transaksi.create') }}">
            <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-industry text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Tambah SPBE/BPT</span>
        </a>
    </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('tambah-material') ? 'active' : '' }}" href="{{ url('/tambah-material') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-box text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tambah Data Material</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link text-danger {{ request()->is('upp-material') ? 'active' : '' }}" href="{{ url('/upp-material') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-sync-alt text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">UPP Material</span>
        </a>
      </li>

      {{-- ================= Menu Laporan ================= --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Laporan</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('laporan-grafik') ? 'active' : '' }}" href="{{ url('laporan-grafik') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-chart-line text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Laporan Grafik</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('aktivitas') ? 'active' : '' }}" href="{{ url('aktivitas') }}">
          <div class="icon icon-shape icon-sm text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-calendar-check text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Aktivitas Harian</span>
        </a>
      </li>

      {{-- ================= Menu Pengguna ================= --}}
      @role('Manager')
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
      @endrole

    </ul>

  </div>
</aside>
