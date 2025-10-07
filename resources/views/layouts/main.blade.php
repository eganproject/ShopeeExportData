<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ asset('public/logo/cok-logo.png') }}" type="image/png" sizes="32x32"/>
    <title>Cahaya Optima Karya</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .navbar-nav .nav-link.active,
        .navbar-nav .dropdown-item.active {
            background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
            color: #fff !important;
            border-radius: 0.5rem;
        }

        .navbar-nav .dropdown-menu {
            min-width: 200px;
        }

        @media (max-width: 991.98px) {
            .navbar-nav .dropdown-menu {
                position: static;
                float: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <img src="{{ asset('public/logo/cok-logosss.png') }}"  height="64" alt="Cahaya Optima Karya" class="me-2" />
                {{-- Cahaya Optima Karya --}}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavModern" aria-controls="navbarNavModern" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavModern">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->is('/') ? 'active text-primary' : '' }}"
                            href="/">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold {{ request()->is('performa-produk') || request()->is('performa-produk/compare') || request()->is('performa-produk/kategori') || request()->is('performa-produk/compare-sales') || request()->is('performa-produk/compare-sales/kategori') || request()->is('performa-produk/shops')    ? 'active text-primary' : '' }}"
                            href="#" id="produkDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Produk
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                            aria-labelledby="produkDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->is('performa-produk') ? 'active text-primary fw-bold' : '' }}"
                                    href="/performa-produk">
                                    <i class="bi bi-graph-up me-2"></i>Performa
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->is('performa-produk/compare') ? 'active text-primary fw-bold' : '' }}"
                                    href="/performa-produk/compare">
                                    <i class="bi bi-bar-chart-steps me-2"></i>Comparative
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->is('performa-produk/compare-sales') || request()->is('performa-produk/compare-sales/kategori') ? 'active text-primary fw-bold' : '' }}"
                                    href="/performa-produk/compare-sales">
                                    <i class="fa-regular fa-calendar-days me-2"></i>Comparative from Sales
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->is('performa-produk/kategori') ? 'active text-primary fw-bold' : '' }}"
                                    href="/performa-produk/kategori">
                                    <i class="bi bi-tags me-2"></i>Kategori
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->is('performa-produk/shops') ? 'active text-primary fw-bold' : '' }}"
                                    href="/performa-produk/shops">
                                    <i class="bi bi-shop me-2"></i>Shop
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    @if(optional(auth()->user())->shop_id == 0)
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->is('performa-produk/user-activities') ? 'active text-primary' : '' }}"
                           href="{{ route('userActivities.index') }}">
                           <i class="bi bi-clock-history me-2"></i>User Activity
                        </a>
                    </li>
                    @endif
                    <!-- Add this new list item for logout -->
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a class="nav-link fw-semibold text-danger" href="#" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Bootstrap Icons CDN -->
    <div class="container-fluid">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <!-- JSZip (untuk export excel) -->

    <!-- DataTables JS -->

    <!-- Buttons extension -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>
