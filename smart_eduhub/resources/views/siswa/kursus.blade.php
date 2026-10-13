@extends('layouts.siswa')

@section('title', 'Kursus Saya')
@section('page_title', 'Materi Pembelajaran')

@section('content')
@php
    $list     = collect($courses);
    $total    = $list->count();
    $pct      = fn ($c) => max(0, min(100, (int) ($c->student_progress ?? 0)));
    $avg      = $total ? (int) round($list->avg($pct)) : 0;
    $done     = $list->filter(fn ($c) => $pct($c) >= 100)->count();
    $ongoing  = $list->filter(fn ($c) => $pct($c) > 0 && $pct($c) < 100)->count();
    $notStart = $total - $done - $ongoing;
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-16 px-2 sm:px-0">

    <!-- Header (selaras dengan halaman Keuangan) -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 pb-8 px-5 text-white overflow-hidden shadow-xl shadow-brand-950/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-5 lg:pb-6">
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-8 left-12 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/10">
                    <span class="material-symbols-outlined text-white text-lg" style="font-variation-settings:'FILL' 1;">school</span>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-white/75 block leading-none">Kurikulum siswa</span>
                    <h1 class="text-lg font-black tracking-tight block mt-1">Materi Pembelajaran</h1>
                </div>
            </div>

            <div class="flex items-center gap-2.5 px-3.5 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-bold text-white/90">{{ $total }} kursus terdaftar</span>
            </div>
        </div>
    </div>

    @if($total > 0)
    <!-- Ringkasan -->
    <section class="grid grid-cols-3 gap-3 sm:gap-4" aria-label="Ringkasan belajar">
        <div class="kc-stat">
            <span class="kc-stat-icon bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                <span class="material-symbols-outlined text-xl">menu_book</span>
            </span>
            <div class="min-w-0">
                <p class="kc-stat-value">{{ $total }}</p>
                <p class="kc-stat-label">Total kursus</p>
            </div>
        </div>

        <div class="kc-stat">
            <span class="kc-ring" style="--p: {{ $avg }}" role="img" aria-label="Rata-rata progres {{ $avg }} persen">
                <span>{{ $avg }}<small>%</small></span>
            </span>
            <div class="min-w-0">
                <p class="kc-stat-value sr-only">{{ $avg }}%</p>
                <p class="kc-stat-label">Rata-rata progres</p>
            </div>
        </div>

        <div class="kc-stat">
            <span class="kc-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">task_alt</span>
            </span>
            <div class="min-w-0">
                <p class="kc-stat-value">{{ $done }}</p>
                <p class="kc-stat-label">Selesai</p>
            </div>
        </div>
    </section>

    <!-- Pencarian & filter -->
    <section class="flex flex-col lg:flex-row lg:items-center gap-3" aria-label="Cari dan saring kursus">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
            <input id="kcSearch" type="search" autocomplete="off" placeholder="Cari kursus"
                   class="w-full h-12 pl-12 pr-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-base sm:text-sm font-medium text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all"/>
        </div>

        <div id="kcFilters" class="flex gap-2 overflow-x-auto -mx-2 px-2 lg:mx-0 lg:px-0 pb-1 lg:pb-0" role="group" aria-label="Filter status">
            <button type="button" class="kc-chip" data-filter="semua" aria-pressed="true">Semua <span class="kc-chip-n">{{ $total }}</span></button>
            <button type="button" class="kc-chip" data-filter="berjalan" aria-pressed="false">Sedang berjalan <span class="kc-chip-n">{{ $ongoing }}</span></button>
            <button type="button" class="kc-chip" data-filter="baru" aria-pressed="false">Belum dimulai <span class="kc-chip-n">{{ $notStart }}</span></button>
            <button type="button" class="kc-chip" data-filter="selesai" aria-pressed="false">Selesai <span class="kc-chip-n">{{ $done }}</span></button>
        </div>
    </section>
    @endif

    <!-- Grid kursus -->
    <div id="kcGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        @forelse($courses as $course)
        @php
            $p      = $pct($course);
            $status = $p >= 100 ? 'selesai' : ($p > 0 ? 'berjalan' : 'baru');
            $label  = $status === 'selesai' ? 'Selesai' : ($status === 'berjalan' ? 'Sedang berjalan' : 'Belum dimulai');
            $cta    = $status === 'selesai' ? 'Tinjau materi' : ($status === 'berjalan' ? 'Lanjutkan' : 'Mulai belajar');
        @endphp
        <article class="kc-card group relative flex flex-col bg-white dark:bg-slate-900 rounded-[1.75rem] border border-slate-200/80 dark:border-slate-800 overflow-hidden"
                 data-course data-status="{{ $status }}" data-title="{{ \Illuminate\Support\Str::lower($course->title . ' ' . strip_tags((string) $course->description)) }}">

            <!-- Sampul -->
            <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E]">
                <span class="material-symbols-outlined absolute -right-3 -bottom-5 text-[8rem] leading-none text-white/10 select-none" aria-hidden="true">auto_stories</span>

                @if($course->image)
                <img src="{{ asset('storage/'.$course->image) }}" alt="" loading="lazy"
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                     onerror="this.remove()">
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-[#8A1E1E]/70 via-transparent to-transparent"></div>

                <span class="kc-badge absolute top-3.5 left-3.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'selesai' ? 'bg-emerald-400' : ($status === 'berjalan' ? 'bg-amber-300' : 'bg-white/70') }}"></span>
                    {{ $label }}
                </span>

                <span class="kc-badge absolute top-3.5 right-3.5 tabular-nums">{{ $p }}%</span>
            </div>

            <!-- Isi -->
            <div class="flex-1 flex flex-col p-5 sm:p-6">
                <h2 class="text-base sm:text-[1.0625rem] font-extrabold text-slate-900 dark:text-white leading-snug tracking-tight line-clamp-2 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                    {{ $course->title }}
                </h2>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">
                    {{ $course->description }}
                </p>

                <div class="mt-auto pt-5">
                    <div class="flex items-center justify-between text-xs font-semibold mb-2">
                        <span class="text-slate-500 dark:text-slate-400">Progres belajar</span>
                        <span class="text-slate-900 dark:text-white tabular-nums">{{ $p }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $p }}" aria-label="Progres {{ $course->title }}">
                        <div class="kc-bar h-full rounded-full {{ $status === 'selesai' ? 'bg-emerald-500' : 'bg-gradient-to-r from-[#F6863B] to-[#EB4325]' }}" style="width: {{ $p }}%"></div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 flex items-center justify-center text-[11px] font-black shrink-0">AX</span>
                        <span class="min-w-0">
                            <span class="block text-[11px] font-medium text-slate-400 leading-none">Mitra kelas</span>
                            <span class="block text-xs font-bold text-slate-700 dark:text-slate-200 truncate mt-1">Axioo Education</span>
                        </span>
                    </div>

                    <a href="{{ route('siswa.modules', $course->id) }}"
                       class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-[#EB4325] dark:hover:bg-[#EB4325] dark:hover:text-white text-xs font-bold whitespace-nowrap transition-colors active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#EB4325] after:absolute after:inset-0 after:content-['']">
                        {{ $cta }}
                        <span class="material-symbols-outlined text-base transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                    </a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full py-20 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-[2rem] text-center p-8">
            <div class="w-16 h-16 bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">auto_stories</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum ada kelas aktif</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1.5 leading-relaxed">
                Anda belum terdaftar di kelas pembelajaran industri mana pun. Hubungi wali kelas atau admin kurikulum sekolah Anda.
            </p>
        </div>
        @endforelse
    </div>

    <!-- Hasil pencarian kosong -->
    <div id="kcNoResult" class="hidden py-16 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-[2rem] text-center p-8">
        <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="material-symbols-outlined text-2xl">search_off</span>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Kursus tidak ditemukan</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Coba kata kunci lain atau ganti filter.</p>
        <button type="button" id="kcReset" class="mt-5 inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
            <span class="material-symbols-outlined text-base">restart_alt</span>
            Atur ulang
        </button>
    </div>

    <p id="kcCount" class="text-center text-xs font-medium text-slate-400" aria-live="polite"></p>
</div>

<style>
    /* Kartu */
    .kc-card { box-shadow: 0 1px 2px rgba(15, 23, 42, .04); transition: transform .3s cubic-bezier(.16, 1, .3, 1), box-shadow .3s, border-color .3s; }
    .kc-card:hover { transform: translateY(-4px); border-color: rgba(235, 67, 37, .35); box-shadow: 0 18px 40px -18px rgba(138, 30, 30, .35); }
    .kc-card:focus-within { border-color: rgba(235, 67, 37, .6); }
    .kc-card.is-hidden { display: none; }

    .kc-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .3rem .7rem; border-radius: 999px;
        background: rgba(255, 255, 255, .18); border: 1px solid rgba(255, 255, 255, .22);
        color: #fff; font-size: 11px; font-weight: 700;
        -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
    }

    .kc-bar { animation: kcGrow .9s cubic-bezier(.16, 1, .3, 1) both; }
    @keyframes kcGrow { from { width: 0; } }

    /* Statistik */
    .kc-stat {
        display: flex; align-items: center; gap: .75rem; min-width: 0;
        padding: .875rem; border-radius: 1.25rem;
        background: #fff; border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .dark .kc-stat { background: #0f172a; border-color: #1e293b; }
    .kc-stat-icon { width: 2.5rem; height: 2.5rem; border-radius: .875rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kc-stat-value { font-size: 1.25rem; font-weight: 800; line-height: 1; letter-spacing: -.02em; color: #0f172a; font-variant-numeric: tabular-nums; }
    .dark .kc-stat-value { color: #fff; }
    .kc-stat-label { margin-top: .25rem; font-size: 11px; font-weight: 600; color: #64748b; line-height: 1.2; }

    .kc-ring {
        --p: 0;
        width: 2.5rem; height: 2.5rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: conic-gradient(#EB4325 calc(var(--p) * 1%), #f1f5f9 0);
        position: relative;
    }
    .dark .kc-ring { background: conic-gradient(#EB4325 calc(var(--p) * 1%), #1e293b 0); }
    .kc-ring::before { content: ''; position: absolute; inset: 4px; border-radius: 50%; background: #fff; }
    .dark .kc-ring::before { background: #0f172a; }
    .kc-ring span { position: relative; font-size: 10px; font-weight: 800; color: #0f172a; font-variant-numeric: tabular-nums; }
    .dark .kc-ring span { color: #fff; }
    .kc-ring small { font-size: 7px; font-weight: 700; }

    @media (max-width: 420px) {
        .kc-stat { flex-direction: column; align-items: flex-start; gap: .6rem; }
    }

    /* Filter */
    .kc-chip {
        display: inline-flex; align-items: center; gap: .5rem; flex-shrink: 0;
        height: 2.5rem; padding: 0 .9rem; border-radius: 999px;
        background: #fff; border: 1px solid #e2e8f0;
        font-size: 12px; font-weight: 700; color: #475569; white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s, transform .12s;
    }
    .dark .kc-chip { background: #0f172a; border-color: #1e293b; color: #cbd5e1; }
    .kc-chip:hover { border-color: #fdba74; }
    .kc-chip:active { transform: scale(.96); }
    .kc-chip:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }
    .kc-chip[aria-pressed="true"] { background: #0f172a; border-color: #0f172a; color: #fff; }
    .dark .kc-chip[aria-pressed="true"] { background: #fff; border-color: #fff; color: #0f172a; }
    .kc-chip-n { min-width: 1.25rem; padding: 0 .35rem; border-radius: 999px; background: rgba(100, 116, 139, .15); font-size: 11px; line-height: 1.25rem; text-align: center; font-variant-numeric: tabular-nums; }
    .kc-chip[aria-pressed="true"] .kc-chip-n { background: rgba(255, 255, 255, .2); }
    .dark .kc-chip[aria-pressed="true"] .kc-chip-n { background: rgba(15, 23, 42, .12); }

    #kcFilters { scrollbar-width: none; }
    #kcFilters::-webkit-scrollbar { display: none; }

    @media (prefers-reduced-motion: reduce) {
        .kc-card, .kc-card:hover { transition: none; transform: none; }
        .kc-bar { animation: none; }
    }
</style>

<script>
(function () {
    var grid = document.getElementById('kcGrid');
    if (!grid) return;

    var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-course]'));
    if (!cards.length) return;

    var search = document.getElementById('kcSearch');
    var chips = Array.prototype.slice.call(document.querySelectorAll('#kcFilters [data-filter]'));
    var noResult = document.getElementById('kcNoResult');
    var count = document.getElementById('kcCount');
    var state = { q: '', filter: 'semua' };

    function apply() {
        var q = state.q.trim().toLowerCase();
        var shown = 0;

        cards.forEach(function (card) {
            var okFilter = state.filter === 'semua' || card.getAttribute('data-status') === state.filter;
            var okQuery = !q || (card.getAttribute('data-title') || '').indexOf(q) !== -1;
            var visible = okFilter && okQuery;
            card.classList.toggle('is-hidden', !visible);
            if (visible) shown++;
        });

        noResult.classList.toggle('hidden', shown !== 0);
        grid.classList.toggle('hidden', shown === 0);
        count.textContent = shown === cards.length
            ? ''
            : 'Menampilkan ' + shown + ' dari ' + cards.length + ' kursus';
    }

    function setFilter(value) {
        state.filter = value;
        chips.forEach(function (chip) {
            chip.setAttribute('aria-pressed', String(chip.getAttribute('data-filter') === value));
        });
        apply();
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () { setFilter(chip.getAttribute('data-filter')); });
    });

    if (search) {
        search.addEventListener('input', function () { state.q = search.value; apply(); });
    }

    var reset = document.getElementById('kcReset');
    if (reset) {
        reset.addEventListener('click', function () {
            if (search) search.value = '';
            state.q = '';
            setFilter('semua');
        });
    }
})();
</script>
@endsection