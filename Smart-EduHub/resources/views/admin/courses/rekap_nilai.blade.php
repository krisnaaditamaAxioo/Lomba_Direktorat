@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Rekap Nilai & Rata-Rata Siswa',
        'eyebrow' => 'E-Learning',
        'subtitle' => $course->title . ' · ' . ($course->angkatan ? 'Angkatan: '.$course->angkatan->name : 'Semua Angkatan') . ' · ' . $gradableModules->count() . ' Modul',
        'icon' => 'fact_check',
    ])

    {{-- Aksi (Hidden on Print) --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 flex-wrap print:hidden">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Kursus
        </a>
        <span class="text-slate-300 hidden sm:inline">•</span>
        <a href="{{ route('admin.courses.all-rekap') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            Rekap Semua Kursus
        </a>
        <a href="{{ route('admin.courses.progress', $course) }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">analytics</span>
            <span>Progres Belajar</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-orange-600 text-white hover:bg-orange-700 rounded-2xl text-xs font-bold shadow-lg shadow-orange-100 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-base">print</span>
            <span>Cetak Rekap Nilai</span>
        </button>
        <a href="{{ route('admin.courses.rekap-nilai.print', $course) }}" target="_blank" class="px-5 py-2.5 bg-slate-800 text-white hover:bg-slate-900 rounded-2xl text-xs font-bold transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-base">print</span>
            <span>Halaman Cetak</span>
        </a>
    </div>

    {{-- Official Print Header (Only visible on Print) --}}
    <div class="hidden print:block text-center border-b-2 border-slate-900 pb-4 mb-6">
        <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">REKAPITULASI NILAI SISWA PER KURSUS</h1>
        <h2 class="text-base font-semibold text-slate-800 mt-1">{{ $course->title }}</h2>
        <p class="text-xs text-slate-600 mt-0.5">Target: {{ $course->angkatan ? $course->angkatan->name : 'Semua Angkatan' }} | Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    {{-- Top Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Card 1: Rata-Rata Kursus --}}
        <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-3xl p-6 text-white shadow-xl shadow-orange-100 dark:shadow-none relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-200">Rata-Rata Kursus</span>
                <div class="w-10 h-10 bg-white/15 rounded-2xl flex items-center justify-center backdrop-blur-md">
                    <span class="material-symbols-outlined text-white text-xl">grade</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black tracking-tight">{{ number_format($courseAverageScore, 1) }}<span class="text-lg font-bold opacity-80">%</span></div>
                <p class="text-[11px] text-orange-100 font-medium mt-1">Dihitung dari {{ $totalCourseStudentsWithGrades }} siswa dinilai</p>
            </div>
        </div>

        {{-- Card 2: Nilai Tertinggi --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Nilai Tertinggi</span>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">military_tech</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight text-emerald-600 dark:text-emerald-400">
                    {{ number_format($highestScore, 1) }}<span class="text-lg font-bold">%</span>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate mt-1">
                    {{ $highestStudent ? $highestStudent->nama_lengkap : '-' }}
                </p>
            </div>
        </div>

        {{-- Card 3: Nilai Terendah --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Nilai Terendah</span>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">trending_down</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight text-amber-600 dark:text-amber-400">
                    {{ number_format($lowestScore, 1) }}<span class="text-lg font-bold">%</span>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate mt-1">
                    {{ $lowestStudent ? $lowestStudent->nama_lengkap : '-' }}
                </p>
            </div>
        </div>

        {{-- Card 4: Tingkat Ketuntasan --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Tingkat Ketuntasan</span>
                <div class="w-10 h-10 bg-orange-50 text-orange-600 dark:bg-orange-950 dark:text-orange-400 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">verified</span>
                </div>
            </div>
            <div class="mt-4">
                @php($passPct = $students->count() > 0 ? round(($passedCount / $students->count()) * 100, 1) : 0)
                <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tight text-orange-600 dark:text-orange-400">
                    {{ $passPct }}<span class="text-lg font-bold">%</span>
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">
                    {{ $passedCount }} dari {{ $students->count() }} siswa (Skor &ge; 75)
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Search & Main Table Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        {{-- Search Toolbar (Hidden on Print) --}}
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 print:hidden">
            <div class="relative w-full sm:w-80">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" id="searchInput" onkeyup="filterRekapTable()" placeholder="Cari nama siswa / NISN..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
            </div>
            <div class="text-xs font-bold text-slate-500">
                Total Siswa: <span class="text-slate-900 dark:text-white font-black">{{ $students->count() }}</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table id="rekapTable" class="w-full min-w-[850px] text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-12 sticky left-0 bg-slate-50 dark:bg-slate-800/80 z-10">No</th>
                        <th class="px-5 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest min-w-[200px] sticky left-12 bg-slate-50 dark:bg-slate-800/80 z-10">Nama Siswa</th>
                        @foreach($gradableModules as $mod)
                            <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center min-w-[120px]" title="{{ $mod->title }}">
                                <div class="truncate max-w-[120px] mx-auto">{{ $mod->title }}</div>
                                <span class="text-[9px] font-normal text-slate-400 lowercase block">(Maks {{ $mod->questions->sum('score') ?: 100 }})</span>
                            </th>
                        @endforeach
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center min-w-[110px] bg-slate-100/50 dark:bg-slate-800/50">Total Skor</th>
                        <th class="px-5 py-4 text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest text-center min-w-[130px] bg-orange-50/50 dark:bg-orange-950/40">Rata-Rata Nilai</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center min-w-[120px]">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($students as $index => $student)
                        <tr class="student-row hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-4 text-xs font-bold text-slate-400 text-center sticky left-0 bg-white dark:bg-slate-900">{{ $index + 1 }}</td>
                            <td class="px-5 py-4 sticky left-12 bg-white dark:bg-slate-900">
                                <div class="font-bold text-sm text-slate-900 dark:text-white leading-snug">{{ $student->nama_lengkap }}</div>
                                <div class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $student->nisn ?: ($student->username ?: 'Siswa') }}</div>
                            </td>

                            {{-- Module Scores --}}
                            @foreach($gradableModules as $mod)
                                @php($item = $student->scores_list[$mod->id] ?? null)
                                <td class="px-4 py-4 text-center">
                                    @if($item && $item['score'] !== null)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-black
                                                {{ $item['percentage'] >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900' :
                                                   ($item['percentage'] >= 70 ? 'bg-orange-50 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-100 dark:border-orange-900' :
                                                   'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-100 dark:border-amber-900') }}">
                                                {{ number_format($item['score'], 0) }} / {{ $item['max_score'] }}
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400 mt-1">{{ number_format($item['percentage'], 1) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-slate-300 dark:text-slate-700">-</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Total Skor --}}
                            <td class="px-4 py-4 text-center bg-slate-50/50 dark:bg-slate-800/30">
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                    {{ number_format($student->total_score, 0) }} <span class="text-[10px] font-normal text-slate-400">/ {{ $student->total_max_score }}</span>
                                </span>
                            </td>

                            {{-- Rata-Rata Nilai --}}
                            <td class="px-5 py-4 text-center bg-orange-50/30 dark:bg-orange-950/20">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl font-black text-sm shadow-sm
                                    {{ $student->average_score >= 85 ? 'bg-emerald-600 text-white' :
                                       ($student->average_score >= 75 ? 'bg-orange-600 text-white' :
                                       ($student->average_score >= 65 ? 'bg-amber-500 text-white' : 'bg-rose-500 text-white')) }}">
                                    <span>{{ number_format($student->average_score, 1) }}</span>
                                    <span class="text-[10px] opacity-80">%</span>
                                </div>
                            </td>

                            {{-- Predikat --}}
                            <td class="px-4 py-4 text-center">
                                @php($avg = $student->average_score)
                                @if($avg >= 90)
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">A (Sangat Baik)</span>
                                @elseif($avg >= 80)
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300">B (Baik)</span>
                                @elseif($avg >= 70)
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">C (Cukup)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">D (Kurang)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $gradableModules->count() + 5 }}" class="px-6 py-16 text-center text-sm font-medium text-slate-400">
                                Belum ada siswa terdaftar pada angkatan/kursus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Summary Footer Row --}}
                @if($students->isNotEmpty() && $gradableModules->isNotEmpty())
                <tfoot class="bg-slate-100/80 dark:bg-slate-800/90 border-t-2 border-slate-200 dark:border-slate-700 font-bold">
                    <tr>
                        <td colspan="2" class="px-5 py-4 text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider sticky left-0 bg-slate-100/90 dark:bg-slate-800/90">
                            Rata-Rata Kelas / Modul
                        </td>
                        @foreach($gradableModules as $mod)
                            @php($modStat = $moduleSummary[$mod->id] ?? null)
                            <td class="px-4 py-4 text-center">
                                @if($modStat && $modStat['count'] > 0)
                                    <div class="text-xs font-black text-orange-700 dark:text-orange-400">
                                        {{ number_format($modStat['avg_percentage'], 1) }}%
                                    </div>
                                    <div class="text-[9px] text-slate-400 font-normal">({{ $modStat['count'] }} siswa)</div>
                                @else
                                    <span class="text-xs font-normal text-slate-400">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-4 text-center text-xs font-black text-slate-600 dark:text-slate-400">SUMMARY</td>
                        <td class="px-5 py-4 text-center bg-orange-100/60 dark:bg-orange-950/60">
                            <span class="text-sm font-black text-orange-900 dark:text-orange-200">{{ number_format($courseAverageScore, 1) }}%</span>
                        </td>
                        <td class="px-4 py-4 text-center text-xs font-black text-slate-600 dark:text-slate-400">OVERALL</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
function filterRekapTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll(".student-row");

    rows.forEach(row => {
        const text = row.textContent || row.innerText;
        if (text.toUpperCase().indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<style>
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    table {
        border: 1px solid #cbd5e1 !important;
    }
    th, td {
        border: 1px solid #e2e8f0 !important;
        padding: 6px 8px !important;
        font-size: 11px !important;
    }
}
</style>
@endsection
