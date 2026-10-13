@extends('layouts.app')

@section('title', 'Laporan Absensi Bulanan')

@section('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* Di ponsel header menempel ke tepi atas layar (layout sudah menghapus padding-top
       untuk halaman yang punya #pageHero). Beri ruang untuk notch/status bar. */
    #pageHero { padding-top: max(2rem, calc(env(safe-area-inset-top, 0px) + 1.25rem)); }

    /* Field kaca di dalam header */
    .glass-field {
        width: 100%; min-height: 48px; padding: .5rem .875rem;
        border: 1px solid rgba(255,255,255,.22); border-radius: 14px;
        background: rgba(255,255,255,.14); color: #fff;
        font-size: 1rem; font-weight: 700; line-height: 1.5;
        outline: none; color-scheme: dark;
        -webkit-appearance: none; appearance: none;
        min-width: 0; max-width: 100%; box-sizing: border-box;
        transition: background .15s, box-shadow .15s;
    }
    .glass-field:focus { background: rgba(255,255,255,.22); box-shadow: 0 0 0 3px rgba(255,255,255,.3); }
    .glass-field::placeholder { color: rgba(255,255,255,.65); font-weight: 600; }
    .glass-field option { color: #0f172a; background: #fff; }
    .glass-field::-webkit-date-and-time-value { text-align: left; min-height: 1.5em; }
    .glass-field::-webkit-calendar-picker-indicator { cursor: pointer; opacity: .9; }
    @media (min-width: 640px) { .glass-field { font-size: .875rem; } }

    .attendance-cell { width: 30px; min-width: 30px; height: 30px; }
    .scroll-none { scrollbar-width: none; }
    .scroll-none::-webkit-scrollbar { display: none; }

    .table-wrapper { position: relative; }
    .table-wrapper::after {
        content: ''; position: absolute; top: 0; right: 0; bottom: 0; width: 32px;
        background: linear-gradient(to left, rgba(255,255,255,.95), transparent);
        pointer-events: none; z-index: 5;
    }
    .table-wrapper.at-end::after { display: none; }

    @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }

    .print-only { display: none; }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .print-table { display: block !important; }
        .screen-cards { display: none !important; }
        body { background: #fff !important; }
        .table-wrapper::after { display: none !important; }
    }
</style>
@endsection

@section('content')
@php
    $isSummary = ($mode ?? 'detail') === 'summary';
    $today     = now()->toDateString();

    $cellStyle = [
        'L' => 'bg-slate-100 text-slate-500',
        'O' => 'bg-emerald-100 text-emerald-700',
        'T' => 'bg-amber-100 text-amber-700',
        'I' => 'bg-orange-100 text-orange-700',
        'S' => 'bg-sky-100 text-sky-700',
        'A' => 'bg-rose-100 text-rose-700',
    ];

    // ---- Info per tanggal ----
    $dateMeta = [];
    foreach ($dates as $d) {
        $c = \Carbon\Carbon::parse($d);
        $holiday = \App\Models\Holiday::checkHoliday($d);
        $dateMeta[$d] = [
            'day'     => $c->format('d'),
            'holiday' => $holiday,
            'name'    => $holiday ? \App\Models\Holiday::getHolidayName($d) : '',
            'future'  => $c->isFuture(),
        ];
    }
    $firstDate = collect($dates)->first();
    $leadBlank = $firstDate ? \Carbon\Carbon::parse($firstDate)->isoWeekday() - 1 : 0;

    // ---- Matriks kehadiran ----
    $totals = ['O' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
    $rows = [];
    foreach ($students as $student) {
        // Indeks absensi per tanggal (ambil yang pertama bila ada duplikat)
        $byDate = [];
        foreach ($student->attendances as $att) {
            $key = \Carbon\Carbon::parse($att->date)->toDateString();
            if (! isset($byDate[$key])) {
                $byDate[$key] = $att;
            }
        }

        $cells = [];
        $cnt = ['O' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0];

        foreach ($dates as $d) {
            $m = $dateMeta[$d];
            $attendance = $byDate[$d] ?? null;
            $char = '';

            if ($m['holiday']) {
                $char = 'L';
            } elseif ($attendance) {
                if ($attendance->status === 'present') {
                    $char = 'O';
                } elseif ($attendance->status === 'late') {
                    $char = 'T';
                } elseif ($attendance->status === 'excused') {
                    $char = strpos(strtolower((string) $attendance->location), 'sakit') !== false ? 'S' : 'I';
                } elseif ($attendance->status === 'absent') {
                    $char = 'A';
                }
            } elseif (! $m['future']) {
                $char = 'A';
            }

            if ($char && $char !== 'L') {
                $cnt[$char]++;
                $totals[$char]++;
            }
            $cells[] = $char;
        }

        $hadir  = $cnt['O'] + $cnt['T'];
        $hari   = $hadir + $cnt['I'] + $cnt['S'] + $cnt['A'];
        $persen = $hari > 0 ? round($hadir / $hari * 100) : 0;
        $rows[] = ['student' => $student, 'cells' => $cells, 'cnt' => $cnt, 'hadir' => $hadir, 'persen' => $persen];
    }
    $totalPresent = $totals['O'] + $totals['T'];
    $totalAll     = $totalPresent + $totals['I'] + $totals['S'] + $totals['A'];
    $totalPersen  = $totalAll > 0 ? round($totalPresent / $totalAll * 100) : 0;
    $colspan      = ($isSummary ? 0 : count($dates)) + 8;

    // ---- Rentang cepat ----
    $curStart = \Carbon\Carbon::parse($start_date)->toDateString();
    $curEnd   = \Carbon\Carbon::parse($end_date)->toDateString();
    $ranges = [
        'Bulan ini'        => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        'Bulan lalu'       => [now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
        '30 hari terakhir' => [now()->subDays(29)->toDateString(), now()->toDateString()],
    ];
    $keepQuery = request()->except(['start_date', 'end_date', 'page', 'print']);

    $statCards = [
        ['label' => 'Tepat waktu',  'value' => $totals['O'],                'icon' => 'check_circle',         'tile' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Terlambat',    'value' => $totals['T'],                'icon' => 'schedule',             'tile' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Izin / sakit', 'value' => $totals['I'] + $totals['S'], 'icon' => 'assignment_turned_in', 'tile' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Alpa',         'value' => $totals['A'],                'icon' => 'cancel',               'tile' => 'bg-rose-50 text-rose-600'],
    ];

    $legend = [
        ['code' => 'O', 'label' => 'Tepat waktu'],
        ['code' => 'T', 'label' => 'Telat'],
        ['code' => 'I', 'label' => 'Izin'],
        ['code' => 'S', 'label' => 'Sakit'],
        ['code' => 'A', 'label' => 'Alpa'],
        ['code' => 'L', 'label' => 'Libur'],
    ];

    $periodLabel = \Carbon\Carbon::parse($start_date)->locale('id')->isoFormat('D MMM') . ' – ' . \Carbon\Carbon::parse($end_date)->locale('id')->isoFormat('D MMM Y');
    $scopeLabel  = $angkatan ? 'Angkatan ' . $angkatan : 'Semua Angkatan';
    $weekdays    = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    // Data untuk export CSV
    $exportRows = [];
    foreach ($rows as $r) {
        $exportRows[] = [
            (string) $r['student']->nama_lengkap,
            (string) ($r['student']->angkatan ?? '-'),
            $r['hadir'], $r['cnt']['O'], $r['cnt']['T'], $r['cnt']['I'], $r['cnt']['S'], $r['cnt']['A'],
            $r['persen'] . '%',
        ];
    }
    $exportFile = 'laporan-absensi-' . $curStart . '_' . $curEnd . '.csv';
@endphp

<div class="flex w-full min-w-0 flex-col gap-5 pb-4 lg:pb-0">

    {{-- Judul khusus cetak --}}
    <div class="print-only">
        <h1 class="text-xl font-black">Laporan Absensi Bulanan</h1>
        <p class="text-sm">{{ $periodLabel }} · {{ $scopeLabel }}</p>
    </div>

    <div class="no-print">
        {{-- ================= HEADER =================
             Ponsel: menempel ke tepi kiri/kanan/atas (negatif margin = padding <main> di layout).
             Desktop: kartu bersudut membulat selebar area konten. --}}
        <header id="pageHero"
                class="relative -mx-4 overflow-hidden rounded-b-[2.5rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-4 pb-24 text-white shadow-xl shadow-[#EB4325]/20 sm:-mx-6 sm:px-6 lg:mx-0 lg:rounded-3xl lg:px-8 lg:pb-20">
            <div class="pointer-events-none absolute -right-12 -top-12 h-56 w-56 rounded-full bg-white/10" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-16 top-40 h-44 w-44 rounded-full bg-black/10 blur-2xl" aria-hidden="true"></div>

            <div class="relative z-10 w-full space-y-4">
                <div class="min-w-0">
                    <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Laporan Absensi Bulanan</h1>
                    <p class="mt-1 text-xs text-white/85 sm:text-sm">Rekapitulasi kehadiran siswa dan persentase hadir</p>
                </div>

                {{-- Rentang cepat --}}
                <nav aria-label="Rentang cepat" class="scroll-none -mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
                    <ul class="flex w-max gap-2">
                        @foreach($ranges as $label => $r)
                            @php $active = $curStart === $r[0] && $curEnd === $r[1]; @endphp
                            <li>
                                <a href="{{ route('attendance.report', array_merge($keepQuery, ['start_date' => $r[0], 'end_date' => $r[1]])) }}"
                                   @if($active) aria-current="true" @endif
                                   class="focus-ring inline-flex h-10 items-center rounded-full border px-4 text-xs font-bold transition active:scale-95 {{ $active ? 'border-white bg-white text-[#C2331A]' : 'border-white/30 bg-white/10 text-white hover:bg-white/20' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Filter kaca --}}
                <form action="{{ route('attendance.report') }}" method="GET"
                      class="grid grid-cols-2 gap-3 rounded-3xl border border-white/20 bg-white/10 p-3.5 backdrop-blur-md sm:p-4 md:grid-cols-3 xl:grid-cols-5">
                    <div>
                        <label for="start_date" class="mb-1 block text-xs font-bold text-white/90">Dari tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $curStart }}" onchange="this.form.submit()" class="glass-field cursor-pointer">
                    </div>
                    <div>
                        <label for="end_date" class="mb-1 block text-xs font-bold text-white/90">Sampai tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $curEnd }}" onchange="this.form.submit()" class="glass-field cursor-pointer">
                    </div>
                    <div>
                        <label for="angkatan" class="mb-1 block text-xs font-bold text-white/90">Angkatan</label>
                        <select id="angkatan" name="angkatan" onchange="this.form.submit()" class="glass-field cursor-pointer">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatans as $angkatanItem)
                                <option value="{{ $angkatanItem->code }}" {{ $angkatan === $angkatanItem->code ? 'selected' : '' }}>{{ $angkatanItem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="mode" class="mb-1 block text-xs font-bold text-white/90">Mode tampilan</label>
                        <select id="mode" name="mode" onchange="this.form.submit()" class="glass-field cursor-pointer">
                            <option value="detail" {{ !$isSummary ? 'selected' : '' }}>Harian</option>
                            <option value="summary" {{ $isSummary ? 'selected' : '' }}>Ringkasan</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label for="search" class="mb-1 block text-xs font-bold text-white/90">Cari nama siswa</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik nama..." class="glass-field">
                    </div>
                    <button type="submit"
                            class="focus-ring col-span-2 inline-flex h-12 items-center justify-center gap-1.5 rounded-2xl bg-white text-sm font-bold text-[#C2331A] shadow-lg shadow-black/10 transition hover:bg-white/90 active:scale-95 md:col-span-3 xl:col-span-5 xl:ml-auto xl:w-44">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">search</span>
                        Terapkan
                    </button>
                </form>
            </div>
        </header>

        {{-- ================= KARTU RINGKASAN (menimpa header) ================= --}}
        <section aria-label="Ringkasan kehadiran"
                 class="relative z-10 -mt-14 grid w-full grid-cols-2 gap-3 px-0 sm:grid-cols-4 lg:-mt-12 lg:grid-cols-5">
            <div class="col-span-2 flex items-center gap-4 rounded-3xl bg-emerald-700 p-4 text-white shadow-lg shadow-emerald-900/20 sm:col-span-4 lg:col-span-1">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/20" aria-hidden="true">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
                </span>
                <div class="min-w-0">
                    <p class="text-3xl font-extrabold leading-none tabular-nums">{{ number_format($totalPresent) }}</p>
                    <p class="mt-1 text-xs font-semibold text-white/85">Total hadir · {{ $totalPersen }}%</p>
                </div>
            </div>

            @foreach($statCards as $card)
                <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-3.5 shadow-lg shadow-slate-900/5 sm:p-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $card['tile'] }}" aria-hidden="true">
                        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">{{ $card['icon'] }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-2xl font-extrabold leading-none tabular-nums text-slate-900">{{ number_format($card['value']) }}</p>
                        <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>
    </div>

    {{-- ================= DATA KEHADIRAN ================= --}}
    <section aria-labelledby="tblTitle" class="w-full min-w-0 overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
            <div class="min-w-0">
                <h2 id="tblTitle" class="truncate text-base font-extrabold tracking-tight text-slate-900">Data kehadiran</h2>
                <p class="mt-0.5 truncate text-[13px] tabular-nums text-slate-500">{{ $periodLabel }} · {{ $scopeLabel }} · {{ $students->count() }} siswa</p>
            </div>

            <div class="no-print flex shrink-0 items-center gap-2">
                <button type="button" id="btnExport" aria-label="Export CSV"
                        class="focus-ring inline-flex h-11 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 active:scale-95 sm:px-4">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">download</span>
                    <span class="hidden sm:inline">Export CSV</span>
                </button>
                <a href="{{ route('attendance.report', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" rel="noopener"
                   class="focus-ring inline-flex h-11 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 active:scale-95 sm:px-4">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">print</span>
                    <span class="hidden sm:inline">Cetak PDF</span>
                </a>
            </div>
        </div>

        {{-- Legenda --}}
        <div class="no-print scroll-none flex gap-2 overflow-x-auto border-b border-slate-100 px-4 py-3 sm:px-5" aria-label="Keterangan kode">
            @foreach($legend as $item)
                <div class="flex shrink-0 items-center gap-2 rounded-full border border-slate-100 bg-slate-50 py-1 pl-1 pr-3">
                    <span class="grid h-6 w-6 place-items-center rounded-full text-[11px] font-bold {{ $cellStyle[$item['code']] }}">{{ $item['code'] }}</span>
                    <span class="text-xs font-semibold text-slate-600">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Kartu siswa (ponsel & tablet): 1 kolom di ponsel, 2 kolom di tablet --}}
        <div class="screen-cards no-print grid grid-cols-1 gap-3 bg-slate-50/50 p-3 sm:p-4 md:grid-cols-2 lg:hidden">
            @forelse($rows as $row)
                @php $student = $row['student']; $cnt = $row['cnt']; $pc = $row['persen']; @endphp
                <article class="min-w-0 rounded-2xl border border-slate-100 bg-white p-3.5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-orange-50 text-sm font-bold text-orange-600" aria-hidden="true">
                            {{ strtoupper(mb_substr((string) $student->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $student->nama_lengkap }}</p>
                            <p class="text-xs text-slate-500">Angkatan {{ $student->angkatan ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-extrabold leading-none tabular-nums {{ $pc >= 90 ? 'text-emerald-600' : ($pc >= 75 ? 'text-amber-600' : 'text-rose-600') }}">{{ $pc }}%</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">hadir</p>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-5 gap-1.5 text-center">
                        @foreach(['O' => 'Tepat', 'T' => 'Telat', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpa'] as $k => $lbl)
                            <div class="rounded-xl py-1.5 {{ $cellStyle[$k] }}">
                                <p class="text-sm font-extrabold leading-none tabular-nums">{{ $cnt[$k] }}</p>
                                <p class="mt-0.5 text-[10px] font-semibold opacity-80">{{ $lbl }}</p>
                            </div>
                        @endforeach
                    </div>

                    @unless($isSummary)
                        {{-- Kalender mini --}}
                        <div class="mt-3 rounded-2xl border border-slate-100 p-2.5">
                            <div class="grid grid-cols-7 gap-1.5 pb-1.5 text-center text-[10px] font-semibold text-slate-400" aria-hidden="true">
                                @foreach($weekdays as $w)<span>{{ $w }}</span>@endforeach
                            </div>
                            <div class="grid grid-cols-7 gap-1.5">
                                @for($b = 0; $b < $leadBlank; $b++)<span></span>@endfor
                                @foreach($dates as $i => $d)
                                    @php $char = $row['cells'][$i]; @endphp
                                    <div class="flex h-11 flex-col items-center justify-center rounded-xl text-[11px] font-semibold leading-tight {{ $char ? $cellStyle[$char] : 'text-slate-300' }} {{ $d === $today ? 'ring-2 ring-slate-900' : '' }}"
                                         title="{{ $d }}{{ $dateMeta[$d]['name'] ? ' · ' . $dateMeta[$d]['name'] : '' }}">
                                        <span>{{ (int) $dateMeta[$d]['day'] }}</span>
                                        @if($char)<span class="text-[10px] font-bold">{{ $char }}</span>@endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endunless
                </article>
            @empty
                <div class="rounded-2xl border border-slate-100 bg-white px-6 py-10 text-center md:col-span-2">
                    <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-300">
                        <span class="material-symbols-outlined text-2xl">person_off</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Tidak ada data siswa</h3>
                    <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Ubah filter angkatan, rentang tanggal, atau kata pencarian.</p>
                </div>
            @endforelse
        </div>

        {{-- Tabel (desktop & cetak) --}}
        <div class="print-table table-wrapper hidden w-full overflow-x-auto lg:block" id="tableWrapper">
            <table class="w-full border-collapse text-left" style="{{ $isSummary ? '' : 'min-width: 1200px;' }}">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="sticky left-0 z-20 w-56 border-r border-slate-100 bg-slate-50 p-4 text-xs font-bold text-slate-500">Nama siswa</th>
                        @unless($isSummary)
                            @foreach($dates as $d)
                                <th class="w-10 border-r border-slate-100 p-2 text-center text-[11px] font-bold tabular-nums text-slate-400">{{ $dateMeta[$d]['day'] }}</th>
                            @endforeach
                        @endunless
                        <th class="border-l-2 border-emerald-100 bg-emerald-50/60 p-3 text-center text-xs font-bold text-emerald-600">Hadir</th>
                        <th class="bg-emerald-50/60 p-3 text-center text-xs font-bold text-emerald-600">O</th>
                        <th class="bg-amber-50/60 p-3 text-center text-xs font-bold text-amber-600">T</th>
                        <th class="bg-orange-50/60 p-3 text-center text-xs font-bold text-orange-600">I</th>
                        <th class="bg-sky-50/60 p-3 text-center text-xs font-bold text-sky-600">S</th>
                        <th class="bg-rose-50/60 p-3 text-center text-xs font-bold text-rose-600">A</th>
                        <th class="bg-slate-50 p-3 text-center text-xs font-bold text-slate-500">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rows as $row)
                        @php $student = $row['student']; $cnt = $row['cnt']; $pc = $row['persen']; @endphp
                        <tr class="group transition-colors hover:bg-orange-50/30">
                            <td class="sticky left-0 z-10 border-r border-slate-100 bg-white p-3 group-hover:bg-orange-50">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-orange-50 text-xs font-bold text-orange-600">
                                        {{ strtoupper(mb_substr((string) $student->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-900">{{ $student->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-500">Angkatan {{ $student->angkatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            @unless($isSummary)
                                @foreach($dates as $i => $d)
                                    @php $char = $row['cells'][$i]; @endphp
                                    <td class="border-r border-slate-50 p-1">
                                        @if($char)
                                            <div class="attendance-cell mx-auto flex items-center justify-center rounded-lg text-[10px] font-bold {{ $cellStyle[$char] }}"
                                                 title="{{ $dateMeta[$d]['name'] }}">{{ $char }}</div>
                                        @endif
                                    </td>
                                @endforeach
                            @endunless

                            <td class="border-l-2 border-emerald-100 bg-emerald-500 p-3 text-center text-sm font-black tabular-nums text-white">{{ $row['hadir'] }}</td>
                            <td class="bg-emerald-50/40 p-3 text-center text-sm font-bold tabular-nums text-emerald-700">{{ $cnt['O'] }}</td>
                            <td class="bg-amber-50/40 p-3 text-center text-sm font-bold tabular-nums text-amber-700">{{ $cnt['T'] }}</td>
                            <td class="bg-orange-50/40 p-3 text-center text-sm font-bold tabular-nums text-orange-700">{{ $cnt['I'] }}</td>
                            <td class="bg-sky-50/40 p-3 text-center text-sm font-bold tabular-nums text-sky-700">{{ $cnt['S'] }}</td>
                            <td class="bg-rose-50/40 p-3 text-center text-sm font-bold tabular-nums text-rose-700">{{ $cnt['A'] }}</td>
                            <td class="p-3 text-center text-sm font-bold tabular-nums {{ $pc >= 90 ? 'text-emerald-600' : ($pc >= 75 ? 'text-amber-600' : 'text-rose-600') }}">{{ $pc }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
                                <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-300">
                                    <span class="material-symbols-outlined text-2xl">person_off</span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">Tidak ada data siswa</h3>
                                <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Ubah filter angkatan, rentang tanggal, atau kata pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
(function () {
    // Petunjuk scroll tabel
    const wrapper = document.getElementById('tableWrapper');
    if (wrapper) {
        const check = () => wrapper.classList.toggle('at-end', wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth - 5);
        wrapper.addEventListener('scroll', check, { passive: true });
        window.addEventListener('resize', check);
        check();
    }

    // Export CSV (dibuka langsung oleh Excel)
    const exportHeader = ['Nama', 'Angkatan', 'Hadir', 'Tepat waktu', 'Telat', 'Izin', 'Sakit', 'Alpa', 'Persen'];
    const exportRows   = {!! json_encode($exportRows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
    const exportFile   = {!! json_encode($exportFile, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    function csvCell(v) {
        let s = String(v == null ? '' : v);
        if (/^[=+\-@\t\r]/.test(s)) s = "'" + s;   // cegah formula injection
        return '"' + s.replace(/"/g, '""') + '"';
    }

    const btn = document.getElementById('btnExport');
    if (btn) {
        btn.addEventListener('click', function () {
            const lines = [exportHeader].concat(exportRows).map(r => r.map(csvCell).join(','));
            const blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = exportFile;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        });
    }
})();
</script>
@endsection