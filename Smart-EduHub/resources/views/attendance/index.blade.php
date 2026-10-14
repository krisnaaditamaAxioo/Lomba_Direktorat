@extends('layouts.app')

@section('title', 'Pemantauan Kehadiran')

@section('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* Field tanggal di atas header gradient */
    .glass-field {
        width: 100%;
        min-height: 44px;
        padding: .5rem .75rem;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 12px;
        background: rgba(255, 255, 255, .12);
        color: #fff;
        font-size: 1rem; /* 16px di ponsel agar iOS tidak zoom */
        font-weight: 600;
        outline: none;
        color-scheme: dark;
        transition: background .15s, box-shadow .15s;
        -webkit-appearance: none;
        appearance: none;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
        line-height: 1.5;
    }
    .glass-field::-webkit-date-and-time-value { text-align: left; min-height: 1.5em; }
    .glass-field::-webkit-calendar-picker-indicator { cursor: pointer; opacity: .9; }
    .glass-field:focus { background: rgba(255, 255, 255, .2); box-shadow: 0 0 0 3px rgba(255, 255, 255, .3); }
    @media (min-width: 640px) { .glass-field { font-size: .875rem; } }
</style>
@endsection

@section('content')
@php
    // ---- Meta status (kode => tampilan & label) ----
    $statusMeta = [
        'present' => ['label' => 'Hadir',       'pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'tile' => 'text-emerald-700'],
        'late'    => ['label' => 'Terlambat',   'pill' => 'bg-amber-50 text-amber-800 border-amber-200',       'tile' => 'text-amber-700'],
        'absent'  => ['label' => 'Tidak Hadir', 'pill' => 'bg-rose-50 text-rose-800 border-rose-200',          'tile' => 'text-rose-700'],
        'excused' => ['label' => 'Izin/Sakit',  'pill' => 'bg-orange-50 text-orange-800 border-orange-200',    'tile' => 'text-orange-700'],
    ];
    $fallbackMeta = ['label' => null, 'pill' => 'bg-slate-100 text-slate-600 border-slate-200', 'tile' => 'text-slate-500'];

    // ---- Rentang cepat (tanggal) ----
    $curDate = \Carbon\Carbon::parse($date)->toDateString();
    $quick = [
        'Hari ini' => now()->toDateString(),
        'Kemarin'  => now()->subDay()->toDateString(),
    ];

    // ---- Kartu ringkasan ----
    $presentPct = $stats['total_enrollment'] > 0 ? ($stats['present_today'] / $stats['total_enrollment']) * 100 : 0;
    $statCards = [
        ['label' => 'Total terdaftar', 'value' => $stats['total_enrollment'], 'icon' => 'group',                'tile' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Tepat waktu',     'value' => $stats['on_time_today'],    'icon' => 'check_circle',         'tile' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Terlambat',       'value' => $stats['late_arrival'],     'icon' => 'schedule',             'tile' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Izin / sakit',    'value' => $stats['excused_today'],    'icon' => 'assignment_turned_in', 'tile' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Tidak hadir',     'value' => $stats['absent_today'],     'icon' => 'person_off',           'tile' => 'bg-rose-50 text-rose-600'],
    ];

    // ---- Data grafik ----
    $chartData = [
        ['label' => 'Tepat Waktu', 'value' => $stats['on_time_today'], 'color' => '#10b981'],
        ['label' => 'Terlambat',   'value' => $stats['late_arrival'],  'color' => '#f59e0b'],
        ['label' => 'Tidak Hadir', 'value' => $stats['absent_today'],  'color' => '#ef4444'],
        ['label' => 'Izin/Sakit',  'value' => $stats['excused_today'], 'color' => '#f97316'],
    ];
    $chartTotal = array_sum(array_column($chartData, 'value'));
    $chartMax   = $chartTotal > 0 ? $chartTotal : 1;
@endphp

<div class="flex w-full flex-col gap-5 sm:gap-6 lg:gap-8">

    {{-- Header + filter + ringkasan (wrapper sendiri agar kartu menimpa header) --}}
    <div>
        <header id="pageHero" class="relative  overflow-hidden rounded-b-[2.5rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pb-24 pt-8 text-white shadow-xl shadow-[#EB4325]/15 sm:px-6 lg:mx-0 lg:mt-0 lg:rounded-[2.5rem] lg:pb-20">
            <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/5" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-12 top-24 h-40 w-40 rounded-full bg-white/5" aria-hidden="true"></div>

            <div class="relative z-10 space-y-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight lg:text-3xl">Pemantauan Kehadiran</h1>
                        <p class="mt-1 flex items-center gap-1 text-xs text-white/85 sm:text-sm">
                            <span class="material-symbols-outlined text-base" aria-hidden="true">calendar_today</span>
                            {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>

                    {{-- Aksi cepat --}}
                    <div class="grid grid-cols-3 gap-2 sm:flex sm:gap-3">
                        <a href="{{ route('attendance.scan') }}"
                           class="focus-ring flex flex-col items-center justify-center gap-1 rounded-2xl bg-white px-3 py-2.5 text-[11px] font-bold text-[#C2331A] shadow-lg shadow-black/10 transition hover:bg-white/90 active:scale-95 sm:flex-row sm:gap-1.5 sm:px-4 sm:text-sm">
                            <span class="material-symbols-outlined text-[22px] sm:text-[20px]" aria-hidden="true">qr_code_scanner</span>
                            Scan QR
                        </a>
                        <a href="{{ route('attendance.bulk') }}"
                           class="focus-ring flex flex-col items-center justify-center gap-1 rounded-2xl border border-white/25 bg-white/10 px-3 py-2.5 text-[11px] font-bold text-white transition hover:bg-white/20 active:scale-95 sm:flex-row sm:gap-1.5 sm:px-4 sm:text-sm">
                            <span class="material-symbols-outlined text-[22px] sm:text-[20px]" aria-hidden="true">groups</span>
                            Absen Massal
                        </a>
                        <a href="{{ route('attendance.create') }}"
                           class="focus-ring flex flex-col items-center justify-center gap-1 rounded-2xl border border-white/25 bg-white/10 px-3 py-2.5 text-[11px] font-bold text-white transition hover:bg-white/20 active:scale-95 sm:flex-row sm:gap-1.5 sm:px-4 sm:text-sm">
                            <span class="material-symbols-outlined text-[22px] sm:text-[20px]" aria-hidden="true">add</span>
                            Input Manual
                        </a>
                    </div>
                </div>

                {{-- Rentang cepat --}}
                <nav aria-label="Tanggal cepat" class="-mx-5 overflow-x-auto px-5 [scrollbar-width:none] sm:mx-0 sm:px-0">
                    <ul class="flex w-max gap-2">
                        @foreach($quick as $label => $d)
                            @php $active = $curDate === $d; @endphp
                            <li>
                                <a href="{{ route('attendance', ['date' => $d]) }}"
                                   @if($active) aria-current="true" @endif
                                   class="focus-ring inline-flex h-9 items-center rounded-full border px-3.5 text-xs font-semibold transition active:scale-95 {{ $active ? 'border-white bg-white text-[#C2331A]' : 'border-white/25 bg-white/10 text-white hover:bg-white/20' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Form filter --}}
                <form action="{{ route('attendance') }}" method="GET"
                      class="grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-white/10 p-3 backdrop-blur-md sm:max-w-sm sm:p-4">
                    <div>
                        <label for="date" class="mb-1 block text-xs font-semibold text-white/85">Tanggal</label>
                        <input type="date" id="date" name="date" value="{{ $curDate }}" onchange="this.form.submit()" class="glass-field cursor-pointer">
                    </div>
                    <noscript>
                        <button type="submit" class="h-11 rounded-xl bg-white text-sm font-bold text-[#C2331A]">Terapkan</button>
                    </noscript>
                </form>
            </div>
        </header>

        {{-- Kartu ringkasan --}}
        <section aria-label="Ringkasan kehadiran" class="relative z-10 -mt-14 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:-mt-12 lg:grid-cols-6">
            {{-- Hero: total hadir --}}
            <div class="col-span-2 rounded-3xl bg-emerald-700 p-4 text-white shadow-lg shadow-emerald-900/15 sm:col-span-3 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white/20 text-white" aria-hidden="true">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-3xl font-extrabold leading-none tabular-nums">
                            {{ number_format($stats['present_today']) }}
                            @if($stats['total_enrollment'] > 0)
                                <span class="text-xs font-semibold text-white/80">/ {{ number_format($presentPct, 1) }}%</span>
                            @endif
                        </p>
                        <p class="mt-1 text-xs font-semibold text-white/85">Total hadir</p>
                    </div>
                </div>
                <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-white/20">
                    <div class="h-full bg-white" style="width: {{ $presentPct }}%"></div>
                </div>
            </div>

            @foreach($statCards as $card)
                <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 shadow-lg shadow-slate-900/5">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl {{ $card['tile'] }}" aria-hidden="true">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">{{ $card['icon'] }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-2xl font-extrabold leading-none tabular-nums text-slate-900">{{ number_format($card['value']) }}</p>
                        <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>
    </div>

    {{-- Konten utama --}}
    <div class="grid grid-cols-1 gap-5 sm:gap-6 lg:grid-cols-12">

        {{-- Kiri: log kehadiran --}}
        <section aria-labelledby="logTitle" class="space-y-3 lg:col-span-8">
            <div class="flex items-center justify-between gap-3 px-1">
                <div class="min-w-0">
                    <h2 id="logTitle" class="text-base font-extrabold tracking-tight text-slate-900">Status kehadiran</h2>
                    <p class="mt-0.5 text-xs font-semibold tabular-nums text-slate-500">{{ $attendances->total() }} catatan</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.location.reload()" title="Segarkan"
                            class="focus-ring grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 active:scale-95">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">refresh</span>
                        <span class="sr-only">Segarkan</span>
                    </button>
                    <button type="button" onclick="window.print()" title="Cetak"
                            class="focus-ring inline-flex h-10 items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50 active:scale-95">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">print</span>
                        Cetak
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
                @if($attendances->count() > 0)
                    <ul class="divide-y divide-slate-100">
                        @foreach($attendances as $attendance)
                            @php
                                $meta  = $statusMeta[$attendance->status] ?? $fallbackMeta;
                                $label = $meta['label'] ?? ucfirst($attendance->status);
                                $day   = \Carbon\Carbon::parse($attendance->date)->locale('id');
                            @endphp
                            <li class="flex flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3.5 sm:px-5">
                                {{-- Tile tanggal --}}
                                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-100" aria-hidden="true">
                                    <span class="text-[11px] font-bold leading-none {{ $meta['tile'] }}">{{ $day->isoFormat('ddd') }}</span>
                                    <span class="mt-1 text-xl font-extrabold leading-none tabular-nums text-slate-900">{{ $day->isoFormat('DD') }}</span>
                                </div>

                                {{-- Siswa --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $attendance->student->nama_lengkap }}</p>
                                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                        <span class="rounded-md bg-orange-50 px-1.5 py-0.5 text-[10px] font-bold uppercase text-orange-600">Angkatan {{ $attendance->student->angkatan ?? '-' }}</span>
                                        <span class="flex min-w-0 items-center gap-1">
                                            <span class="material-symbols-outlined text-sm" aria-hidden="true">location_on</span>
                                            <span class="truncate">{{ $attendance->location ?: 'Gerbang Utama' }}</span>
                                        </span>
                                    </p>
                                </div>

                                {{-- Jam & status --}}
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold tabular-nums {{ $attendance->check_in ? 'text-slate-900' : 'text-slate-300' }}">
                                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '–' }}
                                    </p>
                                    <span class="mt-1 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $meta['pill'] }}">
                                        {{ $label }}
                                    </span>
                                </div>

                                {{-- Aksi --}}
                                <div class="flex basis-full items-center justify-end gap-1.5 border-t border-slate-100 pt-2 sm:basis-auto sm:border-0 sm:pt-0">
                                    <a href="{{ route('attendance.edit', $attendance) }}"
                                       class="focus-ring inline-flex h-9 items-center gap-1 rounded-full bg-slate-100 px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                        <span class="material-symbols-outlined text-base" aria-hidden="true">edit</span>
                                        <span class="sm:sr-only">Ubah</span>
                                    </a>
                                    <form action="{{ route('attendance.destroy', $attendance) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kehadiran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="focus-ring inline-flex h-9 items-center gap-1 rounded-full bg-rose-50 px-3 text-xs font-bold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                            <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                                            <span class="sm:sr-only">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-300">
                            <span class="material-symbols-outlined text-2xl">person_off</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada catatan kehadiran</h3>
                        <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Tidak ada data untuk tanggal ini. Coba pilih tanggal lain.</p>
                    </div>
                @endif
            </div>

            <div class="px-1">
                {{ $attendances->links() }}
            </div>
        </section>

        {{-- Kanan: ringkasan --}}
        <aside aria-labelledby="sumTitle" class="space-y-5 lg:col-span-4">
            <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-4 sm:p-5">
                    <h2 id="sumTitle" class="text-base font-extrabold tracking-tight text-slate-900">Ringkasan kehadiran</h2>
                    <p class="mt-0.5 text-[13px] text-slate-500">Sebaran status pada tanggal terpilih</p>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="flex h-40 items-end gap-2 rounded-2xl border border-slate-100 bg-slate-50 px-6 pb-4 pt-8">
                        @foreach($chartData as $bar)
                            <div class="group relative h-full flex-1 rounded-t-lg bg-slate-200/70">
                                <div class="absolute bottom-0 w-full rounded-t-lg transition-all duration-500"
                                     style="height: {{ ($bar['value'] / $chartMax) * 100 }}%; background-color: {{ $bar['color'] }}">
                                    <div class="absolute -top-8 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded bg-slate-800 px-2 py-1 text-[10px] text-white opacity-0 transition-opacity group-hover:opacity-100">
                                        {{ $bar['label'] }}: {{ $bar['value'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Legenda yang sekaligus rekap --}}
                <div class="border-t border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                    <dl class="grid grid-cols-2 gap-2.5">
                        @foreach($chartData as $bar)
                            <div class="flex items-center gap-2.5 rounded-2xl border border-slate-100 bg-white p-2.5">
                                <dt class="grid h-8 w-8 shrink-0 place-items-center rounded-lg" style="background-color: {{ $bar['color'] }}1f">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $bar['color'] }}"></span>
                                    <span class="sr-only">{{ $bar['label'] }}</span>
                                </dt>
                                <dd class="min-w-0">
                                    <span class="block text-base font-extrabold leading-none tabular-nums text-slate-900">{{ number_format($bar['value']) }}</span>
                                    <span class="mt-1 block truncate text-xs text-slate-500">{{ $bar['label'] }}</span>
                                </dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-500">Rata-rata mingguan</span>
                            <span class="font-bold tabular-nums text-slate-900">94.8%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-500">Angkatan tertua</span>
                            <span class="font-bold text-emerald-600">{{ $oldestAngkatan }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-500">Angkatan terbaru</span>
                            <span class="font-bold text-rose-600">{{ $newestAngkatan }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

<script>
// Mobile: sembunyikan navbar atas (Search) & tempelkan header ke ujung atas layar
(function () {
    const hero = document.getElementById('pageHero');
    if (!hero) return;
    const mq = window.matchMedia('(max-width: 767px)');

    const searchBox = document.querySelector('input[type="search"], input[placeholder*="earch" i]');
    let topbar = null;
    if (searchBox) {
        let el = searchBox;
        while (el && el !== document.body) {
            if (el.contains(hero)) break;
            const pos = getComputedStyle(el).position;
            if (el.tagName === 'HEADER' || el.tagName === 'NAV' || pos === 'sticky' || pos === 'fixed') topbar = el;
            el = el.parentElement;
        }
    }

    function snapHero() {
        if (topbar) topbar.style.display = mq.matches ? 'none' : '';
        hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
        if (!mq.matches) return;
        const r = hero.getBoundingClientRect();
        const cs = getComputedStyle(hero);
        const mt = parseFloat(cs.marginTop) || 0;
        const ml = parseFloat(cs.marginLeft) || 0;
        const mr = parseFloat(cs.marginRight) || 0;
        const top = r.top + window.scrollY;
        const right = document.documentElement.clientWidth - r.right;
        if (top > 0) hero.style.marginTop = (mt - top) + 'px';
        if (r.left !== 0) hero.style.marginLeft = (ml - r.left) + 'px';
        if (right !== 0) hero.style.marginRight = (mr - right) + 'px';
    }
    const run = () => requestAnimationFrame(snapHero);
    run();
    window.addEventListener('load', run);
    window.addEventListener('resize', run);
    if (mq.addEventListener) mq.addEventListener('change', run);
})();
</script>
@endsection