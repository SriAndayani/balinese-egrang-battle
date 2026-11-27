<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Guru')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/Guru/dashboardGuru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Guru/detailRoom.css') }}">
</head>
<body class="bg-gray-100">

@php
    $menus = [
        ['label' => 'Dashboard',       'icon' => 'dashboard.svg', 'route' => 'guru.dashboardGuru',         'active' => 'guru.dashboardGuru'],
        ['label' => 'Kelola Room', 'icon' => 'room.svg', 'route' => 'guru.room.index',  'active' => 'guru.room.*'],
        ['label' => 'Hasil Siswa',     'icon' => 'chart.svg', 'route' => 'guru.hasil.index',        'active' => 'guru.hasil.*'],
    ];
@endphp

<div class="guru-shell">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="guru-sidebar">
        <div>
            {{-- Logo --}}
            <div class="guru-logo">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo Ngiring Malajah Basa Bali">
            </div>

            <nav class="guru-nav" aria-label="Menu guru">
                @foreach ($menus as $menu)
                    @php $aktif = request()->routeIs($menu['active']); @endphp
                    <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                       class="guru-nav__item {{ $aktif ? 'is-active' : '' }}"
                       @if ($aktif) aria-current="page" @endif>
                        <img src="{{ asset('icons/' . $menu['icon']) }}" alt="">
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Logout --}}
        <div class="guru-logout-wrap">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="guru-logout">
                    <span>Logout</span>
                    <img src="{{ asset('icons/logout.svg') }}" alt="">
                </button>
            </form>
        </div>
    </aside>

    {{-- ============ KONTEN UTAMA ============ --}}
    <main class="guru-main">

        <div class="guru-main__inner">
            {{-- Pesan sukses dari controller --}}
            @if (session('success'))
                <div class="guru-alert guru-alert--success" role="status">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('js/dashboardGuru/guruRoom.js') }}"></script>
<script src="{{ asset('js/dashboardGuru/detailRoom.js') }}"></script>
</body>
</html>
