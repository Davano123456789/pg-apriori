<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'InApp Inventory Dashboard')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('Dashboard-tamplate/src/assets/images/favicon_io/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('Dashboard-tamplate/src/assets/images/favicon_io/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('Dashboard-tamplate/src/assets/images/favicon_io/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('Dashboard-tamplate/src/assets/images/favicon_io/site.webmanifest') }}">

  <!-- CDN Assets -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .sidebar { width: 250px; height: 100vh; position: fixed; background: #fff; border-right: 1px solid #eee; padding-top: 70px; z-index: 100; }
    .topbar { height: 70px; z-index: 1000; }
    .content { margin-left: 250px; padding-top: 90px; min-height: 100vh; }
    .nav-link { color: #666; padding: 10px 20px; display: flex; align-items: center; gap: 10px; }
    .nav-link.active { color: #0d6efd; background: rgba(13, 110, 253, 0.05); border-right: 3px solid #0d6efd; }
    .nav-link:hover { background: #f8f9fa; }
    .logo-area { position: fixed; top: 0; left: 0; width: 250px; height: 70px; display: flex; align-items: center; padding: 0 20px; background: #fff; z-index: 1100; border-right: 1px solid #eee; border-bottom: 1px solid #eee; }
    .avatar { object-fit: cover; }
    .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; }
    .sidebar.show { transform: translateX(0); z-index: 1100; padding-top: 20px; }
    @media (max-width: 991.98px) {
      .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; z-index: 1100; padding-top: 20px; }
      .content { margin-left: 0; }
      .overlay.show { display: block; }
      .topbar-toggle { display: block !important; }
    }
    .topbar-toggle { display: none; }
  </style>

  @stack('styles')
</head>

<body>
  <div id="overlay" class="overlay"></div>
  <!-- TOPBAR -->
  <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <div class="container-fluid">
      <div class="d-flex align-items-center">
        <button class="btn btn-light border-0 me-2 topbar-toggle" id="sidebarToggle">
          <i class="ti ti-menu-2 fs-4"></i>
        </button>
        <a href="{{ url('/') }}" class="navbar-brand fw-bold text-dark fs-4 m-0">Palu Gada</a>
      </div>
      <div class="ms-auto">
        <!-- Right side empty -->
      </div>
    </div>
  </nav>

  <!-- SIDEBAR -->
  @include('layouts.sidebar')

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
        @yield('content')
        
        <!-- FOOTER -->
        @include('layouts.footer')
    </div>
  </main>

  <!-- jQuery and Bootstrap JS Bundle with Popper -->
  <style>
    .bg-light-primary {
        background-color: rgba(102, 126, 234, 0.1) !important;
    }
    .text-primary {
        color: #667eea !important;
    }
    .bg-primary {
        background-color: #667eea !important;
    }
    .border-dashed {
        border-bottom: 1px dashed #ddd !important;
    }
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        vertical-align: middle;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function() {
      @if(session('success'))
        Swal.fire({
          title: 'Berhasil!',
          text: "{{ session('success') }}",
          icon: 'success',
          confirmButtonText: 'Oke'
        });
      @endif

      @if(session('error'))
        Swal.fire({
          title: 'Gagal!',
          text: "{{ session('error') }}",
          icon: 'error',
          confirmButtonText: 'Oke'
        });
      @endif

      @if($errors->any())
        Swal.fire({
          title: 'Gagal!',
          text: "{{ $errors->first() }}",
          icon: 'error',
          confirmButtonText: 'Oke'
        });
      @endif
      // Sidebar Toggle Logic
      $('#sidebarToggle, #closeSidebar, #overlay').on('click', function() {
        $('.sidebar').toggleClass('show');
        $('#overlay').toggleClass('show');
      });
    });
  </script>
  @stack('scripts')
</body>

</html>
