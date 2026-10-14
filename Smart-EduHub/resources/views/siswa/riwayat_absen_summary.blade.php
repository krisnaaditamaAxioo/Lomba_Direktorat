@extends('layouts.siswa')

@section('title', 'Ringkasan Presensi')
@section('page_title', 'Ringkasan Kehadiran')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Ringkasan Presensi</h1>
            <p class="text-slate-500 font-medium mt-1">Rekapitulasi kehadiran Anda per bulan.</p>
        </div>
        
        <form action="{{ route('siswa.attendance') }}" method="GET" class="flex flex-row items-end gap-2 md:gap-3 w-full md:w-auto">
            <input type="hidden" name="mode" value="summary">
            <div class="flex flex-col gap-1 flex-1 md:flex-none">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari</label>
                <input type="date" name="start_date" value="{{ $start_date }}" onchange="this.form.submit()" class="w-full px-3 md:px-5 py-2 bg-white border border-slate-200 rounded-xl text-xs md:text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all cursor-pointer">
            </div>
            <div class="flex flex-col gap-1 flex-1 md:flex-none">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai</label>
                <input type="date" name="end_date" value="{{ $end_date }}" onchange="this.form.submit()" class="w-full px-3 md:px-5 py-2 bg-white border border-slate-200 rounded-xl text-xs md:text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all cursor-pointer">
            </div>
            <div class="flex flex-col gap-1 flex-1 md:flex-none">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mode</label>
                <select name="mode" onchange="this.form.submit()" class="w-full px-3 md:px-5 py-2 bg-white border border-slate-200 rounded-xl text-xs md:text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all cursor-pointer">
                    <option value="detail" {{ $mode === 'detail' ? 'selected' : '' }}>Harian</option>
                    <option value="summary" {{ $mode === 'summary' ? 'selected' : '' }}>Ringkasan</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Legends (Summary View) -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-4 md:p-6 border-b border-slate-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h3 class="text-lg md:text-xl font-black text-slate-900 leading-tight">Rekapitulasi Bulanan<br class="sm:hidden"><span class="text-xs md:text-sm text-slate-400 font-bold block sm:inline sm:ml-2">{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</span></h3>
            <div class="flex gap-2 no-print w-full sm:w-auto">
                <a href="{{ route('siswa.attendance', array_merge(request()->all(), ['print' => 1])) }}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-slate-50 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-100 transition-all">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Cetak PDF
                </a>
            </div>
        </div>
        
        <div class="w-full overflow-x-auto">
            <div class="p-4 md:p-6">
                <table class="w-full text-left border-collapse" style="min-width: 600px;">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky left-0 bg-slate-50 z-20 w-40 md:w-48 border-r border-slate-100" rowspan="2">Bulan</th>
                            <th class="p-2 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-r border-slate-100" colspan="6">Statistik Kehadiran</th>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <th class="p-2 text-center text-[9px] font-black text-emerald-700 uppercase border-r border-slate-100 bg-emerald-500 text-white">H</th>
                            <th class="p-2 text-center text-[9px] font-black text-emerald-600 uppercase border-r border-slate-100 bg-emerald-50/30">O</th>
                            <th class="p-2 text-center text-[9px] font-black text-amber-600 uppercase border-r border-slate-100 bg-amber-50/30">T</th>
                            <th class="p-2 text-center text-[9px] font-black text-brand-600 uppercase border-r border-slate-100 bg-brand-50/30">I</th>
                            <th class="p-2 text-center text-[9px] font-black text-brand-600 uppercase border-r border-slate-100 bg-brand-50/30">S</th>
                            <th class="p-2 text-center text-[9px] font-black text-rose-600 uppercase border-r border-slate-100 bg-rose-50/30">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                            $total_o = 0; $total_t = 0; $total_i = 0; $total_s = 0; $total_a = 0;
                        @endphp
                        @foreach($months as $month)
                            @php
                                // Get all dates in this month within the selected period
                                $monthStart = \Carbon\Carbon::parse($month['key'] . '-01');
                                $monthEnd = $monthStart->copy()->endOfMonth();
                                $reportStart = \Carbon\Carbon::parse($start_date);
                                $reportEnd = \Carbon\Carbon::parse($end_date);
                                $today = \Carbon\Carbon::today();
                                
                                $periodStart = $monthStart->max($reportStart);
                                $periodEnd = $monthEnd->min($reportEnd)->min($today); // Only count up to today
                                
                                // Calculate total holidays in this period
                                $holidayCount = 0;
                                if ($periodStart <= $periodEnd) {
                                    $currentDate = $periodStart->copy();
                                    while ($currentDate <= $periodEnd) {
                                        if (\App\Models\Holiday::checkHoliday($currentDate)) {
                                            $holidayCount++;
                                        }
                                        $currentDate->addDay();
                                    }
                                }
                                
                                $totalDaysInMonth = 0;
                                if ($periodStart <= $periodEnd) {
                                    $totalDaysInMonth = $periodStart->diffInDays($periodEnd) + 1 - $holidayCount;
                                }
                                
                                $monthAttendances = $student->attendances->filter(function($a) use ($month) {
                                    return strpos($a->date, $month['key']) === 0;
                                });
                                $o = $monthAttendances->where('status', 'present')->count();
                                $t = $monthAttendances->where('status', 'late')->count();
                                $i_count = $monthAttendances->filter(function($a) {
                                    return $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') === false;
                                })->count();
                                $s = $monthAttendances->filter(function($a) {
                                    return $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') !== false;
                                })->count();
                                $recordedAbsent = $monthAttendances->where('status', 'absent')->count();
                                
                                // Calculate total absent: days without any attendance (up to today, minus holidays) + recorded absent
                                $daysWithAttendance = $o + $t + $i_count + $s + $recordedAbsent;
                                $a = ($totalDaysInMonth - $daysWithAttendance) + $recordedAbsent;
                                
                                $total_o += $o; $total_t += $t; $total_i += $i_count; $total_s += $s; $total_a += $a;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 sticky left-0 bg-white z-10 border-r border-slate-100 font-bold text-xs text-slate-600">{{ $month['name'] }}</td>
                                <td class="p-2 text-center text-[11px] font-black text-white bg-emerald-500 border-r border-slate-50">{{ ($o + $t) ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold text-emerald-700 bg-emerald-50/10 border-r border-slate-50">{{ $o ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold text-amber-700 bg-amber-50/10 border-r border-slate-50">{{ $t ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold text-brand-700 bg-brand-50/10 border-r border-slate-50">{{ $i_count ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold text-brand-700 bg-brand-50/10 border-r border-slate-50">{{ $s ?: '-' }}</td>
                                <td class="p-2 text-center text-[11px] font-bold text-rose-700 bg-rose-50/10 border-r border-slate-100">{{ $a ?: '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-slate-100/50">
                            <td class="p-4 sticky left-0 bg-slate-50 z-10 border-r border-slate-200 font-black text-xs text-slate-900 uppercase">Total Akumulasi</td>
                            <td class="p-2 text-center text-[11px] font-black text-white bg-emerald-600 border-r border-slate-200">{{ ($total_o + $total_t) ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-black text-emerald-800 bg-emerald-100/20 border-r border-slate-200">{{ $total_o ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-black text-amber-800 bg-amber-100/20 border-r border-slate-200">{{ $total_t ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-black text-brand-800 bg-brand-100/20 border-r border-slate-200">{{ $total_i ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-black text-brand-800 bg-brand-100/20 border-r border-slate-200">{{ $total_s ?: '-' }}</td>
                            <td class="p-2 text-center text-[11px] font-black text-rose-800 bg-rose-100/20">{{ $total_a ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend -->
        <div class="px-4 md:px-6 py-4 bg-slate-50/50 border-t border-slate-50 flex flex-wrap gap-3 md:gap-4 no-print">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">H</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[10px]">T</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Telat</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px]">I</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Izin</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px]">S</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Sakit</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-[10px]">A</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Alpa</span>
            </div>
        </div>
    </div>
</div>
@endsection
