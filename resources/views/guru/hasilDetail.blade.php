@extends('guru.dashboard')

@section('title', 'Detail Hasil - ' . $room->nama_room)

@section('content')
<link rel="stylesheet" href="{{ asset('css/Guru/hasilDetail.css') }}">

<div class="rf rf--full">

    <a href="{{ route('guru.hasil.index') }}" class="hd-back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali ke daftar
    </a>

    {{-- ============ JUDUL ROOM ============ --}}
    <div class="hd-top">
        <div>
            <h1 class="rf-title">{{ $room->nama_room }}</h1>
            <p class="hd-kode-line">
                Kode Room:
                <span class="kr-kode">{{ $room->kode_room ?? '-' }}</span>
            </p>
        </div>

        @if ($jumlahSiswa > 0)
            <form method="POST" action="{{ route('guru.hasil.destroy', $room) }}"
                  onsubmit="return confirm('Hapus SEMUA hasil siswa di room {{ $room->nama_room }}? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                <button type="submit" class="rf-btn kr-btn-hapus">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                    Hapus semua hasil
                </button>
            </form>
        @endif
    </div>

    {{-- Belum ada siswa yang mengerjakan --}}
    @if ($jumlahSiswa === 0)
        <div class="kr-empty hd-empty">
            Belum ada siswa yang mengerjakan room ini.
        </div>
    @else

        {{-- 1. RINGKASAN --}}
        <section class="hd-card">
            <h2 class="hd-card__title">Ringkasan</h2>

            <div class="hd-stats">
                <div class="hd-stat">
                    <p class="rf-overline">Jumlah Siswa</p>
                    <p class="hd-stat__value">{{ $jumlahSiswa }}</p>
                </div>
                <div class="hd-stat">
                    <p class="rf-overline">Skor Rata-rata</p>
                    <p class="hd-stat__value">{{ $rataRata }}</p>
                </div>
                <div class="hd-stat">
                    <p class="rf-overline">Skor Tertinggi</p>
                    <p class="hd-stat__value">{{ $tertinggi->skor }}</p>
                    <p class="hd-stat__sub">{{ $tertinggi->nama }}</p>
                </div>
            </div>

            <div class="hd-charts">
                <div class="hd-chart">
                    <h3 class="hd-chart__title">Benar atau salah per soal</h3>

                    <div class="hd-chart__area hd-chart__area--soal">
                        @foreach ($perSoal as $s)
                            <div class="hd-bar-stack"
                                 title="Soal {{ $s['nomor'] }}: {{ $s['benar'] }} benar, {{ $s['salah'] }} salah, {{ $s['kosong'] }} tidak dijawab">
                                <div class="hd-seg hd-seg--benar"  style="height: {{ $s['pb'] }}%"></div>
                                <div class="hd-seg hd-seg--salah"  style="height: {{ $s['ps'] }}%"></div>
                                <div class="hd-seg hd-seg--kosong" style="height: {{ $s['pk'] }}%"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hd-chart__labels hd-chart__labels--soal">
                        @foreach ($perSoal as $s)
                            <div>S{{ $s['nomor'] }}</div>
                        @endforeach
                    </div>

                    <div class="hd-legend">
                        <span><span class="hd-legend__box hd-seg--benar"></span> Benar</span>
                        <span><span class="hd-legend__box hd-seg--salah"></span> Salah</span>
                        <span><span class="hd-legend__box hd-seg--kosong"></span> Tidak dijawab</span>
                    </div>
                </div>

                <div class="hd-chart">
                    <h3 class="hd-chart__title">Sebaran skor</h3>

                    <div class="hd-chart__area hd-chart__area--skor">
                        @foreach ($grafikSkor as $b)
                            <div class="hd-bar-col"
                                 title="Skor {{ $b['dari'] }} - {{ $b['sampai'] }}: {{ $b['jumlah'] }} siswa">
                                @if ($b['jumlah'] > 0)
                                    <span class="hd-bar-col__num">{{ $b['jumlah'] }}</span>
                                @endif
                                <div class="hd-bar-col__bar" style="height: {{ $b['persen'] }}%"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hd-chart__labels hd-chart__labels--skor">
                        @foreach ($grafikSkor as $b)
                            <div>{{ $b['dari'] }}</div>
                        @endforeach
                    </div>
                    <p class="hd-chart__note">Rentang skor (jumlah siswa di atas batang)</p>
                </div>
            </div>
        </section>

        {{-- 2. PERINGKAT  --}}
        <section class="hd-card">
            <h2 class="hd-card__title">Peringkat</h2>

            <div class="hd-table-wrap hd-table-wrap--rank">
                <table class="hd-table">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama</th>
                            <th class="hd-right">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['emas', 'perak', 'perunggu'] as $i => $medali)
                            @php $p = $peringkat[$i] ?? null; @endphp
                            <tr>
                                <td>
                                    <span class="hd-medal hd-medal--{{ $medali }}">{{ $i + 1 }}</span>
                                </td>
                                <td class="hd-strong">{{ $p->nama ?? '-' }}</td>
                                <td class="hd-right hd-strong">{{ $p->skor ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="hd-card">
            <h2 class="hd-card__title">Hasil per Siswa</h2>

            <div class="hd-table-wrap">

                {{-- Judul kolom --}}
                <div class="hd-grid hd-grid--siswa hd-grid__head">
                    <div>Siswa</div>
                    <div>Dikumpulkan</div>
                    <div class="hd-center">Skor</div>
                    <div class="hd-center">Benar</div>
                    <div class="hd-center">Salah</div>
                </div>

                @foreach ($siswa as $s)
                    <details class="hd-details">
                        <summary class="hd-grid hd-grid--siswa">
                            <div class="hd-name">
                                <svg class="hd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                                {{ $s['nama'] }}
                            </div>
                            <div class="hd-muted">{{ $s['waktu'] }}</div>
                            <div class="hd-center hd-strong">{{ $s['skor'] }}</div>
                            <div class="hd-center hd-benar">{{ $s['benar'] }}</div>
                            <div class="hd-center hd-salah">{{ $s['salah'] }}</div>
                        </summary>

                        <div class="hd-rincian">
                            <table class="hd-subtable">
                                <thead>
                                    <tr>
                                        <th class="hd-col-no">No</th>
                                        <th>Soal</th>
                                        <th>Jawaban</th>
                                        <th class="hd-col-hasil">Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($s['baris'] as $b)
                                        <tr>
                                            <td class="hd-muted">{{ $b->nomor }}</td>
                                            <td>
                                                <div class="hd-cell">
                                                    @if ($b->gambar_soal)
                                                        <img src="{{ asset('storage/' . $b->gambar_soal) }}" alt="" class="hd-thumb">
                                                    @endif
                                                    <span>{{ Str::limit($b->pertanyaan ?? '(soal sudah dihapus)', 70) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hd-cell">
                                                    @if ($b->gambar_jawaban)
                                                        <img src="{{ asset('storage/' . $b->gambar_jawaban) }}" alt="" class="hd-thumb hd-thumb--sm">
                                                    @endif
                                                    <span class="{{ $b->teks_jawaban ? '' : 'hd-kosong' }}">
                                                        {{ $b->teks_jawaban ?? 'Tidak dijawab' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="hd-center">
                                                @if ($b->status === 'benar')
                                                    <span class="hd-hasil hd-hasil--benar" title="Benar">✓</span>
                                                @elseif ($b->status === 'salah')
                                                    <span class="hd-hasil hd-hasil--salah" title="Salah">✗</span>
                                                @else
                                                    <span class="hd-hasil hd-hasil--kosong" title="Tidak dijawab">–</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        </section>

        <section class="hd-card">
            <h2 class="hd-card__title">Hasil per Soal</h2>

            <div class="hd-table-wrap">

                {{-- Judul kolom --}}
                <div class="hd-grid hd-grid--soal hd-grid__head">
                    <div>Soal</div>
                    <div class="hd-center">Benar</div>
                    <div class="hd-center">Salah</div>
                </div>

                @foreach ($perSoal as $s)
                    <details class="hd-details">
                        <summary class="hd-grid hd-grid--soal">
                            <div class="hd-name hd-name--soal">
                                <svg class="hd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                                <span class="hd-nomor">{{ $s['nomor'] }}.</span>
                                @if ($s['gambar'])
                                    <img src="{{ asset('storage/' . $s['gambar']) }}" alt="" class="hd-thumb">
                                @endif
                                <span>
                                    {{ Str::limit($s['pertanyaan'], 70) }}

                                    @if ($s['kosong'] > 0)
                                        <span class="hd-tidak-dijawab">{{ $s['kosong'] }} tidak dijawab</span>
                                    @endif
                                </span>
                            </div>
                            <div class="hd-center hd-benar">{{ $s['benar'] }}</div>
                            <div class="hd-center hd-salah">{{ $s['salah'] }}</div>
                        </summary>

                        <div class="hd-rincian">
                            <table class="hd-subtable">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jawaban</th>
                                        <th class="hd-col-hasil">Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($s['baris'] as $b)
                                        <tr>
                                            <td class="hd-strong">{{ $b->nama }}</td>
                                            <td>
                                                <div class="hd-cell">
                                                    @if ($b->gambar_jawaban)
                                                        <img src="{{ asset('storage/' . $b->gambar_jawaban) }}" alt="" class="hd-thumb hd-thumb--sm">
                                                    @endif
                                                    <span class="{{ $b->teks_jawaban ? '' : 'hd-kosong' }}">
                                                        {{ $b->teks_jawaban ?? 'Tidak dijawab' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="hd-center">
                                                @if ($b->status === 'benar')
                                                    <span class="hd-hasil hd-hasil--benar" title="Benar">✓</span>
                                                @elseif ($b->status === 'salah')
                                                    <span class="hd-hasil hd-hasil--salah" title="Salah">✗</span>
                                                @else
                                                    <span class="hd-hasil hd-hasil--kosong" title="Tidak dijawab">–</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        </section>

    @endif
</div>
@endsection
