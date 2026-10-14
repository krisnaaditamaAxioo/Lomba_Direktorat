@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-600">Portal Guru</p>
            <h1 class="text-3xl font-black text-slate-900">Dashboard Guru</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola kursus, modul, soal evaluasi, dan penilaian dari satu tempat.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-orange-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-orange-100 hover:bg-orange-700 transition-all">
            <span class="material-symbols-outlined">add_circle</span>
            Kelola Kursus & Modul
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-orange-600 text-3xl">auto_stories</span>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kursus</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900">{{ $totalCourses }}</h3>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-violet-600 text-3xl">toc</span>
                <span class="text-[10px] font-black uppercase tracking-wider text-violet-600 bg-violet-50 px-2 py-1 rounded-full">Modul</span>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Modul</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900">{{ $totalModules }}</h3>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-amber-600 text-3xl">quiz</span>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Soal</span>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Soal</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900">{{ $totalQuestions }}</h3>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-emerald-600 text-3xl">grading</span>
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Review</span>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Menunggu Nilai</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900">{{ $pendingGrades }}</h3>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($courses as $course)
            <div class="bg-white border border-slate-100 rounded-[30px] overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">{{ $course->title }}</h2>
                        <p class="text-xs text-slate-400 mt-1">{{ $course->angkatan ? $course->angkatan->name : 'Semua Angkatan' }}</p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('admin.courses.modules.create', $course) }}" class="px-3 py-2 bg-orange-50 text-orange-600 rounded-xl text-xs font-bold">+ Tambah Modul</a>
                        <a href="{{ route('admin.courses.rekap-nilai', $course) }}" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Rekap Nilai</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="px-3 py-2 bg-amber-50 text-amber-600 rounded-xl text-xs font-bold">Edit</a>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($course->modules as $module)
                        <div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/60">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white text-orange-600">
                                            <span class="material-symbols-outlined text-lg">{{ $module->type === 'video' ? 'video_library' : ($module->type === 'quiz' ? 'quiz' : ($module->type === 'coding' ? 'code' : ($module->type === 'assignment' ? 'assignment' : ($module->type === 'text' ? 'article' : 'picture_as_pdf')))) }}</span>
                                        </span>
                                        <h3 class="text-base font-black text-slate-900 truncate">{{ $module->title }}</h3>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2">{{ $module->type }} • {{ $module->questions->count() }} soal</p>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    @if(in_array($module->type, ['assignment', 'quiz', 'coding']))
                                        <a href="{{ route('admin.courses.modules.questions', $module) }}" class="px-3 py-2 bg-orange-600 text-white rounded-xl text-[11px] font-bold">Kelola Soal</a>
                                    @endif
                                    <a href="{{ route('admin.courses.modules.preview', $module) }}" target="_blank" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-xl text-[11px] font-bold">Preview</a>
                                    <a href="{{ route('admin.courses.modules.submissions', $module) }}" class="px-3 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-[11px] font-bold">Nilai Siswa</a>
                                    <a href="{{ route('admin.courses.modules.edit', $module) }}" class="px-3 py-2 bg-amber-50 text-amber-700 rounded-xl text-[11px] font-bold">Edit</a>
                                    <form action="{{ route('admin.courses.modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Hapus modul ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 bg-rose-50 text-rose-600 rounded-xl text-[11px] font-bold">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                            <p class="text-sm text-slate-500">Belum ada modul yang dibuat oleh Anda untuk kursus ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-slate-200 rounded-[30px] p-12 text-center">
                <div class="w-16 h-16 rounded-3xl bg-orange-50 text-orange-600 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">school</span>
                </div>
                <h3 class="text-xl font-black text-slate-900">Belum ada kursus yang Anda buat</h3>
                <p class="text-sm text-slate-500 mt-2">Mulai dengan menambahkan kursus baru lalu buat modul dan soal evaluasi.</p>
                <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 px-5 py-3 mt-6 bg-orange-600 text-white rounded-2xl text-sm font-bold">Buat Kursus Baru</a>
            </div>
        @endforelse
    </div>
</div>
@endsection