<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Guru')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

@php
    $menus = [
        ['label' => 'Dashboard',       'icon' => 'dashboard.svg', 'route' => 'guru.dashboard',         'active' => 'guru.dashboard'],
        ['label' => 'Kelola Room', 'icon' => 'room.svg', 'route' => 'guru.room.index',  'active' => 'guru.room.*'],
        ['label' => 'Hasil Siswa',     'icon' => 'chart.svg', 'route' => '#',        'active' => 'admin.soal.*'],
    ];
@endphp

<div class="flex min-h-screen">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between">
        <div>
            {{-- Logo / Title --}}
            <div class="px-6 py-5 border-b border-gray-200">
                <span class="text-xl font-bold text-gray-800">Dashboard Admin</span>
            </div>

            {{-- Menu: dibuat otomatis dari daftar $menus di atas --}}
            <nav class="mt-4 flex flex-col gap-1 px-3">
                @foreach ($menus as $menu)
                    <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                              {{ request()->routeIs($menu['active']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                        <img
                            src="{{ asset('icons/' . $menu['icon']) }}"
                            alt=""
                            class="w-5 h-5 shrink-0"
                        >
                        <span>{{ $menu['label'] }}</span> 
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Logout --}}
        <div class="px-3 py-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center justify-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                               text-gray-600 hover:bg-gray-100 w-full text-left">
                    <span>Logout</span>
                    <img
                        src="{{ asset('icons/logout.svg') }}"
                        alt="Logout Icon"
                        class="w-5 h-5"    
                    >
                </button>
            </form>
        </div>
    </aside>

    {{-- ============ KONTEN UTAMA ============ --}}
    <div class="flex-1 flex flex-col">

        {{-- Navbar atas (kosong dulu, bisa diisi breadcrumb nanti) --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3">
            <div class="text-sm text-gray-400"></div>
        </header>

        {{-- Isi halaman --}}
        <main class="flex-1 p-6">

            {{-- Pesan sukses/gagal dari controller (dipakai semua halaman admin) --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</div>
<script src="{{ asset('js/guruRoom.js') }}"></script>
</body>
</html>
