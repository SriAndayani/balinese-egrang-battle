@extends('admin.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="guru-title">Dashboard Admin</h1>
    <p class="guru-greeting">Selamat Datang, {{ auth()->user()->username }}!</p>

    <div class="stat-grid">

        {{-- Total Soal --}}
        <section class="stat-card stat-card--total" aria-label="Total room">
            <div class="stat-card__head">
                <span class="stat-card__icon"><img src="{{ asset('icons/room.svg') }}" alt=""></span>
                <h2 class="stat-card__label">Total Soal</h2>
            </div>
            <p class="stat-card__value">{{ $totalSoal }}</p>
            <p class="stat-card__hint">Jumlah Soal yang telah dibuat</p>
            <svg class="stat-card__flower flower--white" viewBox="-62 -62 124 124" aria-hidden="true"><use href="#frangipani"/></svg>
        </section>

        {{-- Guru Aktif --}}
        <section class="stat-card stat-card--active" aria-label="Room aktif">
            <div class="stat-card__head">
                <span class="stat-card__icon"><span class="pulse-dot"></span></span>
                <h2 class="stat-card__label">Guru Aktif</h2>
            </div>
            <p class="stat-card__value">{{ $totalGuruAktif }}</p>
            <svg class="stat-card__flower flower--white" viewBox="-62 -62 124 124" aria-hidden="true"><use href="#frangipani"/></svg>
        </section>

        {{-- Guru Pending --}}
        <section class="stat-card stat-card--active" aria-label="Room aktif">
            <div class="stat-card__head">
                <span class="stat-card__icon"><span class="pulse-dot"></span></span>
                <h2 class="stat-card__label">Menunggu Verifikasi</h2>
            </div>
            <p class="stat-card__value">{{ $totalGuruPending }}</p>
            <svg class="stat-card__flower flower--white" viewBox="-62 -62 124 124" aria-hidden="true"><use href="#frangipani"/></svg>
        </section>

    </div>
@endsection