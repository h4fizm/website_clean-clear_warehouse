<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard_template/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('dashboard_template/assets/img/favicon.png') }}">
  <title>Laman Registrasi Akun</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="{{ asset('dashboard_template/assets/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="">
  <main class="main-content mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Laman Registrasi</h4>
                  <p class="mb-0">Registrasi Anggota Baru</p>
                </div>
                <div class="card-body">
                  <form role="form" method="POST" action="{{ route('register.store') }}" id="registerForm">
                    @csrf
                    <div class="mb-3">
                      <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Nama Lengkap" aria-label="Name" value="{{ old('name') }}">
                      <div class="invalid-feedback" data-for="name"></div>
                    </div>
                    <div class="mb-3">
                      <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email" aria-label="Email" value="{{ old('email') }}">
                      <div class="invalid-feedback" data-for="email"></div>
                    </div>
                    <div class="mb-3">
                      <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" aria-label="Password">
                      <div class="invalid-feedback" data-for="password"></div>
                    </div>
                    <div class="mb-3">
                      <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Ulangi Password" aria-label="Password Confirmation">
                    </div>
                    <div class="mb-3">
                      <select name="role" class="form-control form-control-lg @error('role') is-invalid @enderror" aria-label="Pilih Region">
                        <option selected disabled>Pilih Region</option>
                        @foreach ($roles as $role)
                          <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                          </option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback" data-for="role"></div>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-lg btn-success w-100 mt-4 mb-0">Register</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    Sudah Punya Akun ? Silakan Login
                    <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Disini</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg'); background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Attention is the new currency"</h4>
                <p class="text-white position-relative">The more effortless the writing looks, the more effort the writer actually put into the process.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <script src="{{ asset('dashboard_template/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('dashboard_template/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="{{ asset('dashboard_template/assets/js/argon-dashboard.min.js?v=2.1.0') }}"></script>

  <script>
    document.getElementById('registerForm').addEventListener('submit', async function(event) {
      // Mencegah form dikirim secara normal
      event.preventDefault();

      const form = this;
      const formData = new FormData(form);
      const actionUrl = form.action;

      // Hapus pesan error lama
      document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
      document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

      try {
        const response = await fetch(actionUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
        });

        const result = await response.json();

        if (response.ok) {
          // Jika sukses (HTTP status 200-299)
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            // Arahkan ke halaman login setelah alert ditutup
            window.location.href = "{{ route('login') }}";
          });
        } else if (response.status === 422) {
          // Jika terjadi error validasi (HTTP status 422)
          const errors = result.errors;
          for (const key in errors) {
            const input = form.querySelector(`[name="${key}"]`);
            const errorDiv = form.querySelector(`.invalid-feedback[data-for="${key}"]`);
            if (input) {
              input.classList.add('is-invalid');
            }
            if (errorDiv) {
              errorDiv.textContent = errors[key][0];
            }
          }
        } else {
          // Error server lainnya
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: result.message || 'Terjadi kesalahan pada server.'
          });
        }
      } catch (error) {
        // Error network atau lainnya
        console.error('Fetch error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Tidak dapat terhubung ke server.'
        });
      }
    });
  </script>
</body>

</html>