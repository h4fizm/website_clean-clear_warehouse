<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf--8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard_template/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('dashboard_template/assets/img/favicon.png') }}">
  <title>
    Laman Login
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="{{ asset('dashboard_template/assets/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
</head>

<body class="">
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 text-center mx-auto">
            <h1 class="text-white mb-2 mt-5">Laman Login</h1>
            <p class="text-lead text-white">Silahkan masukkan email dan password Anda untuk masuk ke sistem.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="container pb-5">
      <div class="row mt-lg-n15 mt-md-n11 mt-n10 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
          <div class="card z-index-0">
            <div class="card-header text-center pt-4">
              <h5>Silahkan Login Akun</h5>
            </div>
            <div class="card-body">

              {{-- BAGIAN NOTIFIKASI ERROR --}}
              @error('email')
                <div class="alert alert-danger text-white" role="alert">
                    <span class="text-sm">{{ $message }}</span>
                </div>
              @enderror
              
              {{-- UBAH BAGIAN FORM --}}
              <form role="form" method="POST" action="{{ route('login.authenticate') }}">
                @csrf
                <div class="mb-3">
                  <input type="email" class="form-control" placeholder="Email" aria-label="Email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                  <input type="password" class="form-control" placeholder="Password" aria-label="Password" name="password" required>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary w-100 my-4 mb-2">Login</button>
                </div>
                <p class="text-sm mt-3 mb-0 text-center">
                  Belum Punya Akun ? Silahkan Registrasi
                  <a href="/register" class="text-dark font-weight-bolder">Disini</a>
                </p>
              </form>

            </div>
          </div>
          
          <div class="text-center mt-4">
              <a href="/" class="text-secondary">
                  <i class="fas fa-home me-1"></i>
                  Kembali ke Beranda
              </a>
          </div>

        </div>
      </div>
    </div>
  </main>
  <footer class="footer py-5 mt-n5">
    <div class="container">
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
            Copyright © <script>
              document.write(new Date().getFullYear())
            </script> Soft by Creative Tim.
          </p>
        </div>
      </div>
    </div>
  </footer>
  <script src="{{ asset('dashboard_template/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="{{ asset('dashboard_template/assets/js/argon-dashboard.min.js?v=2.1.0') }}"></script>
</body>

</html>