@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Progres Siswa',
        'eyebrow' => 'E-Learning',
        'subtitle' => $course->title . ' · ' . $course->modules->count() . ' modul',
        'icon' => 'trending_up',
    ])

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-orange-600">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke kursus
        </a>
        <a href="{{ route('admin.courses.rekap-nilai', $course) }}" class="px-5 py-2.5 bg-orange-600 text-white hover:bg-orange-700 rounded-2xl text-xs font-bold transition-all shadow-md shadow-orange-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-base">fact_check</span>
            <span>Rekap Nilai & Rata-Rata</span>
        </a>
        <div class="px-4 py-2.5 bg-orange-50 dark:bg-orange-950 text-orange-700 dark:text-orange-300 rounded-2xl text-xs font-bold">
            {{ $students->count() }} siswa terdaftar
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-x-auto">
        <table class="w-full min-w-[760px] text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/60">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky left-0 bg-slate-50 dark:bg-slate-800/60">Siswa</th>
                    <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                    @foreach($course->modules as $module)
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest min-w-[130px]">{{ $module->title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($students as $student)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                        <td class="px-6 py-4 sticky left-0 bg-white dark:bg-slate-900">
                            <div class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $student->nama_lengkap }}</div>
                            <div class="text-[10px] text-slate-400 mt-1">{{ $student->nisn ?: $student->username }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="w-24">
                                <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1"><span>{{ $student->course_progress }}%</span></div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-orange-600 rounded-full" style="width: {{ $student->course_progress }}%"></div></div>
                            </div>
                        </td>
                        @foreach($course->modules as $module)
                            @php($value = $student->module_progress[$module->id]->progress ?? 0)
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 bg-slate-100 rounded-full overflow-hidden"><div class="h-full {{ $value >= 100 ? 'bg-emerald-500' : 'bg-orange-500' }} rounded-full" style="width: {{ $value }}%"></div></div>
                                    <span class="text-[11px] font-bold {{ $value >= 100 ? 'text-emerald-600' : 'text-slate-500' }}">{{ $value }}%</span>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $course->modules->count() + 2 }}" class="px-6 py-16 text-center text-sm text-slate-400">Belum ada siswa pada angkatan kursus ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
