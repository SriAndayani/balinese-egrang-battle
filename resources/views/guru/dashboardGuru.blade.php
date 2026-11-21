@extends('guru.dashboard')

@section('title', 'Dashboard Guru')

@section('content')
    <h1 class="guru-title">Dashboard Guru</h1>
    <p class="guru-greeting">Selamat Datang, {{ auth()->user()->username }}!</p>

    <div class="stat-grid">

        {{-- Total Room --}}
        <section class="stat-card stat-card--total" aria-label="Total room">
            <div class="stat-card__head">
                <span class="stat-card__icon"><img src="{{ asset('icons/room.svg') }}" alt=""></span>
                <h2 class="stat-card__label">Total Room</h2>
            </div>
            <p class="stat-card__value">{{ $totalRoom }}</p>
            <p class="stat-card__hint">Semua room yang kamu buat</p>
            <svg class="stat-card__flower flower--white" viewBox="-62 -62 124 124" aria-hidden="true"><use href="#frangipani"/></svg>
        </section>

        {{-- Room Aktif --}}
        <section class="stat-card stat-card--active" aria-label="Room aktif">
            <div class="stat-card__head">
                <span class="stat-card__icon"><span class="pulse-dot"></span></span>
                <h2 class="stat-card__label">Room Aktif</h2>
            </div>
            <p class="stat-card__value">{{ $roomAktif }}</p>
            <p class="stat-card__hint">Room yang sedang dibuka</p>
            <svg class="stat-card__flower flower--white" viewBox="-62 -62 124 124" aria-hidden="true"><use href="#frangipani"/></svg>
        </section>

    </div>
@endsection