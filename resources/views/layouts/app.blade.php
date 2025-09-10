<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Control - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }

        .sidebar .nav-link {
            color: #adb5bd;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #343a40;
        }

        footer {
            background: #fff;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar p-3">
            <h5 class="text-white mb-4">🚦 Traffic Control</h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('traffic.control.dashboard')) active @endif"
                        href="{{ route('traffic.control.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('traffic.control.logs')) active @endif"
                        href="{{ route('traffic.control.logs') }}">
                        <i class="bi bi-list-ul"></i> Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('traffic.control.manage-ip')) active @endif"
                        href="{{ route('traffic.control.manageIp') }}">
                        <i class="bi bi-globe"></i> Manage IP
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('traffic.control.settings')) active @endif"
                        href="{{ route('traffic.control.settings') }}">
                        <i class="bi bi-gear-fill"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('traffic.control.alerts')) active @endif"
                        href="{{ route('traffic.control.alerts') }}">
                        <i class="bi bi-bell-fill"></i> Alerts
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Topbar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <span class="navbar-text">
                    @yield('title', 'Dashboard')
                </span>
            </nav>

            <!-- Page Content -->
            <main class="container-fluid py-4">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="text-center py-3">
                <small>© {{ now()->year }} AreiaLab Traffic Control. All rights reserved.</small>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
