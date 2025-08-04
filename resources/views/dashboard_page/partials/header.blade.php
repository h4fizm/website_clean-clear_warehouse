@push('styles')
<style>
    /* Custom styles for burger button positioning on mobile */
    .invisible {
        visibility: hidden;
    }
    .dropdown.show .dropdown-menu {
        visibility: visible !important;
    }

    /* General Navbar layout */
    .navbar-main .container-fluid {
        display: flex;
        justify-content: space-between; /* Pushes left and right sections apart */
        align-items: center;
        width: 100%;
        /* Set very minimal horizontal padding directly on container-fluid */
        padding-left: 0.5rem !important; /* Adjusted from 0.75rem to 0.5rem */
        padding-right: 0.5rem !important; /* Adjusted from 0.75rem to 0.5rem */
        box-sizing: border-box; /* Ensures padding is included in width */
    }

    /* Group for Burger and User Icon on the right */
    .navbar-right-icons {
        display: flex;
        align-items: center;
        gap: 1rem; /* Still no gap between them */
        /* Ensure no extra padding/margin from this group */
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Specific adjustment for the burger button within the right-icons group */
    .navbar-right-icons .nav-item.d-xl-none {
        /* No specific padding-right here, let the overall container-fluid padding handle spacing */
        padding: 0 !important; /* Remove all padding from this list item */
        margin: 0 !important; /* Remove all margin from this list item */
    }

    /* Ensure breadcrumb text doesn't wrap excessively on smaller screens */
    .navbar-main .breadcrumb {
        flex-shrink: 1; /* Allow breadcrumb to shrink */
        min-width: 0; /* Allow content to overflow if needed, or truncate */
        /* Ensure no extra padding/margin from breadcrumb */
        padding: 0 !important;
        margin: 0 !important;
        margin-left: 0.5rem !important; /* Keep a minimal margin if needed, otherwise set to 0 */
    }
    .navbar-main .breadcrumb li {
        white-space: nowrap; /* Prevent individual breadcrumb items from wrapping */
        overflow: hidden;
        text-overflow: ellipsis; /* Add ellipsis if text is too long */
    }
    .navbar-main .breadcrumb h6 {
        white-space: nowrap; /* Prevent title from wrapping */
        overflow: hidden;
        text-overflow: ellipsis; /* Add ellipsis if text is too long */
    }

    /* Mobile specific adjustments */
    @media (max-width: 991.98px) { /* Applies to screens smaller than large (xl-none) */
        .navbar-main .breadcrumb {
            margin-right: 0.5rem !important; /* Keep a minimal margin between breadcrumb and right icons group */
        }
        /* Hide "Nama User" text on small screens to save space */
        .navbar-nav .d-sm-inline {
            display: none !important;
        }
        /* User icon itself needs no extra spacing when text is hidden */
        .navbar-nav .nav-link {
            padding-right: 0 !important; /* Remove any default padding from user icon link */
            padding-left: 0 !important; /* Remove any default padding from user icon link */
        }
        .navbar-nav .nav-item {
            padding-right: 0 !important; /* Ensure the li itself for user dropdown has no extra padding */
        }
    }

    /* Desktop specific adjustments (if any are needed to override mobile) */
    @media (min-width: 992px) {
        /* On desktop, burger button is hidden by d-xl-none, so no specific styling needed for its placement */
        .navbar-main .container-fluid {
            padding-left: 1rem !important; /* Reset to more standard padding for desktop */
            padding-right: 1rem !important; /* Reset to more standard padding for desktop */
        }
        .navbar-main .breadcrumb {
            margin: 0; /* Reset all margins */
            padding: 0; /* Reset all paddings */
        }
        .navbar-nav .d-sm-inline {
            display: inline !important; /* Ensure "Nama User" text is visible on larger screens */
        }
        .navbar-right-icons {
            gap: 0; /* Ensure no gap on desktop */
            padding: 0; /* Reset padding for desktop */
            margin: 0; /* Reset margin for desktop */
        }
        .navbar-right-icons .nav-item.d-xl-none {
            padding: 0; /* Reset for desktop as it's hidden */
            margin: 0;
        }
         .navbar-nav .nav-link {
            padding: 0.5rem 0.5rem !important; /* Standard padding for nav links on desktop */
        }
        .navbar-nav .nav-item {
            padding-left: 0.75rem !important; /* Bootstrap default nav-item spacing */
        }
    }
</style>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
    <div class="container-fluid py-1 px-3"> {{-- Keep original classes, override with custom CSS --}}

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

        <div class="navbar-right-icons">
            {{-- Sidenav Burger Button - Visible only on extra large screens and below --}}
            <li class="nav-item d-xl-none d-flex align-items-center list-unstyled">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                        <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </li>

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

    </div>
</nav>
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