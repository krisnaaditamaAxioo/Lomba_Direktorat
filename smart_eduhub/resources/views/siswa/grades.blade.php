@extends('layouts.siswa')

@section('title', 'Rekap Nilai Tugas Saya')

@section('content')
@php
    $subs      = collect($submissions);
    $sentCount = $subs->whereIn('status', ['submitted', 'graded'])->count();
    $nGraded   = $subs->where('status', 'graded')->count();
    $nWaiting  = $subs->where('status', 'submitted')->count();
    $nDraft    = $subs->count() - $nGraded - $nWaiting;
    $avg       = max(0, min(100, (float) $avgScore));

    $typeMap = [
        'quiz'       => ['label' => 'Kuis',   'class' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300'],
        'coding'     => ['label' => 'Coding', 'class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'],
        'assignment' => ['label' => 'Tugas',  'class' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300'],
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-6 pb-20 px-2 sm:px-0">

    <!-- Header (selaras dengan halaman Keuangan & Kursus) -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 pb-8 px-5 text-white overflow-hidden shadow-xl shadow-brand-950/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-6 lg:pb-7">
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 left-12 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/10 shrink-0">
                    <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1;">grade</span>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-white/75 block leading-none">Portal akademik siswa</span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight leading-tight mt-1.5">Rekap nilai tugas &amp; kuis</h1>
                    <p class="text-sm text-white/80 max-w-xl leading-relaxed mt-1.5">
                        Hasil nilai, skor kuis pilihan ganda, dan catatan instruktur untuk modul yang sudah Anda kerjakan.
                    </p>
                </div>
            </div>

            <a href="{{ route('siswa.grades.print') }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2 h-11 px-5 bg-white text-[#8A1E1E] hover:bg-orange-50 rounded-xl text-sm font-bold transition-colors active:scale-95 shrink-0">
                <span class="material-symbols-outlined text-lg">print</span>
                Cetak rekap
            </a>
        </div>
    </div>

    <!-- Ringkasan -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4" aria-label="Ringkasan nilai">
        <div class="rn-stat">
            <span class="rn-ring" style="--p: {{ $avg }}; --c: #EB4325" role="img" aria-label="Rata-rata nilai {{ $avgScore }}">
                <span>{{ $avgScore }}</span>
            </span>
            <div class="min-w-0">
                <p class="rn-stat-label">Rata-rata nilai</p>
                <p class="rn-stat-sub">dari {{ $totalGraded }} modul dinilai</p>
            </div>
        </div>

        <div class="rn-stat">
            <span class="rn-stat-icon bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
            </span>
            <div class="min-w-0">
                <p class="rn-stat-value">{{ $sentCount }}</p>
                <p class="rn-stat-label">Tugas dikirim</p>
            </div>
        </div>

        <div class="rn-stat">
            <span class="rn-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">verified</span>
            </span>
            <div class="min-w-0">
                <p class="rn-stat-value">{{ $totalGraded }}<span class="rn-stat-unit">modul</span></p>
                <p class="rn-stat-label">Sudah dinilai</p>
            </div>
        </div>
    </section>

    @if($subs->count() > 0)
    <!-- Cari & filter -->
    <section class="flex flex-col lg:flex-row lg:items-center gap-3" aria-label="Cari dan saring riwayat">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
            <input id="rnSearch" type="search" autocomplete="off" placeholder="Cari modul atau kursus"
                   class="w-full h-12 pl-12 pr-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-base sm:text-sm font-medium text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all"/>
        </div>

        <div id="rnFilters" class="flex gap-2 overflow-x-auto -mx-2 px-2 lg:mx-0 lg:px-0 pb-1 lg:pb-0" role="group" aria-label="Filter status">
            <button type="button" class="rn-chip" data-filter="semua" aria-pressed="true">Semua <span class="rn-chip-n">{{ $subs->count() }}</span></button>
            <button type="button" class="rn-chip" data-filter="graded" aria-pressed="false">Sudah dinilai <span class="rn-chip-n">{{ $nGraded }}</span></button>
            <button type="button" class="rn-chip" data-filter="submitted" aria-pressed="false">Menunggu <span class="rn-chip-n">{{ $nWaiting }}</span></button>
            @if($nDraft > 0)
            <button type="button" class="rn-chip" data-filter="draft" aria-pressed="false">Draf <span class="rn-chip-n">{{ $nDraft }}</span></button>
            @endif
        </div>
    </section>
    @endif

    <!-- Daftar pengerjaan -->
    <section id="rnList" class="space-y-3 sm:space-y-4" aria-label="Riwayat pengerjaan">
        @forelse($submissions as $sub)
        @php
            $mod     = $sub->module;
            $course  = $mod->course;
            $type    = $typeMap[$mod->type] ?? ['label' => ucfirst((string) $mod->type), 'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'];
            $status  = in_array($sub->status, ['graded', 'submitted'], true) ? $sub->status : 'draft';
            $max     = (float) ($sub->max_score ?? 100);
            $score   = $sub->score;
            $ratio   = ($score !== null && $max > 0) ? max(0, min(100, round($score / $max * 100))) : 0;
            $tone    = $ratio >= 80 ? '#10b981' : ($ratio >= 60 ? '#f59e0b' : '#f43f5e');
        @endphp
        <article class="rn-card" data-item data-status="{{ $status }}"
                 data-title="{{ \Illuminate\Support\Str::lower(($mod->title ?? '') . ' ' . ($course->title ?? '')) }}">

            <div class="flex items-start gap-4">
                <!-- Indikator nilai -->
                <div class="shrink-0">
                    @if($status === 'graded')
                        <span class="rn-ring rn-ring-lg" style="--p: {{ $ratio }}; --c: {{ $tone }}" role="img" aria-label="Nilai {{ $score }} dari {{ $sub->max_score ?? 100 }}">
                            <span>{{ $score }}</span>
                        </span>
                        <p class="text-[11px] font-semibold text-slate-400 text-center mt-1.5 tabular-nums">/ {{ $sub->max_score ?? 100 }}</p>
                    @elseif($status === 'submitted')
                        <span class="rn-icon-ring text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="material-symbols-outlined text-3xl">hourglass_top</span>
                        </span>
                    @else
                        <span class="rn-icon-ring text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400">
                            <span class="material-symbols-outlined text-3xl">edit_note</span>
                        </span>
                    @endif
                </div>

                <!-- Info -->
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="rn-pill bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $course->title ?? 'Kursus' }}</span>
                        <span class="rn-pill {{ $type['class'] }}">{{ $type['label'] }}</span>
                    </div>

                    <h2 class="mt-2 text-base sm:text-[1.0625rem] font-extrabold text-slate-900 dark:text-white leading-snug tracking-tight">{{ $mod->title }}</h2>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            {{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : 'Belum dikirim' }}
                        </span>

                        @if($status === 'graded')
                            <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sudah dinilai
                            </span>
                        @elseif($status === 'submitted')
                            <span class="inline-flex items-center gap-1.5 text-amber-700 dark:text-amber-400 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu penilaian instruktur
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draf sementara
                            </span>
                        @endif

                        @if($status === 'submitted' && $score !== null)
                            <span class="font-semibold tabular-nums">Skor pilihan ganda: {{ $score }}/{{ $sub->max_score }}</span>
                        @endif
                    </div>
                </div>

                <!-- Aksi (desktop) -->
                <a href="{{ route('siswa.module.soal', $mod->id) }}"
                   class="rn-btn hidden sm:inline-flex shrink-0">
                    <span class="material-symbols-outlined text-base">visibility</span>
                    Lihat jawaban
                </a>
            </div>

            @if($sub->feedback)
            <div class="rn-feedback mt-4">
                <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-xl shrink-0" style="font-variation-settings:'FILL' 1;">format_quote</span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Catatan instruktur</p>
                    <p class="rn-fb-text text-sm text-slate-600 dark:text-slate-300 leading-relaxed mt-0.5">{{ $sub->feedback }}</p>
                    <button type="button" class="rn-fb-toggle hidden mt-1 text-xs font-bold text-orange-700 dark:text-orange-400 hover:underline underline-offset-4">Selengkapnya</button>
                </div>
            </div>
            @endif

            <!-- Aksi (mobile) -->
            <a href="{{ route('siswa.module.soal', $mod->id) }}" class="rn-btn sm:hidden w-full mt-4 justify-center">
                <span class="material-symbols-outlined text-base">visibility</span>
                Lihat jawaban
            </a>
        </article>
        @empty
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-12 border border-slate-200/80 dark:border-slate-800 text-center">
            <div class="w-16 h-16 bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">grade</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum ada nilai tugas</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1.5 leading-relaxed">Selesaikan modul dan kerjakan evaluasi atau tugas untuk mendapatkan nilai dari instruktur.</p>
            <a href="{{ route('siswa.modules') }}" class="mt-6 inline-flex items-center gap-2 h-11 px-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-[#EB4325] dark:hover:bg-[#EB4325] dark:hover:text-white rounded-xl font-bold text-sm transition-colors active:scale-95">
                <span class="material-symbols-outlined text-lg">auto_stories</span>
                Lihat daftar modul
            </a>
        </div>
        @endforelse
    </section>

    <!-- Hasil pencarian kosong -->
    <div id="rnNoResult" class="hidden py-14 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-[2rem] text-center p-8">
        <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="material-symbols-outlined text-2xl">search_off</span>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tidak ada hasil</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Coba kata kunci lain atau ganti filter.</p>
        <button type="button" id="rnReset" class="mt-5 inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
            <span class="material-symbols-outlined text-base">restart_alt</span>
            Atur ulang
        </button>
    </div>

    <p id="rnCount" class="text-center text-xs font-medium text-slate-400" aria-live="polite"></p>
</div>

<style>
    /* Statistik */
    .rn-stat {
        display: flex; align-items: center; gap: .9rem; min-width: 0;
        padding: 1rem 1.1rem; border-radius: 1.5rem;
        background: #fff; border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .dark .rn-stat { background: #0f172a; border-color: #1e293b; }
    .rn-stat-icon { width: 3rem; height: 3rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rn-stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; letter-spacing: -.02em; color: #0f172a; font-variant-numeric: tabular-nums; }
    .dark .rn-stat-value { color: #fff; }
    .rn-stat-unit { margin-left: .35rem; font-size: .75rem; font-weight: 700; color: #94a3b8; letter-spacing: 0; }
    .rn-stat-label { margin-top: .3rem; font-size: 12px; font-weight: 600; color: #64748b; line-height: 1.25; }
    .rn-stat-value + .rn-stat-label { margin-top: .4rem; }
    .rn-stat > div > .rn-stat-label:first-child { margin-top: 0; font-size: 14px; font-weight: 800; color: #0f172a; }
    .dark .rn-stat > div > .rn-stat-label:first-child { color: #fff; }
    .rn-stat-sub { margin-top: .2rem; font-size: 12px; font-weight: 500; color: #64748b; }

    /* Cincin nilai */
    .rn-ring {
        --p: 0; --c: #EB4325;
        position: relative; width: 3.25rem; height: 3.25rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: conic-gradient(var(--c) calc(var(--p) * 1%), #f1f5f9 0);
    }
    .dark .rn-ring { background: conic-gradient(var(--c) calc(var(--p) * 1%), #1e293b 0); }
    .rn-ring::before { content: ''; position: absolute; inset: 5px; border-radius: 50%; background: #fff; }
    .dark .rn-ring::before { background: #0f172a; }
    .rn-ring span { position: relative; font-size: 13px; font-weight: 800; color: #0f172a; font-variant-numeric: tabular-nums; }
    .dark .rn-ring span { color: #fff; }
    .rn-ring-lg { width: 4.25rem; height: 4.25rem; }
    .rn-ring-lg::before { inset: 6px; }
    .rn-ring-lg span { font-size: 1.125rem; }

    .rn-icon-ring { width: 4.25rem; height: 4.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    /* Kartu riwayat */
    .rn-card {
        padding: 1.1rem; border-radius: 1.75rem;
        background: #fff; border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        transition: box-shadow .25s, border-color .25s, transform .25s cubic-bezier(.16, 1, .3, 1);
    }
    @media (min-width: 640px) { .rn-card { padding: 1.4rem 1.5rem; } }
    .dark .rn-card { background: #0f172a; border-color: #1e293b; }
    .rn-card:hover { border-color: rgba(235, 67, 37, .3); box-shadow: 0 14px 32px -18px rgba(138, 30, 30, .35); }
    .rn-card.is-hidden { display: none; }

    .rn-pill { display: inline-flex; align-items: center; padding: .2rem .65rem; border-radius: 999px; font-size: 11px; font-weight: 700; line-height: 1.3; max-width: 100%; }

    .rn-feedback {
        display: flex; align-items: flex-start; gap: .7rem;
        padding: .9rem 1rem; border-radius: 1.25rem;
        background: #fff7ed; border: 1px solid #ffedd5;
    }
    .dark .rn-feedback { background: rgba(249, 115, 22, .08); border-color: rgba(249, 115, 22, .18); }
    .rn-fb-text { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; white-space: pre-line; }
    .rn-fb-text.is-open { display: block; -webkit-line-clamp: unset; }

    .rn-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        height: 2.5rem; padding: 0 1rem; border-radius: .875rem;
        background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 700; white-space: nowrap;
        transition: background-color .15s, color .15s, transform .12s;
    }
    .dark .rn-btn { background: #1e293b; color: #e2e8f0; }
    .rn-btn:hover { background: #EB4325; color: #fff; }
    .rn-btn:active { transform: scale(.96); }
    .rn-btn:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* Filter */
    .rn-chip {
        display: inline-flex; align-items: center; gap: .5rem; flex-shrink: 0;
        height: 2.5rem; padding: 0 .9rem; border-radius: 999px;
        background: #fff; border: 1px solid #e2e8f0;
        font-size: 12px; font-weight: 700; color: #475569; white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s, transform .12s;
    }
    .dark .rn-chip { background: #0f172a; border-color: #1e293b; color: #cbd5e1; }
    .rn-chip:hover { border-color: #fdba74; }
    .rn-chip:active { transform: scale(.96); }
    .rn-chip:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }
    .rn-chip[aria-pressed="true"] { background: #0f172a; border-color: #0f172a; color: #fff; }
    .dark .rn-chip[aria-pressed="true"] { background: #fff; border-color: #fff; color: #0f172a; }
    .rn-chip-n { min-width: 1.25rem; padding: 0 .35rem; border-radius: 999px; background: rgba(100, 116, 139, .15); font-size: 11px; line-height: 1.25rem; text-align: center; font-variant-numeric: tabular-nums; }
    .rn-chip[aria-pressed="true"] .rn-chip-n { background: rgba(255, 255, 255, .2); }
    .dark .rn-chip[aria-pressed="true"] .rn-chip-n { background: rgba(15, 23, 42, .12); }
    #rnFilters { scrollbar-width: none; }
    #rnFilters::-webkit-scrollbar { display: none; }

    @media (prefers-reduced-motion: reduce) {
        .rn-card, .rn-btn, .rn-chip { transition: none; }
    }
</style>

<script>
(function () {
    var list = document.getElementById('rnList');
    if (!list) return;

    /* ---------- Catatan instruktur: ringkas / selengkapnya ---------- */
    list.querySelectorAll('.rn-fb-text').forEach(function (text) {
        var toggle = text.parentElement.querySelector('.rn-fb-toggle');
        if (!toggle) return;
        // Tampilkan tombol hanya kalau teks benar-benar terpotong
        if (text.scrollHeight > text.clientHeight + 1) toggle.classList.remove('hidden');
        toggle.addEventListener('click', function () {
            var open = text.classList.toggle('is-open');
            toggle.textContent = open ? 'Lebih sedikit' : 'Selengkapnya';
        });
    });

    /* ---------- Cari & filter ---------- */
    var items = Array.prototype.slice.call(list.querySelectorAll('[data-item]'));
    if (!items.length) return;

    var search = document.getElementById('rnSearch');
    var chips = Array.prototype.slice.call(document.querySelectorAll('#rnFilters [data-filter]'));
    var noResult = document.getElementById('rnNoResult');
    var count = document.getElementById('rnCount');
    var state = { q: '', filter: 'semua' };

    function apply() {
        var q = state.q.trim().toLowerCase();
        var shown = 0;

        items.forEach(function (el) {
            var okFilter = state.filter === 'semua' || el.getAttribute('data-status') === state.filter;
            var okQuery = !q || (el.getAttribute('data-title') || '').indexOf(q) !== -1;
            var visible = okFilter && okQuery;
            el.classList.toggle('is-hidden', !visible);
            if (visible) shown++;
        });

        noResult.classList.toggle('hidden', shown !== 0);
        count.textContent = shown === items.length ? '' : 'Menampilkan ' + shown + ' dari ' + items.length + ' pengerjaan';
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

    var reset = document.getElementById('rnReset');
    if (reset) reset.addEventListener('click', function () {
        if (search) search.value = '';
        state.q = '';
        setFilter('semua');
    });
})();
</script>
@endsection