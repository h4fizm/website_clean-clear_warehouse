@push('styles')
<style>
  .invisible {
    visibility: hidden;
  }
  .dropdown.show .dropdown-menu {
    visibility: visible !important;
  }
</style>

<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
  <div class="container-fluid py-1 px-3 d-flex justify-content-between align-items-center">

    <!-- Left: Breadcrumb -->
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
          <li class="breadcrumb-item text-sm"><span class="opacity-5 text-white">Menu</span></li>
          <li class="breadcrumb-item text-sm">
            <a class="text-white" href="#">Nama Menu</a>
          </li>
        </ol>
        <h6 class="font-weight-bolder text-white mb-0">Nama Menu</h6>
      </nav>
    </div>

    {{-- Sidenav Burger Button --}}
    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
      <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
        <div class="sidenav-toggler-inner">
          <i class="sidenav-toggler-line bg-white"></i>
          <i class="sidenav-toggler-line bg-white"></i>
          <i class="sidenav-toggler-line bg-white"></i>
        </div>
      </a>
    </li>

    <!-- User Dropdown -->
    <ul class="navbar-nav d-flex align-items-center">
      <li class="nav-item dropdown">
        <a href="#" class="nav-link text-white d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user-circle me-2" style="font-size: 1.4rem;"></i>
          <span class="d-none d-sm-inline">Nama User</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end mt-2 invisible" aria-labelledby="userDropdown" id="userDropdownMenu">
          <li>
            <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a>
          </li>
          <li>
            <a class="dropdown-item text-danger" href="#" onclick="logout()"> 
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>

  </div>
</nav>
<!-- End Navbar -->

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Tunggu hingga Bootstrap aktif, baru hapus invisible
    const dropdown = document.querySelector('.dropdown');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    if (dropdown && dropdownMenu) {
      dropdown.addEventListener('show.bs.dropdown', function () {
        dropdownMenu.classList.remove('invisible');
      });
    }
  });
  function logout() {
    // Hapus semua local storage / sessionStorage jika ada (jika pakai frontend SPA)
    // window.localStorage.clear();

    // Redirect paksa ke /login
    window.location.href = '/login';
  }
</script>
@endpush

