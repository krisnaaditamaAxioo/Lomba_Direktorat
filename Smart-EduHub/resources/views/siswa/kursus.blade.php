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

    // Status ringkasan (warna dibuat lebih cerah)
    if ($total === 0)                         { $act = 'none'; $stLabel = 'Belum ada kursus';        $stIcon = 'hourglass_empty'; $stColor = '#38bdf8'; }
    elseif ($done === $total)                 { $act = 'done'; $stLabel = 'Semua kursus selesai';    $stIcon = 'check_circle';    $stColor = '#00932E'; }
    elseif ($ongoing > 0 || $done > 0)        { $act = 'run';  $stLabel = 'Pembelajaran berjalan';   $stIcon = 'trending_up';     $stColor = '#fbbf24'; }
    else                                      { $act = 'new';  $stLabel = 'Belum ada progres';       $stIcon = 'play_circle';     $stColor = '#38bdf8'; }
@endphp

<div class="kc-page w-full max-w-none pb-28 sm:pb-16">

    <!-- ===== BANNER ===== -->
    <header class="relative left-1/2 right-1/2 -mx-[50vw] w-screen -mt-4 sm:-mt-6 lg:left-auto lg:right-auto lg:mx-0 lg:mt-0 lg:w-auto
                   overflow-hidden text-white bg-gradient-to-br from-[#C95D05] via-[#EC4700] to-[#C62E00]
                   rounded-b-[2rem] sm:rounded-b-[2.5rem] lg:rounded-[2rem] shadow-xl shadow-orange-400/30
                   pb-24 sm:pb-28 lg:pb-16">
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/20"></div>
        <div class="pointer-events-none absolute top-24 -left-16 w-44 h-44 rounded-full bg-white/20"></div>
        <div class="pointer-events-none absolute -bottom-12 right-1/3 w-36 h-36 rounded-full bg-yellow-200/20"></div>

        <div class="relative px-5 sm:px-6 pt-[max(1.25rem,env(safe-area-inset-top))] lg:pt-6">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">Kursus Saya</h1>
                    <p class="text-sm text-white/95 mt-0.5">Materi pembelajaran dan progres belajar</p>
                </div>

                <span class="inline-flex shrink-0 items-center gap-2 h-10 px-4 rounded-full bg-white text-slate-900 text-xs font-extrabold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#00932E]"></span>
                    {{ $total }} kursus
                </span>
            </div>
        </div>
    </header>

    <div class="px-4 sm:px-6 lg:px-0 -mt-16 sm:-mt-20 lg:-mt-10 relative space-y-6 sm:space-y-8">

        @if($total > 0)
        <!-- ===== RINGKASAN ===== -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 lg:items-stretch" aria-label="Ringkasan belajar">

            <div class="lg:col-span-2 kc-panel p-5 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-slate-900">Rata-rata progres belajar</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-200 text-orange-700 text-[11px] font-bold">
                        {{ $total }} kursus terdaftar
                    </span>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                    <div class="mx-auto sm:mx-0">
                        <span class="kc-ring" style="--p: {{ $avg }}; --c: {{ $stColor }}" role="img" aria-label="Rata-rata progres {{ $avg }} persen">
                            <span class="flex flex-col items-center leading-none">
                                <b>{{ $avg }}%</b>
                                <small>rata-rata</small>
                            </span>
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2" style="color: {{ $stColor }}">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">{{ $stIcon }}</span>
                            <h2 class="text-lg font-extrabold tracking-tight">{{ $stLabel }}</h2>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-900">Progres keseluruhan</span>
                                <span class="tabular-nums" style="color: {{ $stColor }}">{{ $avg }}%</span>
                            </div>
                            <div class="mt-1.5 h-2.5 rounded-full bg-orange-100 overflow-hidden">
                                <div class="kc-bar h-full rounded-full" style="width: {{ $avg }}%; background: {{ $stColor }}"></div>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-2.5">
                            <div class="kc-tier {{ $act === 'new' ? 'is-active' : '' }}" style="--t:#38bdf8">
                                <p><i></i>Belum dimulai</p><span>{{ $notStart }} kursus</span>
                            </div>
                            <div class="kc-tier {{ $act === 'run' ? 'is-active' : '' }}" style="--t:#fbbf24">
                                <p><i></i>Berjalan</p><span>{{ $ongoing }} kursus</span>
                            </div>
                            <div class="kc-tier {{ $act === 'done' ? 'is-active' : '' }}" style="--t:#00932E">
                                <p><i></i>Selesai</p><span>{{ $done }} kursus</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-4">
                    <div class="pt-3 border-t border-orange-100 text-xs text-slate-800">
                        Dihitung dari seluruh kursus yang Anda ikuti
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-1 lg:grid-rows-2 gap-4 sm:gap-5">
                <div class="kc-panel kc-side">
                    <span class="kc-stat-icon bg-orange-200 text-orange-700">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">menu_book</span>
                    </span>
                    <div class="min-w-0">
                        <p class="kc-side-label">Total kursus</p>
                        <p class="kc-side-value text-orange-600">{{ $total }}<span>kursus</span></p>
                    </div>
                </div>

                <div class="kc-panel kc-side">
                    <span class="kc-stat-icon bg-[#c8ffdf] text-[#00c853]">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">task_alt</span>
                    </span>
                    <div class="min-w-0">
                        <p class="kc-side-label">Selesai</p>
                        <p class="kc-side-value text-[#00c853]">{{ $done }}<span>kursus</span></p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ===== DAFTAR KURSUS ===== -->
        <section aria-label="Daftar kursus">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-950">Daftar kursus</h2>
                    <p class="text-sm text-slate-800 mt-0.5">Pilih kursus untuk membuka modul pembelajaran.</p>
                </div>

                @if($total > 0)
                <div class="relative sm:w-72">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-orange-400 text-xl pointer-events-none">search</span>
                    <input id="kcSearch" type="search" autocomplete="off" placeholder="Cari kursus"
                           class="w-full h-11 pl-12 pr-4 rounded-2xl bg-white border border-orange-100 shadow-sm text-base sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-400/20 focus:border-orange-400 transition-all"/>
                </div>
                @endif
            </div>

            @if($total > 0)
            <div id="kcFilters" class="mt-4 flex gap-2 overflow-x-auto -mx-4 px-4 sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0 pb-1" role="group" aria-label="Filter status">
                <button type="button" class="kc-chip" data-filter="semua" aria-pressed="true">Semua <span class="kc-chip-n">{{ $total }}</span></button>
                <button type="button" class="kc-chip" data-filter="berjalan" aria-pressed="false">Sedang berjalan <span class="kc-chip-n">{{ $ongoing }}</span></button>
                <button type="button" class="kc-chip" data-filter="baru" aria-pressed="false">Belum dimulai <span class="kc-chip-n">{{ $notStart }}</span></button>
                <button type="button" class="kc-chip" data-filter="selesai" aria-pressed="false">Selesai <span class="kc-chip-n">{{ $done }}</span></button>
            </div>
            @endif

            <div id="kcGrid" class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
                @forelse($courses as $course)
                @php
                    $p      = $pct($course);
                    $status = $p >= 100 ? 'selesai' : ($p > 0 ? 'berjalan' : 'baru');
                    $label  = $status === 'selesai' ? 'Selesai' : ($status === 'berjalan' ? 'Sedang berjalan' : 'Belum dimulai');
                    $cta    = $status === 'selesai' ? 'Tinjau materi' : ($status === 'berjalan' ? 'Lanjutkan' : 'Mulai belajar');
                    $accent = $status === 'selesai' ? '#00932E' : ($status === 'berjalan' ? '#fbbf24' : '#38bdf8');
                @endphp
                <article class="kc-card group relative flex flex-col bg-white rounded-[1.75rem] border border-orange-100 overflow-hidden"
                         data-course data-status="{{ $status }}" data-title="{{ \Illuminate\Support\Str::lower($course->title . ' ' . strip_tags((string) $course->description)) }}">

                    <!-- Sampul: gambar, atau ikon modul bila tidak ada gambar -->
                    <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-[#FF8C00] via-[#FF5A00] to-[#E63600]">
                        <div class="kc-cover-icon absolute inset-0 flex items-center justify-center" aria-hidden="true">
                            <span class="w-20 h-20 rounded-3xl bg-white/25 border border-white/40 flex items-center justify-center backdrop-blur-sm">
                                <span class="material-symbols-outlined text-white text-5xl" style="font-variation-settings:'FILL' 1;">auto_stories</span>
                            </span>
                        </div>

                        @if($course->image)
                        <img src="{{ asset('storage/'.$course->image) }}" alt="" loading="lazy"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                             onerror="this.remove()">
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-orange-700/30 via-transparent to-transparent pointer-events-none"></div>

                        <span class="kc-badge absolute top-3.5 left-3.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ $status === 'selesai' ? 'bg-[#00e676]' : ($status === 'berjalan' ? 'bg-yellow-200' : 'bg-white/80') }}"></span>
                            {{ $label }}
                        </span>
                        <span class="kc-badge absolute top-3.5 right-3.5 tabular-nums">{{ $p }}%</span>
                    </div>

                    <!-- Isi -->
                    <div class="flex-1 flex flex-col p-5 sm:p-6">
                        <h3 class="text-base sm:text-[1.0625rem] font-extrabold text-slate-950 leading-snug tracking-tight line-clamp-2 group-hover:text-orange-600 transition-colors">
                            {{ $course->title }}
                        </h3>
                        <p class="mt-1.5 text-sm text-slate-800 leading-relaxed line-clamp-2">
                            {{ $course->description }}
                        </p>

                        <div class="mt-auto pt-5">
                            <div class="flex items-center justify-between text-xs font-semibold mb-2">
                                <span class="text-slate-800">Progres belajar</span>
                                <span class="tabular-nums" style="color: {{ $accent }}">{{ $p }}%</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-orange-100 overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $p }}" aria-label="Progres {{ $course->title }}">
                                <div class="kc-bar h-full rounded-full" style="width: {{ $p }}%; background: {{ $accent }}"></div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-orange-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-8 h-8 rounded-lg bg-orange-200 text-orange-700 flex items-center justify-center text-[11px] font-black shrink-0">AX</span>
                                <span class="min-w-0">
                                    <span class="block text-[11px] font-medium text-slate-600 leading-none">Mitra kelas</span>
                                    <span class="block text-xs font-bold text-slate-900 truncate mt-1">Axioo Education</span>
                                </span>
                            </div>

                            <a href="{{ route('siswa.modules', $course->id) }}" class="kc-btn after:absolute after:inset-0 after:content-['']">
                                {{ $cta }}
                                <span class="material-symbols-outlined text-base transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full kc-panel p-10 sm:p-14 text-center">
                    <div class="w-12 h-12 bg-orange-200 text-orange-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">auto_stories</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-950">Belum ada kelas aktif</h3>
                    <p class="text-sm text-slate-800 max-w-xs mx-auto mt-1 leading-relaxed">
                        Anda belum terdaftar di kelas pembelajaran industri mana pun. Hubungi wali kelas atau admin kurikulum sekolah Anda.
                    </p>
                </div>
                @endforelse
            </div>

            <div id="kcNoResult" class="hidden mt-4 kc-panel text-center p-10 sm:p-14">
                <div class="w-14 h-14 bg-orange-100 text-orange-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl">search_off</span>
                </div>
                <h3 class="text-base font-bold text-slate-950">Kursus tidak ditemukan</h3>
                <p class="text-sm text-slate-800 mt-1">Coba kata kunci lain atau ganti filter.</p>
                <button type="button" id="kcReset" class="mt-5 inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-600 text-xs font-bold transition-colors">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    Atur ulang
                </button>
            </div>

            <p id="kcCount" class="mt-4 text-center text-xs font-medium text-slate-600" aria-live="polite"></p>
        </section>
    </div>
</div>

<style>
    /* Latar halaman terang (menggantikan latar gelap) */
    .kc-page { color-scheme: light; }

    .kc-panel { border-radius: 1.75rem; background: #fff; border: 1px solid #ffedd5; box-shadow: 0 10px 30px -12px rgba(255, 90, 0, .22); }

    /* Kartu samping */
    .kc-side { display: flex; align-items: center; gap: 1rem; padding: 1.1rem 1.25rem; min-width: 0; }
    @media (min-width: 1024px) { .kc-side { padding: 1.5rem 1.75rem; } }
    @media (max-width: 479px) { .kc-side { flex-direction: column; align-items: flex-start; } }
    .kc-stat-icon { width: 3rem; height: 3rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kc-side-label { font-size: 12px; font-weight: 500; color: #1e293b; }
    .kc-side-value { margin-top: .1rem; font-size: 1.625rem; font-weight: 800; line-height: 1.1; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .kc-side-value span { margin-left: .35rem; font-size: .75rem; font-weight: 500; color: #475569; letter-spacing: 0; }

    /* Kotak status */
    .kc-tier { padding: .65rem .5rem; border-radius: 1rem; text-align: center; background: #fff7ed; border: 2px solid #ffedd5; transition: all .2s; }
    .kc-tier p { display: flex; align-items: center; justify-content: center; gap: .3rem; font-size: 11px; font-weight: 700; color: #1e293b; white-space: nowrap; }
    .kc-tier i { width: 7px; height: 7px; border-radius: 50%; background: var(--t); flex-shrink: 0; }
    .kc-tier span { display: block; margin-top: .15rem; font-size: 10px; color: #94a3b8; }
    .kc-tier.is-active { background: #fff; border-color: var(--t); box-shadow: 0 6px 16px -8px var(--t); }
    @media (max-width: 400px) { .kc-tier p { font-size: 10px; } }

    /* Cincin */
    .kc-ring {
        --p: 0; --c: #FF5A00;
        position: relative; width: 9.5rem; height: 9.5rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: conic-gradient(var(--c) calc(var(--p) * 1%), #ffedd5 0);
    }
    .kc-ring::before { content: ''; position: absolute; inset: 14px; border-radius: 50%; background: #fff; }
    .kc-ring > span { position: relative; color: #020617; font-variant-numeric: tabular-nums; }
    .kc-ring b { font-size: 2.25rem; font-weight: 800; letter-spacing: -.03em; }
    .kc-ring small { margin-top: .35rem; font-size: 11px; font-weight: 500; color: #1e293b; }

    /* Kartu kursus */
    .kc-card { box-shadow: 0 4px 14px -6px rgba(255, 90, 0, .25); transition: transform .3s cubic-bezier(.16, 1, .3, 1), box-shadow .3s, border-color .3s; }
    .kc-card:hover { transform: translateY(-4px); border-color: rgba(255, 90, 0, .5); box-shadow: 0 18px 40px -18px rgba(232, 67, 0, .45); }
    .kc-card:focus-within { border-color: rgba(255, 90, 0, .8); }
    .kc-card.is-hidden { display: none; }

    .kc-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .3rem .7rem; border-radius: 999px;
        background: rgba(255, 255, 255, .28); border: 1px solid rgba(255, 255, 255, .4);
        color: #fff; font-size: 11px; font-weight: 700;
        -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
    }

    .kc-bar { animation: kcGrow .9s cubic-bezier(.16, 1, .3, 1) both; }
    @keyframes kcGrow { from { width: 0; } }

    .kc-btn {
        display: inline-flex; align-items: center; gap: .4rem; height: 2.5rem; padding: 0 1rem; border-radius: 1rem;
        background: #ffedd5; color: #c2410c; font-size: 12px; font-weight: 700; white-space: nowrap;
        transition: background-color .15s, color .15s, transform .12s;
    }
    .kc-btn:hover { background: linear-gradient(135deg, #FF8C00, #E84300); color: #fff; }
    .kc-btn:active { transform: scale(.96); }
    .kc-btn:focus-visible { outline: 2px solid #E84300; outline-offset: 2px; }

    /* Filter */
    .kc-chip {
        display: inline-flex; align-items: center; gap: .5rem; flex-shrink: 0;
        height: 2.5rem; padding: 0 .9rem; border-radius: 999px;
        background: #fff; border: 1px solid #fed7aa;
        font-size: 12px; font-weight: 700; color: #9a3412; white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s, transform .12s;
    }
    .kc-chip:hover { border-color: #fb923c; background: #fff7ed; }
    .kc-chip:active { transform: scale(.96); }
    .kc-chip:focus-visible { outline: 2px solid #E84300; outline-offset: 2px; }
    .kc-chip[aria-pressed="true"] { background: linear-gradient(135deg, #FF8C00, #E84300); border-color: transparent; color: #fff; box-shadow: 0 8px 18px -8px rgba(232, 67, 0, .65); }
    .kc-chip-n { min-width: 1.25rem; padding: 0 .35rem; border-radius: 999px; background: rgba(251, 146, 60, .2); font-size: 11px; line-height: 1.25rem; text-align: center; font-variant-numeric: tabular-nums; }
    .kc-chip[aria-pressed="true"] .kc-chip-n { background: rgba(255, 255, 255, .3); }
    #kcFilters { scrollbar-width: none; }
    #kcFilters::-webkit-scrollbar { display: none; }

    @media (prefers-reduced-motion: reduce) {
        .kc-card, .kc-card:hover { transition: none; transform: none; }
        .kc-bar { animation: none; }
        .kc-tier, .kc-btn, .kc-chip { transition: none; }
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
        count.textContent = shown === cards.length ? '' : 'Menampilkan ' + shown + ' dari ' + cards.length + ' kursus';
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
    if (search) search.addEventListener('input', function () { state.q = search.value; apply(); });

    var reset = document.getElementById('kcReset');
    if (reset) reset.addEventListener('click', function () {
        if (search) search.value = '';
        state.q = '';
        setFilter('semua');
    });
})();
</script>
@endsection