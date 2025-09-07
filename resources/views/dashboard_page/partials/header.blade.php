<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
    <div class="container-fluid py-1 px-3">
        @php
            $menuMap = [
                'dashboard'         => ['label' => 'Dashboard', 'url' => '/dashboard'],
                'pusat'             => ['label' => 'Data P.Layang (Pusat)', 'url' => '/pusat'],
                'pusat/create'      => ['label' => 'Tambah Data Material', 'url' => '/pusat/create'],
                'transaksi'         => ['label' => 'Data Transaksi', 'url' => '/transaksi'],
                'transaksi/tambah'  => ['label' => 'Tambah SPBE/BPT', 'url' => '/transaksi/tambah'],
                'upp-material'      => ['label' => 'UPP Material', 'url' => '/upp-material'],
                // Mengubah label untuk halaman 'aktivitas'
                'aktivitas'         => ['label' => 'Aktivitas Harian', 'url' => '/aktivitas'],
                'pengguna'          => ['label' => 'Manajemen User', 'url' => '/pengguna'],
            ];

            $path = trim(request()->path(), '/');
            $breadcrumbs = [['label' => 'Menu']];

            // Menangani kasus 'aktivitas-transaksi' secara spesifik
            if ($path === 'aktivitas-transaksi') {
                $breadcrumbs[] = ['label' => 'Aktivitas Harian', 'url' => null];
            } elseif (isset($menuMap[$path])) {
                $breadcrumbs[] = $menuMap[$path];
            } elseif (request()->routeIs('materials.index')) {
                $facility = request()->route('facility'); 
                $breadcrumbs[] = $menuMap['transaksi'];
                $breadcrumbs[] = ['label' => 'Daftar Stok Material - ' . ($facility->nama ?? 'SPBE/BPT'), 'url' => null];
            } elseif ($path === 'profil') {
                $breadcrumbs[] = $menuMap['pengguna']; 
                $breadcrumbs[] = ['label' => 'Edit Profil', 'url' => null];
            } else {
                $breadcrumbs[] = ['label' => 'Menu', 'url' => null];
            }
        @endphp

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                @foreach ($breadcrumbs as $key => $crumb)
                    @if ($key === count($breadcrumbs) - 1 || empty($crumb['url']))
                        <li class="breadcrumb-item text-sm text-white">{{ $crumb['label'] }}</li>
                    @else
                        <li class="breadcrumb-item text-sm">
                            <a class="text-white" href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">
                {{ $pageTitle ?? ($breadcrumbs[count($breadcrumbs)-1]['label'] ?? 'Menu') }}
            </h6>
        </nav>

        <div class="navbar-right-icons">
            {{-- Burger --}}
            <li class="nav-item d-xl-none d-flex align-items-center list-unstyled">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </li>

            {{-- User --}}
            @auth
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link text-white d-flex align-items-center"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2" style="font-size: 1.4rem;"></i>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-2 invisible"
                        aria-labelledby="userDropdown" id="userDropdownMenu">
                        <li>
                            <a class="dropdown-item" href="/profil"><i class="fas fa-user me-2"></i> Profile</a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @endauth
        </div>
    </div>
</nav>