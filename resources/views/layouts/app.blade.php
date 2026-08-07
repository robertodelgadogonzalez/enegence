<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Enegence') | Prueba Técnica</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body>
    <div class="enegence-topbar">
        <div class="container d-flex justify-content-between align-items-center py-1">
            <span class="navbar-user">
                <i class="fas fa-user-circle me-1"></i> Roberto Delgado González
            </span>
            <div class="d-flex gap-3">
                <a href="https://www.facebook.com" target="_blank" rel="noopener" class="text-white"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.twitter.com" target="_blank" rel="noopener" class="text-white"><i class="fab fa-twitter"></i></a>
                <a href="https://www.linkedin.com" target="_blank" rel="noopener" class="text-white"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>

    <header class="enegence-header py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('estados.index') }}">
                <img src="{{ asset('images/enegence-logo.png') }}" alt="Enegence" class="logo">
            </a>
            <nav class="enegence-nav">
                <a href="{{ route('estados.index') }}" class="nav-link d-inline-block active">Estados</a>
            </nav>
        </div>
    </header>

    <main class="container py-4">
        @yield('content')
    </main>

    <footer class="enegence-footer">
        <div class="container d-flex justify-content-center">
            <img src="{{ asset('images/enegence-logo-white.png') }}" alt="Enegence" class="logo">
        </div>
    </footer>

    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</body>
</html>
