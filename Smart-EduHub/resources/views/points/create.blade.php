@extends('layouts.app')

@section('title', 'Catat Poin Perilaku')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* Tema SEH: latar lavender muda + font Inter */
    body{background-color:#f4f4fa;font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif}

    .scrollbar-hide::-webkit-scrollbar{display:none}
    .scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}

    @keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .animate-slide-up{animation:slideUp .4s ease-out both}
    @media (prefers-reduced-motion:reduce){.animate-slide-up{animation:none}}

    /* Header mobile: ujung atas aman untuk notch / status bar */
    #heroBleed{padding-top:max(1.25rem, env(safe-area-inset-top))}

    /* Input tanggal: tinggi konsisten di iOS */
    input[type="date"].field{min-height:48px;-webkit-appearance:none;appearance:none}
    input[type="number"].no-spin::-webkit-outer-spin-button,
    input[type="number"].no-spin::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
    input[type="number"].no-spin{-moz-appearance:textfield}
</style>
@endsection

@section('content')
@php
    $field = 'field w-full bg-slate-50 border rounded-xl py-3 px-4 text-base sm:text-sm font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-600/10 focus:border-orange-600 outline-none transition-all';
    $ok    = 'border-slate-200';
    $bad   = 'border-red-400 bg-red-50/40';
    $label = 'flex items-center gap-1.5 text-xs font-extrabold text-slate-600 uppercase tracking-wide';

    $todayStr   = now()->toDateString();
    $maxDateStr = now()->addDay()->toDateString();   // toleransi selisih zona waktu
    $oldType    = old('type', 'achievement');
@endphp

<div>

    {{-- ============ HERO HEADER ============
         Mobile  : full-bleed, menempel ke ujung atas & kiri-kanan layar (diatur script di bawah)
         Desktop : kartu membulat biasa --}}
    <header id="heroBleed" class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-red-500 to-red-800 px-5 sm:px-10 pb-16 sm:pb-20 rounded-b-[2rem] sm:rounded-3xl lg:rounded-[2.5rem] shadow-xl shadow-red-500/20">
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
                <a href="{{ route('points') }}" class="inline-flex items-center gap-1.5 bg-white rounded-full pl-3 pr-4 py-2 text-xs font-extrabold text-slate-900 shadow-sm active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-base text-orange-500">arrow_back</span> Kembali
                </a>
            </div>

            <div class="text-center mt-6 sm:mt-9">
                <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-1.5 text-orange-200 text-[11px] font-bold uppercase tracking-[0.18em]">
                    <a href="{{ route('points') }}" class="hover:text-white transition-colors">Penghargaan &amp; Disiplin</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-white">Catat Poin</span>
                </nav>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mt-2">Tambah Catatan Poin</h2>
                <p class="text-orange-100/90 text-sm sm:text-base mt-2 max-w-xl mx-auto">Catat penghargaan atau pelanggaran seorang siswa.</p>
            </div>
        </div>
    </header>

    {{-- ============ FORM (menimpa sedikit bagian bawah header) ============ --}}
    <div class="relative z-10 -mt-8 sm:-mt-10 grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 max-w-5xl mx-auto">

        <form id="pointForm" action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-8 space-y-4 sm:space-y-5" novalidate>
            @csrf

            {{-- Ringkasan error --}}
            @if($errors->any())
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 flex items-start gap-3 animate-slide-up">
                <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                <div class="min-w-0">
                    <p class="text-sm font-extrabold text-red-700">Catatan belum bisa disimpan</p>
                    <ul class="mt-1 text-xs text-red-600 space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- 1. Siswa --}}
            <section class="bg-white rounded-3xl border border-slate-100 shadow-lg shadow-slate-200/60 p-5 sm:p-7 space-y-4 animate-slide-up">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center"><span class="material-symbols-outlined">person</span></span>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Siswa</h3>
                        <p class="text-[11px] text-slate-400">Pilih siswa yang dicatat</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="studentSearch" class="{{ $label }}">
                        <span class="material-symbols-outlined text-sm text-orange-500">search</span> Cari siswa
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                        <input type="search" id="studentSearch" autocomplete="off" placeholder="Ketik nama atau ID siswa..." class="{{ $field }} {{ $ok }} !pl-11">
                    </div>
                    <p id="studentCount" class="text-[11px] text-slate-400" aria-live="polite">{{ count($students) }} siswa</p>
                </div>

                <div class="space-y-2">
                    <label for="student_id" class="{{ $label }}">Pilih Siswa</label>
                    <select name="student_id" id="student_id" class="{{ $field }} {{ $errors->has('student_id') ? $bad : $ok }}" required>
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nama_lengkap }} ({{ $student->id_siswa }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- 2. Jenis & poin --}}
            <section class="bg-white rounded-3xl border border-slate-100 shadow-lg shadow-slate-200/60 p-5 sm:p-7 space-y-5 animate-slide-up" style="animation-delay:60ms">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center"><span class="material-symbols-outlined">workspace_premium</span></span>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Jenis &amp; Poin</h3>
                        <p class="text-[11px] text-slate-400">Penghargaan atau pelanggaran, beserta nilainya</p>
                    </div>
                </div>

                {{-- Jenis poin --}}
                <fieldset class="space-y-2">
                    <legend class="{{ $label }} mb-2">Jenis Poin</legend>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="achievement" class="peer sr-only" {{ $oldType === 'achievement' ? 'checked' : '' }} required>
                            <div class="h-full rounded-2xl border-2 border-slate-200 bg-slate-50 p-3.5 sm:p-4 flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-3 text-center sm:text-left transition-all
                                        hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-md peer-checked:shadow-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                                <span class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">emoji_events</span></span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-extrabold text-slate-900">Penghargaan</span>
                                    <span class="block text-[11px] text-slate-500">Perilaku positif</span>
                                </span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="violation" class="peer sr-only" {{ $oldType === 'violation' ? 'checked' : '' }}>
                            <div class="h-full rounded-2xl border-2 border-slate-200 bg-slate-50 p-3.5 sm:p-4 flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-3 text-center sm:text-left transition-all
                                        hover:border-rose-300 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:shadow-md peer-checked:shadow-rose-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-rose-500/20">
                                <span class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0"><span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">gavel</span></span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-extrabold text-slate-900">Pelanggaran</span>
                                    <span class="block text-[11px] text-slate-500">Perlu pembinaan</span>
                                </span>
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </fieldset>

                {{-- Poin --}}
                <div class="space-y-2">
                    <label for="score" class="{{ $label }}">
                        <span class="material-symbols-outlined text-sm text-orange-500">stars</span> Poin
                    </label>
                    <div class="flex items-stretch gap-2">
                        <button type="button" data-step="-5" aria-label="Kurangi 5 poin" class="step-btn shrink-0 w-12 rounded-xl border border-slate-200 bg-white text-slate-700 font-extrabold text-sm hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all">−5</button>
                        <input type="number" inputmode="numeric" step="1" name="score" id="score" value="{{ old('score', 10) }}"
                               class="{{ $field }} {{ $errors->has('score') ? $bad : $ok }} no-spin !text-center !text-xl !font-extrabold" required>
                        <button type="button" data-step="5" aria-label="Tambah 5 poin" class="step-btn shrink-0 w-12 rounded-xl border border-slate-200 bg-white text-slate-700 font-extrabold text-sm hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all">+5</button>
                    </div>
                    <div class="flex gap-2 overflow-x-auto scrollbar-hide pt-1">
                        @foreach([5, 10, 20, 30, 50] as $preset)
                            <button type="button" data-score="{{ $preset }}" class="score-chip shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all">{{ $preset }} poin</button>
                        @endforeach
                    </div>
                    @error('score') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- 3. Waktu, bukti & alasan --}}
            <section class="bg-white rounded-3xl border border-slate-100 shadow-lg shadow-slate-200/60 p-5 sm:p-7 space-y-5 animate-slide-up" style="animation-delay:120ms">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center"><span class="material-symbols-outlined">edit_note</span></span>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Detail Catatan</h3>
                        <p class="text-[11px] text-slate-400">Tanggal, bukti foto, dan alasan</p>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="space-y-2">
                    <label for="date" class="{{ $label }}">
                        <span class="material-symbols-outlined text-sm text-orange-500">calendar_month</span> Tanggal
                    </label>
                    <input type="date" name="date" id="date" value="{{ old('date', $todayStr) }}"
                           min="2020-01-01" max="{{ $maxDateStr }}" data-server-today="{{ $todayStr }}"
                           class="{{ $field }} {{ $errors->has('date') ? $bad : $ok }}" required>
                    <div class="flex gap-2 pt-1">
                        <button type="button" data-date="today" class="date-chip px-3.5 py-1.5 rounded-full text-xs font-bold border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all">Hari ini</button>
                        <button type="button" data-date="yesterday" class="date-chip px-3.5 py-1.5 rounded-full text-xs font-bold border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all">Kemarin</button>
                    </div>
                    <p id="dateHint" role="status" aria-live="polite" class="hidden text-xs font-semibold text-red-500"></p>
                    @error('date') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Foto bukti --}}
                <div class="space-y-2">
                    <span class="{{ $label }}">
                        <span class="material-symbols-outlined text-sm text-orange-500">photo_camera</span> Foto Bukti (Opsional)
                    </span>

                    <label for="proof_photo" id="dropzone"
                           class="group relative flex flex-col items-center justify-center gap-1.5 rounded-2xl border-2 border-dashed {{ $errors->has('proof_photo') ? 'border-red-400 bg-red-50/40' : 'border-slate-200 bg-slate-50' }} px-4 py-6 text-center cursor-pointer hover:border-orange-400 hover:bg-orange-50/40 transition-all">
                        <input type="file" name="proof_photo" id="proof_photo" accept="image/*" class="sr-only">

                        <div id="dzEmpty" class="flex flex-col items-center gap-1.5">
                            <span class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:scale-105 transition-transform"><span class="material-symbols-outlined">cloud_upload</span></span>
                            <p class="text-sm font-bold text-slate-700">Ketuk untuk pilih foto</p>
                            <p class="text-[11px] text-slate-400">atau seret &amp; lepas gambar di sini</p>
                        </div>

                        <div id="dzFilled" class="hidden w-full flex items-center gap-3 text-left">
                            <img id="dzPreview" alt="Pratinjau foto bukti" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0 bg-white">
                            <div class="min-w-0 flex-1">
                                <p id="dzName" class="text-sm font-bold text-slate-800 truncate"></p>
                                <p id="dzSize" class="text-[11px] text-slate-400"></p>
                            </div>
                            <button type="button" id="dzRemove" class="shrink-0 w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-300 flex items-center justify-center active:scale-95 transition-all" aria-label="Hapus foto">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </label>
                    <p class="text-[11px] text-slate-400 italic">Gambar akan dikompres otomatis untuk menghemat ruang.</p>
                    <p id="dzError" class="hidden text-xs font-semibold text-red-500"></p>
                    @error('proof_photo') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Alasan --}}
                <div class="space-y-2">
                    <label for="reason" class="{{ $label }}">
                        <span class="material-symbols-outlined text-sm text-orange-500">chat</span> Alasan / Keterangan
                    </label>
                    <textarea name="reason" id="reason" rows="4" placeholder="Jelaskan singkat apa yang terjadi..."
                              class="{{ $field }} {{ $errors->has('reason') ? $bad : $ok }} resize-y" required>{{ old('reason') }}</textarea>
                    <div class="flex items-start justify-between gap-3">
                        <div id="reasonChips" class="flex gap-2 overflow-x-auto scrollbar-hide pb-1 flex-1"></div>
                        <span id="reasonCount" class="text-[11px] text-slate-400 shrink-0 pt-1.5 tabular-nums">0 karakter</span>
                    </div>
                    @error('reason') <p class="text-xs font-semibold text-red-500">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-2">
                <a href="{{ route('points') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 active:scale-[0.98] transition-all">Batal</a>
                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 shadow-md shadow-orange-600/25 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                    <span id="submitIcon" class="material-symbols-outlined text-lg">save</span>
                    <span id="submitText">Simpan Catatan</span>
                </button>
            </div>
        </form>

        {{-- Pratinjau (desktop) --}}
        <aside class="hidden lg:block lg:col-span-4" aria-label="Pratinjau catatan">
            <div class="sticky top-6 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-lg shadow-slate-200/60 overflow-hidden">
                    <div id="pvBand" class="h-2 bg-emerald-500 transition-colors"></div>
                    <div class="p-6">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Pratinjau</p>

                        <div class="mt-4 flex items-center gap-4">
                            <div id="pvBadge" class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex flex-col items-center justify-center shrink-0 transition-colors">
                                <span id="pvScore" class="text-3xl font-extrabold leading-none tabular-nums">+10</span>
                                <span class="text-[10px] font-extrabold uppercase mt-1 opacity-70">poin</span>
                            </div>
                            <div class="min-w-0">
                                <span id="pvType" class="inline-block px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 transition-colors">Penghargaan</span>
                                <p id="pvStudent" class="mt-1.5 text-sm font-extrabold text-slate-900 break-words">Belum memilih siswa</p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 text-sm border-t border-slate-100 pt-4">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="font-medium text-slate-500 shrink-0">Tanggal</dt>
                                <dd id="pvDate" class="font-bold text-slate-900 text-right">-</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="font-medium text-slate-500 shrink-0">Bukti foto</dt>
                                <dd id="pvPhoto" class="font-bold text-slate-900 text-right">Tidak ada</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-slate-500">Keterangan</dt>
                                <dd id="pvReason" class="mt-1 text-[13px] text-slate-700 leading-relaxed break-words">-</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4 flex items-start gap-3">
                    <span class="material-symbols-outlined text-orange-600 text-xl mt-0.5">lightbulb</span>
                    <p class="text-xs text-orange-800 leading-relaxed">Tulis keterangan yang jelas dan singkat. Lampirkan foto bila ada bukti agar catatan mudah ditinjau kembali.</p>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
(function () {
    /* ================= 1) Header full-bleed di mobile =================
       Menempel ke ujung atas + kiri + kanan layar, berapa pun padding yang dipakai layout.
       Bila ada top-bar fixed/sticky milik layout, header diletakkan tepat di bawahnya (tanpa celah). */
    const hero = document.getElementById('heroBleed');
    function fitHero() {
        if (!hero) return;
        hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
        if (window.innerWidth >= 640) return;

        const vw = document.documentElement.clientWidth;
        const r  = hero.getBoundingClientRect();

        let barBottom = 0;
        document.elementsFromPoint(Math.round(vw / 2), 2).forEach(el => {
            if (hero.contains(el) || el === document.documentElement || el === document.body) return;
            const pos = getComputedStyle(el).position;
            if (pos === 'fixed' || pos === 'sticky') barBottom = Math.max(barBottom, el.getBoundingClientRect().bottom);
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

    /* ================= elemen form ================= */
    const form      = document.getElementById('pointForm');
    const studentSel = document.getElementById('student_id');
    const search    = document.getElementById('studentSearch');
    const countEl   = document.getElementById('studentCount');
    const score     = document.getElementById('score');
    const dateIn    = document.getElementById('date');
    const dateHint  = document.getElementById('dateHint');
    const reason    = document.getElementById('reason');
    const photo     = document.getElementById('proof_photo');
    const types     = form.querySelectorAll('input[name="type"]');

    const pad = n => String(n).padStart(2, '0');
    const ymd = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    const currentType = () => (form.querySelector('input[name="type"]:checked') || {}).value || 'achievement';

    /* ================= 2) Cari siswa (filter dropdown) ================= */
    const allOptions = Array.from(studentSel.options).slice(1).map(o => ({ value: o.value, text: o.text }));
    function renderStudents() {
        const q = search.value.trim().toLowerCase();
        const keep = studentSel.value;
        const list = q ? allOptions.filter(o => o.text.toLowerCase().includes(q)) : allOptions;

        studentSel.innerHTML = '';
        studentSel.add(new Option('Pilih Siswa', ''));
        list.forEach(o => studentSel.add(new Option(o.text, o.value, false, o.value === keep)));
        if (keep && !list.some(o => o.value === keep)) studentSel.value = '';

        countEl.textContent = q ? (list.length + ' dari ' + allOptions.length + ' siswa') : (allOptions.length + ' siswa');
        updatePreview();
    }
    search.addEventListener('input', renderStudents);

    /* ================= 3) Poin: stepper + preset ================= */
    function setScore(v) { score.value = Math.round(Number(v) || 0); updatePreview(); }
    document.querySelectorAll('.step-btn').forEach(b => b.addEventListener('click', () => setScore((Number(score.value) || 0) + Number(b.dataset.step))));
    document.querySelectorAll('.score-chip').forEach(b => b.addEventListener('click', () => setScore(b.dataset.score)));
    score.addEventListener('input', updatePreview);

    /* ================= 4) Tanggal: validasi + jalan pintas ================= */
    function validDate(v) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(v || '')) return false;
        const [y, m, d] = v.split('-').map(Number);
        const dt = new Date(Date.UTC(y, m - 1, d));
        const real = dt.getUTCFullYear() === y && dt.getUTCMonth() === m - 1 && dt.getUTCDate() === d;
        return real && v >= dateIn.min && v <= dateIn.max;
    }
    function dateMsg(v) {
        if (!v) return 'Tanggal belum lengkap atau belum dipilih.';
        if (v > dateIn.max) return 'Tanggal tidak boleh di masa depan.';
        if (v < dateIn.min) return 'Tanggal terlalu lama.';
        return 'Format tanggal tidak valid.';
    }
    function showDate(msg) {
        dateIn.classList.add('border-red-400', 'bg-red-50/40');
        dateHint.textContent = msg; dateHint.classList.remove('hidden');
    }
    function clearDate() {
        dateIn.classList.remove('border-red-400', 'bg-red-50/40');
        dateHint.textContent = ''; dateHint.classList.add('hidden');
    }
    function checkDate() { if (validDate(dateIn.value)) { clearDate(); return true; } showDate(dateMsg(dateIn.value)); return false; }

    dateIn.addEventListener('change', () => { checkDate(); updatePreview(); });
    dateIn.addEventListener('blur', checkDate);
    document.querySelectorAll('.date-chip').forEach(b => b.addEventListener('click', () => {
        const d = new Date(); if (b.dataset.date === 'yesterday') d.setDate(d.getDate() - 1);
        dateIn.value = ymd(d); checkDate(); updatePreview();
    }));

    /* ================= 5) Foto bukti: pratinjau + seret & lepas ================= */
    const dz = document.getElementById('dropzone');
    const dzEmpty = document.getElementById('dzEmpty'), dzFilled = document.getElementById('dzFilled');
    const dzPreview = document.getElementById('dzPreview'), dzName = document.getElementById('dzName'), dzSize = document.getElementById('dzSize');
    const dzErr = document.getElementById('dzError');
    let objectUrl = null;

    function fmtSize(b) { return b > 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; }
    function clearPhoto() {
        photo.value = '';
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
        dzFilled.classList.add('hidden'); dzEmpty.classList.remove('hidden');
        updatePreview();
    }
    function showPhoto() {
        const f = photo.files && photo.files[0];
        dzErr.classList.add('hidden');
        if (!f) { clearPhoto(); return; }
        if (!f.type.startsWith('image/')) {
            dzErr.textContent = 'File harus berupa gambar (JPG, PNG, dsb).'; dzErr.classList.remove('hidden');
            clearPhoto(); return;
        }
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(f);
        dzPreview.src = objectUrl; dzName.textContent = f.name; dzSize.textContent = fmtSize(f.size);
        dzEmpty.classList.add('hidden'); dzFilled.classList.remove('hidden');
        updatePreview();
    }
    photo.addEventListener('change', showPhoto);
    document.getElementById('dzRemove').addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); clearPhoto(); });

    ['dragenter', 'dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('border-orange-400', 'bg-orange-50/60'); }));
    ['dragleave', 'drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('border-orange-400', 'bg-orange-50/60'); }));
    dz.addEventListener('drop', e => {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
            try { photo.files = e.dataTransfer.files; showPhoto(); } catch (err) { /* browser lama: pilih manual */ }
        }
    });

    /* ================= 6) Alasan: chip cepat + penghitung ================= */
    const chipSets = {
        achievement: ['Juara kelas', 'Membantu teman', 'Disiplin & tepat waktu', 'Aktif berpartisipasi', 'Menjaga kebersihan'],
        violation:   ['Terlambat masuk', 'Tidak memakai seragam', 'Mengganggu kelas', 'Tidak mengerjakan tugas', 'Meninggalkan kelas']
    };
    const chipsBox = document.getElementById('reasonChips');
    function renderChips() {
        chipsBox.innerHTML = '';
        chipSets[currentType()].forEach(text => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = text;
            b.className = 'shrink-0 px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 active:scale-95 transition-all';
            b.addEventListener('click', () => {
                const cur = reason.value.trim();
                reason.value = cur ? (cur.replace(/[.;,]$/, '') + '; ' + text) : text;
                reason.focus(); updatePreview();
            });
            chipsBox.appendChild(b);
        });
    }
    reason.addEventListener('input', updatePreview);

    /* ================= 7) Pratinjau langsung ================= */
    const pv = {
        band: document.getElementById('pvBand'), badge: document.getElementById('pvBadge'), score: document.getElementById('pvScore'),
        type: document.getElementById('pvType'), student: document.getElementById('pvStudent'), date: document.getElementById('pvDate'),
        photo: document.getElementById('pvPhoto'), reason: document.getElementById('pvReason')
    };
    const themes = {
        achievement: { band: 'bg-emerald-500', badge: 'bg-emerald-50 text-emerald-600', type: 'bg-emerald-50 text-emerald-700', label: 'Penghargaan', sign: '+' },
        violation:   { band: 'bg-rose-500',    badge: 'bg-rose-50 text-rose-600',       type: 'bg-rose-50 text-rose-700',       label: 'Pelanggaran', sign: '−' }
    };
    function updatePreview() {
        const t = themes[currentType()];
        pv.band.className  = 'h-2 transition-colors ' + t.band;
        pv.badge.className = 'w-20 h-20 rounded-3xl flex flex-col items-center justify-center shrink-0 transition-colors ' + t.badge;
        pv.type.className  = 'inline-block px-2.5 py-1 rounded-full text-[11px] font-extrabold transition-colors ' + t.type;
        pv.type.textContent = t.label;

        const n = Math.abs(Number(score.value) || 0);
        pv.score.textContent = t.sign + n;

        const opt = studentSel.options[studentSel.selectedIndex];
        pv.student.textContent = (opt && opt.value) ? opt.text : 'Belum memilih siswa';

        if (validDate(dateIn.value)) {
            const [y, m, d] = dateIn.value.split('-').map(Number);
            pv.date.textContent = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(y, m - 1, d));
        } else { pv.date.textContent = '-'; }

        pv.photo.textContent = (photo.files && photo.files.length) ? 'Terlampir' : 'Tidak ada';

        const r = reason.value.trim();
        pv.reason.textContent = r ? (r.length > 160 ? r.slice(0, 160) + '…' : r) : '-';
        document.getElementById('reasonCount').textContent = reason.value.length + ' karakter';
    }
    types.forEach(t => t.addEventListener('change', () => { renderChips(); updatePreview(); }));
    studentSel.addEventListener('change', updatePreview);

    /* ================= 8) Kirim: validasi + cegah klik ganda ================= */
    const btn = document.getElementById('submitBtn');
    form.addEventListener('submit', e => {
        let ok = true;
        if (!studentSel.value) { studentSel.classList.add('border-red-400'); ok = false; }
        if (!checkDate()) ok = false;
        if (!reason.value.trim()) { reason.classList.add('border-red-400'); ok = false; }
        if (score.value === '' || isNaN(Number(score.value))) { score.classList.add('border-red-400'); ok = false; }

        if (!ok) {
            e.preventDefault();
            const firstBad = form.querySelector('.border-red-400');
            if (firstBad) { firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' }); if (firstBad.focus) firstBad.focus({ preventScroll: true }); }
            return;
        }
        btn.disabled = true;
        document.getElementById('submitIcon').textContent = 'progress_activity';
        document.getElementById('submitIcon').classList.add('animate-spin');
        document.getElementById('submitText').textContent = 'Menyimpan...';
    });
    [studentSel, reason, score].forEach(el => el.addEventListener('input', () => el.classList.remove('border-red-400')));

    // Tombol "kembali" browser: pastikan tombol simpan aktif lagi
    window.addEventListener('pageshow', () => {
        btn.disabled = false;
        document.getElementById('submitIcon').textContent = 'save';
        document.getElementById('submitIcon').classList.remove('animate-spin');
        document.getElementById('submitText').textContent = 'Simpan Catatan';
    });

    renderChips();
    updatePreview();
})();
</script>
@endsection