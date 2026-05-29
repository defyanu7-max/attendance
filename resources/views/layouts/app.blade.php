<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PPNI System | @yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- CSS Template Akademi -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/bootstrap-icons/font/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metismenu/css/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- CSS Custom PPNI -->
    <link href="{{ asset('css/ppni-custom.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">

    @stack('styles')

    <!-- Livewire styles -->
    @livewireStyles
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <div class="dots">
                <div class="dot mainDot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <div id="main-wrapper">
        <!-- Nav Header -->
        <div class="nav-header">
            <a href="{{ route('dashboard') }}" class="brand-logo">
                <span class="brand-title fw-bold fs-4" style="color: var(--primary);">
                    PPNI System
                </span>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
            </div>
        </div>

        <!-- Header (topbar) -->
        @include('elements.header-ppni')

        <!-- Sidebar -->
        @include('elements.sidebar-ppni')

        <!-- Content Body -->
        <div class="content-body default-height">
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        <!-- Footer -->
        @include('elements.footer-ppni')
    </div>

    <!-- JS Template Akademi (urutan WAJIB diikuti) -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('vendor/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <!-- Livewire scripts (WAJIB setelah jQuery) -->
    @livewireScripts

    {{-- ================================================================
         PPNI System — Livewire ↔ jQuery/Template Sync
         Mengatasi konflik antara plugin jQuery template Akademi
         dengan lifecycle Livewire 3 (navigate, morph, updated).
    ================================================================ --}}
    <script>
    (function () {
        // ----------------------------------------------------------------
        // Helper: inisialisasi ulang semua plugin jQuery setelah
        // Livewire mengganti DOM (navigate atau morph update).
        // ----------------------------------------------------------------
        function reinitPlugins() {
            // 1. MetisMenu — sidebar navigation tree
            if (typeof $.fn.metisMenu === 'function') {
                try { $('#menu').metisMenu(); } catch (e) {}
            }

            // 2. Bootstrap Select
            if (typeof $.fn.selectpicker === 'function') {
                try { $('select.selectpicker').selectpicker('refresh'); } catch (e) {}
            }

            // 3. Bootstrap Tooltips (BS5 API)
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    var existing = bootstrap.Tooltip.getInstance(el);
                    if (!existing) { new bootstrap.Tooltip(el, { trigger: 'hover' }); }
                });
            }

            // 4. Perfect Scrollbar (sidebar scroll)
            if (typeof PerfectScrollbar !== 'undefined') {
                var ps = document.querySelector('.dlabnav-scroll');
                if (ps && !ps._ps) {
                    ps._ps = new PerfectScrollbar(ps);
                }
            }
        }

        // ----------------------------------------------------------------
        // livewire:navigated — dipanggil setelah navigate:true redirect
        // PENTING: sembunyikan #preloader agar tidak infinite-loading
        // setelah login redirect ke dashboard.
        // ----------------------------------------------------------------
        document.addEventListener('livewire:navigated', function () {
            // Sembunyikan preloader template
            $('#preloader').fadeOut(400, function () { $(this).remove(); });

            // Re-init semua plugin
            reinitPlugins();

            // Scroll ke atas setelah navigasi (mobile UX)
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // ----------------------------------------------------------------
        // livewire:initialized — init pertama kali saat halaman dimuat
        // ----------------------------------------------------------------
        document.addEventListener('livewire:initialized', function () {
            // Toastr integration
            Livewire.on('notify', function (event) {
                var type = event[0] ? event[0].type : (event.type || 'info');
                var message = event[0] ? event[0].message : (event.message || '');
                if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
                    toastr[type](message);
                }
            });

            reinitPlugins();
        });

        // ----------------------------------------------------------------
        // livewire:updated — re-init setelah setiap partial render
        // (penting untuk bootstrap-select di dalam Livewire component)
        // ----------------------------------------------------------------
        document.addEventListener('livewire:updated', function () {
            reinitPlugins();
        });

        // ----------------------------------------------------------------
        // Hamburger / Sidebar toggle — pastikan bekerja di mobile
        // Template Akademi kadang kehilangan event listener ini setelah
        // Livewire morph mengganti elemen DOM.
        // ----------------------------------------------------------------
        document.addEventListener('livewire:navigated', function() {
            // Re-init Hamburger Menu
            $('.nav-control').off('click').on('click', function() {
                $('#main-wrapper').toggleClass('menu-toggle');
                $(".hamburger").toggleClass("is-active");
            });
            
            // Sembunyikan preloader (safety net)
            $('#preloader').fadeOut(500);
        });

        // ----------------------------------------------------------------
        // Fallback: sembunyikan preloader setelah max 3 detik
        // jika livewire:navigated tidak terpanggil (misalnya halaman biasa)
        // ----------------------------------------------------------------
        window.addEventListener('load', function () {
            setTimeout(function () {
                $('#preloader').fadeOut(500);
            }, 500);
        });
    }());
    </script>

    @stack('scripts')
</body>
</html>
