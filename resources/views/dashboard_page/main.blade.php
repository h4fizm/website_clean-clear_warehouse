<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard_template/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('dashboard_template/assets/img/icon.PNG') }}">
  <title>
    @yield('title')
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome (latest version) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('dashboard_template/assets/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-dark position-absolute w-100"></div>
  
  {{-- SIDEBAR SECTION --}}
  @include('dashboard_page.partials.sidebar')
  {{-- END OF SIDEBAR SECTION --}}
  
  <main class="main-content position-relative border-radius-lg ">
   
    {{-- NAVBAR SECTION --}}
    @include('dashboard_page.partials.header')
    {{-- END OF NAVBAR SECTION --}}
    
    
    <div class="container-fluid py-4">
    {{-- CONTENTS SECTION --}}
    @yield('content')
    {{-- END OF CONTENTS SECTION --}}

      {{-- FOOTER SECTION --}}
      @include('dashboard_page.partials.footer')
      {{-- END OF FOOTER SECTION --}}

    </div>
  </main>
  
  @stack('scripts')
  <!--   Core JS Files   -->
  <script src="{{ asset('dashboard_template/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/chartjs.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('dashboard_template/assets/js/argon-dashboard.min.js?v=2.1.0') }}"></script>
</body>

</html>