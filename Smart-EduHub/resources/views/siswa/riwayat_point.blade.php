@extends('layouts.siswa')

@section('title', 'Riwayat Poin')
@section('page_title', 'Kedisiplinan Anda')

@push('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    .point-row[hidden], #filterEmpty[hidden] { display: none !important; }
    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* Filter chips */
    .chip {
        display: inline-flex; align-items: center; gap: .4rem; min-height: 36px; padding: 0 .85rem;
        border: 1px solid #e2e8f0; border-radius: 999px; background: #fff; color: #475569;
        font-size: .8125rem; font-weight: 600; white-space: nowrap;
        transition: background .15s, color .15s, border-color .15s;
    }
    .chip:hover { background: #f8fafc; }
    .chip.is-active { background: #0f172a; border-color: #0f172a; color: #fff; }
    .chip-count { font-size: .75rem; font-variant-numeric: tabular-nums; opacity: .7; }

    /* The one moment of motion: the gauge draws itself once on load */
    .ring-progress {
        stroke-dasharray: var(--c);
        stroke-dashoffset: var(--o);
        animation: ring-in .9s cubic-bezier(.22, 1, .36, 1) both;
    }
    @keyframes ring-in { from { stroke-dashoffset: var(--c); } }
    @media (prefers-reduced-motion: reduce) { .ring-progress { animation: none; } }
</style>
@endpush

@section('content')
@php
    $student = Auth::guard('student')->user();
    $totalViolation   = $student->points()->where('type', 'violation')->sum('score');
    $totalAchievement = $student->points()->where('type', 'achievement')->sum('score');
    $netPoints = max(0, $totalViolation - $totalAchievement);

    // Same thresholds as before: 70+ Pembinaan, 50+ Waspada, otherwise Prima
    $level = $netPoints >= 70 ? 'coaching' : ($netPoints >= 50 ? 'warning' : 'good');
    $levels = [
        'good' => [
            'label' => 'Prima', 'range' => '0–49', 'advice' => 'Belum perlu tindakan', 'icon' => 'check_circle',
            'hex' => '#10b981', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'bar' => 'bg-emerald-500', 'ring' => 'ring-emerald-400',
        ],
        'warning' => [
            'label' => 'Waspada', 'range' => '50–69', 'advice' => 'Konseling wali kelas', 'icon' => 'report',
            'hex' => '#f59e0b', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'bar' => 'bg-amber-500', 'ring' => 'ring-amber-400',
        ],
        'coaching' => [
            'label' => 'Pembinaan', 'range' => '70 ke atas', 'advice' => 'Rekomendasi surat peringatan', 'icon' => 'report',
            'hex' => '#f43f5e', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'bar' => 'bg-rose-500', 'ring' => 'ring-rose-400',
        ],
    ];
    $lv = $levels[$level];

    $stability = max(0, 100 - $netPoints);

    // Gauge geometry (r = 52)
    $circ   = 2 * M_PI * 52;
    $offset = $circ * (1 - min($netPoints, 100) / 100);

    // Ledger helpers
    $isAchievementOf = fn ($p) => in_array($p->type, ['merit', 'achievement']);
    $pointList = $points instanceof \Illuminate\Pagination\AbstractPaginator ? collect($points->items()) : collect($points);
    $counts = [
        'all'         => $pointList->count(),
        'achievement' => $pointList->filter($isAchievementOf)->count(),
        'violation'   => $pointList->reject($isAchievementOf)->count(),
    ];
    $hasPages = method_exists($points, 'hasPages') && $points->hasPages();
@endphp

<div class="w-full space-y-5 sm:space-y-6">

    {{-- Header + score overview (own wrapper so the card can overlap the header) --}}
    <div>
        <header class="relative -mx-4 -mt-4 overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pb-16 pt-8 text-white shadow-xl shadow-[#EB4325]/15 sm:px-6 lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pb-14 lg:pt-6">
            <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/5" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-10 left-16 h-28 w-28 rounded-full bg-white/5" aria-hidden="true"></div>

            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-2xl font-black tracking-tight lg:text-3xl">Riwayat Poin</h1>
                    <p class="mt-1 text-xs text-white/85 sm:text-sm">Evaluasi kedisiplinan</p>
                </div>

                {{-- Status pill --}}
                <div class="inline-flex h-9 shrink-0 items-center gap-2 rounded-full bg-white px-3.5 text-xs font-bold text-slate-800 shadow-sm" role="status">
                    <span class="h-2 w-2 rounded-full {{ $lv['dot'] }}" aria-hidden="true"></span>
                    {{ $lv['label'] }}
                </div>
            </div>
        </header>

        <div class="relative z-10 -mt-10 grid gap-4 lg:grid-cols-3">

            {{-- Score card --}}
            <section aria-labelledby="scoreTitle" class="rounded-3xl border border-slate-100 bg-white p-5 shadow-lg shadow-slate-900/5 sm:p-6 lg:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="scoreTitle" class="text-xs font-semibold text-slate-500">Indeks pelanggaran bersih</h2>
                    <span class="rounded-full bg-[#FFF3EA] px-2.5 py-1 text-xs font-semibold text-[#9A3412]">SACP Member</span>
                </div>

                <div class="mt-4 flex flex-col items-center gap-6 sm:flex-row sm:items-center sm:gap-8">

                    {{-- Gauge --}}
                    <div class="relative h-36 w-36 shrink-0 sm:h-40 sm:w-40" role="img"
                         aria-label="Indeks pelanggaran bersih {{ $netPoints }} dari 100, status {{ $lv['label'] }}">
                        <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90" aria-hidden="true">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#f1f5f9" stroke-width="10"></circle>
                            <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                                    class="ring-progress"
                                    style="stroke: {{ $lv['hex'] }}; --c: {{ number_format($circ, 2, '.', '') }}; --o: {{ number_format($offset, 2, '.', '') }};"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-black leading-none tracking-tight tabular-nums text-slate-900">{{ $netPoints }}</span>
                            <span class="mt-1.5 text-xs font-semibold text-slate-500">dari 100</span>
                        </div>
                    </div>

                    <div class="w-full min-w-0 flex-1 space-y-5">
                        {{-- Advice --}}
                        <p class="flex items-center justify-center gap-2 text-base font-bold sm:justify-start {{ $lv['text'] }}">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">{{ $lv['icon'] }}</span>
                            {{ $lv['advice'] }}
                        </p>

                        {{-- Stability --}}
                        <div>
                            <div class="mb-1.5 flex items-baseline justify-between text-xs">
                                <span class="font-semibold text-slate-500">Stabilitas karakter</span>
                                <span class="font-bold tabular-nums {{ $lv['text'] }}">{{ $stability }}% stabil</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100" role="presentation">
                                <div class="h-full rounded-full {{ $lv['bar'] }}" style="width: {{ max(4, $stability) }}%"></div>
                            </div>
                        </div>

                        {{-- Levels --}}
                        <ul class="grid grid-cols-3 gap-2" aria-label="Tingkat indeks">
                            @foreach($levels as $key => $l)
                                <li class="rounded-2xl border border-slate-100 bg-slate-50 px-2.5 py-2 text-center {{ $key === $level ? 'ring-2 ring-offset-1 '.$l['ring'].' bg-white' : '' }}"
                                    @if($key === $level) aria-current="true" @endif>
                                    <span class="flex items-center justify-center gap-1.5 text-xs font-bold text-slate-800">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $l['dot'] }}" aria-hidden="true"></span>{{ $l['label'] }}
                                    </span>
                                    <span class="mt-0.5 block text-[11px] tabular-nums text-slate-500">{{ $l['range'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <p class="mt-5 border-t border-slate-100 pt-3 text-xs text-slate-500">
                    Tahun ajaran {{ $currentViewYear ?? 'aktif' }}
                </p>
            </section>

            {{-- Totals --}}
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-1 lg:gap-4">
                <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-50 text-emerald-600" aria-hidden="true">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500">Penghargaan</p>
                        <p class="mt-0.5 text-2xl font-extrabold leading-none tabular-nums text-emerald-600">
                            +{{ $totalAchievement }} <span class="text-xs font-semibold text-slate-400">poin</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-500" aria-hidden="true">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">report</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500">Pelanggaran</p>
                        <p class="mt-0.5 text-2xl font-extrabold leading-none tabular-nums text-rose-500">
                            {{ $totalViolation }} <span class="text-xs font-semibold text-slate-400">poin</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity log --}}
    <section aria-labelledby="logTitle" class="space-y-3">
        <div class="px-1">
            <h2 id="logTitle" class="text-base font-extrabold tracking-tight text-slate-900">Mutasi aktivitas</h2>
            <p class="mt-0.5 text-[13px] text-slate-500">Log kedisiplinan Anda.</p>

            @if($counts['all'] > 0)
                <div class="-mx-5 mt-3 overflow-x-auto px-5 [scrollbar-width:none] sm:mx-0 sm:px-0" role="group" aria-label="Saring berdasarkan jenis">
                    <div class="flex w-max gap-2 pb-1">
                        @foreach(['all' => 'Semua', 'achievement' => 'Penghargaan', 'violation' => 'Pelanggaran'] as $key => $label)
                            <button type="button" data-filter="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}"
                                    class="chip focus-ring {{ $key === 'all' ? 'is-active' : '' }}">
                                {{ $label }}
                                <span class="chip-count">{{ $counts[$key] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @if($hasPages)
                    <p class="mt-2 text-xs text-slate-500">Penyaringan berlaku untuk catatan di halaman ini.</p>
                @endif
            @endif
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            @if($pointList->isNotEmpty())
                <ul class="divide-y divide-slate-100">
                    @foreach($points as $point)
                        @php
                            $isAchievement = $isAchievementOf($point);
                            $day = $point->created_at;
                        @endphp
                        <li class="point-row flex items-center gap-3 px-4 py-3.5 sm:px-5" data-type="{{ $isAchievement ? 'achievement' : 'violation' }}">
                            {{-- Date tile --}}
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-100" aria-hidden="true">
                                <span class="text-[11px] font-bold leading-none {{ $isAchievement ? 'text-emerald-700' : 'text-rose-700' }}">{{ $day->translatedFormat('M') }}</span>
                                <span class="mt-1 text-xl font-extrabold leading-none tabular-nums text-slate-900">{{ $day->format('d') }}</span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="clamp-2 break-words text-sm font-semibold leading-snug text-slate-900">{{ $point->reason }}</p>
                                <p class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                                    <span class="tabular-nums">{{ $day->translatedFormat('j M Y') }}</span>
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $isAchievement ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' }}">
                                        {{ $isAchievement ? 'Penghargaan' : 'Pelanggaran' }}
                                    </span>
                                </span>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-lg font-extrabold leading-none tabular-nums whitespace-nowrap {{ $isAchievement ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $isAchievement ? '+' : '−' }}{{ $point->score }}
                                </p>
                                <p class="mt-1 text-[11px] text-slate-400">poin</p>
                            </div>
                        </li>
                    @endforeach

                    {{-- Shown when a filter has no matches --}}
                    <li id="filterEmpty" hidden class="px-5 py-10 text-center text-[13px] text-slate-500">
                        Tidak ada catatan untuk jenis ini.
                    </li>
                </ul>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-emerald-50 text-emerald-500">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Aman dan terjaga</h3>
                    <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Belum ada catatan poin. Sikap disiplin Anda terjaga dengan baik.</p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($hasPages)
            <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                {{ $points->links() }}
            </div>
        @endif
    </section>

    {{-- Space so the last item clears the bottom nav on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const chips = document.querySelectorAll('[data-filter]');
    const rows  = document.querySelectorAll('.point-row');
    const emptyMsg = document.getElementById('filterEmpty');

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            const filter = chip.dataset.filter;
            let visible = 0;

            chips.forEach(function (c) {
                const on = c === chip;
                c.classList.toggle('is-active', on);
                c.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            rows.forEach(function (row) {
                const show = filter === 'all' || row.dataset.type === filter;
                row.hidden = !show;
                if (show) visible++;
            });
            if (emptyMsg) emptyMsg.hidden = visible !== 0;
        });
    });
})();
</script>
@endpush