@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Rekapitulasi Nilai Seluruh Kursus',
        'eyebrow' => 'E-Learning',
        'subtitle' => 'Ringkasan statistik rata-rata nilai siswa per kursus.',
        'icon' => 'equalizer',
    ])

    <div class="flex justify-center print:hidden">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Kursus
        </a>
    </div>

    {{-- Course List Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kursus</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Angkatan Target</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Modul & Soal</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Siswa Terdaftar</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Siswa Dinilai</th>
                        <th class="px-6 py-4 text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest text-center">Rata-Rata Kursus</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($courses as $course)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $course->title }}</div>
                                @if($course->description)
                                    <div class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $course->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $course->angkatan ? $course->angkatan->name : 'Semua Angkatan' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $course->modules->count() }} Modul
                            </td>
                            <td class="px-4 py-4 text-center text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $course->student_count }} Siswa
                            </td>
                            <td class="px-4 py-4 text-center text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $course->graded_student_count }} Siswa
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl font-black text-sm shadow-sm
                                    {{ $course->course_average >= 80 ? 'bg-emerald-600 text-white' :
                                       ($course->course_average >= 70 ? 'bg-orange-600 text-white' :
                                       ($course->course_average > 0 ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400')) }}">
                                    <span>{{ number_format($course->course_average, 1) }}</span>
                                    <span class="text-[10px] opacity-80">%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.courses.rekap-nilai', $course) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5 shadow-sm">
                                    <span class="material-symbols-outlined text-sm">fact_check</span>
                                    <span>Detail Rekap Nilai</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm font-medium text-slate-400">
                                Belum ada kursus tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
