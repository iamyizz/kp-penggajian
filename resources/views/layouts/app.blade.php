<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Penggajian Klinik Samara') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <style>
        .nav-link:hover:not(.active) {
            background-color: #f1f1f1;
            border-radius: 6px;
        }
    </style>

</head>
<body class="bg-gray-100 text-gray-800 flex h-screen">

    <!-- Sidebar -->
    <x-sidebar />

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white border-bottom shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h5 mb-0">Dashboard</h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">👋 {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Konten -->
        <main class="p-6 overflow-y-auto pb-20">
            @yield('content')
        </main>
        <!-- Footer (fixed di bawah viewport) -->
        <footer class="fixed bottom-0 left-0 right-0 bg-white border-t p-3 text-center text-sm text-gray-600 shadow-md z-40">
            <div class="container mx-auto">
                &copy; {{ now()->year }} {{ config('app.name', 'Sistem Penggajian Klinik Samara') }}. Semua hak dilindungi.
            </div>
        </footer>
        </div>

        <!-- Bootstrap Bundle JS (sudah termasuk Popper.js) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    </body>
    </html>
