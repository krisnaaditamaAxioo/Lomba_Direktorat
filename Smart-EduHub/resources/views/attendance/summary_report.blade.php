@extends('layouts.app')

@section('title', 'Ringkasan Laporan Absensi')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* Tema SEH: latar lavender muda + font Inter */
    body{background-color:#f4f4fa;font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif}

    .scrollbar-hide::-webkit-scrollbar{display:none}
    .scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}

    .table-scroll{scrollbar-width:thin;scrollbar-color:#fdba74 transparent}
    .table-scroll::-webkit-scrollbar{height:8px;width:8px}
    .table-scroll::-webkit-scrollbar-thumb{background:#fdba74;border-radius:999px}

    @keyframes slideUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
    .animate-slide-up{animation:slideUp .4s ease-out both}
    @media (prefers-reduced-motion:reduce){.animate-slide-up{animation:none}}

    details > summary{list-style:none}
    details > summary::-webkit-details-marker{display:none}
    details[open] .chev{transform:rotate(180deg)}

    /* Header mobile: ujung atas aman untuk notch / status bar */
    #heroBleed{padding-top:max(1.25rem, env(safe-area-inset-top))}

    @media print{
        .no-print{display:none !important}
        body{background:#fff}
    }
</style>
@endsection

@section('content')
@php
    use Carbon\Carbon;

    /* ============================================================
     |  PRA-HITUNG (logika sama dengan versi lama, dihitung sekali
     |  supaya dipakai tabel desktop & kartu mobile)
     ============================================================ */
    $reportStart = Carbon::parse($start_date);
    $reportEnd   = Carbon::parse($end_date);
    $today       = Carbon::today();

    /* Periode & jumlah hari kerja per bulan (sama untuk semua siswa) */
    $monthMeta = [];
    foreach ($months as $month) {
        $monthStart  = Carbon::parse($month['key'] . '-01')->startOfDay();
        $monthEnd    = $monthStart->copy()->endOfMonth();
        $periodStart = $monthStart->copy()->max($reportStart->copy());
        $periodEnd   = $monthEnd->copy()->min($reportEnd->copy())->min($today->copy());

        $workDays = 0;
        if ($periodStart <= $periodEnd) {
            $holidayCount = 0;
            $cur  = $periodStart->copy()->startOfDay();
            $last = $periodEnd->copy()->startOfDay();
            while ($cur <= $last) {
                if (\App\Models\Holiday::checkHoliday($cur)) {
                    $holidayCount++;
                }
                $cur->addDay();
            }
            $workDays = max(0, (int) $periodStart->copy()->startOfDay()->diffInDays($last) + 1 - $holidayCount);
        }

        $monthMeta[$month['key']] = ['start' => $periodStart, 'end' => $periodEnd, 'days' => $workDays];
    }

    /* Rekap per siswa */
    $rows  = [];
    $grand = ['o' => 0, 't' => 0, 'i' => 0, 's' => 0, 'a' => 0];

    foreach ($students as $student) {
        $cells = [];
        $tot   = ['o' => 0, 't' => 0, 'i' => 0, 's' => 0, 'a' => 0];

        foreach ($months as $month) {
            $meta = $monthMeta[$month['key']];

            $attendances = $student->attendances->filter(function ($a) use ($meta) {
                return Carbon::parse($a->date)->between($meta['start'], $meta['end']);
            });

            $o = $attendances->where('status', 'present')->count();
            $t = $attendances->where('status', 'late')->count();
            $i = $attendances->filter(fn ($a) => $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') === false)->count();
            $s = $attendances->filter(fn ($a) => $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') !== false)->count();
            $recordedAbsent = $attendances->where('status', 'absent')->count();

            /* Alpa = hari tanpa catatan (s/d hari ini, di luar libur) + alpa tercatat */
            $withAttendance = $o + $t + $i + $s + $recordedAbsent;
            $a = max(0, ($meta['days'] - $withAttendance) + $recordedAbsent);

            $cells[$month['key']] = compact('o', 't', 'i', 's', 'a');
            $tot['o'] += $o; $tot['t'] += $t; $tot['i'] += $i; $tot['s'] += $s; $tot['a'] += $a;
        }

        $present = $tot['o'] + $tot['t'];
        $denom   = $present + $tot['i'] + $tot['s'] + $tot['a'];
        $rate    = $denom > 0 ? (int) round($present / $denom * 100) : 0;

        foreach ($grand as $k => $v) { $grand[$k] += $tot[$k]; }

        $rows[] = ['student' => $student, 'cells' => $cells, 'tot' => $tot, 'present' => $present, 'rate' => $rate];
    }

    $grandPresent = $grand['o'] + $grand['t'];
    $grandDenom   = $grandPresent + $grand['i'] + $grand['s'] + $grand['a'];
    $grandRate    = $grandDenom > 0 ? (int) round($grandPresent / $grandDenom * 100) : 0;
    $studentCount = count($rows);

    $scopeLabel = $angkatan ? 'Angkatan ' . $angkatan : 'Semua Angkatan';

    /* Filter aktif + preset rentang tanggal */
    $searchQuery   = request('search');
    $activeFilters = ($angkatan ? 1 : 0) + ($searchQuery ? 1 : 0);

    $presets = [
        ['Bulan ini',  Carbon::now()->startOfMonth()->toDateString(),                          Carbon::now()->endOfMonth()->toDateString()],
        ['Bulan lalu', Carbon::now()->subMonthNoOverflow()->startOfMonth()->toDateString(),    Carbon::now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
        ['3 Bulan',    Carbon::now()->subMonthsNoOverflow(2)->startOfMonth()->toDateString(),  Carbon::now()->endOfMonth()->toDateString()],
        ['Tahun ini',  Carbon::now()->startOfYear()->toDateString(),                           Carbon::now()->endOfYear()->toDateString()],
    ];

    /* Class reusable */
    $fieldClass = 'w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3.5 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-600/10 focus:border-orange-600 outline-none transition-all';
    $labelClass = 'flex items-center gap-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5';

    $statCards = [
        ['icon' => 'groups',           'label' => 'Total Siswa',   'value' => $studentCount, 'chip' => $scopeLabel,            'chipClass' => 'bg-slate-100 text-slate-500',    'tone' => 'bg-orange-50 text-orange-600'],
        ['icon' => 'how_to_reg',       'label' => 'Total Hadir',   'value' => $grandPresent, 'chip' => $grandRate . '% hadir', 'chipClass' => 'bg-emerald-50 text-emerald-600', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['icon' => 'medical_services', 'label' => 'Izin & Sakit',  'value' => $grand['i'] + $grand['s'], 'chip' => 'I ' . $grand['i'] . ' · S ' . $grand['s'], 'chipClass' => 'bg-amber-50 text-amber-600', 'tone' => 'bg-amber-50 text-amber-600'],
        ['icon' => 'person_off',       'label' => 'Total Alpa',    'value' => $grand['a'],   'chip' => $grand['a'] > 0 ? 'Perlu perhatian' : 'Aman', 'chipClass' => $grand['a'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600', 'tone' => 'bg-rose-50 text-rose-600'],
    ];

    $legends = [
        ['H', 'Hadir (Ontime + Telat)', 'bg-emerald-500 text-white'],
        ['O', 'Ontime',                 'bg-emerald-100 text-emerald-700'],
        ['T', 'Telat',                  'bg-amber-100 text-amber-700'],
        ['I', 'Izin',                   'bg-orange-100 text-orange-700'],
        ['S', 'Sakit',                  'bg-orange-100 text-orange-700'],
        ['A', 'Alpa',                   'bg-rose-100 text-rose-700'],
    ];
@endphp

<div>

    {{-- ============ HERO HEADER ============
         Mobile  : full-bleed, menempel ke atas & kiri-kanan layar (diatur otomatis oleh script di bawah)
         Desktop : kartu membulat biasa --}}
    <header id="heroBleed" class="no-print relative overflow-hidden bg-gradient-to-br from-orange-500 via-red-500 to-red-800 px-5 sm:px-10 pb-12 sm:pb-14 rounded-b-[2rem] sm:rounded-3xl lg:rounded-[2.5rem] shadow-xl shadow-red-500/20">
        <div class="absolute -top-20 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-28 -left-16 w-72 h-72 bg-red-900/25 rounded-full"></div>
        <div class="absolute bottom-0 right-1/4 w-40 h-40 bg-white/5 rounded-full hidden sm:block"></div>

        <div class="relative">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">school</span>
                    </div>
                    <span class="text-white font-extrabold tracking-tight">SEH</span>
                </div>
                <span class="inline-flex items-center gap-1.5 bg-white rounded-full px-3.5 py-2 text-xs font-extrabold text-slate-900 shadow-sm">
                    <span class="material-symbols-outlined text-sm text-orange-500">groups</span>
                    {{ $studentCount }} Siswa
                </span>
            </div>

            <div class="text-center mt-6 sm:mt-9">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-200">Laporan Absensi</p>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mt-1.5">Ringkasan Kehadiran</h2>
                <p class="text-orange-100/90 text-sm sm:text-base mt-2 max-w-xl mx-auto">Rekapitulasi jumlah kehadiran per bulan untuk periode yang dipilih.</p>

                {{-- Segmented: pindah mode laporan --}}
                <div class="inline-flex mt-5 p-1 rounded-full bg-white/15 backdrop-blur border border-white/20">
                    <a href="{{ route('attendance.report', array_merge(request()->all(), ['mode' => 'detail'])) }}"
                       class="px-4 sm:px-5 py-2 rounded-full text-xs font-extrabold transition-all {{ $mode === 'detail' ? 'bg-white text-orange-600 shadow' : 'text-white/90 hover:text-white' }}">Harian</a>
                    <a href="{{ route('attendance.report', array_merge(request()->all(), ['mode' => 'summary'])) }}"
                       class="px-4 sm:px-5 py-2 rounded-full text-xs font-extrabold transition-all {{ $mode === 'summary' ? 'bg-white text-orange-600 shadow' : 'text-white/90 hover:text-white' }}">Ringkasan Bulanan</a>
                </div>
            </div>
        </div>
    </header>

    {{-- Pill periode + aksi (menimpa sedikit bagian bawah header) --}}
    <div class="no-print relative z-10 -mt-7 sm:mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-lg shadow-slate-200/60 sm:shadow-sm py-3 px-4 flex items-center justify-center gap-2 text-sm font-bold text-slate-800 text-center">
            <span class="material-symbols-outlined text-lg shrink-0">calendar_month</span>
            <span>{{ $reportStart->translatedFormat('d M Y') }} &ndash; {{ $reportEnd->translatedFormat('d M Y') }}</span>
        </div>
        <a href="{{ route('attendance.report', array_merge(request()->all(), ['print' => 1])) }}" target="_blank"
           class="bg-orange-600 hover:bg-orange-700 text-white rounded-2xl py-3 px-4 flex items-center justify-center gap-2 text-sm font-bold shadow-md shadow-orange-600/25 active:scale-[0.99] transition-all">
            <span class="material-symbols-outlined text-lg">print</span> Cetak PDF
        </a>
    </div>

    <div class="mt-5 sm:mt-6 space-y-5 sm:space-y-6">

        {{-- ============ STAT CARDS ============ --}}
        <div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($statCards as $stat)
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all animate-slide-up" style="animation-delay: {{ $loop->index * 60 }}ms">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl {{ $stat['tone'] }} flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl">{{ $stat['icon'] }}</span>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] sm:text-[11px] font-bold {{ $stat['chipClass'] }} truncate max-w-[62%]">{{ $stat['chip'] }}</span>
                </div>
                <p class="text-xs font-bold text-slate-500 mt-3 sm:mt-4">{{ $stat['label'] }}</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-0.5">{{ $stat['value'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- ============ FILTER ============ --}}
        <form action="{{ route('attendance.report') }}" method="GET" class="no-print bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            {{-- Kepala filter (di mobile: tombol buka/tutup) --}}
            <button type="button" id="filterToggle" class="w-full flex items-center justify-between gap-3 p-4 sm:p-5 lg:cursor-default text-left" aria-expanded="false" aria-controls="filterBody">
                <span class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">tune</span>
                    </span>
                    <span>
                        <span class="block font-extrabold text-slate-900 text-sm sm:text-base">Filter Laporan</span>
                        <span class="block text-[11px] text-slate-400">
                            {{ $activeFilters > 0 ? $activeFilters . ' filter aktif' : 'Atur angkatan, tanggal & pencarian' }}
                        </span>
                    </span>
                </span>
                <span class="lg:hidden flex items-center gap-2">
                    @if($activeFilters > 0)
                        <span class="min-w-[1.5rem] h-6 px-1.5 rounded-full bg-orange-600 text-white text-[11px] font-extrabold flex items-center justify-center">{{ $activeFilters }}</span>
                    @endif
                    <span id="filterChevron" class="material-symbols-outlined text-slate-400 transition-transform">expand_more</span>
                </span>
            </button>

            <div id="filterBody" class="hidden lg:block px-4 sm:px-5 pb-4 sm:pb-5 border-t border-slate-100">
                {{-- Preset cepat --}}
                <div class="flex gap-2 overflow-x-auto scrollbar-hide pt-4 pb-1">
                    @foreach($presets as [$plabel, $pstart, $pend])
                        @php $presetActive = ($start_date === $pstart && $end_date === $pend); @endphp
                        <a href="{{ route('attendance.report', array_merge(request()->all(), ['start_date' => $pstart, 'end_date' => $pend])) }}"
                           class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all {{ $presetActive ? 'bg-orange-600 text-white border-transparent shadow-md shadow-orange-600/25' : 'bg-white text-slate-600 border-slate-200 hover:border-orange-300 hover:text-orange-600' }}">
                            {{ $plabel }}
                        </a>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 mt-3">
                    <div>
                        <label class="{{ $labelClass }}">Angkatan</label>
                        <select name="angkatan" onchange="this.form.submit()" class="{{ $fieldClass }}">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatans as $angkatanItem)
                                <option value="{{ $angkatanItem->code }}" {{ $angkatan === $angkatanItem->code ? 'selected' : '' }}>{{ $angkatanItem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $start_date }}" onchange="this.form.submit()" class="{{ $fieldClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $end_date }}" onchange="this.form.submit()" class="{{ $fieldClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Mode Laporan</label>
                        <select name="mode" onchange="this.form.submit()" class="{{ $fieldClass }}">
                            <option value="detail" {{ $mode === 'detail' ? 'selected' : '' }}>Harian (1-31 Hari)</option>
                            <option value="summary" {{ $mode === 'summary' ? 'selected' : '' }}>Ringkasan Bulanan</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label class="{{ $labelClass }}">Cari Nama Siswa</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                                <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Ketik nama..." class="{{ $fieldClass }} !pl-10">
                            </div>
                            <button type="submit" class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-4 sm:px-5 rounded-xl shadow-md shadow-orange-600/25 active:scale-95 transition-all flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">filter_alt</span>
                                <span class="hidden sm:inline">Terapkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chip filter aktif (selalu terlihat) --}}
            @if($activeFilters > 0)
            <div class="flex flex-wrap items-center gap-2 px-4 sm:px-5 pb-4 {{ $activeFilters > 0 ? 'border-t border-slate-100 pt-3' : '' }}">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Aktif:</span>
                @if($angkatan)
                    <a href="{{ route('attendance.report', request()->except('angkatan')) }}" class="inline-flex items-center gap-1 pl-3 pr-2 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-bold hover:bg-orange-100 transition-colors">
                        Angkatan {{ $angkatan }} <span class="material-symbols-outlined text-sm">close</span>
                    </a>
                @endif
                @if($searchQuery)
                    <a href="{{ route('attendance.report', request()->except('search')) }}" class="inline-flex items-center gap-1 pl-3 pr-2 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-bold hover:bg-orange-100 transition-colors max-w-full">
                        <span class="truncate">&ldquo;{{ $searchQuery }}&rdquo;</span> <span class="material-symbols-outlined text-sm">close</span>
                    </a>
                @endif
            </div>
            @endif
        </form>

        {{-- ============ LEGEND ============ --}}
        <div class="no-print flex gap-2 overflow-x-auto scrollbar-hide sm:flex-wrap pb-1">
            @foreach($legends as [$code, $text, $cls])
            <div class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 rounded-full pl-1.5 pr-3.5 py-1.5 shadow-sm">
                <span class="w-6 h-6 rounded-full flex items-center justify-center font-extrabold text-[11px] {{ $cls }}">{{ $code }}</span>
                <span class="text-xs font-bold text-slate-600 whitespace-nowrap">{{ $text }}</span>
            </div>
            @endforeach
        </div>

        {{-- ============ DATA ============ --}}
        <div class="bg-white border border-slate-100 rounded-3xl lg:rounded-[2rem] shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">table_chart</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 truncate">Ringkasan Kehadiran &mdash; {{ $scopeLabel }}</h3>
                        <p class="text-xs text-slate-400">{{ $studentCount }} siswa &middot; {{ count($months) }} bulan</p>
                    </div>
                </div>
                <span class="no-print lg:hidden inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-100 rounded-full px-3 py-1.5">
                    <span class="material-symbols-outlined text-sm">touch_app</span> Ketuk siswa untuk rincian bulanan
                </span>
                <span class="no-print hidden lg:inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-100 rounded-full px-3 py-1.5">
                    <span class="material-symbols-outlined text-sm">swap_horiz</span> Geser tabel ke samping
                </span>
            </div>

            @if($studentCount === 0)
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-5xl text-orange-200">search_off</span>
                    <p class="text-sm font-bold text-slate-500 mt-2">Tidak ada data siswa untuk filter ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah angkatan, rentang tanggal, atau kata kunci.</p>
                </div>
            @else

            {{-- ---------- MOBILE & TABLET: kartu per siswa ---------- --}}
            <div class="lg:hidden divide-y divide-slate-100 no-print">
                @foreach($rows as $row)
                @php $st = $row['student']; $tot = $row['tot']; @endphp
                <details class="group">
                    <summary class="cursor-pointer p-4 sm:p-5 active:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-900 truncate">{{ $st->nama_lengkap }}</p>
                                <p class="text-[10px] font-extrabold text-orange-600 uppercase tracking-wide mt-0.5">Angkatan {{ $st->angkatan ?? '-' }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $row['rate'] >= 80 ? 'bg-emerald-50 text-emerald-600' : ($row['rate'] >= 60 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">{{ $row['rate'] }}%</span>
                                <span class="material-symbols-outlined text-slate-400 chev transition-transform">expand_more</span>
                            </div>
                        </div>

                        <div class="h-1.5 rounded-full bg-slate-100 mt-3 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-emerald-500" style="width: {{ $row['rate'] }}%"></div>
                        </div>

                        <div class="grid grid-cols-6 gap-1.5 mt-3 text-center">
                            <div class="rounded-xl bg-emerald-500 text-white py-1.5"><p class="text-[9px] font-extrabold opacity-80">H</p><p class="text-sm font-extrabold leading-tight">{{ $row['present'] }}</p></div>
                            <div class="rounded-xl bg-emerald-50 text-emerald-700 py-1.5"><p class="text-[9px] font-extrabold opacity-70">O</p><p class="text-sm font-extrabold leading-tight">{{ $tot['o'] }}</p></div>
                            <div class="rounded-xl bg-amber-50 text-amber-700 py-1.5"><p class="text-[9px] font-extrabold opacity-70">T</p><p class="text-sm font-extrabold leading-tight">{{ $tot['t'] }}</p></div>
                            <div class="rounded-xl bg-orange-50 text-orange-700 py-1.5"><p class="text-[9px] font-extrabold opacity-70">I</p><p class="text-sm font-extrabold leading-tight">{{ $tot['i'] }}</p></div>
                            <div class="rounded-xl bg-orange-50 text-orange-700 py-1.5"><p class="text-[9px] font-extrabold opacity-70">S</p><p class="text-sm font-extrabold leading-tight">{{ $tot['s'] }}</p></div>
                            <div class="rounded-xl bg-rose-50 text-rose-700 py-1.5"><p class="text-[9px] font-extrabold opacity-70">A</p><p class="text-sm font-extrabold leading-tight">{{ $tot['a'] }}</p></div>
                        </div>
                    </summary>

                    {{-- Rincian per bulan --}}
                    <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-1 space-y-2 bg-slate-50/60">
                        @foreach($months as $month)
                        @php $c = $row['cells'][$month['key']]; @endphp
                        <div class="flex items-center justify-between gap-2 bg-white border border-slate-100 rounded-2xl px-3 py-2.5">
                            <span class="text-xs font-extrabold text-slate-700 truncate">{{ $month['name'] }}</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="min-w-[2rem] text-center rounded-lg bg-emerald-500 text-white text-[11px] font-extrabold py-1 px-1.5">{{ ($c['o'] + $c['t']) ?: '-' }}</span>
                                <span class="min-w-[1.75rem] text-center rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-bold py-1 px-1">{{ $c['o'] ?: '-' }}</span>
                                <span class="min-w-[1.75rem] text-center rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold py-1 px-1">{{ $c['t'] ?: '-' }}</span>
                                <span class="min-w-[1.75rem] text-center rounded-lg bg-orange-50 text-orange-700 text-[11px] font-bold py-1 px-1">{{ $c['i'] ?: '-' }}</span>
                                <span class="min-w-[1.75rem] text-center rounded-lg bg-orange-50 text-orange-700 text-[11px] font-bold py-1 px-1">{{ $c['s'] ?: '-' }}</span>
                                <span class="min-w-[1.75rem] text-center rounded-lg bg-rose-50 text-rose-700 text-[11px] font-bold py-1 px-1">{{ $c['a'] ?: '-' }}</span>
                            </div>
                        </div>
                        @endforeach
                        <p class="text-[10px] text-slate-400 text-center pt-1">Urutan: H &middot; O &middot; T &middot; I &middot; S &middot; A</p>
                    </div>
                </details>
                @endforeach
            </div>

            {{-- ---------- DESKTOP: tabel lengkap (header menempel saat scroll) ---------- --}}
            <div class="hidden lg:block w-full overflow-auto table-scroll max-h-[75vh]">
                <table class="w-full text-left border-collapse" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th class="p-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest sticky left-0 top-0 bg-slate-50 z-30 w-56 border-r border-slate-100" rowspan="2">Nama Siswa</th>
                            @foreach($months as $month)
                                <th class="h-10 p-2.5 text-center text-[10px] font-extrabold text-slate-500 uppercase tracking-widest border-r border-slate-100 sticky top-0 z-20 {{ $loop->even ? 'bg-slate-100' : 'bg-slate-50' }}" colspan="6">{{ $month['name'] }}</th>
                            @endforeach
                            <th class="h-10 p-3 text-[10px] font-extrabold text-white uppercase tracking-widest text-center bg-gradient-to-r from-orange-500 to-red-600 border-l-2 border-orange-200 sticky top-0 z-20" colspan="7">Total Keseluruhan</th>
                        </tr>
                        <tr>
                            @foreach($months as $month)
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-emerald-500 text-white sticky top-10 z-20">H</th>
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-emerald-50 text-emerald-600 sticky top-10 z-20">O</th>
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-amber-50 text-amber-600 sticky top-10 z-20">T</th>
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-orange-50 text-orange-600 sticky top-10 z-20">I</th>
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-orange-50 text-orange-600 sticky top-10 z-20">S</th>
                                <th class="p-2 text-center text-[10px] font-extrabold uppercase border-r border-slate-100 bg-rose-50 text-rose-600 sticky top-10 z-20">A</th>
                            @endforeach
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase text-white bg-emerald-600 border-l-2 border-orange-200 sticky top-10 z-20">H</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700 sticky top-10 z-20">O</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-amber-100 text-amber-700 sticky top-10 z-20">T</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-orange-100 text-orange-700 sticky top-10 z-20">I</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-orange-100 text-orange-700 sticky top-10 z-20">S</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-rose-100 text-rose-700 sticky top-10 z-20">A</th>
                            <th class="p-2 text-center text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700 sticky top-10 z-20">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rows as $row)
                        @php $st = $row['student']; $tot = $row['tot']; @endphp
                        <tr class="group hover:bg-orange-50/40 transition-colors">
                            <td class="p-4 sticky left-0 bg-white group-hover:bg-orange-50 z-10 border-r border-slate-100 transition-colors">
                                <p class="text-xs font-bold text-slate-900 truncate max-w-[13rem]">{{ $st->nama_lengkap }}</p>
                                <p class="text-[9px] font-extrabold text-orange-600 uppercase tracking-tight mt-0.5">Angkatan {{ $st->angkatan ?? '-' }}</p>
                            </td>

                            @foreach($months as $month)
                                @php $c = $row['cells'][$month['key']]; @endphp
                                <td class="p-2 text-center text-[11px] font-extrabold text-white bg-emerald-500 border-r border-emerald-400/40">{{ ($c['o'] + $c['t']) ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold {{ $c['o'] ? 'text-emerald-700' : 'text-slate-300' }} border-r border-slate-50">{{ $c['o'] ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold {{ $c['t'] ? 'text-amber-700 bg-amber-50/60' : 'text-slate-300' }} border-r border-slate-50">{{ $c['t'] ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold {{ $c['i'] ? 'text-orange-700 bg-orange-50/60' : 'text-slate-300' }} border-r border-slate-50">{{ $c['i'] ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold {{ $c['s'] ? 'text-orange-700 bg-orange-50/60' : 'text-slate-300' }} border-r border-slate-50">{{ $c['s'] ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold {{ $c['a'] ? 'text-rose-700 bg-rose-50' : 'text-slate-300' }} border-r border-slate-100">{{ $c['a'] ?: '-' }}</td>
                            @endforeach

                            <td class="p-2 text-center text-[11px] font-extrabold text-white bg-emerald-600 border-l-2 border-orange-200">{{ $row['present'] ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-extrabold text-emerald-800 bg-emerald-50">{{ $tot['o'] ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-extrabold text-amber-800 bg-amber-50">{{ $tot['t'] ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-extrabold text-orange-800 bg-orange-50">{{ $tot['i'] ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-extrabold text-orange-800 bg-orange-50">{{ $tot['s'] ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-extrabold text-rose-800 bg-rose-50">{{ $tot['a'] ?: '-' }}</td>
                            <td class="p-2 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $row['rate'] >= 80 ? 'bg-emerald-100 text-emerald-700' : ($row['rate'] >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $row['rate'] }}%</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    /* ===== Header full-bleed di mobile =====
       Menempel ke ujung atas + kiri + kanan layar, berapa pun padding yang dipakai layout.
       Jika ada top-bar fixed/sticky milik layout, header diletakkan tepat di bawahnya (tanpa celah). */
    const hero = document.getElementById('heroBleed');

    function fitHero() {
        if (!hero) return;
        hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';

        if (window.innerWidth >= 640) return;           // sm ke atas: kartu biasa

        const vw = document.documentElement.clientWidth;
        const r  = hero.getBoundingClientRect();

        /* Cari top-bar fixed/sticky yang menutupi tepi atas layar */
        let barBottom = 0;
        document.elementsFromPoint(Math.round(vw / 2), 2).forEach(el => {
            if (hero.contains(el) || el === document.documentElement || el === document.body) return;
            const pos = getComputedStyle(el).position;
            if (pos === 'fixed' || pos === 'sticky') {
                barBottom = Math.max(barBottom, el.getBoundingClientRect().bottom);
            }
        });

        const up = Math.max(0, r.top - barBottom);
        hero.style.marginTop   = (-up) + 'px';
        hero.style.marginLeft  = (-r.left) + 'px';
        hero.style.marginRight = (-(vw - r.right)) + 'px';
    }

    fitHero();
    window.addEventListener('load', fitHero);
    window.addEventListener('resize', fitHero);
    window.addEventListener('orientationchange', fitHero);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitHero);

    /* ===== Filter: buka/tutup di mobile ===== */
    const toggle  = document.getElementById('filterToggle');
    const body    = document.getElementById('filterBody');
    const chevron = document.getElementById('filterChevron');

    if (toggle && body) {
        toggle.addEventListener('click', () => {
            if (window.innerWidth >= 1024) return;       // desktop: selalu terbuka
            const open = body.classList.toggle('hidden') === false;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (chevron) chevron.style.transform = open ? 'rotate(180deg)' : '';
        });
    }
})();
</script>
@endsection