<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="YouTube Course Scraper - Collect educational playlists from YouTube using AI-powered search">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'YouTube Course Scraper')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    {{-- ── Navbar ─────────────────────────────────────────── --}}
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-icon">
                    <i class="bi bi-youtube"></i>
                </span>
                YouTube Course 
            </a>

            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false">
                <i class="bi bi-list text-light fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                           href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('results') ? 'active' : '' }}"
                           href="{{ route('results') }}">
                            <i class="bi bi-collection-play me-1"></i> Results
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- ── Flash Messages ─────────────────────────────────── --}}
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-custom alert-success-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-custom alert-danger-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-custom alert-danger-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- ── Loading Overlay ────────────────────────────────── --}}
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-custom"></div>
        <div class="loading-text">Fetching playlists... This may take a moment</div>
    </div>

    {{-- ── Main Content ───────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

    {{-- ── Footer ─────────────────────────────────────────── --}}
    <footer class="footer-custom">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-code-slash me-1"></i>
                Built with <span style="color: var(--danger);">&#10084;</span> using Laravel 12 &amp; Bootstrap 5
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ── Loading spinner on form submit ─────────────────── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fetchForm = document.getElementById('fetchForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (fetchForm && loadingOverlay) {
                fetchForm.addEventListener('submit', function() {
                    loadingOverlay.classList.add('active');
                });
            }

            // Auto-dismiss alerts after 8 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 8000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
