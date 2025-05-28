<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kalkulator PIT</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        /* Gradientowe, nieprzezroczyste tło navbara */
nav.navbar {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    border-radius: 0 0 20px 20px;
    box-shadow: 0 4px 15px rgba(37, 117, 252, 0.7);
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1030;
}

/* Styl linków */
nav.navbar .nav-link {
    color: #e0e7ff;
    transition: color 0.3s ease;
}

nav.navbar .nav-link:hover,
nav.navbar .nav-link.active {
    color: #ffdd59;
}

/* Dropdown menu */
.dropdown-menu {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 24px rgba(37, 117, 252, 0.4);
    font-weight: 600;
    background-color: #2575fc; /* ciemne tło dla kontrastu */
    color: #e0e7ff;
}

.dropdown-item {
    transition: background-color 0.2s ease, color 0.2s ease;
}

.dropdown-item:hover {
    background-color: #2575fc;
    color: #fff;
}

/* Toggle button */
.navbar-toggler {
    border: none;
    outline: none;
    box-shadow: none;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(255, 221, 89, 0.8)' stroke-width='3' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Prawa strona: użytkownik */
.navbar-nav.ms-auto > li > a.nav-link {
    padding-left: 1rem;
    padding-right: 1rem;
    color: #e0e7ff;
}

.navbar-nav.ms-auto > li > a.nav-link:hover {
    color: #ffdd59;
}

/* Dropdown toggle */
.nav-link.dropdown-toggle::after {
    border: none;
    content: "▼";
    font-size: 0.6rem;
    margin-left: 0.3rem;
    color: #ffdd59;
}

/* Responsive tweaks */
@media (max-width: 576px) {
    nav.navbar {
        border-radius: 0 0 10px 10px;
        padding: 0.6rem 1rem;
    }

    nav.navbar .nav-link {
        font-size: 0.9rem;
    }
}

    </style>
</head>

<body>

    <!-- Pasek nawigacyjny -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand text-light fw-bold fs-3" href="{{ url('/') }}">Kalkulator PIT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Strona główna</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('pit-calculator') ? 'active' : '' }}"
                                href="{{ route('pit-calculator') }}">Kalkulator PIT</a>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('pit-calculator-demo') ? 'active' : '' }}"
                                href="{{ route('pit-calculator.demo') }}">Kalkulator PIT Demo</a>
                        </li>
                    @endguest
                </ul>

                <!-- Prawa strona paska -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('tax-calculations.index') }}">Moje kalkulacje
                                        PIT</a>
                                </li>
                                @if (auth()->user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li>
        <a class="dropdown-item" href="{{ route('admin.users.index') }}">Użytkownicy</a>
    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('account.settings') }}">Zarządzaj kontem</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Wyloguj się
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Formularz logout -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('login') ? 'active' : '' }}" href="{{ route('login') }}">Zaloguj się</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('register') ? 'active' : '' }}" href="{{ route('register') }}">Zarejestruj się</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Toast -->
    @if (session('status'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div id="loginToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('status') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Zawartość strony -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Jeśli jest status w sesji, pokaż Toast -->
    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var toastEl = document.getElementById('loginToast');
                var toast = new bootstrap.Toast(toastEl, {
                    delay: 5000 // 5 sekund
                });
                toast.show();
            });
        </script>
    @endif

</body>

</html>
