<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clean & Clear Warehouse</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('dashboard_template/assets/img/icon.PNG') }}" rel="icon">
  <link href="{{ asset('dashboard_template/assets/img/icon.PNG') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('landing_template/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing_template/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('landing_template/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('landing_template/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing_template/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('landing_template/assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

  {{-- HEADER --}}
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="/" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Clean & Clear Warehouse</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#work-process">Work Process</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="/login">Login</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1>Solusi Cerdas untuk Monitoring Stok Material</h1>
            <p>Sistem pemantau stok material lengkap dengan rekap aktivitas harian serta fitur ekspor dokumen.</p>
            <div class="d-flex">
              <a href="/register" class="btn-get-started">Registrasi Akun</a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
            <img src="{{ asset('landing_template/assets/img/hero-img.png') }}" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Clients Section -->
    <section id="clients" class="clients section light-background">

      <div class="container" data-aos="zoom-in">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 2,
                  "spaceBetween": 40
                },
                "480": {
                  "slidesPerView": 3,
                  "spaceBetween": 60
                },
                "640": {
                  "slidesPerView": 4,
                  "spaceBetween": 80
                },
                "992": {
                  "slidesPerView": 5,
                  "spaceBetween": 120
                },
                "1200": {
                  "slidesPerView": 6,
                  "spaceBetween": 120
                }
              }
            }
          </script>
          <div class="swiper-wrapper align-items-center">
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-1.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-2.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-3.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-4.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-5.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-6.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-7.webp') }}" class="img-fluid" alt=""></div>
            <div class="swiper-slide"><img src="{{ asset('landing_template/assets/img/clients/clients-8.webp') }}" class="img-fluid" alt=""></div>
          </div>
        </div>

      </div>

    </section><!-- /Clients Section -->

    <!-- About 1 Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Tentang</h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

          <div class="row">

            <div class="col-lg-6 d-flex align-items-center">
              <img src="{{ asset('landing_template/assets/img/illustration/illustration-10.webp') }}" class="img-fluid" alt="Ilustrasi Monitoring Stok Material">
            </div>

            <div class="col-lg-6 pt-4 pt-lg-0 content">

              <h3>Monitoring Transaksi Material Secara Realtime</h3>
              <p class="fst-italic">
                Website ini dirancang untuk memudahkan pemantauan transaksi material dari pusat <b>Pulau Layang</b> ke berbagai <b>Sales Area</b> 
                termasuk Jambi, Bengkulu, Lampung, dan 6 SA lainnya. 
              </p>
              <p>
                Seluruh data transaksi dapat dipantau secara <b>realtime</b>, dilengkapi fitur rekap aktivitas harian, 
                serta dapat diekspor ke dalam berbagai format dokumen. 
                Dengan sistem ini, pengguna lebih mudah dalam melacak <b>history transaksi material per region</b> 
                dan memastikan distribusi material berjalan transparan serta efisien.
              </p>

            </div>
          </div>

        </div>

      </div>

    </section>

    {{-- About 2 / FAQ --}}
    <section id="why-us" class="section why-us light-background" data-builder="section">

      <div class="container-fluid">

        <div class="row gy-4">

          <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">

            <div class="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
              <h3><span>Pertanyaan Umum </span><strong>Tentang Sistem Ini</strong></h3>
              <p>
                Berikut beberapa pertanyaan yang sering diajukan seputar teknologi dan fitur utama pada website monitoring stok material ini.
              </p>
            </div>

            <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

              <!-- FAQ 1 -->
              <div class="faq-item faq-active">
                <h3><span>01</span> Apa teknologi stack yang digunakan?</h3>
                <div class="faq-content">
                  <p>
                    Website ini dibangun menggunakan <b>Laravel</b> sebagai framework utama, 
                    <b>MySQL</b> untuk basis data, dan <b>Bootstrap</b> untuk tampilan berbasis SSR (Server Side Rendering).  
                    Selain itu, sistem ini juga menggunakan <b>Spatie Laravel Permission</b> untuk mengelola role dan akses pengguna.
                  </p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End FAQ item-->

              <!-- FAQ 2 -->
              <div class="faq-item">
                <h3><span>02</span> Apakah data transaksi dapat dipantau secara realtime?</h3>
                <div class="faq-content">
                  <p>
                    Ya, semua transaksi material dari pusat hingga cabang tercatat dan dapat dipantau secara <b>realtime</b>.  
                    Hal ini memudahkan manajemen dalam mengambil keputusan cepat dan akurat.
                  </p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End FAQ item-->

              <!-- FAQ 3 -->
              <div class="faq-item">
                <h3><span>03</span> Apakah tersedia fitur export laporan?</h3>
                <div class="faq-content">
                  <p>
                    Sistem menyediakan fitur <b>export ke Excel maupun dokumen</b> sehingga pengguna dapat dengan mudah 
                    membuat laporan transaksi dan rekap aktivitas harian untuk kebutuhan administrasi maupun analisis.
                  </p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End FAQ item-->

              <!-- FAQ 4 -->
              <div class="faq-item">
                <h3><span>04</span> Bagaimana kemudahan penggunaan sistem ini?</h3>
                <div class="faq-content">
                  <p>
                    Tampilan antarmuka dibuat sederhana dan intuitif, sehingga mudah dipahami baik oleh admin pusat maupun pengguna di level cabang.  
                    Semua menu navigasi dirancang agar proses monitoring lebih cepat dan efisien.
                  </p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End FAQ item-->

            </div>

          </div>

          <div class="col-lg-5 order-1 order-lg-2 why-us-img">
            <img src="{{ asset('landing_template/assets/img/why-us.png') }}" class="img-fluid" alt="FAQ Monitoring Stok" data-aos="zoom-in" data-aos-delay="100">
          </div>
        </div>

      </div>

    </section>

    <!-- About 3 -->
    <section id="about-3" class="about section">

      <div class="container">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

          <div class="row">

            <div class="col-lg-6 d-flex align-items-center">
              <img src="{{ asset('landing_template/assets/img/illustration/illustration-10.webp') }}" class="img-fluid" alt="Ilustrasi Fitur Utama">
            </div>

            <div class="col-lg-6 pt-4 pt-lg-0 content">

              <h3>Fitur Utama Sistem Monitoring</h3>
              <p class="fst-italic">
                Sistem ini tidak hanya mencatat transaksi, tetapi juga dirancang untuk memberikan kemudahan, kecepatan, dan transparansi dalam proses distribusi material.
              </p>

              <ul>
                <li><i class="bi bi-check-circle"></i> Pemantauan stok material secara realtime dari pusat hingga cabang.</li>
                <li><i class="bi bi-check-circle"></i> Rekap aktivitas harian otomatis dan bisa diekspor ke Excel/Dokumen.</li>
                <li><i class="bi bi-check-circle"></i> Pengelolaan akses berbasis role & permission menggunakan Spatie.</li>
                <li><i class="bi bi-check-circle"></i> Dashboard interaktif yang menampilkan data transaksi per region.</li>
              </ul>

            </div>
          </div>

        </div>

      </div>

    </section>

    <!-- Features Section -->
    <section id="features" class="services section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Fitur Utama</h2>
        <p>Sistem ini dilengkapi berbagai fitur yang memudahkan monitoring, pelaporan, dan pengelolaan stok material secara terpusat maupun per cabang.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item position-relative">
              <div class="icon"><i class="bi bi-activity icon"></i></div>
              <h4><a href="#" class="stretched-link">Monitoring Realtime</a></h4>
              <p>Pemantauan stok material dari pusat hingga cabang yang selalu terupdate secara realtime.</p>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon"><i class="bi bi-clipboard-data icon"></i></div>
              <h4><a href="#" class="stretched-link">Rekap Harian</a></h4>
              <p>Aktivitas transaksi material otomatis direkap harian untuk memudahkan pengawasan.</p>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon"><i class="bi bi-file-earmark-excel icon"></i></div>
              <h4><a href="#" class="stretched-link">Export Laporan</a></h4>
              <p>Data dapat diekspor ke Excel atau dokumen lain untuk kebutuhan laporan dan analisis.</p>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon"><i class="bi bi-shield-lock icon"></i></div>
              <h4><a href="#" class="stretched-link">Role & Permission</a></h4>
              <p>Pengelolaan akses pengguna berbasis role untuk menjaga keamanan dan keteraturan data.</p>
            </div>
          </div><!-- End Feature Item -->

        </div>

      </div>

    </section><!-- /Features Section -->


    <!-- Work Process Section -->
    <section id="work-process" class="work-process section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Alur Penggunaan Sistem</h2>
        <p>Sistem monitoring ini dirancang sederhana dan terstruktur, mulai dari login hingga export dokumen, sehingga memudahkan pengguna dalam setiap proses pengelolaan material.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">

          <!-- Step 1 -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="steps-item">
              <div class="steps-image">
                <img src="{{ asset('landing_template/assets/img/steps/steps-1.webp') }}" alt="Step 1" class="img-fluid" loading="lazy">
              </div>
              <div class="steps-content">
                <div class="steps-number">01</div>
                <h3>Login &amp; Registrasi</h3>
                <p>Pengguna masuk ke sistem dengan akun yang sudah terdaftar atau melakukan registrasi terlebih dahulu sebelum mengakses fitur.</p>
                <div class="steps-features">
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Login</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Registrasi Akun</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Akses Dashboard</span></div>
                </div>
              </div>
            </div><!-- End Steps Item -->
          </div>

          <!-- Step 2 -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="steps-item">
              <div class="steps-image">
                <img src="{{ asset('landing_template/assets/img/steps/steps-2.webp') }}" alt="Step 2" class="img-fluid" loading="lazy">
              </div>
              <div class="steps-content">
                <div class="steps-number">02</div>
                <h3>Kelola Data Material & Region</h3>
                <p>CRUD data stok material serta data Region SPBE/BPT untuk memastikan setiap transaksi tercatat sesuai lokasi.</p>
                <div class="steps-features">
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Tambah & Update Material</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Kelola Region SPBE/BPT</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Manajemen Data</span></div>
                </div>
              </div>
            </div><!-- End Steps Item -->
          </div>

          <!-- Step 3 -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
            <div class="steps-item">
              <div class="steps-image">
                <img src="{{ asset('landing_template/assets/img/steps/steps-3.webp') }}" alt="Step 3" class="img-fluid" loading="lazy">
              </div>
              <div class="steps-content">
                <div class="steps-number">03</div>
                <h3>Transaksi Material</h3>
                <p>Melakukan transaksi penerimaan atau penyaluran produk material antar SPBE/BPT atau dari pusat.</p>
                <div class="steps-features">
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Penerimaan Material</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Penyaluran Material</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Distribusi Antar Region</span></div>
                </div>
              </div>
            </div><!-- End Steps Item -->
          </div>

        </div>

        <div class="row gy-5 mt-4">

          <!-- Step 4 -->
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="500">
            <div class="steps-item">
              <div class="steps-image">
                <img src="{{ asset('landing_template/assets/img/steps/steps-1.webp') }}" alt="Step 4" class="img-fluid" loading="lazy">
              </div>
              <div class="steps-content">
                <div class="steps-number">04</div>
                <h3>Log Aktivitas Harian</h3>
                <p>Semua transaksi otomatis tercatat dalam log aktivitas harian, sehingga memudahkan pelacakan dan evaluasi.</p>
                <div class="steps-features">
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Catatan Otomatis</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>History Transaksi</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Monitoring Harian</span></div>
                </div>
              </div>
            </div><!-- End Steps Item -->
          </div>

          <!-- Step 5 -->
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="600">
            <div class="steps-item">
              <div class="steps-image">
                <img src="{{ asset('landing_template/assets/img/steps/steps-2.webp') }}" alt="Step 5" class="img-fluid" loading="lazy">
              </div>
              <div class="steps-content">
                <div class="steps-number">05</div>
                <h3>Export Dokumen</h3>
                <p>Data transaksi dan rekap harian dapat diekspor ke dokumen Excel atau format lain untuk kebutuhan laporan.</p>
                <div class="steps-features">
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Export Excel</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Laporan Otomatis</span></div>
                  <div class="feature-item"><i class="bi bi-check-circle"></i><span>Dokumentasi Data</span></div>
                </div>
              </div>
            </div><!-- End Steps Item -->
          </div>

        </div>

      </div>

    </section><!-- /Work Process Section -->


    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <div class="container section-title" data-aos="fade-up">
        <h2>Kontak Kami</h2>
        <p>Kami siap membantu Anda. Hubungi kami melalui informasi di bawah ini atau kunjungi langsung gudang kami untuk konsultasi lebih lanjut.</p>
      </div><div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="col-lg-12">

          <div class="info-wrap">
            <div class="row">

              <div class="col-md-4 info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h3>Alamat Gudang</h3>
                  <p>Jl. Raya Rungkut Industri No. 10, Surabaya, Jawa Timur 60293</p>
                </div>
              </div><div class="col-md-4 info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-telephone flex-shrink-0"></i>
                <div>
                  <h3>Telepon & WhatsApp</h3>
                  <p>+62 31 1234 5678 (Office)<br>+62 812 3456 7890 (WhatsApp)</p>
                </div>
              </div><div class="col-md-4 info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h3>Email</h3>
                  <p>info@cleanclearwarehouse.com<br>sales@cleanclearwarehouse.com</p>
                </div>
              </div></div>

            <div class="mt-4" data-aos="fade-up" data-aos-delay="500">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15829.41207914023!2d112.7843403871582!3d-7.314592099999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb07399b19c3%3A0x1e3272ba2033a37d!2sRungkut%20Industri!5e0!3m2!1sen!2sid!4v1693129541587!5m2!1sen!2sid" frameborder="0" style="border:0; width: 100%; height: 384px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

          </div>
        </div>

      </div>

    </section>
  </main>

  {{-- FOOTER --}}
  <footer id="footer" class="footer">

    <div class="footer-newsletter">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-6">
            <h4>Dapatkan Update & Tips Terbaru</h4>
            <p>Daftarkan email Anda untuk menerima informasi terbaru seputar monitoring stok, laporan aktivitas harian, dan tips manajemen distribusi.</p>
          </div>
        </div>
      </div>
    </div>


    <div class="container footer-top">
      <div class="row gy-4 justify-content-between">
        
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">Clean & Clear Warehouse</span>
          </a>
          <p class="mt-3">Solusi pergudangan modern yang mengedepankan kebersihan, keamanan, dan transparansi untuk mendukung pertumbuhan bisnis Anda.</p>
          <div class="footer-contact pt-3">
            <p>Jl. Raya Rungkut Industri No. 10</p>
            <p>Surabaya, Jawa Timur 60293</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+62 812 3456 7890</span></p>
            <p><strong>Email:</strong> <span>info@cleanclearwarehouse.com</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Link Navigasi</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#hero">Home</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#about">Tentang Kami</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#features">Fitur</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#contact">Kontak</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Fitur Utama Sistem</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#features">Monitoring Stok Material</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#features">Rekap Aktivitas Harian</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#features">Export Data ke Excel/Dokumen</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#features">Manajemen Akses (Role & Permission)</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright 2025</span> <strong class="px-1 sitename">Clean & Clear Warehouse</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        Created by Myself and thanks to <a href="https://www.creative-tim.com/product/argon-dashboard">BootstrapMade and Argon Dashboard</a> to share cool design 
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('landing_template/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('landing_template/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('landing_template/assets/js/main.js') }}"></script>

</body>

</html>