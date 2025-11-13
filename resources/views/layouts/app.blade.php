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
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold">Dashboard</h1>
            <div>
                <span class="text-sm text-gray-600 mr-3">👋 {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline">Logout</button>
                </form>
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
    </body>
    </html>
