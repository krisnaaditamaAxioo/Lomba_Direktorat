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
    $hasGrade  = $totalGraded > 0;

    // Tingkat capaian berdasarkan rata-rata nilai
    if (!$hasGrade)      { $tier = 'none'; $tierLabel = 'Belum ada nilai';        $tierSub = 'Selesaikan tugas untuk melihat capaian Anda'; $tierColor = '#94a3b8'; $tierIcon = 'hourglass_empty'; }
    elseif ($avg >= 80)  { $tier = 'high'; $tierLabel = 'Capaian sangat baik';    $tierSub = 'Pertahankan prestasi belajar Anda';          $tierColor = '#10b981'; $tierIcon = 'check_circle'; }
    elseif ($avg >= 60)  { $tier = 'mid';  $tierLabel = 'Capaian cukup baik';     $tierSub = 'Masih ada ruang untuk ditingkatkan';         $tierColor = '#f59e0b'; $tierIcon = 'trending_up'; }
    else                 { $tier = 'low';  $tierLabel = 'Perlu ditingkatkan';     $tierSub = 'Pelajari kembali materi dan catatan instruktur'; $tierColor = '#f43f5e'; $tierIcon = 'priority_high'; }

    $typeMap = [
        'quiz'       => ['label' => 'Kuis',   'icon' => 'quiz',       'class' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300'],
        'coding'     => ['label' => 'Coding', 'icon' => 'terminal',   'class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'],
        'assignment' => ['label' => 'Tugas',  'icon' => 'assignment', 'class' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300'],
    ];
@endphp

{{-- Layout mengikuti halaman Riwayat Poin: banner pendek, kartu ringkasan menumpuk di atasnya. --}}
<div class="w-full max-w-none pb-28 sm:pb-16">

    <!-- ===== BANNER ===== -->
    <header class="relative left-1/2 right-1/2 -mx-[50vw] w-screen -mt-4 sm:-mt-6 lg:left-auto lg:right-auto lg:mx-0 lg:mt-0 lg:w-auto
                   overflow-hidden text-white bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E]
                   rounded-b-[2rem] sm:rounded-b-[2.5rem] lg:rounded-[2rem] shadow-xl shadow-orange-600/20
                   pb-24 sm:pb-28 lg:pb-16">
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-24 -left-16 w-44 h-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 right-1/3 w-36 h-36 rounded-full bg-black/10"></div>

        <div class="relative px-5 sm:px-6 lg:px-6 pt-[max(1.25rem,env(safe-area-inset-top))] lg:pt-6">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">Rekap Nilai</h1>
                    <p class="text-sm text-orange-50/90 mt-0.5">Hasil tugas, kuis, dan catatan instruktur</p>
                </div>

                <span class="inline-flex shrink-0 items-center gap-2 h-10 px-4 rounded-full bg-white text-slate-600 text-xs font-extrabold shadow-sm">
                    <span class="w-2 h-2 rounded-full" style="background: {{ $tierColor }}"></span>
                    {{ $hasGrade ? 'Rata-rata ' . $avgScore : 'Belum dinilai' }}
                </span>
            </div>
        </div>
    </header>

    <div class="px-4 sm:px-6 lg:px-0 -mt-16 sm:-mt-20 lg:-mt-10 relative space-y-6 sm:space-y-8">

        <!-- ===== RINGKASAN ===== -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 lg:items-stretch" aria-label="Ringkasan nilai">

            <!-- Kartu utama -->
            <div class="lg:col-span-2 rn-panel p-5 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-300">Rata-rata nilai tugas &amp; kuis</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-300 text-[11px] font-bold">
                        {{ $totalGraded }} modul dinilai
                    </span>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                    <div class="mx-auto sm:mx-0">
                        <span class="rn-ring rn-ring-xl" style="--p: {{ $avg }}; --c: {{ $tierColor }}" role="img" aria-label="Rata-rata nilai {{ $avgScore }} dari 100">
                            <span class="flex flex-col items-center leading-none">
                                <b>{{ $avgScore }}</b>
                                <small>dari 100</small>
                            </span>
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2" style="color: {{ $tierColor }}">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">{{ $tierIcon }}</span>
                            <h2 class="text-lg font-extrabold tracking-tight">{{ $tierLabel }}</h2>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-600 dark:text-slate-300">Capaian belajar</span>
                                <span class="tabular-nums" style="color: {{ $tierColor }}">{{ $hasGrade ? round($avg) . '%' : '-' }}</span>
                            </div>
                            <div class="mt-1.5 h-2 rounded-full bg-slate-100 dark:bg-gray-600 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700" style="width: {{ $hasGrade ? $avg : 0 }}%; background: {{ $tierColor }}"></div>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-2.5">
                            <div class="rn-tier {{ $tier === 'low' ? 'is-active' : '' }}" style="--t:#f43f5e">
                                <p><i></i>Ditingkatkan</p><span>0–59</span>
                            </div>
                            <div class="rn-tier {{ $tier === 'mid' ? 'is-active' : '' }}" style="--t:#f59e0b">
                                <p><i></i>Cukup</p><span>60–79</span>
                            </div>
                            <div class="rn-tier {{ $tier === 'high' ? 'is-active' : '' }}" style="--t:#10b981">
                                <p><i></i>Sangat baik</p><span>80–100</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-4">
                    <div class="pt-3 border-t border-slate-100 dark:border-gray-600 text-xs text-slate-500 dark:text-slate-400">
                        Dari seluruh modul yang sudah dinilai instruktur
                    </div>
                </div>
            </div>

            <!-- Kartu samping -->
            <div class="grid grid-cols-2 lg:grid-cols-1 lg:grid-rows-2 gap-4 sm:gap-5">
                <div class="rn-panel rn-side">
                    <span class="rn-stat-icon bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">assignment_turned_in</span>
                    </span>
                    <div class="min-w-0">
                        <p class="rn-side-label">Tugas dikirim</p>
                        <p class="rn-side-value text-orange-600 dark:text-orange-400">{{ $sentCount }}<span>tugas</span></p>
                    </div>
                </div>

                <div class="rn-panel rn-side">
                    <span class="rn-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">verified</span>
                    </span>
                    <div class="min-w-0">
                        <p class="rn-side-label">Sudah dinilai</p>
                        <p class="rn-side-value text-emerald-600 dark:text-emerald-400">{{ $totalGraded }}<span>modul</span></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== RIWAYAT ===== -->
        <section aria-label="Riwayat pengerjaan">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-600 dark:text-slate-200">Riwayat penilaian</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Log pengerjaan dan nilai Anda.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 lg:w-auto">
                    @if($subs->count() > 0)
                    <div class="relative sm:w-72">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
                        <input id="rnSearch" type="search" autocomplete="off" placeholder="Cari modul atau kursus"
                               class="w-full h-11 pl-12 pr-4 rounded-2xl bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 shadow-sm text-base sm:text-sm font-medium text-slate-600 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 transition-all"/>
                    </div>
                    @endif
                    <a href="{{ route('siswa.grades.print') }}" target="_blank" rel="noopener" aria-label="Cetak rekap" class="rn-btn justify-center">
                        <span class="material-symbols-outlined text-base">print</span>
                        Cetak rekap
                    </a>
                </div>
            </div>

            @if($subs->count() > 0)
            <div id="rnFilters" class="mt-4 flex gap-2 overflow-x-auto -mx-4 px-4 sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0 pb-1 snap-x" role="group" aria-label="Filter status">
                <button type="button" class="rn-chip snap-start" data-filter="semua" aria-pressed="true">Semua <span class="rn-chip-n">{{ $subs->count() }}</span></button>
                <button type="button" class="rn-chip snap-start" data-filter="graded" aria-pressed="false">Sudah dinilai <span class="rn-chip-n">{{ $nGraded }}</span></button>
                <button type="button" class="rn-chip snap-start" data-filter="submitted" aria-pressed="false">Menunggu <span class="rn-chip-n">{{ $nWaiting }}</span></button>
                @if($nDraft > 0)
                <button type="button" class="rn-chip snap-start" data-filter="draft" aria-pressed="false">Draf <span class="rn-chip-n">{{ $nDraft }}</span></button>
                @endif
            </div>
            @endif

            <div id="rnList" class="mt-4 space-y-3 sm:space-y-4">
                @forelse($submissions as $sub)
                @php
                    $mod     = $sub->module;
                    $course  = $mod->course;
                    $type    = $typeMap[$mod->type] ?? ['label' => ucfirst((string) $mod->type), 'icon' => 'description', 'class' => 'bg-slate-100 text-slate-600 dark:bg-gray-600 dark:text-slate-300'];
                    $status  = in_array($sub->status, ['graded', 'submitted'], true) ? $sub->status : 'draft';
                    $max     = (float) ($sub->max_score ?? 100);
                    $score   = $sub->score;
                    $ratio   = ($score !== null && $max > 0) ? max(0, min(100, round($score / $max * 100))) : 0;
                    $tone    = $ratio >= 80 ? '#10b981' : ($ratio >= 60 ? '#f59e0b' : '#f43f5e');
                    $accent  = $status === 'graded' ? $tone : ($status === 'submitted' ? '#f59e0b' : '#94a3b8');
                @endphp
                <article class="rn-card" data-item data-status="{{ $status }}"
                         style="--accent: {{ $accent }}; --i: {{ min($loop->index, 8) }}"
                         data-title="{{ \Illuminate\Support\Str::lower(($mod->title ?? '') . ' ' . ($course->title ?? '')) }}">

                    <div class="flex items-start gap-3.5 sm:gap-5">
                        <div class="shrink-0">
                            @if($status === 'graded')
                                <span class="rn-ring rn-ring-lg" style="--p: {{ $ratio }}; --c: {{ $tone }}" role="img" aria-label="Nilai {{ $score }} dari {{ $sub->max_score ?? 100 }}">
                                    <span><b>{{ $score }}</b></span>
                                </span>
                                <p class="text-[11px] font-semibold text-slate-400 text-center mt-1.5 tabular-nums">/ {{ $sub->max_score ?? 100 }}</p>
                            @elseif($status === 'submitted')
                                <span class="rn-icon-ring text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400">
                                    <span class="material-symbols-outlined text-3xl">hourglass_top</span>
                                </span>
                            @else
                                <span class="rn-icon-ring text-slate-500 bg-slate-100 dark:bg-gray-600 dark:text-slate-400">
                                    <span class="material-symbols-outlined text-3xl">edit_note</span>
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="rn-pill bg-slate-100 text-slate-600 dark:bg-gray-600 dark:text-slate-300"><span class="truncate">{{ $course->title ?? 'Kursus' }}</span></span>
                                <span class="rn-pill gap-1 {{ $type['class'] }}">
                                    <span class="material-symbols-outlined" style="font-size:13px">{{ $type['icon'] }}</span>{{ $type['label'] }}
                                </span>
                            </div>

                            <h3 class="mt-2 text-base sm:text-lg font-extrabold text-slate-600 dark:text-slate-200 leading-snug tracking-tight break-words">{{ $mod->title }}</h3>

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

                            @if($status === 'graded')
                            <div class="mt-3 flex items-center gap-2.5" aria-hidden="true">
                                <div class="h-1.5 flex-1 rounded-full bg-slate-100 dark:bg-gray-600 overflow-hidden">
                                    <div class="h-full rounded-full" style="width: {{ $ratio }}%; background: {{ $tone }}"></div>
                                </div>
                                <span class="text-[11px] font-bold tabular-nums text-slate-500 dark:text-slate-400">{{ $ratio }}%</span>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('siswa.module.soal', $mod->id) }}" class="rn-btn hidden sm:inline-flex shrink-0">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            Lihat jawaban
                        </a>
                    </div>

                    @if($sub->feedback)
                    <div class="rn-feedback mt-4">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400 text-xl shrink-0" style="font-variation-settings:'FILL' 1;">format_quote</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-200">Catatan instruktur</p>
                            <p class="rn-fb-text text-sm text-slate-600 dark:text-slate-300 leading-relaxed mt-0.5">{{ $sub->feedback }}</p>
                            <button type="button" class="rn-fb-toggle hidden mt-1 text-xs font-bold text-orange-700 dark:text-orange-400 hover:underline underline-offset-4">Selengkapnya</button>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('siswa.module.soal', $mod->id) }}" class="rn-btn sm:hidden w-full mt-4 justify-center">
                        <span class="material-symbols-outlined text-base">visibility</span>
                        Lihat jawaban
                    </a>
                </article>
                @empty
                <div class="rn-panel p-10 sm:p-14 text-center">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">check_circle</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-600 dark:text-slate-200">Belum ada nilai tugas</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs mx-auto mt-1 leading-relaxed">Selesaikan modul dan kerjakan evaluasi atau tugas untuk mendapatkan nilai dari instruktur.</p>
                    <a href="{{ route('siswa.modules') }}" class="mt-6 inline-flex items-center gap-2 h-11 px-5 bg-gradient-to-br from-[#F6863B] to-[#EB4325] text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-500/25 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-lg">auto_stories</span>
                        Lihat daftar modul
                    </a>
                </div>
                @endforelse
            </div>

            <div id="rnNoResult" class="hidden mt-4 py-14 rn-panel text-center p-8">
                <div class="w-14 h-14 bg-slate-100 dark:bg-gray-600 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl">search_off</span>
                </div>
                <h3 class="text-base font-bold text-slate-600 dark:text-slate-200">Tidak ada hasil</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Coba kata kunci lain atau ganti filter.</p>
                <button type="button" id="rnReset" class="mt-5 inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-slate-100 dark:bg-gray-600 hover:bg-slate-200 dark:hover:bg-gray-500 text-slate-500 dark:text-slate-200 text-xs font-bold transition-colors">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    Atur ulang
                </button>
            </div>

            <p id="rnCount" class="mt-4 text-center text-xs font-medium text-slate-400" aria-live="polite"></p>
        </section>
    </div>
</div>

<style>
    /* Panel dasar */
    .rn-panel {
        border-radius: 1.75rem; background: #fff;
        border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 10px 30px -12px rgba(15, 23, 42, .08);
    }
    .dark .rn-panel { background: #374151; border-color: #4b5563; box-shadow: none; }

    /* Kartu samping: ikon kiri, label + nilai kanan */
    .rn-side { display: flex; align-items: center; gap: 1rem; padding: 1.1rem 1.25rem; min-width: 0; }
    @media (min-width: 1024px) { .rn-side { padding: 1.5rem 1.75rem; } }
    @media (max-width: 479px) { .rn-side { flex-direction: column; align-items: flex-start; } }
    .rn-stat-icon { width: 3rem; height: 3rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rn-side-label { font-size: 12px; font-weight: 500; color: #64748b; }
    .rn-side-value { margin-top: .1rem; font-size: 1.625rem; font-weight: 800; line-height: 1.1; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .rn-side-value span { margin-left: .35rem; font-size: .75rem; font-weight: 500; color: #94a3b8; letter-spacing: 0; }

    /* Tingkat capaian */
    .rn-tier { padding: .65rem .5rem; border-radius: 1rem; text-align: center; background: #f8fafc; border: 2px solid #f1f5f9; transition: all .2s; }
    .dark .rn-tier { background: #4b5563; border-color: #4b5563; }
    .rn-tier p { display: flex; align-items: center; justify-content: center; gap: .3rem; font-size: 11px; font-weight: 700; color: #64748b; white-space: nowrap; }
    .dark .rn-tier p { color: #cbd5e1; }
    .rn-tier i { width: 6px; height: 6px; border-radius: 50%; background: var(--t); flex-shrink: 0; }
    .rn-tier span { display: block; margin-top: .15rem; font-size: 10px; color: #94a3b8; }
    .rn-tier.is-active { background: #fff; border-color: var(--t); }
    .dark .rn-tier.is-active { background: #374151; }
    @media (max-width: 400px) { .rn-tier p { font-size: 10px; } }

    /* Cincin nilai */
    .rn-ring {
        --p: 0; --c: #EB4325;
        position: relative; width: 3.5rem; height: 3.5rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: conic-gradient(var(--c) calc(var(--p) * 1%), #f1f5f9 0);
    }
    .dark .rn-ring { background: conic-gradient(var(--c) calc(var(--p) * 1%), #4b5563 0); }
    .rn-ring::before { content: ''; position: absolute; inset: 5px; border-radius: 50%; background: #fff; }
    .dark .rn-ring::before { background: #374151; }
    .rn-ring > span { position: relative; color: #64748b; font-variant-numeric: tabular-nums; }
    .dark .rn-ring > span { color: #fff; }
    .rn-ring b { font-size: 1.125rem; font-weight: 800; }
    .rn-ring-lg { width: 4rem; height: 4rem; }
    .rn-ring-lg::before { inset: 6px; }
    @media (min-width: 640px) { .rn-ring-lg { width: 4.5rem; height: 4.5rem; } }
    .rn-ring-xl { width: 9.5rem; height: 9.5rem; }
    .rn-ring-xl::before { inset: 14px; }
    .rn-ring-xl b { font-size: 2.25rem; letter-spacing: -.03em; }
    .rn-ring-xl small { margin-top: .35rem; font-size: 11px; font-weight: 500; color: #64748b; }

    .rn-icon-ring { width: 4rem; height: 4rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    @media (min-width: 640px) { .rn-icon-ring { width: 4.5rem; height: 4.5rem; } }

    /* Kartu riwayat */
    .rn-card {
        position: relative; overflow: hidden;
        padding: 1.1rem 1.1rem 1.1rem 1.35rem; border-radius: 1.75rem;
        background: #fff; border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        transition: box-shadow .25s, border-color .25s, transform .25s cubic-bezier(.16, 1, .3, 1);
        animation: rn-in .45s cubic-bezier(.16, 1, .3, 1) both;
        animation-delay: calc(var(--i, 0) * 45ms);
    }
    .rn-card::before { content: ''; position: absolute; left: 0; top: 1.1rem; bottom: 1.1rem; width: 4px; border-radius: 0 4px 4px 0; background: var(--accent, #94a3b8); }
    @media (min-width: 640px) { .rn-card { padding: 1.4rem 1.5rem 1.4rem 1.75rem; } }
    .dark .rn-card { background: #374151; border-color: #4b5563; }
    .rn-card:hover { border-color: rgba(235, 67, 37, .3); box-shadow: 0 16px 34px -18px rgba(138, 30, 30, .35); }
    @media (hover: hover) { .rn-card:hover { transform: translateY(-2px); } }
    .rn-card.is-hidden { display: none; }
    @keyframes rn-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    .rn-pill { display: inline-flex; align-items: center; padding: .2rem .65rem; border-radius: 999px; font-size: 11px; font-weight: 700; line-height: 1.3; max-width: 100%; min-width: 0; }

    .rn-feedback { display: flex; align-items: flex-start; gap: .7rem; padding: .9rem 1rem; border-radius: 1.25rem; background: #fff7ed; border: 1px solid #ffedd5; }
    .dark .rn-feedback { background: rgba(249, 115, 22, .08); border-color: rgba(249, 115, 22, .18); }
    .rn-fb-text { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; white-space: pre-line; word-break: break-word; }
    .rn-fb-text.is-open { display: block; -webkit-line-clamp: unset; }

    .rn-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        height: 2.75rem; padding: 0 1.1rem; border-radius: 1rem;
        background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 700; white-space: nowrap;
        transition: background-color .15s, color .15s, transform .12s;
    }
    @media (min-width: 640px) { .rn-btn { height: 2.5rem; } }
    .dark .rn-btn { background: #4b5563; color: #e2e8f0; }
    .rn-btn:hover { background: linear-gradient(135deg, #F6863B, #EB4325); color: #fff; }
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
    .dark .rn-chip { background: #374151; border-color: #4b5563; color: #cbd5e1; }
    .rn-chip:hover { border-color: #fdba74; }
    .rn-chip:active { transform: scale(.96); }
    .rn-chip:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }
    .rn-chip[aria-pressed="true"] { background: linear-gradient(135deg, #F6863B, #EB4325); border-color: transparent; color: #fff; box-shadow: 0 8px 18px -8px rgba(235, 67, 37, .6); }
    .rn-chip-n { min-width: 1.25rem; padding: 0 .35rem; border-radius: 999px; background: rgba(100, 116, 139, .15); font-size: 11px; line-height: 1.25rem; text-align: center; font-variant-numeric: tabular-nums; }
    .rn-chip[aria-pressed="true"] .rn-chip-n { background: rgba(255, 255, 255, .25); }
    #rnFilters { scrollbar-width: none; }
    #rnFilters::-webkit-scrollbar { display: none; }

    @media (prefers-reduced-motion: reduce) {
        .rn-card, .rn-btn, .rn-chip, .rn-tier { transition: none; animation: none; }
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