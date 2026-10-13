@extends('layouts.siswa')

@section('title', 'Riwayat Presensi')
@section('page_title', 'Kehadiran Anda')

@push('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* Filter fields on the gradient header */
    .glass-field {
        width: 100%;
        min-height: 44px;
        padding: .5rem .75rem;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 12px;
        background: rgba(255, 255, 255, .12);
        color: #fff;
        font-size: 1rem; /* 16px on phones prevents iOS zoom on focus */
        font-weight: 600;
        outline: none;
        color-scheme: dark; /* white calendar icon on the dark field */
        transition: background .15s, box-shadow .15s;
    }
    .glass-field:focus { background: rgba(255, 255, 255, .2); box-shadow: 0 0 0 3px rgba(255, 255, 255, .3); }
    .glass-field option { color: #0f172a; background: #fff; }
    @media (min-width: 640px) { .glass-field { font-size: .875rem; } }

    /* Calendar day cells */
    .day-cell { transition: transform .12s; }
    .day-cell:active { transform: scale(.95); }
    .day-cell:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }
    .day-cell.is-selected { outline: 2px solid #F6863B; outline-offset: 2px; }

    @media (prefers-reduced-motion: reduce) { .day-cell { transition: none; } }
</style>
@endpush

@section('content')
@php
    // ---- Status meta (code => look & label) ----
    $codeMeta = [
        'O' => ['label' => 'Tepat waktu', 'cell' => 'bg-emerald-100 text-emerald-800', 'pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'month' => 'text-emerald-700'],
        'T' => ['label' => 'Terlambat',   'cell' => 'bg-amber-100 text-amber-800',     'pill' => 'bg-amber-50 text-amber-800 border-amber-200',       'month' => 'text-amber-700'],
        'I' => ['label' => 'Izin',        'cell' => 'bg-orange-100 text-orange-800',   'pill' => 'bg-orange-50 text-orange-800 border-orange-200',     'month' => 'text-orange-700'],
        'S' => ['label' => 'Sakit',       'cell' => 'bg-sky-100 text-sky-800',         'pill' => 'bg-sky-50 text-sky-800 border-sky-200',             'month' => 'text-sky-700'],
        'A' => ['label' => 'Alpa',        'cell' => 'bg-rose-100 text-rose-800',       'pill' => 'bg-rose-50 text-rose-800 border-rose-200',          'month' => 'text-rose-700'],
        'L' => ['label' => 'Libur',       'cell' => 'bg-slate-100 text-slate-500',     'pill' => 'bg-slate-100 text-slate-600 border-slate-200',      'month' => 'text-slate-500'],
    ];

    // Attendance record => code
    $codeOf = function ($att) {
        switch ($att->status) {
            case 'present': return 'O';
            case 'late':    return 'T';
            case 'excused':
                return \Illuminate\Support\Str::contains(strtolower((string) $att->location), 'sakit') ? 'S' : 'I';
            case 'absent':  return 'A';
        }
        return null;
    };

    // ---- Calendar data (same rules as before) ----
    $tally = ['O' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];
    $cal = [];
    foreach ($dates as $date) {
        $attendance = $attendances->where('date', $date)->first();
        $day        = \Carbon\Carbon::parse($date);
        $code       = '';

        if (\App\Models\Holiday::checkHoliday($date)) {
            $code = 'L';
        } elseif ($attendance) {
            $code = $codeOf($attendance) ?? '';
        } elseif (! $day->isFuture()) {
            $code = 'A';
        }

        if ($code) $tally[$code]++;
        $cal[] = ['date' => $day, 'code' => $code, 'attendance' => $attendance];
    }
    $months = collect($cal)->groupBy(fn ($d) => $d['date']->format('Y-m'));
    $todayInRange = collect($cal)->contains(fn ($d) => $d['date']->isToday());

    // ---- Quick ranges ----
    $curStart = \Carbon\Carbon::parse($start_date)->toDateString();
    $curEnd   = \Carbon\Carbon::parse($end_date)->toDateString();
    $ranges = [
        'Bulan ini'        => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        'Bulan lalu'       => [now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
        '30 hari terakhir' => [now()->subDays(29)->toDateString(), now()->toDateString()],
    ];

    // ---- Summary cards ----
    $statCards = [
        ['label' => 'Total hadir',  'value' => $stats['present'] + $stats['late'], 'icon' => 'verified',             'tile' => 'bg-white/20 text-white',          'hero' => true],
        ['label' => 'Tepat waktu',  'value' => $stats['present'],                  'icon' => 'check_circle',         'tile' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Terlambat',    'value' => $stats['late'],                     'icon' => 'schedule',             'tile' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Izin / sakit', 'value' => $stats['excused'],                  'icon' => 'assignment_turned_in', 'tile' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Alpa',         'value' => $stats['absent'],                   'icon' => 'cancel',               'tile' => 'bg-rose-50 text-rose-600'],
    ];
@endphp

<div class="w-full space-y-5 sm:space-y-6">

    {{-- Header + filters + summary (own wrapper so the cards can overlap the header) --}}
    <div>
        <header class="relative -mx-4 -mt-4 overflow-hidden rounded-b-[2.5rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pb-24 pt-8 text-white shadow-xl shadow-[#EB4325]/15 sm:px-6 lg:mx-0 lg:mt-0 lg:rounded-[2.5rem] lg:pb-20">
            <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/5" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-12 top-24 h-40 w-40 rounded-full bg-white/5" aria-hidden="true"></div>

            <div class="relative z-10 space-y-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight lg:text-3xl">Riwayat Kehadiran</h1>
                    <p class="mt-1 text-xs text-white/85 sm:text-sm">Laporan dan rekapitulasi presensi</p>
                </div>

                {{-- Quick ranges --}}
                <nav aria-label="Rentang cepat" class="-mx-5 overflow-x-auto px-5 [scrollbar-width:none] sm:mx-0 sm:px-0">
                    <ul class="flex w-max gap-2">
                        @foreach($ranges as $label => $r)
                            @php $active = $curStart === $r[0] && $curEnd === $r[1]; @endphp
                            <li>
                                <a href="{{ route('siswa.attendance', ['start_date' => $r[0], 'end_date' => $r[1], 'mode' => $mode]) }}"
                                   @if($active) aria-current="true" @endif
                                   class="focus-ring inline-flex h-9 items-center rounded-full border px-3.5 text-xs font-semibold transition active:scale-95 {{ $active ? 'border-white bg-white text-[#C2331A]' : 'border-white/25 bg-white/10 text-white hover:bg-white/20' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Filter form --}}
                <form action="{{ route('siswa.attendance') }}" method="GET"
                      class="grid grid-cols-2 gap-3 rounded-2xl border border-white/10 bg-white/10 p-3 backdrop-blur-md sm:grid-cols-3 sm:p-4">
                    <div>
                        <label for="start_date" class="mb-1 block text-xs font-semibold text-white/85">Dari tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" onchange="this.form.submit()" class="glass-field cursor-pointer">
                    </div>
                    <div>
                        <label for="end_date" class="mb-1 block text-xs font-semibold text-white/85">Sampai tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $end_date }}" onchange="this.form.submit()" class="glass-field cursor-pointer">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="mode" class="mb-1 block text-xs font-semibold text-white/85">Mode tampilan</label>
                        <select id="mode" name="mode" onchange="this.form.submit()" class="glass-field cursor-pointer">
                            <option value="detail" {{ $mode === 'detail' ? 'selected' : '' }}>Harian</option>
                            <option value="summary" {{ $mode === 'summary' ? 'selected' : '' }}>Ringkasan</option>
                        </select>
                    </div>
                    <noscript>
                        <button type="submit" class="col-span-2 h-11 rounded-xl bg-white text-sm font-bold text-[#C2331A] sm:col-span-3">Terapkan</button>
                    </noscript>
                </form>
            </div>
        </header>

        {{-- Summary cards: no hidden horizontal scrolling --}}
        <section aria-label="Ringkasan kehadiran" class="relative z-10 -mt-14 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:-mt-12 lg:grid-cols-5">
            @foreach($statCards as $card)
                @if(! empty($card['hero']))
                    <div class="col-span-2 flex items-center gap-3 rounded-3xl bg-emerald-700 p-4 text-white shadow-lg shadow-emerald-900/15 sm:col-span-4 lg:col-span-1">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $card['tile'] }}" aria-hidden="true">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ $card['icon'] }}</span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-3xl font-extrabold leading-none tabular-nums">{{ $card['value'] }}</p>
                            <p class="mt-1 text-xs font-semibold text-white/85">{{ $card['label'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 shadow-lg shadow-slate-900/5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl {{ $card['tile'] }}" aria-hidden="true">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">{{ $card['icon'] }}</span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-2xl font-extrabold leading-none tabular-nums text-slate-900">{{ $card['value'] }}</p>
                            <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </section>
    </div>

    {{-- Calendar --}}
    <section aria-labelledby="calTitle" class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
            <div class="min-w-0">
                <h2 id="calTitle" class="text-base font-extrabold tracking-tight text-slate-900">Kalender kehadiran</h2>
                <p class="mt-0.5 text-[13px] tabular-nums text-slate-500">
                    {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M') }} – {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}
                </p>
            </div>
            <a href="{{ route('siswa.attendance', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" rel="noopener"
               class="focus-ring inline-flex h-10 shrink-0 items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50 active:scale-95">
                <span class="material-symbols-outlined text-lg" aria-hidden="true">print</span>
                Cetak PDF
            </a>
        </div>

        <div class="p-4 sm:p-5">
            {{-- Selected day detail --}}
            <div id="dayDetail" aria-live="polite" class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p id="dayPrompt" class="text-[13px] leading-relaxed text-slate-500">
                    Ketuk tanggal pada kalender untuk melihat jam masuk dan lokasi.
                    @if($todayInRange) Bingkai gelap menandai hari ini. @endif
                </p>
                <div id="dayInfo" hidden>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p id="dayDate" class="text-sm font-bold text-slate-900"></p>
                        <span id="dayPill" class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"></span>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <dt class="text-xs text-slate-500">Jam masuk</dt>
                            <dd id="dayTime" class="mt-0.5 text-sm font-bold tabular-nums text-slate-900"></dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-500">Lokasi</dt>
                            <dd id="dayLoc" class="mt-0.5 truncate text-sm font-bold text-slate-900"></dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Month grids --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($months as $ym => $items)
                    @php
                        $first  = $items->first()['date'];
                        $offset = $first->dayOfWeekIso - 1; // week starts on Monday
                    @endphp
                    <div class="rounded-2xl border border-slate-100 p-3 sm:p-4">
                        <h3 class="mb-3 text-sm font-bold text-slate-900">{{ $first->translatedFormat('F Y') }}</h3>
                        <div class="grid grid-cols-7 gap-1.5 text-center">
                            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $wd)
                                <span class="pb-1 text-[11px] font-semibold text-slate-400">{{ $wd }}</span>
                            @endforeach

                            @for($k = 0; $k < $offset; $k++)
                                <span aria-hidden="true"></span>
                            @endfor

                            @foreach($items as $d)
                                @php
                                    $meta  = $d['code'] ? $codeMeta[$d['code']] : null;
                                    $att   = $d['attendance'];
                                    $today = $d['date']->isToday();
                                    $time  = ($att && $att->check_in) ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '';
                                    $loc   = $att ? ($att->location ?: 'Main Gate') : '';
                                @endphp
                                @if($meta)
                                    <button type="button"
                                            class="day-cell flex aspect-square flex-col items-center justify-center gap-0.5 rounded-xl {{ $meta['cell'] }} {{ $today ? 'ring-2 ring-slate-900' : '' }}"
                                            aria-pressed="false"
                                            aria-label="{{ $d['date']->translatedFormat('l, j F Y') }}: {{ $meta['label'] }}"
                                            data-today="{{ $today ? 1 : 0 }}"
                                            data-date="{{ $d['date']->translatedFormat('l, j F Y') }}"
                                            data-label="{{ $meta['label'] }}"
                                            data-pill="{{ $meta['pill'] }}"
                                            data-time="{{ $time }}"
                                            data-location="{{ $loc }}">
                                        <span class="text-[13px] font-semibold leading-none tabular-nums">{{ $d['date']->format('j') }}</span>
                                        <span class="text-[10px] font-bold leading-none">{{ $d['code'] }}</span>
                                    </button>
                                @else
                                    <div class="flex aspect-square items-center justify-center rounded-xl text-[13px] tabular-nums text-slate-300 {{ $today ? 'ring-2 ring-slate-900' : '' }}">
                                        {{ $d['date']->format('j') }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Legend that doubles as the recap --}}
        <div class="border-t border-slate-100 bg-slate-50/60 p-4 sm:p-5">
            <h3 class="mb-3 text-xs font-semibold text-slate-600">Rekap kalender</h3>
            <dl class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($codeMeta as $code => $meta)
                    <div class="flex items-center gap-2.5 rounded-2xl border border-slate-100 bg-white p-2.5">
                        <dt class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-xs font-bold {{ $meta['cell'] }}">{{ $code }}</dt>
                        <dd class="min-w-0">
                            <span class="block text-base font-extrabold leading-none tabular-nums text-slate-900">{{ $tally[$code] }}</span>
                            <span class="mt-1 block truncate text-xs text-slate-500">{{ $meta['label'] }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Detailed log --}}
    <section aria-labelledby="logTitle" class="space-y-3">
        <div class="flex items-center justify-between gap-3 px-1">
            <h2 id="logTitle" class="text-base font-extrabold tracking-tight text-slate-900">Rincian log presensi</h2>
            <span class="text-xs font-semibold tabular-nums text-slate-500">{{ $attendances->count() }} catatan</span>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            @if($attendances->count() > 0)
                <ul class="divide-y divide-slate-100">
                    @foreach($attendances as $attendance)
                        @php
                            $code = $codeOf($attendance);
                            $meta = $code ? $codeMeta[$code] : ['label' => $attendance->status, 'pill' => 'bg-slate-100 text-slate-600 border-slate-200', 'month' => 'text-slate-500'];
                            $day  = \Carbon\Carbon::parse($attendance->date);
                        @endphp
                        <li class="flex items-center gap-3 px-4 py-3.5 sm:px-5">
                            {{-- Date tile --}}
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-100" aria-hidden="true">
                                <span class="text-[11px] font-bold leading-none {{ $meta['month'] }}">{{ $day->translatedFormat('D') }}</span>
                                <span class="mt-1 text-xl font-extrabold leading-none tabular-nums text-slate-900">{{ $day->format('d') }}</span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-900">{{ $day->translatedFormat('l, j M Y') }}</p>
                                <p class="mt-0.5 flex items-center gap-1 truncate text-xs text-slate-500">
                                    <span class="material-symbols-outlined text-sm" aria-hidden="true">location_on</span>
                                    <span class="truncate">{{ $attendance->location ?: 'Main Gate' }}</span>
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold tabular-nums {{ $attendance->check_in ? 'text-slate-900' : 'text-slate-300' }}">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : '–' }}
                                </p>
                                <span class="mt-1 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $meta['pill'] }}">
                                    {{ $meta['label'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-300">
                        <span class="material-symbols-outlined text-2xl">event_busy</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Tidak ada log presensi</h3>
                    <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Ubah rentang tanggal atau pilih rentang cepat untuk mencari data.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Space so the last item clears the bottom nav on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const cells  = document.querySelectorAll('.day-cell');
    const prompt = document.getElementById('dayPrompt');
    const info   = document.getElementById('dayInfo');
    const dateEl = document.getElementById('dayDate');
    const pill   = document.getElementById('dayPill');
    const timeEl = document.getElementById('dayTime');
    const locEl  = document.getElementById('dayLoc');
    const pillBase = 'inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold';
    let current = null;

    function clear() {
        if (current) {
            current.classList.remove('is-selected');
            current.setAttribute('aria-pressed', 'false');
        }
    }

    cells.forEach(function (cell) {
        cell.addEventListener('click', function () {
            const same = current === cell;
            clear();

            if (same) {
                current = null;
                info.hidden = true;
                prompt.hidden = false;
                return;
            }

            current = cell;
            cell.classList.add('is-selected');
            cell.setAttribute('aria-pressed', 'true');

            dateEl.textContent = cell.dataset.date;
            pill.textContent   = cell.dataset.label;
            pill.className     = pillBase + ' ' + cell.dataset.pill;
            timeEl.textContent = cell.dataset.time || '–';
            locEl.textContent  = cell.dataset.location || '–';

            prompt.hidden = true;
            info.hidden = false;
        });
    });

    // Show today's details by default when today is in the range
    const today = document.querySelector('.day-cell[data-today="1"]');
    if (today) today.click();
})();
</script>
@endpush