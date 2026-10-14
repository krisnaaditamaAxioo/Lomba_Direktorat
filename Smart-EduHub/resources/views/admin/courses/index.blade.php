@extends('layouts.app')

@section('title', 'Manajemen Kursus & Modul')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    $isGuru = auth()->user()->isGuru();
    $userId = auth()->id();

    // Satu tempat untuk ikon, warna, dan label tiap tipe modul
    $typeMeta = [
        'pdf'        => ['icon' => 'picture_as_pdf', 'box' => 'bg-slate-100 text-slate-500',    'text' => 'text-slate-500',   'label' => 'PDF'],
        'video'      => ['icon' => 'video_library',  'box' => 'bg-red-50 text-red-500',          'text' => 'text-red-500',     'label' => 'Video'],
        'link'       => ['icon' => 'link',           'box' => 'bg-sky-50 text-sky-600',          'text' => 'text-sky-600',     'label' => 'Link'],
        'text'       => ['icon' => 'article',        'box' => 'bg-orange-50 text-orange-500',    'text' => 'text-orange-500',  'label' => 'Teks'],
        'assignment' => ['icon' => 'assignment',     'box' => 'bg-amber-50 text-amber-600',      'text' => 'text-amber-600',   'label' => 'Tugas'],
        'quiz'       => ['icon' => 'quiz',           'box' => 'bg-violet-50 text-violet-600',    'text' => 'text-violet-600',  'label' => 'Kuis'],
        'coding'     => ['icon' => 'code',           'box' => 'bg-emerald-50 text-emerald-600',  'text' => 'text-emerald-600', 'label' => 'Coding'],
    ];
    $questionTypes = [
        'multiple_choice' => ['Pilihan Ganda', 'bg-violet-100 text-violet-700'],
        'essay'           => ['Essay',         'bg-amber-100 text-amber-700'],
        'coding'          => ['Coding',        'bg-emerald-100 text-emerald-700'],
        'file'            => ['PDF Upload',    'bg-orange-100 text-orange-700'],
    ];
    $soalTypes = ['assignment', 'quiz', 'coding'];
@endphp

<div class="space-y-5 lg:space-y-6 pb-28 md:pb-8">

    {{-- ================= HERO HEADER ================= --}}
    <header data-hero class="relative overflow-hidden rounded-b-[2rem] md:rounded-3xl bg-gradient-to-br from-orange-500 via-orange-600 to-red-700 text-white px-5 pt-8 pb-8 sm:px-8 sm:py-9 shadow-xl shadow-orange-200/60">
        <div class="pointer-events-none absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-16 -right-4 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-8 h-36 w-36 rounded-full bg-black/10"></div>

        <div class="relative flex items-start justify-between gap-3">
            <a href="{{ url()->previous() }}" class="md:hidden grid h-10 w-10 place-items-center rounded-full bg-white/20 backdrop-blur hover:bg-white/30 transition" aria-label="Kembali">
                <span class="material-symbols-outlined text-[22px]">arrow_back</span>
            </a>
            <div class="hidden md:block"></div>
            <div class="flex items-center gap-2 rounded-full bg-white text-slate-900 px-3.5 py-2 shadow-lg">
                <span class="material-symbols-outlined text-orange-600 text-[18px]">menu_book</span>
                <span class="text-[10px] font-bold text-slate-400">Kursus</span>
                <span class="text-sm font-black">{{ $courses->count() }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">E-Learning</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Kursus &amp; Modul</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">
                {{ $isGuru ? 'Modul ajar yang Anda buat untuk siswa binaan.' : 'Kelola materi pembelajaran untuk siswa binaan.' }}
            </p>
        </div>

        <div class="relative mt-5 flex flex-col sm:flex-row gap-2.5">
            <button type="button" onclick="openCourseModal()" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                Tambah Kursus Baru
            </button>
            <a href="{{ route('admin.courses.all-rekap') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur hover:bg-white/25 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">equalizer</span>
                {{ $isGuru ? 'Rekap Nilai Modul Saya' : 'Rekap Nilai Semua Kursus' }}
            </a>
        </div>
    </header>

    {{-- ================= ALERTS ================= --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="mb-2 flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <p class="text-sm font-bold text-rose-800">Terjadi kesalahan</p>
            </div>
            <ul class="ml-9 list-disc space-y-0.5 text-xs font-medium text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ================= DAFTAR KURSUS ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:gap-6 xl:grid-cols-2">
        @forelse($courses as $course)
            <article class="flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">

                {{-- Header kursus --}}
                <div class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:p-6">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <div class="grid h-12 w-12 sm:h-14 sm:w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-md shadow-orange-200/60">
                            <span class="material-symbols-outlined text-[26px] sm:text-3xl">auto_stories</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base sm:text-lg font-black leading-tight text-slate-900 break-words">{{ $course->title }}</h3>
                            <span class="mt-1 inline-block rounded-full bg-orange-50 px-2.5 py-0.5 text-[11px] font-bold text-orange-600">
                                {{ $course->angkatan ? $course->angkatan->name : 'Semua Angkatan' }}
                            </span>
                        </div>

                        {{-- Rekap / edit / hapus kursus --}}
                        <div class="flex shrink-0 items-center gap-1">
                            @if(!$isGuru)
                                <a href="{{ route('admin.courses.rekap-nilai', $course) }}" class="rounded-xl p-2 text-slate-400 transition hover:bg-orange-50 hover:text-orange-600" title="Rekap Nilai & Rata-Rata Siswa">
                                    <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                </a>
                                <a href="{{ route('admin.courses.progress', $course) }}" class="rounded-xl p-2 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600" title="Lihat Progres Siswa">
                                    <span class="material-symbols-outlined text-[20px]">analytics</span>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-xl p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-500" title="Edit Kursus">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Hapus kursus ini beserta semua modulnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-500" title="Hapus Kursus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            @elseif($course->modules->count() > 0)
                                <a href="{{ route('admin.courses.rekap-nilai', $course) }}" class="rounded-xl p-2 text-slate-400 transition hover:bg-orange-50 hover:text-orange-600" title="Rekap Nilai Modul Saya">
                                    <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi utama --}}
                    @php $firstSoalModule = $course->modules->whereIn('type', $soalTypes)->first(); @endphp
                    <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                        <a href="{{ route('admin.courses.modules.create', $course) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-orange-600 px-4 py-2.5 text-xs font-extrabold text-white shadow-md shadow-orange-200 transition hover:bg-orange-700 active:scale-[.98]">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            Tambah Modul
                        </a>
                        @if($firstSoalModule)
                            <a href="{{ route('admin.courses.modules.questions', $firstSoalModule->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-100 active:scale-[.98]">
                                <span class="material-symbols-outlined text-[18px]">quiz</span>
                                Tambah Soal
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Isi kursus --}}
                <div class="flex-1 p-4 sm:p-6">
                    @if($course->description)
                        <p class="mb-5 line-clamp-2 text-sm font-medium text-slate-500">{{ $course->description }}</p>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-extrabold text-slate-400">Daftar Modul &amp; Soal</p>
                            <span class="text-xs font-bold text-slate-400">{{ $course->modules->count() }} modul</span>
                        </div>

                        @forelse($course->modules as $module)
                            @php
                                $meta = $typeMeta[$module->type] ?? ['icon' => 'description', 'box' => 'bg-slate-100 text-slate-400', 'text' => 'text-slate-400', 'label' => $module->type];
                                $isSoal = in_array($module->type, $soalTypes);
                                $canManageModule = !$isGuru || $module->created_by === $userId;
                                $qCount = $module->questions->count();
                            @endphp
                            <div class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50 p-3 sm:p-3.5">
                                {{-- Baris modul --}}
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $meta['box'] }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $meta['icon'] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-slate-800">{{ $module->title }}</span>
                                            <div class="mt-0.5 flex flex-wrap items-center gap-2">
                                                <span class="text-[11px] font-black {{ $meta['text'] }}">{{ $meta['label'] }}</span>
                                                @if($isSoal)
                                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $qCount > 0 ? 'bg-orange-100 text-orange-600' : 'bg-rose-100 text-rose-500' }}">
                                                        {{ $qCount > 0 ? $qCount.' Soal' : 'Belum Ada Soal' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1 sm:shrink-0 sm:justify-end">
                                        @if($canManageModule && $isSoal)
                                            <a href="{{ route('admin.courses.modules.questions', $module->id) }}" class="inline-flex items-center gap-1 rounded-xl bg-orange-600 px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-orange-700" title="Tambah / Kelola Soal">
                                                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                                Soal
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.courses.modules.preview', $module->id) }}" target="_blank" rel="noopener" class="rounded-xl p-2 text-orange-500 transition hover:bg-orange-50" title="Preview Modul">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                        @if($canManageModule)
                                            <a href="{{ route('admin.courses.modules.submissions', $module->id) }}" class="rounded-xl p-2 text-emerald-600 transition hover:bg-emerald-50" title="Lihat Pengerjaan & Penilaian Siswa">
                                                <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                                            </a>
                                            <a href="{{ route('admin.courses.modules.edit', $module->id) }}" class="rounded-xl p-2 text-amber-500 transition hover:bg-amber-50" title="Edit Modul">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            <form action="{{ route('admin.courses.modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-xl p-2 text-rose-500 transition hover:bg-rose-50" title="Hapus Modul">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Daftar soal inline --}}
                                @if($isSoal)
                                    <div class="space-y-2 rounded-xl border border-slate-100 bg-white p-3">
                                        <div class="flex items-center justify-between gap-2 text-xs">
                                            <span class="flex items-center gap-1 font-extrabold text-slate-400">
                                                <span class="material-symbols-outlined text-[16px] text-orange-500">quiz</span>
                                                Soal modul ini
                                            </span>
                                            <a href="{{ route('admin.courses.modules.questions', $module->id) }}" class="flex items-center gap-0.5 font-bold text-orange-600 hover:underline">
                                                <span class="material-symbols-outlined text-[14px]">add</span>
                                                Kelola soal
                                            </a>
                                        </div>

                                        <div class="max-h-60 space-y-1.5 overflow-y-auto">
                                            @forelse($module->questions as $qIndex => $q)
                                                @php $qMeta = $questionTypes[$q->type] ?? [$q->type, 'bg-slate-100 text-slate-600']; @endphp
                                                <div class="flex items-center justify-between gap-2 rounded-lg bg-slate-50 px-2.5 py-2 text-xs text-slate-700">
                                                    <div class="flex min-w-0 flex-1 items-center gap-2">
                                                        <span class="w-5 shrink-0 text-[11px] font-black text-slate-400">{{ $qIndex + 1 }}.</span>
                                                        <span class="hidden shrink-0 rounded px-1.5 py-0.5 text-[10px] font-black sm:inline-block {{ $qMeta[1] }}">{{ $qMeta[0] }}</span>
                                                        <span class="min-w-0 flex-1 truncate font-medium text-slate-800">{{ strip_tags($q->question) }}</span>
                                                    </div>
                                                    <span class="shrink-0 rounded bg-orange-50 px-1.5 py-0.5 text-[10px] font-bold text-orange-600">{{ $q->score }} Poin</span>
                                                </div>
                                            @empty
                                                <div class="flex flex-col gap-2 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2.5 text-xs text-amber-800 sm:flex-row sm:items-center sm:justify-between">
                                                    <span class="flex items-center gap-1.5 font-medium">
                                                        <span class="material-symbols-outlined text-[18px] text-amber-600">info</span>
                                                        Belum ada soal pada modul ini.
                                                    </span>
                                                    <a href="{{ route('admin.courses.modules.questions', $module->id) }}" class="inline-flex items-center justify-center gap-1 rounded-lg bg-amber-600 px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-amber-700">
                                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                                        Buat Soal
                                                    </a>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-300">
                                    <span class="material-symbols-outlined">folder_open</span>
                                </div>
                                <p class="text-sm font-bold text-slate-500">Belum ada modul</p>
                                <p class="text-xs text-slate-400">Klik "Tambah Modul" untuk menambahkan.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pintasan kelola soal per modul --}}
                    @php
                        $manageable = $course->modules->filter(fn ($sm) => !$isGuru || $sm->created_by === $userId);
                    @endphp
                    <div class="mt-4 space-y-2 border-t border-slate-100 pt-4">
                        @forelse($manageable as $sm)
                            @php $smCount = $sm->questions->count(); @endphp
                            <a href="{{ route('admin.courses.modules.questions', $sm->id) }}"
                               class="flex w-full items-center justify-between gap-2 rounded-2xl border-2 border-dashed px-3 py-2.5 text-xs font-bold transition sm:px-4
                                      {{ $smCount > 0 ? 'border-orange-200 text-orange-600 hover:border-orange-400 hover:bg-orange-50' : 'border-emerald-200 text-emerald-600 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                <span class="flex min-w-0 items-center gap-2">
                                    <span class="material-symbols-outlined shrink-0 text-[18px]">quiz</span>
                                    <span class="hidden shrink-0 sm:inline">Kelola soal &mdash;</span>
                                    <span class="truncate font-black">{{ $sm->title }}</span>
                                </span>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-black {{ $smCount > 0 ? 'bg-orange-100 text-orange-600' : 'bg-emerald-100 text-emerald-600' }}">
                                    {{ $smCount > 0 ? $smCount.' soal' : '+ Tambah soal' }}
                                </span>
                            </a>
                        @empty
                            <a href="{{ route('admin.courses.modules.create', $course) }}" class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-orange-200 py-2.5 text-xs font-bold text-orange-600 transition hover:bg-orange-50">
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                Buat modul baru dulu untuk menambah soal
                            </a>
                        @endforelse
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center xl:col-span-2">
                <span class="material-symbols-outlined text-5xl text-slate-300">menu_book</span>
                <p class="mt-2 font-bold text-slate-600">Belum ada kursus</p>
                <button type="button" onclick="openCourseModal()" class="mt-1 text-sm font-bold text-orange-600 hover:underline">Tambah kursus pertama</button>
            </div>
        @endforelse
    </div>
</div>

{{-- ================= MODAL TAMBAH KURSUS (bottom sheet di mobile) ================= --}}
<div id="addCourseModal" class="fixed inset-0 z-[70] hidden items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm sm:p-4" onclick="if(event.target===this) closeCourseModal()">
    <div class="flex max-h-[92vh] w-full max-w-lg flex-col overflow-hidden rounded-t-[2rem] sm:rounded-3xl bg-white shadow-2xl">
        <div class="mx-auto mt-3 h-1.5 w-12 shrink-0 rounded-full bg-slate-200 sm:hidden"></div>

        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-7">
            <div class="flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-orange-50 text-orange-600">
                    <span class="material-symbols-outlined">add_circle</span>
                </span>
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Tambah Kursus Baru</h3>
            </div>
            <button type="button" onclick="closeCourseModal()" class="grid h-9 w-9 place-items-center rounded-full text-slate-400 hover:bg-slate-100" aria-label="Tutup">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 space-y-4 overflow-y-auto px-5 py-6 sm:px-7">
            @csrf
            <div>
                <label for="c_title" class="mb-1.5 block text-xs font-bold text-slate-500">Judul Kursus</label>
                <input id="c_title" type="text" name="title" required placeholder="Contoh: Dasar Pemrograman Web"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
            </div>
            <div>
                <label for="c_desc" class="mb-1.5 block text-xs font-bold text-slate-500">Deskripsi</label>
                <textarea id="c_desc" name="description" rows="3" placeholder="Ringkasan singkat isi kursus"
                          class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"></textarea>
            </div>
            <div>
                <label for="c_angkatan" class="mb-1.5 block text-xs font-bold text-slate-500">Angkatan</label>
                <select id="c_angkatan" name="angkatan_id"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
                    <option value="">Semua Angkatan</option>
                    @foreach(($angkatans ?? []) as $angkatan)
                        <option value="{{ $angkatan->id }}">{{ $angkatan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="c_image" class="mb-1.5 block text-xs font-bold text-slate-500">Gambar Sampul</label>
                <input id="c_image" type="file" name="image" accept="image/*"
                       class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-3 text-xs text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-orange-600 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-orange-700">
            </div>

            <div class="flex flex-col-reverse gap-2.5 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeCourseModal()" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 hover:bg-orange-700 active:scale-[.98] transition">Simpan Kursus</button>
            </div>
        </form>
    </div>
</div>

<script>
    const courseModal = document.getElementById('addCourseModal');
    function openCourseModal()  { courseModal.classList.remove('hidden'); courseModal.classList.add('flex'); }
    function closeCourseModal() { courseModal.classList.add('hidden');    courseModal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCourseModal(); });

    // Mobile: tarik header sampai menempel penuh ke tepi atas/kiri/kanan layar,
    // berapa pun padding yang dipasang layout di atasnya.
    (function () {
        const hero = document.querySelector('[data-hero]');
        if (!hero) return;
        function fit() {
            hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
            if (window.innerWidth >= 768) return;
            const r = hero.getBoundingClientRect();
            const top = r.top + window.scrollY;
            hero.style.marginTop   = -Math.max(top, 0) + 'px';
            hero.style.marginLeft  = -Math.max(r.left, 0) + 'px';
            hero.style.marginRight = -Math.max(window.innerWidth - r.right, 0) + 'px';
        }
        fit();
        window.addEventListener('load', fit);
        window.addEventListener('resize', fit);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    })();
</script>
@endsection